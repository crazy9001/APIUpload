<?php

namespace App\Http\Controllers;

use App\File;
use App\Folder;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;
use App\Exceptions\MediaInvalidParent;

class DependencyUploadController extends UploadController
{
    /**
     * Handles the file upload
     *
     * @param FileReceiver $receiver
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws UploadMissingFileException
     *
     */
    public function uploadFile(Request $request, FileReceiver $receiver)
    {
        // check if the upload is success, throw exception or return response you need
        if ($receiver->isUploaded() === false) {
            throw new UploadMissingFileException();
        }
        // receive the file
        $save = $receiver->receive();

        // check if the upload has finished (in chunk mode it will send smaller files)
        if ($save->isFinished()) {
            // save the file and return any response you need
            return $this->saveFile($request, $save->getFile());
        }

        // we are in chunk mode, lets send the current progress
        /** @var AbstractHandler $handler */
        $handler = $save->handler();
        return response()->json([
            "done" => $handler->getPercentageDone()
        ]);
    }

    public function getGallery(Request $request)
    {
        $action = $request->input('action');
        $folderSlug = $request->input('folder');

        session()->forget('media_action');
        session()->put('media_action', $action);
        try {
            $contents = $this->getDirectory($request, $folderSlug);

        } catch (MediaInvalidParent $e) {
            return response()->json($e);
        }
        return response()->json($contents);

    }

    private function getDirectory($request, $folderSlug)
    {
        $folderModel = new Folder();
        $fileModel = new File();

        try {
            $contents = [];
            $folder = null;
            if (is_string($folderSlug) && $folderSlug !== null) {
                $folder = $folderModel->getFirstBy(['slug' => $folderSlug]);
                if (!$folder) {
                    throw new MediaInvalidParent;
                }
                $folderId = $folder->id;
            } else {
                $folderId = 0;
            }
            // Get the folders
            $contents['folders'] = $folderModel->getFolderByParentId($request, $folderId);

            if (session('media_action') == 'image') {
                // Get all the files
                $contents['files'] = $fileModel->getFilesByFolderId($request, $folderId, ['image/jpeg', 'image/jpg', 'image/png']);
            }elseif (session('media_action') == 'video') {
                // Get all the files
                $contents['files'] = $fileModel->getFilesByFolderId($request, $folderId, ['video/mp4']);
            } else {
                // Get all the files
                $contents['files'] = $fileModel->getFilesByFolderId($request, $folderId);
            }

            // Get parent folder details
            if ($folderId == 0) {
                $contents['parentFolder'] = -1;
            } elseif ($folder->parent == 0) {
                $contents['parentFolder'] = null;
            } else {
                $contents['parentFolder'] = $folder->parentFolder()
                    ->first()->slug;
            }
            $contents['currentFolder'] = $folderSlug != null ? $folderSlug : 0;
            return $contents;
        } catch (MediaInvalidParent $e) {
            return response()->json($e);
        }

    }

}