<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdminInventoryAdjustment implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;


    public $data;
    public $reportResult;
    public $totalRow;

    public function __construct($data)
    {
        $this->data = $data;
        $this->reportResult = $data['inventoryAdjustmentList'];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->reportResult);
    }

    public function headings(): array
    {
        return [
            'ADMIN',
            'ITEM',
            'OLD QTY',
            'NEW QTY',
            'DIFFERENCE',
            'DATE',
            'TIME',
        ];
    }

}
