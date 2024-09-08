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

class DailySalesSummary implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;
    public $totalRow;
    public $reportResult;

    public function __construct($data){
        $this->data = $data;
        $this->reportResult = DB::select("SELECT date(order_items.created_at) as 'order_date', sum(order_items.quantity) as volume, sum(order_items.amount) as amount, sum(order_items.quantity * order_items.cost_price) as cost, sum(order_items.amount - (order_items.quantity * order_items.cost_price)) as 'gross_profit' from order_items join orders on orders.order_id = order_items.order_id where  orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? GROUP BY orders.order_date ORDER BY orders.order_date", [ $this->data['storeId'], $this->data['fromDate'], $this->data['toDate'], $this->data['qtyId'] ] );
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    public function collection()
    {
        $this->reportResult[] = ["TOTAL", $this->data['totals']['totalQty'], $this->data['totals']['totalAmount'], $this->data['totals']['totalCost'],$this->data['totals']['totalGrossProfit']  ];
        return collect($this->reportResult); 
    }

    public function headings(): array{
        return [
            'ORDER DATE',
            'QUANTITY',
            'AMOUNT',
            'COST',
            'GROSS PROFIT'
        ];
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
