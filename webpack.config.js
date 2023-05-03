const wpConfig = require( '@wordpress/scripts/config/webpack.config' );
var path = require('path');

module.exports =  {
    ...wpConfig,
    entry: {
        "admin": path.resolve(__dirname, './src/js/admin.tsx'),
        "front": path.resolve(__dirname, './src/js/scripts.ts'),

        // BLOCKS
        "block-cookie-notice": path.resolve(__dirname, './src/js/blocks/cookie-notice.tsx'),
        "block-meta-field": path.resolve(__dirname, './src/js/blocks/meta-field.tsx'),
        "block-title": path.resolve(__dirname, './src/js/blocks/title.tsx'),
        // "block-query-loop": path.resolve(__dirname, './src/js/blocks/query-loop.tsx'),
        "blocks-extensions": path.resolve(__dirname, './src/js/blocks/blocks-extensions.ts'),
        "block-breadcrumbs": path.resolve(__dirname, './src/js/blocks/breadcrumb.tsx'),
        "block-price": path.resolve(__dirname, './src/js/blocks/price.tsx'),

        // METABOX
        "breadcrumbs-metabox": path.resolve(__dirname, './src/js/metaboxes/breadcrumbs.tsx'),
        "seo-metabox": path.resolve(__dirname, './src/js/metaboxes/seo.tsx'),
    },
    output: {
        path: path.resolve(__dirname, './assets'),
        filename: '[name].min.js'
    },
}