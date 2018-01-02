<?php

namespace App\Jobs\Diagnostic;

use App\Support\Facades\TextFileFormat;
use SjorsO\TextFile\Facades\TextEncoding;
use SjorsO\TextFile\Facades\TextFileIdentifier;
use SjorsO\TextFile\Facades\TextFileReader;
use App\Models\StoredFile;
use App\Models\StoredFileMeta;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CollectStoredFileMetaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public $timeout = 30;

    protected $storedFile;

    public function __construct(StoredFile $storedFile)
    {
        $this->storedFile = $storedFile;
    }

    public function handle()
    {
        $filePath = $this->storedFile->filePath;

        if (StoredFileMeta::query()->where('stored_file_id', $this->storedFile->id)->count() > 0) {
            \Log::error("Tried running a CollectStoredFileMetaJob for a stored file that already has meta info ({$this->storedFile->id})");
            return;
        }

        if (!file_exists($filePath)) {
            \Log::error("CollectStoredFileMetaJob: file does not exist ({$this->storedFile->id})");
            return;
        }

        $meta = new StoredFileMeta();

        $meta->stored_file_id = $this->storedFile->id;

        $meta->size = filesize($filePath);

        $meta->mime = file_mime($filePath);

        if (TextFileIdentifier::isTextFile($filePath)) {
            $meta->is_text_file = true;

            $meta->encoding = TextEncoding::detectFromFile($filePath);

            $meta->identified_as = get_class(TextFileFormat::getMatchingFormat($filePath, false));

            $meta->line_count = count(TextFileReader::getLines($filePath));

            $string = TextFileReader::getContent($filePath);

            $lineEndings = [
                "\r\n" => 'CRLF',
                "\n" => 'LF',
                "\r" => 'CR',
            ];

            $highestCount = 0;
            $detectedEol = 'unknown';

            foreach (array_keys($lineEndings) as $eol) {
                $count = substr_count($string, $eol);

                if ($count > $highestCount) {
                    $highestCount = $count;
                    $detectedEol = $eol;
                }
            }

            $meta->line_endings = $detectedEol === 'unknown' ? 'unknown' : $lineEndings[$detectedEol];
        }

        $meta->save();
    }

    public function failed(Exception $exception)
    {
        \Log::error("Failed collecting stored file meta for {$this->storedFile->id}");
        \Log::error($exception->getMessage());
    }
}
