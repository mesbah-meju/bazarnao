<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CoaPrintExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.accounts.coa_export', [
            'coaData' => $this->data['coaData'],
            'maxLevel' => $this->data['maxLevel'],
        ]);
    }
}

