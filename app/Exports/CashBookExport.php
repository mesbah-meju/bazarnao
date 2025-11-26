<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashBookExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.cashbook.export', [
            'HeadName2' => $this->data['HeadName2'],
            'prebalance' => $this->data['prebalance'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'HeadName' => $this->data['HeadName'],
            'warehouseName' => $this->data['warehouseName'],
        ]);
    }
}

