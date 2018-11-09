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

    /**
     * Find a single entity by key value.
     *
     * @param array $condition
     * @param array $select
     * @param array $with
     * @return mixed
     * @author Toinn
     */
    public function getFirstBy(array $condition = [], array $select = [], array $with = [])
    {
        $query = $this->make($with);

        if (!empty($select)) {
            return $query->where($condition)->select($select)->first();
        }

        return $query->where($condition)->first();
    }

    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     * @return mixed
     * @author Toinn
     */
    public function make(array $with = [])
    {
        return $this->with($with);
    }

}