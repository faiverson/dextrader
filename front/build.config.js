module.exports = {
	banner: {
		full:
		'/**!\n' +
		' * <%= pkg.name %> - v<%= pkg.version %>\n' +
		' * <%= pkg.description %>\n' +
		' *\n' +
		' * (c) ' + new Date().getFullYear() + ' - <%= pkg.author %>\n' +
		' * <%= pkg.license %> License' +
		' * <%= pkg.repository.url %>\n' +
		' *\n' +
		' */\n\n',
		min:
		'/**!\n' +
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
                'vendor/angular-jwt/dist/angular-jwt.js'
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
		output: 'home.blade.php',
		tpl: {
			modules: 'src/modules/**/*.tpl.html',
			common: 'src/common/**/*.tpl.html'
		}
	},
	assets: {
		images: {
			input: ['src/assets/**/*.{png,gif,jpeg,jpg,woff}'],
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
	placeholders : {
		dev: [
			{
				match: 'SITE_URL',
				replacement: 'http://localhost'
			},
			{
				match: 'API_URL',
				replacement: 'http://localhost:8000/api/'
			},
			{
				match: 'SITE_NAME',
				replacement: 'Dex Trader'
			}
		],
		prod: [
			{
				match: 'SITE_URL',
				replacement: 'http://dextrader.com'
			},
			{
				match: 'API_URL',
				replacement: 'http://dextrader.com/api/'
			},
			{
				match: 'SITE_NAME',
				replacement: 'Dex Trader'
			}
		]
	}
};