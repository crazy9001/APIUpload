<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 11/9/2018
 * Time: 11:16 AM
 */

namespace App;

use Eloquent;

class Folder extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    protected $table = 'media_folders';

    /**
     * The date fields for the model.clear
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @author Toi Nguyen
     */
    public function files()
    {
        return $this->hasMany(File::class, 'folder_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     * @author Toi Nguyen
     */
    public function parentFolder()
    {
        return $this->hasOne(Folder::class, 'id', 'parent');
    }

    /**
     *
     */
    public function __wakeup()
    {
        parent::boot();
    }

    /**
     * @param $folderId
     * @return mixed
     * @author Toinn
     */
    public function getFolderByParentId($request, $folderId)
    {
        return $this->where('parent', '=', $folderId)
            ->where(function ($query) use($request) {
                $query->orWhere('user_id', '=', $request->user_id)
                    ->orWhere('user_id', '=', 0)
                    ->where('client_id', '=', getClientId($request));
            })
            ->orderBy('name', 'asc')
            ->get();
    }

}