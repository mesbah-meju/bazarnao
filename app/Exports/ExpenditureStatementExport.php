<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExpenditureStatementExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.expenditure_statement.export', [
            'expenses' => $this->data['expenses'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'warehouseName' => $this->data['warehouseName'],
            'currency' => $this->data['currency'],
        ]);
    }
}

