<?php

namespace Tests\Unit;

use App\Subtitles\Tools\Options\ToolOptions;
use Tests\TestCase;

class ToolOptionsTest extends TestCase
{
    /** @test */
    function it_serializes_public_properties()
    {
        $class = new class extends ToolOptions {
            public $a = 123;

            public $b = 'wow';

            protected $c = 'not this one';
        };

        $this->assertSame([
            'a' => 123,
            'b' => 'wow',
        ], $class->toArray());
    }

    /** @test */
    function it_restores_properties()
    {
        $options = [
            'a' => 123,
            'b' => 'wow',
        ];

        $class = new class($options) extends ToolOptions {
            public $a = null;

            public $b = null;

            protected $c = null;
        };

        $this->assertSame(123, $class->a);

        $this->assertSame('wow', $class->b);
    }
}
