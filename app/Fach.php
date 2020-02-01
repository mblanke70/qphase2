<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fach extends Model
{
    protected $table      = 'faecher';
    protected $primaryKey = 'code';
    //protected $keyType    = string;

    public $incrementing  = false;
}
