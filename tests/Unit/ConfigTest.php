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

            preg_match('/class (.*?) /', $content, $controllerName);

            preg_match('/\$indexRouteName = \'(.*?)\';/', $content, $indexRouteName);

            $fileJobControllers['App\\Http\\Controllers\\'.$controllerName[1]] = $indexRouteName[1];
        }

        $registeredControllers = config('st.tool_routes');

        ksort($registeredControllers);
        ksort($fileJobControllers);

        $this->assertNotEmpty($fileJobControllers);

        $this->assertSame($registeredControllers, $fileJobControllers);
    }
}
