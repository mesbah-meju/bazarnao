<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'voucher_id',
        'fyear',
        'voucher_no',
        'voucher_type',
        'reference_no',
        'voucher_date',
        'coa_id',
        'narration',
        'cheque_no',
        'cheque_date',
        'is_honour',
        'ledger_comment',
        'debit',
        'credit',
        'store_id',
        'is_posted',
        'is_opening',
        'created_by',
        'updated_by',
        'is_approved',
        'rev_code',
        'sub_type',
        'sub_code',
        'warehouse_id',
        'relational_type',
        'relational_value',
    ];


    public function vouchers()
    {
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

    public function subtypes()
    {
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

    public function reltype()
    {
        return $this->belongsTo(AccSubtype::class, 'relational_type');
    }

    public function relvalue()
    {
        return $this->belongsTo(AccSubcode::class, 'relational_value');
    }
}
