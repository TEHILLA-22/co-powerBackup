<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExportTemplate implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
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
                19.99,
                100,
                10,
                180.00,
                50,
                10,
                864.00,
                10,
                10,
                3456.00,
                5,
                10,
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'EAN',
            'SKU',
            'Name',
            'Brand',
            'Manufacturer',
            'Category',
            'Short Description',
            'Description',
            'MOQ',
            'MOQ Enforced',
            'MOQ Increment',
            'Is Active',
            'Is Featured',
            'Is On Sale',
            'Unit Price',
            'Unit Stock',
            'Unit MOQ',
            'Case Price',
            'Case Stock',
            'Case MOQ',
            'Layer Price',
            'Layer Stock',
            'Layer MOQ',
            'Pallet Price',
            'Pallet Stock',
            'Pallet MOQ',
        ];
    }
}