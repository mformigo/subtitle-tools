let mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');
require('laravel-mix-purgecss');

mix.disableNotifications()
   .js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/admin.scss', 'public/css')
   .postCss('resources/assets/pcss/main.pcss', 'public/css', [
        tailwindcss('tailwind.js')
    ])
    .purgeCss({
        globs: [
            path.join(__dirname, 'resources/views/**/*.php'),
            path.join(__dirname, 'resources/assets/js/**/*.js'),
            path.join(__dirname, 'resources/assets/js/**/*.vue'),
        ],

        extensions: ['html', 'js', 'php', 'vue'],
    })
    .version();
