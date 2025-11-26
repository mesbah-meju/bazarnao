<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SingleEmployeePerformanceExport implements FromView
{
    protected $data;
    protected $currentYear;
    protected $totals;
    protected $months;

    public function __construct($data, $currentYear, $totals, $months)
    {
        $this->data = $data;
        $this->currentYear = $currentYear;
        $this->totals = $totals;
        $this->months = $months;
    }

    public function view(): View
    {
        return view('backend.reports.export.single_employee_performance_export', [
            'orders' => $this->data['orders'],
            'currentYear' => $this->currentYear,
            'totals' => $this->totals,
            'months' => $this->months,
        ]);
    }
}


