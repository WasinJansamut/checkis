<?php

namespace App\Models;

//use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobsModel extends BaseModel
{
    // use HasFactory;
    protected $table = 'jobs';
    protected $dates = ['start_time', 'start_date', 'end_date'];

    public function getHospName()
    {
        return $this->hasOne(HospcodeModel::class, 'hospcode', 'hosp');
    }

    public function getUser()
    {
        return $this->hasOne(User::class, 'username', 'hosp');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
