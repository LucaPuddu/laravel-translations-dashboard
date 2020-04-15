const mix = require('laravel-mix');
const env = process.env.NODE_ENV;

mix.setPublicPath('dist')
    .js('src/assets/js/app.js', 'dist/js')
    .sass('src/assets/scss/app.scss', 'dist/css')
    .extract(['bootstrap', 'jquery', 'popper.js'])
    .options({
        processCssUrls: false
    })
    .version()
    .copyDirectory('src/assets/icons/fonts', 'dist/fonts');

if (env === 'production') {

}

if (env === 'development') {
    mix.sourceMaps();
}
