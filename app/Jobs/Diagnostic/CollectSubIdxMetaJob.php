<?php

namespace App\Jobs\Diagnostic;

use App\Models\SubIdx;
use App\Models\SubIdxMeta;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CollectSubIdxMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 30;

    protected $subIdx;

    public function __construct(SubIdx $subIdx)
    {
        $this->subIdx = $subIdx;
    }

    public function handle()
    {
        if($this->subIdx->meta !== null) {
            \Log::error("Tried running a CollectSubIdxMetaJob for a SubIdx that already has meta info ({$this->subIdx->id})");
            return;
        }

        $subFilePath = $this->subIdx->filePathWithoutExtension.'.sub';
        $idxFilePath = $this->subIdx->filePathWithoutExtension.'.idx';

        $stillExist = file_exists($subFilePath) && file_exists($idxFilePath);

        $allSuccessful = $this->subIdx->languages->every(function ($language) {
            return $language->output_stored_file_id !== null;
        });

        $meta = new SubIdxMeta();

        $meta->sub_idx_id = $this->subIdx->id;

        $meta->sub_file_size = $stillExist ? filesize($subFilePath) : 0;
        $meta->idx_file_size = $stillExist ? filesize($idxFilePath) : 0;

        $meta->all_successful = $allSuccessful;

        $meta->save();
    }

    public function failed(Exception $exception)
    {
        \Log::error("Failed collecting subidx meta for {$this->subIdx->id}");
        \Log::error($exception->getMessage());
    }
}
