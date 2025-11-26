<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class Group_product extends Model
{
  //
  public function Product()
  {
      return $this->belongsTo(Product::class);
  }

}
