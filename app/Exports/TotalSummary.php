<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class TotalSummary implements FromCollection, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */

    public $data;

    public function __construct($data)
    {
        $this->data = $data["data"];
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        $lastrow = count($this->data);
        return [
            
            AfterSheet::class => function(AfterSheet $event) use ($lastrow) {
                $event->sheet->getDelegate()->getStyle('A1:E1')->applyFromArray(
                    [
                        'font' => [
                            'bold' => true,
                        ]                        
                    ]
                );
                $event->sheet->getDelegate()->getStyle("A".$lastrow.':E'.$lastrow)->applyFromArray(
                    [
                        'font' => [
                            'bold' => true,
                        ]                        
                    ]
                );
            },
        ];
    }

}
