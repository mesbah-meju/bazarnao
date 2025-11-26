<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransferSummaryExport implements FromView
{
    protected $transfers;
    protected $totalQty;
    protected $totalAmount;

    public function __construct($transfers, $totalQty, $totalAmount)
    {
        $this->transfers = $transfers;
        $this->totalQty = $totalQty;
        $this->totalAmount = $totalAmount;
    }

    public function view(): View
    {
        return view('backend.reports.export.transfer_summary_export', [
            'transfers' => $this->transfers,
            'totalQty' => $this->totalQty,
            'totalAmount' => $this->totalAmount,
        ]);
    }
}

