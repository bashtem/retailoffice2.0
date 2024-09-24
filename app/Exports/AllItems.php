<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Database\Eloquent\Collection;

class AllItems implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public $reportResult;
    public $data;
    public $totalRow;

    public function __construct($data){
        $this->data = $data;
        $this->reportResult = $data["allItems"];
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    public function collection()
    {
        $this->reportResult[] = ["TOTAL", '', $this->data['totalQty'], '', '' ];
        return collect($this->reportResult); 
    }

    public function headings(): array{
        return [
            'ITEM',
            'UNIT',
            'QUANTITY',
            'COST PRICE',
            'SALE PRICE'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '#,##0.000',
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        $lastrow = $this->totalRow;
        return [
            
            AfterSheet::class => function(AfterSheet $event) use ($lastrow){
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
