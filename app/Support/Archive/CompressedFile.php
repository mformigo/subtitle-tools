<?php

namespace App\Support\Archive;

class CompressedFile
{
    protected $index = null;

    protected $name = '';

    protected $realSize = 0;

    public function __construct($index, $name, $realSize)
    {
        $this->index = $index;

        $this->name = $name;

        $this->realSize = $realSize;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRealSize()
    {
        return (int) $this->realSize;
    }
}
