<?php

namespace App\Subtitles\VobSub;

interface VobSub2SrtInterface
{
    /**
     * @return $this
     */
    public function get();

    /**
     * @param $pathWithoutExtension
     *
     * @return $this
     */
    public function path($pathWithoutExtension);

    public function languages();

    public function extract($index, $language);
}
