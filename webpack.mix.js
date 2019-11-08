let mix = require('laravel-mix');
let env  = require('minimist')(process.argv.slice(2));

mix.webpackConfig({
    resolve: {
        alias: {
            '@admin': path.resolve('resources/backend/js'),
            '@cssAdmin': path.resolve('resources/backend/sass'),
        }
    }
});

mix.js('resources/backend/js/app.js', 'js')
    .sass('resources/backend/sass/app.scss', 'css')
    .extract(['vue', 'axios', 'lodash', 'jquery', 'element-ui', 'vue-cookie'])
    .version();
mix.setPublicPath('public/backend');