<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HappyHourTranslation extends Model
{
  protected $fillable = ['title', 'lang', 'happy_hour_id'];

  public function happy_hour(){
    return $this->belongsTo(HappyHour::class);
  }

}
