<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ProductWiseSalesReport implements FromView
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('backend.reports.excel.product_wise_sales_report', $this->data);
    }
}
