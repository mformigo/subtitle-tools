let mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');
let glob = require('glob-all');
let PurgecssPlugin = require('purgecss-webpack-plugin');

mix.disableNotifications();



mix.js('resources/assets/js/app.js', 'public/js')
   .sass('resources/assets/sass/admin.scss', 'public/css')
   .postCss('resources/assets/pcss/main.pcss', 'public/css', [
        tailwindcss('tailwind.js')
    ]);

mix.version();

if (mix.inProduction()) {
    mix.webpackConfig({
        plugins: [
            new PurgecssPlugin({

                paths: glob.sync([
                    path.join(__dirname, 'resources/views/**/*.php'),
                    path.join(__dirname, 'resources/assets/js/**/*.js'),
                    path.join(__dirname, 'resources/assets/js/**/*.vue')
                ]),
                extractors: [
                    {
                        extractor: class {
                            static extract(content) {
                                return content.match(/[A-z0-9-:\/]+/g) || []
                            }
                        },

                        extensions: ['html', 'js', 'php', 'vue']
                    }
                ]
            })
        ]
    });
}
