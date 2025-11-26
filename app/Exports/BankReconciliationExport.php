<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BankReconciliationExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.bank_reconciliation.export', [
            'vouchers' => $this->data['vouchers'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'warehouseName' => $this->data['warehouseName'],
            'bankName' => $this->data['bankName'],
            'currency' => $this->data['currency'],
        ]);
    }
}

