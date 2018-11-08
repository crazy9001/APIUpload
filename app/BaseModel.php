<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 11/8/2018
 * Time: 5:41 PM
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public function scopeSortOrder($query, $direction = 'DESC')
    {
        return $query->orderBy('sort_order', $direction)->orderBy('id', 'DESC');
    }

    public function scopeWhereActive($query)
    {
        return $query->where('active', 1);
    }

}