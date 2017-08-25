<?php

namespace App\Utils\Archive;

class CompressedFile
{
    protected $name = "";

    protected $realSize = 0;

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setRealSize($size)
    {
        $this->realSize = $size;

        return $this;
    }

    public function getRealSize()
    {
        return $this->realSize;
    }
}