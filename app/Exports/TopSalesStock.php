<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Database\Eloquent\Collection;


class TopSalesStock implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public $data;
    public $records;
    public $totalRow;
    public $reportResult;

    public function __construct($data){
        $this->data = $data;
        $this->records =  collect(DB::select("SELECT SUM(order_items.quantity) as totalQty, SUM((order_items.price)*(order_items.quantity)) as totalAmount, SUM(order_items.cost_price * order_items.quantity) as cost_amount, SUM(order_items.amount - (order_items.cost_price * order_items.quantity)) as gross_profit, COUNT(distinct(order_items.price)) as totalRecords from order_items JOIN orders ON order_items.order_id = orders.order_id where orders.store_id = ? AND (date(order_items.created_at) between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ", [$this->data['storeId'], $this->data['fromDate'], $this->data['toDate'], $this->data['qtyId'] ] ))->first();
        $this->reportResult = DB::select("SELECT items.item_name, qty_types.qty_desc, SUM(order_items.quantity) as totalQty, SUM((order_items.price)*(order_items.quantity)) as amount, SUM(order_items.cost_price * order_items.quantity) as cost_amount, SUM(order_items.amount - (order_items.cost_price * order_items.quantity)) as gross_profit FROM order_items JOIN orders ON order_items.order_id = orders.order_id JOIN items ON items.item_id = order_items.item_id JOIN qty_types ON orders.qty_id = qty_types.qty_id where orders.store_id = ? AND ( date(order_items.created_at) between ? AND ? ) AND orders.order_status !='CANCLED' AND orders.qty_id = ? GROUP BY order_items.item_id  ORDER BY items.item_name", [$this->data['storeId'], $this->data['fromDate'], $this->data['toDate'], $this->data['qtyId'] ]);
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    public function collection(){
        $this->reportResult[] = ["TOTAL", '', $this->records->totalQty, $this->records->totalAmount, $this-> records->cost_amount, $this-> records->gross_profit];
        return collect($this->reportResult); 
    }

    public function headings(): array{
        return [
            'ITEM NAME',
            'UNIT',            
            'QTY',
            'SALE AMOUNT',
            'COST AMOUNT',
            'GROSS PROFIT',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        $lastrow = $this->totalRow;
        return [
            
            AfterSheet::class => function(AfterSheet $event) use ($lastrow){
                $event->sheet->getDelegate()->getStyle('A1:F1')->applyFromArray(
                    [
                        'font' => [
                            'bold' => true,
                        ]                        
                    ]
                );
                $event->sheet->getDelegate()->getStyle("A".$lastrow.':F'.$lastrow)->applyFromArray(
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