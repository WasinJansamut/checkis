<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibHospcodeModel extends Model
{
    //    use HasFactory;
    protected $connection = "mysql_is";
    protected $table = "lib_hospcode";
    protected $primaryKey = 'off_id';
    protected $guarded = [];
}
