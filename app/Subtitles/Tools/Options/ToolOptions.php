<?php

namespace App\Subtitles\Tools\Options;

use Illuminate\Http\Request;
use ReflectionClass;

abstract class ToolOptions
{
    public function __construct($options = [])
    {
        $this->load(
            (array) $options
        );
    }

    public function rules(): array
    {
        return [];
    }

    public function load($options)
    {
        if ($options instanceof Request) {
            return $this->loadRequest($options);
        }

        foreach ($options as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }

        return $this;
    }

    public function loadRequest(Request $request)
    {
        return $this->load(
            $request->all()
        );
    }

    public function toArray()
    {
        $reflection = new ReflectionClass($this);

        $properties = [];

        foreach ($reflection->getProperties() as $property) {
            if ($property->isPublic()) {
                $properties[$property->getName()] = $this->{$property->getName()};
            }
        }

        return $properties;
    }
}
