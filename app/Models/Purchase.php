<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //protected $table = 'purchases';
    protected $fillable = ['supplier_id','date','total_value','purchase_no','voucher_img'];
    public function purchase_details()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

}
