<?php

namespace App\Exports;

use App\Support\SpreadsheetResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductsExportTemplate
{
    public static function spreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headings = ProductsExport::headings();
        $sheet->fromArray([$headings], null, 'A1');

        $sample = [
            '1234567890123',
            'PROD-001',
            'Sample Product',
            'Sample Brand',
            'Sample Manufacturer',
            'Baby Care',
            'Short description here',
            'Full description here',
            10,
            'Yes',
            5,
            'Yes',
            'No',
            'No',
        ];

        $row = array_pad($sample, count($headings), null);
        $row[14] = 19.99;   // Unit Price
        $row[15] = 100;     // Unit Stock
        $row[16] = 10;      // Unit MOQ
        $row[17] = 180.00;  // Case Price
        $row[18] = 50;      // Case Stock
        $row[19] = 10;      // Case MOQ
        $row[20] = 864.00;  // Layer Price
        $row[21] = 10;      // Layer Stock
        $row[22] = 10;      // Layer MOQ
        $row[23] = 3456.00; // Pallet Price
        $row[24] = 5;       // Pallet Stock
        $row[25] = 10;      // Pallet MOQ
        $sheet->fromArray([$row], null, 'A2');

        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F3D5E');
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->getColor()->setARGB('FFFFFFFF');

        // Freeze header row
        $sheet->freezePane('A2');

        // Add a notes block on a second sheet
        $notes = $spreadsheet->createSheet();
        $notes->setTitle('Instructions');
        $notes->setCellValue('A1', 'How to use this template');
        $notes->getStyle('A1')->getFont()->setBold(true);
        $notes->setCellValue('A3', '1. Keep the header row as-is. It tells the system which column is which.');
        $notes->setCellValue('A4', '2. Use EAN or SKU to update an existing product. A brand new combination creates a new product.');
        $notes->setCellValue('A5', '3. Variant columns: unit, case, layer, pallet. Leave price blank to skip that variant.');
        $notes->setCellValue('A6', '4. Price columns are WHOLESALE base prices in GBP. Stock is the quantity you hold.');
        $notes->setCellValue('A7', '6. Booleans accept Yes/No, 1/0, true/false. Yes = enabled.');

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    public static function download(string $format = 'xlsx')
    {
        return SpreadsheetResponse::download(static::spreadsheet(), "product_import_template.{$format}");
    }
}