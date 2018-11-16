<?php
namespace App\Http\Controllers;

use App\File as FileModel;
use App\Folder;
use App\Jobs\ProcessGenerateThumbVideo;
use Storage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use File;
use Validator;
use Image;
use Carbon\Carbon;
use Thumbnail;

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
    protected function saveFile($request, UploadedFile $fileUpload)
    {
        // Group file by folder name
        $folderId = $request->input('folder');
        $folder = Folder::where(['slug' => $folderId])->select(['slug', 'id'])->first();
        $folderId = $folder ? $folder->id : 0;
        $folderName = $folder ?  $folder->slug : null;

        $fileName = $this->createFilename($fileUpload);
        // Group files by mime type
        $mime = str_replace('/', '-', $fileUpload->getMimeType());
        $mimeType =  $fileUpload->getMimeType();
        // Group files by the date (week
        $dateFolder = date("Y/m/d");
        //Get Client Name
        $clientName = getClientName($request);
        // Build the file path
        //$filePath = "media/$clientName/{$mime}/{$dateFolder}/";
        $filePath = "/media/$clientName/{$dateFolder}/";
        // It's better to use streaming Streaming (laravel 5.4+)
        // move the file name
        $this->disk->putFileAs($filePath, $fileUpload, $fileName);
        try {
            $filters['user'] = $request->user;
            $filters['name'] = File::name($fileUpload->getClientOriginalName());
            $filters['folder'] = $folderId;
            // save data
            $file = new FileModel();
            $file->user_id = $request->user;
            $file->client_id = getClientId($request);
            $file->folder_id = $folderId;
            $file->mime_type = $mimeType;
            $file->file_name = $fileName;
            $file->name = $file->createName($filters['name'], $filters);
            $file->path = $filePath . $fileName;
            $file->size = $this->disk->size($filePath . $fileName);
            $file->save();

            $data['client'] = getClientName($request);
            $data['file']   = $file;
            $data['fileUpload'] = $fileUpload;

            $queue['file'] = $file;
            $queue['client'] = getClientName($request);
            if(substr($file->mime_type, 0, 5) == 'image') {
                $this->generateThumbImage($data);
            }elseif(substr($file->mime_type, 0, 5) == 'video') {
                ProcessGenerateThumbVideo::dispatch($queue);
            }
            return $this->sendResponse('Upload success');
        }
        catch (\Exception $exception) {
            return response()->json($exception);
        }
    }

    public function generateThumbVideo(array $data)
    {

        $path = $this->disk->getAdapter()->getPathPrefix();
        $fileName = str_replace(".".$data['fileUpload']->getClientOriginalExtension(), "", $data['fileUpload']->getClientOriginalName()) . '.jpg';
        $duration = \FFMpeg\FFProbe::create([
            'ffmpeg.binaries'  => config('thumbnail.binaries.path.ffmpeg'),
            'ffprobe.binaries' => config('thumbnail.binaries.path.ffprobe'),
        ])
            ->format($path. $data['file']->path)
            ->get('duration');
        $time_to_image    = rand(10, floor(($duration)/2));

        $defaultThumbVideo = config('dimensions.dimensions_video');
        $array = array();
        foreach($defaultThumbVideo as $key => $thumb){
            $dateFolder = date("Y/m/d");
            $fixPath = "/thumb/{$data['client']}/$key/{$dateFolder}";
            $thumbPath = $path . '/' . $fixPath;
            if(!File::isDirectory($thumbPath)){
                File::makeDirectory($thumbPath, 0777, true, true);
            }
            $arrayDimension = explode('x', $thumb);
            Thumbnail::getThumbnail($path . $data['file']->path, $thumbPath, $fileName, $time_to_image);
            $urlThumb = $fixPath . '/' . $fileName;
            $array[$key] = $urlThumb;
        }
        $file = FileModel::find($data['file']->id);
        $file->update(['thumbnails' => $array]);
    }

    public function generateThumbImage(array $data)
    {
        //Get Client Name
        $defaultThumb = config('dimensions.dimensions_image');
        $fileName = $this->createFilename($data['fileUpload']);
        $array = array();
        foreach($defaultThumb as $key => $thumb){
            $dateFolder = date("Y/m/d");
            $thumbPath = "/thumb/{$data['client']}/$key/{$dateFolder}/";
            $resize = Image::make($data['fileUpload'])->resize($thumb, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode($data['fileUpload']->getClientOriginalExtension());
            $this->disk->put($thumbPath . $fileName , $resize->__toString());
            $urlThumb = $thumbPath . $fileName;
            $array[$key] = $urlThumb;
        }
        $file = FileModel::find($data['file']->id);
        $file->update(['thumbnails' => $array]);
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

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
        ];

        if(!empty($errorMessages)){
            $response['message'] = $errorMessages;
        }

        return response()->json($response, $code);
    }

    public function sendResponse($message)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        return response()->json($response, 200);
    }

}