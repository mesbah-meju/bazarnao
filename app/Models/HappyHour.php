<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class HappyHour extends Model
{
    protected $with = ['happy_hour_translations'];

    public function getTranslation($field = '', $lang = false){
        $lang = $lang == false ? App::getLocale() : $lang;
        $happy_hour_translation = $this->happy_hour_translations->where('lang', $lang)->first();
        return $happy_hour_translation != null ? $happy_hour_translation->$field : $this->$field;
    }

    public function happy_hour_translations(){
      return $this->hasMany(HappyHourTranslation::class);
    }
    public function happy_hour_products()
    {
        return $this->hasMany(HappyHourProduct::class);
    }
}
