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
                'vendor/datatables/media/js/jquery.dataTables.js',
                'vendor/bootstrap/dist/js/bootstrap.js',
                'vendor/angular/angular.js',
                'vendor/angular-mocks/angular-mocks.js',
                'vendor/angular-ui-router/release/angular-ui-router.js',
                'vendor/angular-bootstrap/ui-bootstrap-tpls.js',
                'vendor/angular-datatables/dist/angular-datatables.js',
                'vendor/angular-datatables/dist/plugins/bootstrap/angular-datatables.bootstrap.js'

            ],

            app: [
                'src/**/*.js',
                '!src/**/*.spec.js',
                '!src/**/*.scenario.js'
            ],

            buildDest: '../public_html/js',
            buildDestPattern: '../public_html/**/*.js'
        },

        less: {
            main: [
                'src/less/main.less',
                'src/common/**/*.less',
                'src/modules/**/*.less',
                'vendor/angular-datatables/dist/plugins/bootstrap/datatables.bootstrap.css'
            ],

            buildDest: '../public_html/css'
        },

        css: {
            vendor_files: [
                'vendor/angular-datatables/dist/plugins/bootstrap/datatables.bootstrap.css'
            ],

            buildDest: '../public_html/css'
        },

        html: {
            index: 'src/index.html',

            tpls: {
                modules: 'src/modules/**/*.tpl.html',

                common: 'src/common/**/*.tpl.html'
            },

            buildDestFileName: 'home.php',

            buildDest: '../laravel/resources/views'
        },

        img: {
            src: 'src/img/**/*',

            buildDest: '../public_html/img'
        }
    }
};