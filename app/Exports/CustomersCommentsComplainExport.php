<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomersCommentsComplainExport implements FromView
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('backend.reports.export.customers_comments_complain_export', [
            'comment_complain' => $this->data['comment_complain'],
        ]);
    }
}



