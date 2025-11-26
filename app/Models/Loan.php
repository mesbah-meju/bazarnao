<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    public function schedules() {
        return $this->hasMany(AmortizationSchedule::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
}
