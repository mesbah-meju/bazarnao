<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadGroupChildReportModel implements FromCollection, WithMapping, WithHeadings
{
    public function __construct($start_date=null, $end_date=null,$user_id=null,$warehouse=null)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->user_id = $user_id;
        $this->warehouse = $warehouse;
    }

    public function collection()
    {
        $warehouse = $this->warehouse;
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
        $sort_user_id = $this->user_id;

        $cust = array();
        $orders = array();

        $products = DB::table('products')
        ->whereNotNull('parent_id')
        ->get();

        return collect($products);
    }

    public function headings(): array
    {
        return [
            'Product Name',
            'Price',
        ];
    }



    public function map($products): array
    {
        return [
            $products->name,
            $products->unit_price,
           
        ];
    }
}
