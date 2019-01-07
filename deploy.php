<?php

namespace Deployer;

require 'recipe/laravel.php';

set('application', 'Subtitle Tools');
set('repository', 'git@github.com:SjorsO/subtitle-tools.git');
set('git_tty', true);
set('keep_releases', 3);

host('sjors@subtitletools.com')->set('deploy_path', '/var/www/st');


task('build-npm-assets', 'npm i; npm run prod');

task('clear-opcache', 'sudo service apache2 restart');


after('deploy:failed', 'deploy:unlock');


task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',

    'deploy:vendors',
    'build-npm-assets',

    'deploy:writable',

//    'artisan:storage:link',
    'artisan:view:clear',
//    'artisan:config:cache',
//    'artisan:route:cache',

    'artisan:migrate',
    'clear-opcache',
    'artisan:queue:restart',

    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);
