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

class SaleItems implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
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
        $this->reportResult = DB::select("SELECT orders.order_date, orders.order_no, customers.cus_name, users.name, items.item_name, qty_types.qty_desc,  order_items.price, order_items.quantity, (order_items.price * order_items.quantity) as amount, (? * order_items.quantity) as cost, ((order_items.price * order_items.quantity) - (? * order_items.quantity)) as gross_profit  FROM orders join order_items on orders.order_id = order_items.order_id join items on items.item_id = order_items.item_id join customers on customers.cus_id = orders.cus_id join users on users.user_id = orders.user_id join qty_types on qty_types.qty_id = orders.qty_id where  orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [$this->data['costPrice'], $this->data['costPrice'], $this->data['storeId'], $this->data['fromDate'], $this->data['toDate'], $this->data['qtyId'] ]);
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    public function collection()
    {
        $this->reportResult[] = ["TOTAL", '', '', '', '', '', '', $this->data['totals']['totalQty'], $this->data['totals']['totalAmount'], $this->data['totals']['totalCost'], $this->data['totals']['totalGrossProfit']];
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
            'COST',
            'GROSS PROFIT'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        $lastrow = $this->totalRow;
        return [
            
            AfterSheet::class => function(AfterSheet $event) use ($lastrow){
                $event->sheet->getDelegate()->getStyle('A1:K1')->applyFromArray(
                    [
                        'font' => [
                            'bold' => true,
                        ]                        
                    ]
                );
                $event->sheet->getDelegate()->getStyle("A".$lastrow.':K'.$lastrow)->applyFromArray(
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
