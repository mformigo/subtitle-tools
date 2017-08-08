<?php

namespace Tests;

use App\Models\SubIdx;
use Illuminate\Http\UploadedFile;

trait PostsVobSubs
{
    private $defaultSubIdxName = "error-and-nl";

    private function getSubUploadedFile($fileNameWithoutExtension = null)
    {
        $fileNameWithoutExtension = $fileNameWithoutExtension ?: $this->defaultSubIdxName;

        return new UploadedFile(
            $this->testFilesStoragePath . "SubIdxFiles/{$fileNameWithoutExtension}.sub",
            "{$fileNameWithoutExtension}.sub",
            null, null, null, true
        );
    }

    private function getIdxUploadedFile($fileNameWithoutExtension = null)
    {
        $fileNameWithoutExtension = $fileNameWithoutExtension ?: $this->defaultSubIdxName;

        return new UploadedFile(
            $this->testFilesStoragePath . "SubIdxFiles/{$fileNameWithoutExtension}.idx",
            "{$fileNameWithoutExtension}.idx",
            null, null, null, true
        );
    }

    private function getSubIdxPostData($fileNameWithoutExtension = null)
    {
        return [
            'sub' => $this->getSubUploadedFile($fileNameWithoutExtension),
            'idx' => $this->getIdxUploadedFile($fileNameWithoutExtension),
        ];
    }

    /**
     * @return SubIdx
     */
    private function postVobSub()
    {
        $response = $this->post(route('sub-idx-index'), $this->getSubIdxPostData());

        $subIdx = SubIdx::where('original_name', $this->defaultSubIdxName)->firstOrFail();

        $response->assertStatus(302)
            ->assertRedirect(route('sub-idx-detail', ['pageId' => $subIdx->page_id]));

        return $subIdx;
    }
}
