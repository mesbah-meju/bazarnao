<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ParentChildProductListExport implements FromView
{
    protected $parents;
    protected $children;

    public function __construct($data)
    {
        $this->parents = $data['parents'];
        $this->children = $data['children'];
    }

    public function view(): View
    {
        return view('backend.reports.export.parent_child_product_export', [
            'parents' => $this->parents,
            'children' => $this->children,
        ]);
    }
}
