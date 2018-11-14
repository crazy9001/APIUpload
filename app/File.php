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

    public $fillable = ['user_id', 'client_id', 'folder_id', 'mime_type', 'file_name', 'path', 'size', 'name', 'thumbnails'];

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
     * @param array $filters
     * @param array $type
     * @return mixed
     */
    public function getFilesByFolderId(array $filters, $type = [])
    {
        $query = $this->where(function ($que) use($filters, $type) {
            $que->where('client_id', '=', $filters['client']);
            $que->where('user_id', '=', $filters['user']);
            $que->where('folder_id', '=', $filters['folderId']);
            if(isset($type) && !empty($type)){
                $que->whereIn('mime_type', $type);
            }
        });
        unset($filters['folderSlug']);
        return $query->orderBy('file_name', 'asc')
            ->paginate(50)->appends($filters);
    }

    public function createName($name, array $filters)
    {
        $index = 1;
        $baseName = $filters['name'];
        while ($this->where('name', '=', $name)
            ->where('folder_id', '=', $filters['folder'])
            ->where('user_id', '=', $filters['user'])
            ->first()) {
            $name = '(' . $index++ . ') ' . $baseName;
        }
        return $name;
    }

}