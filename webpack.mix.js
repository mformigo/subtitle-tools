let mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');
require('laravel-mix-purgecss');

mix.disableNotifications()
   .js('resources/js/app.js', 'public/js')
   .sass('resources/sass/admin.scss', 'public/css')
   .postCss('resources/pcss/main.pcss', 'public/css', [
        tailwindcss('tailwind.js')
    ])
    .purgeCss({
        globs: [
            path.join(__dirname, 'resources/views/**/*.php'),
            path.join(__dirname, 'resources/js/**/*.js'),
            path.join(__dirname, 'resources/js/**/*.vue'),
        ],

        extensions: ['html', 'js', 'php', 'vue'],
    })
    .version();
