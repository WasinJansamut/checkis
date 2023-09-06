<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLogModel extends Model
{
    protected $table = 'system_log';

    protected $casts = ['detail'=>"json"];

}
