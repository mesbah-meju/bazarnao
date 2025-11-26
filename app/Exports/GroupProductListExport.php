<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GroupProductListExport implements FromView
{
    protected $groupProducts;
    protected $productDetails;

    public function __construct($data)
    {
        $this->groupProducts = $data['groupProducts'];
        $this->productDetails = $data['productDetails'];
    }

    public function view(): View
    {
        return view('backend.reports.export.group_product_export', [
            'groupProducts' => $this->groupProducts,
            'productDetails' => $this->productDetails,
        ]);
    }
}
