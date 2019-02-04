<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConfigTest extends TestCase
{
    /** @test */
    function all_file_job_controllers_are_registered()
    {
        $basePath = app_path('Http/Controllers/');

        $controllers = array_filter(scandir($basePath), function ($name) {
            return strpos($name, '.php') !== false;
        });

        $fileJobControllers = [];

        foreach ($controllers as $controller) {
            $content = file_get_contents($basePath.$controller);

            if (stripos($content, ' extends FileJobController') === false) {
                continue;
            }

            preg_match('/class (.*?) /', $content, $matches);

            $fileJobControllers[] = 'App\\Http\\Controllers\\'.$matches[1];
        }

        $registeredControllers = array_keys(config('st.tool_routes'));

        sort($registeredControllers);
        sort($fileJobControllers);

        $this->assertNotEmpty($fileJobControllers);

        $this->assertSame($registeredControllers, $fileJobControllers);
    }
}
