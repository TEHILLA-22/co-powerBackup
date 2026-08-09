<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SpreadsheetResponse
{
    protected static array $mimeTypes = [
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xls' => 'application/vnd.ms-excel',
        'csv' => 'text/csv',
        'tsv' => 'text/tab-separated-values',
    ];

    public static function save(Spreadsheet $spreadsheet, string $format): string
    {
        $format = strtolower($format) === 'xls' ? 'xls' : (strtolower($format) === 'tsv' ? 'tsv' : (strtolower($format) === 'csv' ? 'csv' : 'xlsx'));
        $file = tempnam(sys_get_temp_dir(), 'cw_') . '.' . $format;

        switch ($format) {
            case 'csv':
                $writer = (new Csv($spreadsheet))->setDelimiter(',');
                $writer->save($file);
                break;
            case 'tsv':
                $writer = (new Csv($spreadsheet))->setDelimiter("\t");
                $writer->save($file);
                break;
            case 'xls':
                $writer = new Xls($spreadsheet);
                $writer->save($file);
                break;
            default:
                $writer = new Xlsx($spreadsheet);
                $writer->save($file);
        }

        return $file;
    }

    public static function download(Spreadsheet $spreadsheet, string $filename): BinaryFileResponse
    {
        $format = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file = self::save($spreadsheet, $format);

        return response()
            ->download($file, $filename, [
                'Content-Type' => self::$mimeTypes[$format] ?? 'application/octet-stream',
            ])
            ->deleteFileAfterSend(true);
    }

    public static function load(string $path): Spreadsheet
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $reader = null;

        switch ($ext) {
            case 'xls':
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                break;
            case 'csv':
                $reader = (new \PhpOffice\PhpSpreadsheet\Reader\Csv())->setDelimiter(',');
                break;
            case 'tsv':
                $reader = (new \PhpOffice\PhpSpreadsheet\Reader\Csv())->setDelimiter("\t");
                break;
            default:
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }

        $reader->setReadDataOnly(true);

        return $reader->load($path);
    }
}