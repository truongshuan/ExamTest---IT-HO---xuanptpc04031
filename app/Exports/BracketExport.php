<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BracketExport implements FromCollection, ShouldAutoSize, WithStyles
{
    protected $bracket;

    public function __construct($bracket)
    {
        $this->bracket = $bracket;
    }

    public function collection()
    {
        $data = [];

        foreach ($this->bracket as $round => $matches) {
            foreach ($matches as $match) {
                $data[] = [
                    $match[0],
                    $match[1],
                    'Vòng ' . ($round + 1)
                ];
            }
        }

        return collect($data);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->getBorders()->getAllBorders()->setBorderStyle('thin');
    }
}
