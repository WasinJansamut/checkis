<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IsModel extends Model
{
    protected $connection = "mysql_is";
    protected $table = "is";
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $hidden = ['pid', 'name', 'fname', 'address'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            return false; // ป้องกันการเพิ่มข้อมูล
        });

        static::updating(function ($model) {
            return false; // ป้องกันการอัปเดตข้อมูล
        });

        static::deleting(function ($model) {
            return false; // ป้องกันการลบข้อมูล
        });
    }
}
