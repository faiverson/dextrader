module.exports = {
    banner: {
        full: '/**!\n' +
        ' * <%= pkg.name %> - v<%= pkg.version %>\n' +
        ' * <%= pkg.description %>\n' +
        ' *\n' +
        ' * (c) ' + new Date().getFullYear() + ' - <%= pkg.author %>\n' +
        ' * <%= pkg.license %> License' +
        ' * <%= pkg.repository.url %>\n' +
        ' *\n' +
        ' */\n\n',
        min: '/**!\n' +
        ' * <%= pkg.name %> - v<%= pkg.version %>\n' +
        ' * <%= pkg.description %>\n' +
        ' *\n' +
        ' * (c) ' + new Date().getFullYear() + ' - <%= pkg.author %>\n' +
        ' * <%= pkg.license %> License' +
        ' * <%= pkg.repository.url %>\n' +
        ' *\n' +
        ' */\n\n'
    },
    port: 8000,
    paths: {
        input: 'src/**/*',
        output: '../public_html/front/',
        server_path: '../public_html'
    },
    js: {
        vendor: {
            input: [
                'vendor/jquery/dist/jquery.js',
                'vendor/datatables/media/js/jquery.dataTables.js',
                'vendor/bootstrap/dist/js/bootstrap.js',
                'vendor/angular/angular.js',
                'vendor/angular-cookies/angular-cookies.js',
                'vendor/angular-mocks/angular-mocks.js',
                'vendor/angular-ui-router/release/angular-ui-router.js',
                'vendor/angular-bootstrap/ui-bootstrap-tpls.js',
                'vendor/angular-datatables/dist/angular-datatables.js',
                'vendor/angular-datatables/dist/plugins/bootstrap/angular-datatables.bootstrap.js',
                'vendor/angular-bootstrap-show-errors/src/showErrors.js',
                'vendor/angular-local-storage/dist/angular-local-storage.js',
                'vendor/angular-jwt/dist/angular-jwt.js',
                'vendor/angular-ui-notification/dist/angular-ui-notification.js',
                'vendor/angular-youtube-mb/dist/angular-youtube-embed.min.js',
                'vendor/angular-animate/angular-animate.js',
                'vendor/ui-select/dist/select.js',
                'vendor/angular-sanitize/angular-sanitize.js',
                'vendor/angular-moment/angular-moment.js',
                'vendor/moment/min/moment.min.js',
                'vendor/angular-ui-mask/dist/mask.js'
            ],
            output: 'vendor.js'
        },
        files: {
            input: [
                'src/**/*.js',
                '!src/**/*.spec.js',
                '!src/**/*.scenario.js'
            ],
            output: '../public_html/front/js/'
        }
    },
    less: {
        input: [
            'src/less/main.less',
            'src/common/**/*.less',
            'src/modules/**/*.less'
        ],
        output: '../public_html/front/css/'
    },
    html: {
        input: 'src/index.html',
        output: 'home.php',
        tpl: {
			output: 'templates.js',
			modules: 'src/modules/**/*.tpl.html',
            common: 'src/common/**/*.tpl.html'
        }
    },
    assets: {
        fonts: {
            input: ['src/assets/fonts/**/*.{ttf,woff,woff2,eof,eot}'],
            output: '../public_html/front/assets/fonts/'
        },
        images: {
            input: ['src/assets/**/*.{png,gif,jpeg,jpg}'],
            output: '../public_html/front/assets/'
        },
        svg: {
            input: 'src/assets/**/*.svg',
            output: '../public_html/front/assets/'
        }
    },
    docs: {
        input: 'src/docs/*.{html,md,markdown}',
        output: 'docs/',
        templates: 'src/docs/_templates/',
        assets: 'src/docs/assets/**'
    },
    test: {
        input: 'src/**/*.spec.js',
        karma: 'test/karma.conf.js',
        spec: 'test/spec/**/*.js',
        coverage: 'test/coverage/',
        results: 'test/results/'
    },
    placeholders: [
		{
			match: 'SITE_URL',
			replacement: process.env.URL
		},
		{
			match: 'API_URL',
			replacement: process.env.URL + '/api/'
		}
	]
};