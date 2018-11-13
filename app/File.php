<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 11/9/2018
 * Time: 11:15 AM
 */

namespace App;

use Illuminate\Support\Facades\DB;

class File extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'media_storage';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo

     */
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'id', 'folder_id');
    }

    public $fillable = ['user_id', 'client_id', 'folder_id', 'mime_type', 'file_name', 'path', 'size'];

    /**
     * @author Toi Nguyen
     */

    public function __wakeup()
    {
        parent::boot();
    }

    public function createFile($data)
    {
        return $data;
        DB::beginTransaction();
        $file = $this->create(array_only($data, $this->filter));
        DB::commit();
        return $file;
    }

    /**
     * @param $folder_id
     * @param array $type
     * @return mixed
     * @author Toinn
     */
    public function getFilesByFolderId($request, $folder_id, $type = [])
    {
        $files = $this->where('folder_id', '=', $folder_id)
            ->where('user_id', '=', $request->user)
            ->where('client_id', '=', getClientId($request));
        if (!empty($type)) {
            $files = $files->whereIn('mime_type', $type);
        }
        return $files->orderBy('file_name', 'asc')
            ->get();
    }

}