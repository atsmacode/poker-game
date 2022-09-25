let mix = require('laravel-mix');

/**
 * __VUE_PROD_DEVTOOLS__ set this to true to show Vue devtools.
 * 
 * Should be set to false for actual live dist.
 */
mix.js('resources/js/app.js', 'public/js')
    .css('resources/css/app.css', 'public/css')
    .webpackConfig((webpack) => {
        return {
            plugins: [
                new webpack.DefinePlugin({
                    __VUE_OPTIONS_API__: true,
                    __VUE_PROD_DEVTOOLS__: true,
                }),
            ],
        };
    })
    .vue();
