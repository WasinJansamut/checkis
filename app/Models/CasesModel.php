<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasesModel extends Model
{
    protected $table = 'cases';
    public $timestamps = false;

    public function _error_type()
    {
        return $this->hasOne(ErrorTypeModel::class, 'id', 'errorType');
    }
}
