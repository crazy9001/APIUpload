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
use Validator;

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
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'file' =>'required'
        ], [
            'user.required' => 'Missing param',
            'file.required' => 'Missing param'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 400);
        }

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
        $validator = Validator::make($request->all(), [
            'user' => 'required',
        ], [
            'user.required' => 'Missing param'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 400);
        }

        $action = $request->input('action');
        $folderSlug = $request->input('folder');
        $user = $request->input('user');


        $filters['action'] = $action;
        $filters['folderSlug'] = $folderSlug;
        $filters['user'] = $user;
        $filters['client'] = getClientId($request);

        try {
            $contents = $this->getDirectory($filters);

        } catch (MediaInvalidParent $e) {
            return response()->json($e);
        }
        return response()->json($contents);

    }

    private function getDirectory($filters)
    {
        $folderModel = new Folder();
        $fileModel = new File();

        try {
            $contents = [];
            $folder = null;
            if (is_string($filters['folderSlug']) && $filters['folderSlug'] !== null) {
                $folder = $folderModel->getFirstBy(['slug' => $filters['folderSlug']]);
                if (!$folder) {
                    throw new MediaInvalidParent;
                }
                $folderId = $folder->id;
            } else {
                $folderId = 0;
            }
            $filters['folderId'] = $folderId;
            // Get the folders
            $contents['folders'] = $folderModel->getFolderByParentId($filters);

            if ($filters['action'] == 'image') {
                // Get all the files
                $contents['files'] = $fileModel->getFilesByFolderId($filters, ['image/jpeg', 'image/jpg', 'image/png']);
            } elseif ($filters['action'] == 'video') {
                // Get all the files
                $contents['files'] = $fileModel->getFilesByFolderId($filters, ['video/mp4', 'video/x-flv', 'video/x-msvideo', 'video/x-m4v']);
            } else {
                // Get all the files
                $contents['files'] = $fileModel->getFilesByFolderId($filters);
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
            $contents['currentFolder'] = $filters['folderSlug'] != null ? $filters['folderSlug'] : 0;
            return $contents;
        } catch (MediaInvalidParent $e) {
            return response()->json($e);
        }

    }

}