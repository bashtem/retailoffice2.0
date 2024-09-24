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

class SalesItemsByUnitPrice implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
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
        $this->reportResult = DB::select("SELECT items.item_name, qty_types.qty_desc, order_items.price, order_items.quantity, (order_items.price * order_items.quantity) as 'sale_amount' from order_items join items on items.item_id = order_items.item_id join orders on orders.order_id = order_items.order_id join qty_types on qty_types.qty_id = orders.qty_id where  orders.store_id = ? AND (orders.order_date between ? AND ?) AND orders.order_status !='CANCLED' AND orders.qty_id = ? ORDER BY orders.order_date", [ $this->data['storeId'], $this->data['fromDate'], $this->data['toDate'], $this->data['qtyId'] ] );
        $this->totalRow = (new Collection($this->reportResult))->count() + 2;
    }

    public function collection()
    {
        $this->reportResult[] = ["TOTAL", '', '',  $this->data['totals']['totalQty'], $this->data['totals']['totalAmount'] ];
        return collect($this->reportResult); 
    }

    public function headings(): array{
        return [
            'ITEM',
            'UNIT',
            'PRICE',
            'QUANTITY',
            'SALE AMOUNT'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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
