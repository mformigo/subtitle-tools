<?php

namespace App\Jobs;

use App\Facades\TempDir;
use App\Models\SupJob;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use SjorsO\Sup\SupFile;

class SupToSrtJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 330;

    protected $supJob;

    public function __construct(SupJob $supJob)
    {
        $this->supJob = $supJob;
    }

    public function handle()
    {
        $this->supJob->measureStart();

        $this->supJob->temp_dir = TempDir::make('sup');

        $sup = null;

        try {
            $sup = SupFile::open($this->supJob->inputStoredFile->file_path);
        }
        catch(Exception $exception) {
            return $this->abortWithError('messages.sup.exception_when_reading', $exception->getMessage());
        }

        if($sup === false) {
            return $this->abortWithError('messages.sup.not_a_sup_file');
        }

        $outputFilePaths = $sup->extractImages($this->supJob->temp_dir);

        $this->supJob->measureEnd();

        return $this->supJob;
    }

    public function failed()
    {
        $this->supJob->measureEnd();
    }

    protected function abortWithError($errorMessage, $internalErrorMessage = null)
    {
        $this->supJob->update([
            'error_message' => $errorMessage,
            'internal_error_message' => $internalErrorMessage,
        ]);

        $this->supJob->measureEnd();

        return $this->supJob;
    }
}
