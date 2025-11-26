<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class IncomeStatementYearlyExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.income_statement.yearly_export', [
            'incomes' => $this->data['incomes'],
            'costofgoodsolds' => $this->data['costofgoodsolds'],
            'expenses' => $this->data['expenses'],
            'curentYear' => $this->data['curentYear'],
            'financial_years' => $this->data['financial_years'],
            'warehouseName' => $this->data['warehouseName'],
        ]);
    }
}

