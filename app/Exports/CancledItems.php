<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Database\Eloquent\Collection;

class CancledItems implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;
    public $reportResult;
    public $totalRow;

    public function __construct($data){
        $this->data = $data;
        $this->reportResult = $data['cancledItems'];
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    public function collection()
    {
        $this->reportResult[] = ["TOTAL", '', '', '', '', '', '', $this->data['totals']['totalQty'], $this->data['totals']['totalAmount'], '' ];
        return collect($this->reportResult); 
    }

    public function headings(): array{
        return [
            'ORDER DATE',
            'ORDER NO.',
            'CUSTOMER',
            'CASHIER',
            'ITEM',
            'UNIT',
            'PRICE',
            'QTY.',
            'AMOUNT',
            'NOTE'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        $lastrow = $this->totalRow;
        return [
            
            AfterSheet::class => function(AfterSheet $event) use ($lastrow){
                $event->sheet->getDelegate()->getStyle('A1:J1')->applyFromArray(
                    [
                        'font' => [
                            'bold' => true,
                        ]                        
                    ]
                );
                $event->sheet->getDelegate()->getStyle("A".$lastrow.':J'.$lastrow)->applyFromArray(
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
