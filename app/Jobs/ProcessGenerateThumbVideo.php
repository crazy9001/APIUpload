<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Storage;
use App\File as FileModel;
use File;
use Thumbnail;
use FFMpeg;

class ProcessGenerateThumbVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $disk;

    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->disk = Storage::disk(config('filesystems.default'));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $defaultThumbVideo = config('dimensions.dimensions_video');
        $media = FFMpeg::open($this->data['file']->path);
        $durationInSeconds = $media->getDurationInSeconds();
        $time_to_image    = rand(10, floor(($durationInSeconds)/2));
        $array = array();
        foreach($defaultThumbVideo as $key => $thumb){
            $dateFolder = date("Y/m/d");
            $thumbPath = "/thumb/{$this->data['client']}/$key/{$dateFolder}";
            $thumbName = 'thumb-' . str_slug($this->data['file']->name) . '.png';

            $media->getFrameFromSeconds($time_to_image)
                ->export()
                ->toDisk('public')
                ->save($thumbPath . '/' . $thumbName);
            $array[$key] = $thumbPath . '/' . $thumbName;
        }
        $file = FileModel::find($this->data['file']->id);
        $file->update(['thumbnails' => $array]);
    }
}
