<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TransferDetailsExport implements FromView
{
    protected $transfers;

    public function __construct($transfers)
    {
        $this->transfers = $transfers;
    }

    public function view(): View
    {
        return view('backend.reports.export.transfer_details_export', [
            'transfers' => $this->transfers,
        ]);
    }
}
