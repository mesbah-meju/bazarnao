<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccSubtype extends Model
{
    use HasFactory; 

    protected $fillable = [
        'name',
        'reference_no',
        'status',
        'created_by',
        'created_at',
    ];

    public function subcodes()
    {
        return $this->hasMany(AccSubcode::class, 'sub_type_id');
    }
}
