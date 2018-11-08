<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 11/8/2018
 * Time: 5:25 PM
 */

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;

class ApiKey extends BaseModel
{
    use SoftDeletes;

    protected $table = 'api_keys';

    protected $hidden = ['id'];

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

}