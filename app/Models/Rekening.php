<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $fillable = ['user_id', 'bank', 'rekening', 'nama'];
    public $timestamps = false;
}
