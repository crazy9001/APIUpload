<?php
namespace App\Http\Controllers;

use App\File;
use App\Folder;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploadController extends Controller
{

    /**
     * @var mixed
     */
    protected $disk;

    public function __construct()
    {
        $this->disk = Storage::disk(config('filesystems.default'));
    }

    /**
     * Handles the file upload
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws UploadMissingFileException
     * @throws \Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException
     */
    public function upload(Request $request) {
        // create the file receiver
        $receiver = new FileReceiver("file", $request, HandlerFactory::classFromRequest($request));

        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }

        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need, current example uses `move` function. If you are
            // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
            return $this->saveFile($save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();

        return response()->json([
            "done" => $handler->getPercentageDone(),
            'status' => true
        ]);
    }

    /**
     * Saves the file to S3 server
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFileToS3($file)
    {
        $fileName = $this->createFilename($file);

        $disk = Storage::disk('s3');
        // It's better to use streaming Streaming (laravel 5.4+)
        $disk->putFileAs('photos', $file, $fileName);

        // for older laravel
        // $disk->put($fileName, file_get_contents($file), 'public');
        $mime = str_replace('/', '-', $file->getMimeType());

        // We need to delete the file when uploaded to s3
        unlink($file->getPathname());

        return response()->json([
            'path' => $disk->url($fileName),
            'name' => $fileName,
            'mime_type' =>$mime
        ]);
    }

    /**
     * Saves the file
     *
     * @param UploadedFile $file
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function saveFile($request, UploadedFile $file)
    {
        // Group file by folder name
        $folderId = $request->input('folder');
        $folder = Folder::where(['slug' => $folderId])->select(['slug', 'id'])->first();
        $folderId = $folder ? $folder->id : 0;
        $folderName = $folder ?  $folder->slug : null;

        $fileName = $this->createFilename($file);
        // Group files by mime type
        $mime = str_replace('/', '-', $file->getMimeType());
        $mimeType =  $file->getMimeType();
        // Group files by the date (week
        $dateFolder = date("Y-m-d");
        //Get Client Name
        $clientName = getClientName($request);
        // Build the file path
        $filePath = "upload/$clientName/{$mime}/{$dateFolder}/";
        // It's better to use streaming Streaming (laravel 5.4+)
        // move the file name
        $this->disk->putFileAs($filePath, $file, $fileName);

        try {
            // save data
            $file = new File();
            $file->user_id = $request->user;
            $file->client_id = getClientId($request);
            $file->folder_id = $folderId;
            $file->mime_type = $mimeType;
            $file->file_name = $fileName;
            $file->path = $this->disk->url($filePath . $fileName);
            $file->size = $this->disk->size($filePath . $fileName);
            $file->save();

            return response()->json($file);
        }
        catch (\Exception $exception) {
            return response()->json($exception);
        }

    }

    /**
     * Create unique filename for uploaded file
     * @param UploadedFile $file
     * @return string
     */
    protected function createFilename(UploadedFile $file)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = str_replace(".".$extension, "", $file->getClientOriginalName()); // Filename without extension

        // Add timestamp hash to name of the file
        $filename = md5(time()) . '-' . $filename . "." . $extension;

        return $filename;
    }
}