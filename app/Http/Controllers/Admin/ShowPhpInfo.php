<?php

namespace App\Http\Controllers\Admin;

class ShowPhpInfo extends Controller
{
    public function __invoke()
    {
        dump(
            opcache_get_status(false)
        );

        phpinfo();

        dump('Start of cli');

        echo '<code>'.nl2br(shell_exec("php -r 'phpinfo();'")).'</code>';
    }
}
