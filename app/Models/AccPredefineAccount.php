<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AccPredefineAccount extends Model
{
    use HasFactory;
    
    public static function get_predefine_code()
    {
        return DB::getSchemaBuilder()->getColumnListing('acc_predefine_accounts');
    }

    public static function get_predefine_code_values()
    {
        return self::first();
    }
}
