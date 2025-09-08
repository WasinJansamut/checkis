<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibHospcode extends Model
{
    use HasFactory;
    protected $table = "lib_hospcode";
    protected $primaryKey = 'off_id';
    protected $guarded = [];

    // ถ้า off_id ต้องการเก็บเลข 0 ด้านหน้าให้เป็น string เสมอ
    protected $casts = [
        'off_id' => 'string',
        'region' => 'string',
    ];

    protected static function booted()
    {
        static::addGlobalScope('active_status', function ($query) {
            $query->whereIn('status', ['กำลังใช้งาน', 'เปิดดำเนินการ']);
        });
    }

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
