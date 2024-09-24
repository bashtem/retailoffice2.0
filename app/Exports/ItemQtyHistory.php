<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class ItemQtyHistory implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
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

    public function headings(): array{
        return [
            'USER',
            'OLD QTY',
            'NEW QTY',
            'DIFFERENCE',
            'DATE',
            'TIME',
        ];
    }

    public function registerEvents(): array
    {
        return [
            
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getDelegate()->getStyle('A1:F1')->applyFromArray(
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
