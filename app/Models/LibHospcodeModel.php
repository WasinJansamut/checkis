<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibHospcodeModel extends Model
{
    protected $connection = "mysql_is";
    protected $table = "lib_hospcode";
    protected $primaryKey = 'off_id';
    protected $guarded = [];

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

    public function _changwat() // จังหวัด
    {
        return $this->hasOne(LibChangwatModel::class, 'code', 'changwatcode');
    }
}
