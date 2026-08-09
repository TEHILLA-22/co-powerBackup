<?php

namespace App\Console\Commands;

use App\Imports\SianProductsImport;
use Illuminate\Console\Command;

class ImportSianProducts extends Command
{
    protected $signature = 'sian:import
                            {file : Absolute path to the SIAN price-list .xlsx file}';

    protected $description = 'Import products from the SIAN price-list spreadsheet (Blade layout contract).';

    public function handle(SianProductsImport $importer): int
    {
        $file = $this->argument('file');

        if (! is_file($file) || ! is_readable($file)) {
            $this->error('File not found or not readable: ' . $file);

            return self::FAILURE;
        }

        $start = hrtime(true);
        $stats = $importer->import($file);
        $elapsed = number_format((hrtime(true) - $start) / 1e9, 2);

        $this->info("Imported: {$stats['imported']}");
        $this->info("Updated: {$stats['updated']}");
        $this->info("Failed: {$stats['failed']}");
        $this->info("Elapsed: {$elapsed}s");

        foreach ($stats['errors'] as $error) {
            $this->warn($error);
        }

        return self::SUCCESS;
    }
}