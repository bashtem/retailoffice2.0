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

class CustomerAmount implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;


    public $data;
    public $reportResult;
    public $totalRow;

    public function __construct($data)
    {
        $this->data = $data;
        $this->reportResult = $data['cusByAmountList'];
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $this->reportResult[] = ["TOTAL", $this->data['totals']['totalQty'], $this->data['totals']['totalAmount']];
        return collect($this->reportResult);
    }

    public function headings(): array
    {
        return [
            'CUSTOMER',
            'QUANTITY',
            'AMOUNT',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        $lastrow = $this->totalRow;
        return [

            AfterSheet::class => function (AfterSheet $event) use ($lastrow) {
                $event->sheet->getDelegate()->getStyle('A1:D1')->applyFromArray(
                    [
                        'font' => [
                            'bold' => true,
                        ]
                    ]
                );
                $event->sheet->getDelegate()->getStyle("A" . $lastrow . ':D' . $lastrow)->applyFromArray(
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
