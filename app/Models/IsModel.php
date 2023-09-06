<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsModel extends Model
{
    //    use HasFactory;

    protected $connection = "mysql_is";
    protected $table = "is";
    protected $primaryKey = 'id';
    protected $guarded = [];

    protected $hidden = ['pid', 'name', 'fname', 'address'];
}
