<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    /**
     * Where If will only check if value exist
     *
     * @param $query
     * @param string $column
     * @param $value
     * @return mixed
     */
    protected $hidden = ['created_at', 'updated_at'];


    public function scopeWhereIf($query, string $column, $value = NULL)
    {
        if ($value) {
            return $query->where($column, $value);
        }
        return $query;
    }

    public function scopeWhereIfBetween($query, string $column, $value = [])
    {
        if (count($value) == 2) {
            if($value[0]){
                $query->where($column , '>=',$value[0]);
            }
            if($value[1]){
                $query->where($column , '<=',$value[1]);
            }

        }
        return $query;
    }

    public function scopeWhereIfWithOp($query, string $column,$operator = '=', $value = NULL)
    {
        if ($value) {
            return $query->where($column, $operator, $value);
        }
        return $query;
    }

    public function scopeWhereInCastArray($query, string $column, $value = [], $data = NULL)
    {
        /**
         * get the model
         * filter array $column with $value
         * if pass, collect that id into $ids
         * return query wherein id in ids
         */
        $ids = [];
        if(count($value) > 0 && !empty($value[0])) {
            $data = $data ?? $query->get();
            foreach($data as $datum){
                foreach ($value as $item) {
                    $isHave = false;
                    foreach ($datum->{$column} as $item_value) {
                        if ($item_value == $item) {
                            $isHave = true;
                            break;
                        }
                    }
                    if(!$isHave){
                        break;
                    }
                }
                if($isHave){
                    $ids[] = $datum->id;
                }
            }
            return $query->whereIn('id',$ids);
        }
        return $query;

    }

    /**
     * Where like if will only check if value exist
     *
     * @param $query
     * @param string $column
     * @param $value
     * @return mixed
     */
    public function scopeWhereLikeIf($query, string $column, $value = NULL)
    {
        if ($value) {
            return $query->where($column, 'like', "%{$value}%");
        }
        return $query;
    }
}
