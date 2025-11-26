<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class WarehouseCashTransferListExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.reports.export.cash_transfer_list_export', [
            'transfer' => $this->data['transfer'],
        ]);
    }
}



