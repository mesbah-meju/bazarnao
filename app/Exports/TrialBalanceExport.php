<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TrialBalanceExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.trialbalance.export', [
            'results' => $this->data['results'],
            'openings' => $this->data['openings'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'warehouseName' => $this->data['warehouseName'],
            'currency' => $this->data['currency'],
        ]);
    }
}

