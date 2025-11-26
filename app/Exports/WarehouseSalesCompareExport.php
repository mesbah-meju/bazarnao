<?php

namespace App\Exports;

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WarehouseSalesCompareExport implements FromView, WithStyles
{
    protected $productsData;
    protected $currentYear;

    public function __construct($productsData, $currentYear)
    {
        $this->productsData = $productsData;
        $this->currentYear = $currentYear;
    }

    public function view(): View
    {
        return view('backend.reports.export.warehouse_sales_compare_export', [
            'productsData' => $this->productsData,
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




