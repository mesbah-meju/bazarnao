<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccSubcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_type_id',
        'name',
        'reference_no',
        'status',
        'created_by',
        'created_at',
    ];
    
    public function subtype()
    {
        return $this->belongsTo(AccSubtype::class, 'sub_type_id');
    }
}
