<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CashTransferExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.cash_transfer.export', [
            'transfers' => $this->data['transfers'],
            'from_warehouse_name' => $this->data['from_warehouse_name'],
            'to_warehouse_name' => $this->data['to_warehouse_name'],
            'from_date' => $this->data['from_date'],
            'to_date' => $this->data['to_date'],
        ]);
    }
}

