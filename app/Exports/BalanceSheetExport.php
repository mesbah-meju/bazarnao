<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BalanceSheetExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.balance_sheet.export', [
            'assets' => $this->data['assets'],
            'liabilities' => $this->data['liabilities'],
            'equitys' => $this->data['equitys'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'warehouseName' => $this->data['warehouseName'],
            'financialyears' => $this->data['financialyears'],
            'currency' => $this->data['currency'],
        ]);
    }
}

