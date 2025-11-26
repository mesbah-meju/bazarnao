<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Warehouse extends Model
{
   protected $fillable = ['name', 'code', 'status'];

}
