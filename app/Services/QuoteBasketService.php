<?php

namespace App\Services;

use App\Models\QuoteItem;
use Illuminate\Support\Facades\DB;

/**
 * DB-backed replacement for the session-based quote basket.
 *
 * Keeps the exact same array shape the controllers already use:
 *   [basket_key => ['variant_id', 'product_id', 'quantity', 'variant_type']]
 * but persists each item as a row keyed to the authenticated user, so the
 * basket survives logout/login.
 */
class QuoteBasketService
{
    /**
     * Load the current user's basket in the legacy array shape.
     */
    public function items(): array
    {
        if (!auth()->check()) {
            return [];
        }

        $items = [];

        foreach (QuoteItem::where('user_id', auth()->id())->orderBy('id')->get() as $row) {
            $items[$row->basket_key] = [
                'variant_id' => $row->product_variant_id,
                'product_id' => $row->product_id,
                'quantity' => (int) $row->quantity,
                'variant_type' => $row->variant_type,
            ];
        }

        return $items;
    }

    /**
     * Replace the current user's basket with the given array.
     */
    public function save(array $items): void
    {
        if (!auth()->check()) {
            return;
        }

        $userId = auth()->id();

        DB::transaction(function () use ($userId, $items) {
            QuoteItem::where('user_id', $userId)->delete();

            foreach ($items as $key => $item) {
                QuoteItem::create([
                    'user_id' => $userId,
                    'basket_key' => (string) $key,
                    'product_variant_id' => $item['variant_id'] ?? null,
                    'product_id' => $item['product_id'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'variant_type' => $item['variant_type'] ?? null,
                ]);
            }
        });
    }

    /**
     * Empty the current user's basket.
     */
    public function clear(): void
    {
        if (!auth()->check()) {
            return;
        }

        QuoteItem::where('user_id', auth()->id())->delete();
    }

    /**
     * Number of items in the current user's basket (guests: 0).
     */
    public function count(): int
    {
        if (!auth()->check()) {
            return 0;
        }

        return QuoteItem::where('user_id', auth()->id())->count();
    }
}