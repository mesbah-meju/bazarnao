<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccVoucher extends Model
{
    use HasFactory;

    public function vouchers() {
        return $this->hasMany(AccVoucher::class, 'voucher_no', 'voucher_no');
    }

    public function get_financial_year()
    {
        return $this->belongsTo(FinancialYear::class, 'fyear');
    }

    public function coa()
    {
        return $this->belongsTo(AccCoa::class, 'coa_id', 'head_code');
    }

    public function rev_coa()
    {
        return $this->belongsTo(AccCoa::class, 'rev_code', 'head_code');
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

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function relvalues() {
        return $this->hasMany(AccSubcode::class, 'sub_type_id', 'relational_type');
    }

    public function reltype()
    {
        return $this->belongsTo(AccSubtype::class, 'relational_type');
    }

    public function relvalue()
    {
        return $this->belongsTo(AccSubcode::class, 'relational_value');
    }
}
