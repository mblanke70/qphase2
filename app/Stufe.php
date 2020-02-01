<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stufe extends Model
{
    protected $table      = 'stufen';
    protected $primaryKey = 'code';
    //protected $keyType  = string;

    public $incrementing  = false;
}
