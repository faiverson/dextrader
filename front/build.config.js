module.exports = {
    port: 8000,

    files: {
        js: {

            // Use uncompressed versions of 3rd-pary libraries.
            // They will be compressed in production.
            // Any libraries added to /vendor must be added here.
            // If you remove a library you must remove it here too.
            vendor: [
                'vendor/jquery/dist/jquery.js',
                'vendor/bootstrap/dist/js/bootstrap.js',
                'vendor/angular/angular.js',
                'vendor/angular-mocks/angular-mocks.js',
                'vendor/angular-ui-router/release/angular-ui-router.js',
                'vendor/angular-bootstrap/ui-bootstrap-tpls.js'
            ],

            app: [
                'src/common/**/*.js',
                '!src/common/**/*.spec.js',
                '!src/common/**/*.scenario.js',

                'src/modules/**/*.js',
                '!src/modules/**/*.spec.js',
                '!src/modules/**/*.scenario.js'
            ],

            buildDest: 'build/js'
        },

        less: {
            main: [
                'src/less/main.less',
                'src/common/**/*.less',
                'src/modules/**/*.less'
            ],

            buildDest: 'build/css'
        },


        html: {
            index: 'src/index.html',

            tpls: {
                modules: 'src/modules/**/*.tpl.html',

                common: 'src/common/**/*.tpl.html'
            },

            buildDest: 'build'
        },

        img: {
            src: 'src/img/**/*',

            buildDest: 'build/img'
        },

        test: {
            e2e: 'src/**/*.scenario.js',

            unit: [
                'build/js/vendor.js',
                'build/js/templates-*.js',
                'build/js/app.js',
                'src/**/*.spec.js'
            ]
        }
    }
};