<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $uploads = '/sliderimg/';

    protected $guarded = [];

    public function getFileAttribute($photo){

        return $this->uploads . $photo;

    }
}
