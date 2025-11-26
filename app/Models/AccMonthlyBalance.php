<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccMonthlyBalance extends Model
{
    use HasFactory;

    public function get_financial_year()
    {
        return $this->belongsTo(FinancialYear::class, 'fyear');
    }

    public function coa()
    {
        return $this->belongsTo(AccCoa::class, 'coa_id', 'head_code');
    }
}
