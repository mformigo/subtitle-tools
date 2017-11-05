let mix = require('laravel-mix');

mix.disableNotifications();

if (mix.inProduction()) {
    mix.version();
}

mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/admin.scss', 'public/css');
