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

class Sales implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
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
        $this->reportResult = DB::select("SELECT orders.order_date, customers.cus_name, orders.order_no, users.name, payment_types.payment_desc, orders.order_total_amount as amount, sum(order_items.quantity * order_items.cost_price) as 'cost', sum(order_items.amount - (order_items.quantity * order_items.cost_price)) as 'gross_margin' from orders join customers on customers.cus_id = orders.cus_id join users on users.user_id = orders.user_id join payment_types on payment_types.payment_id = orders.payment_id join order_items on order_items.order_id = orders.order_id where  orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? GROUP BY orders.order_no ORDER BY orders.order_date", [ $this->data['storeId'], $this->data['fromDate'], $this->data['toDate'], $this->data['qtyId'] ]);
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    public function collection()
    {
        $this->reportResult[] = ["TOTAL", '', '', '', '', $this->data['totals']['totalAmount'], $this->data['totals']['totalCost'], $this->data['totals']['totalGrossProfit'] ];
        return collect($this->reportResult); 
    }

    public function headings(): array{
        return [
            'ORDER DATE',
            'CUSTOMER',
            'ORDER NO.',
            'CASHIER',
            'PAYMENT',
            'AMOUNT',
            'COST',
            'GROSS MARGIN'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        $lastrow = $this->totalRow;
        return [
            
            AfterSheet::class => function(AfterSheet $event) use ($lastrow){
                $event->sheet->getDelegate()->getStyle('A1:H1')->applyFromArray(
                    [
                        'font' => [
                            'bold' => true,
                        ]                        
                    ]
                );
                $event->sheet->getDelegate()->getStyle("A".$lastrow.':H'.$lastrow)->applyFromArray(
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