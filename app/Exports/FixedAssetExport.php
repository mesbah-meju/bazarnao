<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FixedAssetExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.fixed_asset.export', [
            'fixedAssets' => $this->data['fixedAssets'],
            'currentYear' => $this->data['currentYear'],
            'warehouseName' => $this->data['warehouseName'],
        ]);
    }
}

