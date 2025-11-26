<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccOpeningBalance extends Model
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

    public function subtype()
    {
        return $this->belongsTo(AccSubtype::class, 'sub_type');
    }

    public function subcode()
    {
        return $this->belongsTo(AccSubcode::class, 'sub_code');
    }

    public function subtypes() {
        return $this->hasMany(AccSubcode::class, 'sub_type_id', 'sub_type');
    }

    public function financial_year()
    {
        return $this->belongsTo(FinancialYear::class, 'fyear');
    }
}
