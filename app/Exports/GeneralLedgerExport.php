<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class GeneralLedgerExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.general_ledger.export', [
            'HeadName' => $this->data['HeadName'],
            'HeadName2' => $this->data['HeadName2'],
            'prebalance' => $this->data['prebalance'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'ledger' => $this->data['ledger'],
            'warehouseName' => $this->data['warehouseName'],
        ]);
    }
}

