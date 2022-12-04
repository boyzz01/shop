<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OlshopLink extends Model
{
    protected $fillable = ['user_id', 'link', 'icon'];
    public $timestamps = false;
}
