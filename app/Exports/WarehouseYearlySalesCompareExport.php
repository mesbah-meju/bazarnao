<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarehouseYearlySalesCompareExport implements FromView, WithStyles
{
    protected $productsData;
    protected $startYear;
    protected $currentYear;

    public function __construct($productsData, $startYear, $currentYear)
    {
        $this->productsData = $productsData;
        $this->startYear = $startYear;
        $this->currentYear = $currentYear;
    }

    public function view(): View
    {
        return view('backend.reports.export.warehouse_yearly_sales_compare_export', [
            'productsData' => $this->productsData,
            'startYear' => $this->startYear,
            'currentYear' => $this->currentYear,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
