<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Services\Catalog\ProductTaxonomy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AssignProductCategories extends Command
{
    protected $signature = 'categories:assign {--dry : Dry run - report coverage without writing} {--limit=0 : Limit product count (0 = all)}';

    protected $description = 'Keyword-classify all products into the parent/child taxonomy and assign category_id';

    public function handle(ProductTaxonomy $taxonomy): int
    {
        $dry = (bool) $this->option('dry');
        $limit = (int) $this->option('limit');

        // 1. Ensure all taxonomy categories exist -> slug => id map
        $categoryIds = $this->syncCategories($taxonomy);

        if ($dry) {
            $this->info('[dry-run] Categories synced but NOT saved.');
        } else {
            $this->info('Categories synced (' . count($categoryIds) . ' parents/children).');
        }

        // 2. Classify products
        $query = Product::query();
        if ($limit > 0) {
            $query->limit($limit);
        }

        $rows = $query->select(['id', 'name', 'short_description', 'description'])->get();

        $this->newLine();
        $this->line('Classifying ' . number_format($rows->count()) . ' products...');
        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        $assignments = [];
        $unmatched = [];
        $perChild = [];

        foreach ($rows as $product) {
            $text = $product->name . ' ' . $product->short_description . ' ' . $product->description;
            $result = $taxonomy->classify($text);

            if ($result === null) {
                $unmatched[] = $product->name;
            } else {
                [$parentSlug, $childSlug] = $result;
                $assignments[$product->id] = $categoryIds[$childSlug];
                $perChild[$childSlug] = ($perChild[$childSlug] ?? 0) + 1;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // 3. Report
        $assigned = count($assignments);
        $total = $rows->count();
        $this->info(sprintf('Assigned: %d / %d (%.1f%%)', $assigned, $total, $total > 0 ? $assigned / $total * 100 : 0));
        $this->info('Unmatched: ' . count($unmatched));

        $this->newLine();
        $this->line('Per-child breakdown:');
        arsort($perChild);
        foreach ($perChild as $childSlug => $count) {
            $parent = $this->parentSlugFor($taxonomy, $childSlug);
            $this->line(sprintf('  [%-2d] %s/%s', $count, $parent, $childSlug));
        }

        if ($unmatched && ! $limit) {
            $this->newLine();
            $this->line('Sample unmatched products:');
            foreach (array_slice($unmatched, 0, 60) as $name) {
                $this->line('  - ' . $name);
            }
        }

        if ($dry) {
            $this->newLine();
            $this->warn('Dry-run complete. No changes written. Re-run without --dry to apply.');
            return 0;
        }

        // 4. Bulk write assignments
        if ($assignments) {
            DB::transaction(function () use ($assignments) {
                $chunks = array_chunk($assignments, 500, true);
                foreach ($chunks as $chunk) {
                    $ids = array_keys($chunk);
                    $sql = 'CASE id ';
                    foreach ($chunk as $id => $categoryId) {
                        $sql .= 'WHEN ' . (int) $id . ' THEN ' . (int) $categoryId . ' ';
                    }
                    $sql .= 'END';

                    Product::whereIn('id', $ids)
                        ->update(['category_id' => DB::raw($sql)]);
                }
            });

            $this->newLine();
            $this->info('Assigned category_id to ' . count($assignments) . ' products.');
        }

        return 0;
    }

    /**
     * Create/refresh taxonomy categories. Returns childSlug => id map.
     */
    protected function syncCategories(ProductTaxonomy $taxonomy): array
    {
        $ids = [];
        $order = 0;

        foreach ($taxonomy->taxonomy() as $parentSlug => $parent) {
            $order++;
            $parentCategory = Category::firstOrCreate(
                ['slug' => $parentSlug],
                [
                    'name' => $parent['name'],
                    'icon' => $parent['icon'],
                    'is_active' => true,
                ]
            );

            $parentCategory->update([
                'name' => $parent['name'],
                'icon' => $parent['icon'],
                'display_order' => $order,
            ]);

            foreach ($parent['children'] as $childSlug => $child) {
                $childOrder = 0;
                $childCategory = Category::firstOrCreate(
                    ['slug' => $childSlug],
                    [
                        'name' => $child['name'],
                        'parent_id' => $parentCategory->id,
                        'icon' => $child['icon'],
                        'is_active' => true,
                    ]
                );

                $childCategory->update([
                    'name' => $child['name'],
                    'parent_id' => $parentCategory->id,
                    'icon' => $child['icon'],
                    'display_order' => ++$childOrder,
                ]);

                $ids[$childSlug] = $childCategory->id;
            }
        }

        return $ids;
    }

    protected function parentSlugFor(ProductTaxonomy $taxonomy, string $childSlug): string
    {
        foreach ($taxonomy->taxonomy() as $parentSlug => $parent) {
            if (isset($parent['children'][$childSlug])) {
                return $parentSlug;
            }
        }
        return '?';
    }
}