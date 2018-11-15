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
        FFMpeg::fromDisk('public')
            ->open($this->data['file']->path)
            ->getFrameFromSeconds(10)
            ->export()
            ->toDisk('public')
            ->save('FrameAt10sec.png');

    }
}
