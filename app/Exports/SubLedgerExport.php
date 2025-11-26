<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SubLedgerExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.sub_ledger.export', [
            'ledger' => $this->data['ledger'],
            'subLedger' => $this->data['subLedger'],
            'HeadName2' => $this->data['HeadName2'],
            'prebalance' => $this->data['prebalance'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'warehouseName' => $this->data['warehouseName'],
        ]);
    }
}

