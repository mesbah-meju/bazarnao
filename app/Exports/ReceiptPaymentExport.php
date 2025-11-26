<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReceiptPaymentExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.reports.receipt_payment.export', [
            'cashOpening' => $this->data['cashOpening'],
            'bankOpening' => $this->data['bankOpening'],
            'advOpening' => $this->data['advOpening'],
            'cashClosing' => $this->data['cashClosing'],
            'bankClosing' => $this->data['bankClosing'],
            'advClosing' => $this->data['advClosing'],
            'receiptitems' => $this->data['receiptitems'],
            'paymentitems' => $this->data['paymentitems'],
            'dtpFromDate' => $this->data['dtpFromDate'],
            'dtpToDate' => $this->data['dtpToDate'],
            'warehouseName' => $this->data['warehouseName'],
            'currency' => $this->data['currency'],
        ]);
    }
}

