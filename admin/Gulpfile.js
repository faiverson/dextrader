var gulp = require('gulp'),
	gutil = require('gulp-util'),
	fs = require('fs'),
	del = require('del'),
	lazypipe = require('lazypipe'),
	plumber = require('gulp-plumber'),
	flatten = require('gulp-flatten'),
	tap = require('gulp-tap'),
	rename = require('gulp-rename'),
	header = require('gulp-header'),
	footer = require('gulp-footer'),
	prefix = require('gulp-autoprefixer'),
	watch = require('gulp-watch'),
    jshint = require('gulp-jshint'),
	stylish = require('jshint-stylish'),
    less = require('gulp-less'),
	LessPluginCleanCSS = require('less-plugin-clean-css'),
	LessPluginAutoPrefix = require('less-plugin-autoprefix'),
	html2js = require('gulp-html2js'),
	gulpif = require('gulp-if'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
	svgmin = require('gulp-svgmin'),
	svgstore = require('gulp-svgstore'),
    htmlmin = require('gulp-htmlmin'),
    imagemin = require('gulp-imagemin'),
    cache = require('gulp-cache'),
    livereload = require('gulp-livereload'),
    wrap = require('gulp-wrap'),
	ngAnnotate = require('gulp-ng-annotate'),
	markdown = require('gulp-markdown'),
	sourcemaps = require('gulp-sourcemaps'),
	prettify = require('gulp-jsbeautifier'),
    connectPhp = require('gulp-connect-php'),
	debug = require('gulp-debug'),
    inject = require('gulp-inject'),
	sInject = require('gulp-inject-string'),
	replace = require('gulp-replace-task'),
    connect = require('connect'),
	pkg = require('./package.json'),
	environment = 'dev',
	config = require('./build.config.js');

// FACADES
gulp.task('default', ['clean:dist', 'jshint', 'js:vendor', 'js:files', 'js:templates', 'css', 'build:images', 'build:svg', 'html']);
// compile all for development env
gulp.task('dev', ['default']);
// compile all for production env
gulp.task('prod', ['env', 'default']);
// run a server and a watcher
gulp.task('run', ['server', 'watch']);

// TASKS
// set the environment
gulp.task('env', function() {
	var task = this.seq.slice(-1)[0];
	environment = task;
	gutil.log('Environment: ' + task);
});

// clean public folder
gulp.task('clean:dist', function () {
	// we don't want to remove favicons
	// and the main file either
	del.sync([
		config.paths.output + '**/*',
		'!' + config.paths.output + '.htaccess',
		'!' + config.paths.output + 'favicon.ico',
		'!' + config.paths.output + 'favicon.png',
		'!' + config.paths.output + 'apple-touch-icon-57x57-precomposed.png',
		'!' + config.paths.output + 'apple-touch-icon-72x72-precomposed.png',
		'!' + config.paths.output + 'apple-touch-icon-114x114-precomposed.png',
		'!' + config.paths.output + 'apple-touch-icon-144x144-precomposed.png',
		'!' + config.paths.output + 'apple-touch-icon.png',
		'!' + config.paths.output + 'index.php'
	], {force: true});
});

// Remove pre-existing content from text folders
gulp.task('clean:test', function () {
	del.sync([
		config.test.coverage,
		config.test.results
	]);
});

// create a file with all JS vendors
gulp.task('js:vendor', function() {
	var condition = (environment !== 'dev'),
		jsDev = lazypipe()
			.pipe(header, config.banner.full, { pkg: pkg });

		jsLive = lazypipe()
			.pipe(rename, { suffix: '.min' })
			.pipe(uglify, {outSourceMap: config.js.vendor.output + '.map'})
			.pipe(header, config.banner.min, { pkg: pkg });

	return gulp.src(config.js.vendor.input)
		.pipe(plumber())
		.pipe(concat(config.js.vendor.output))
		.pipe(gulpif(!condition, jsDev()))
		.pipe(gulpif(condition, jsLive()))
		.pipe(gulp.dest(config.js.files.output))
		.on('error', gutil.log);
});

// Process app's JS into app.js.
gulp.task('js:files', function () {
	var condition = (environment !== 'dev'),
		filename = pkg.name + '-v' + pkg.version + '.js',
		jsLive = lazypipe()
			.pipe(concat, filename)
			.pipe(wrap, '(function ( window, angular, undefined ) {\n'
			+ '\'use strict\';\n'
			+ '<%= contents %>'
			+ '})( window, window.angular );')
			.pipe(header, config.banner.min, { pkg: pkg })
			.pipe(rename, { suffix: '.min' })
			.pipe(uglify, {outSourceMap: filename + '.map'});

	return gulp.src(config.js.files.input)
		.pipe(plumber())
		.pipe(replace({
			patterns: config.placeholders[environment]
		}))
		.pipe(ngAnnotate())
		.pipe(prettify({
			mode: 'VERIFY_AND_WRITE',
			js: {
				indentSize: 4,
				indentWithTabs: false,
				jslintHappy: true,
				preserveNewlines: false,
				spaceBeforeConditional: true,
				spaceInParen: true
			}}))
		.pipe(gulpif(condition, jsLive()))
		//.pipe(debug({verbose: true}))
		.pipe(gulp.dest(config.js.files.output))
		.on('error', gutil.log);
});

// start a server localhost in php
gulp.task('server', function (next) {
	connectPhp.server({
		//hostname: 'localhost',
		port: config.port,
		base: config.paths.server_path
	});
});

gulp.task('build:svg', function () {
	return gulp.src(config.assets.svg.input)
		.pipe(plumber())
		.pipe(tap(function (file, t) {
			if ( file.isDirectory() ) {
				var name = file.relative + '.svg';
				return gulp.src(file.path + '/*.svg')
					.pipe(svgmin())
					.pipe(svgstore({
						fileName: name,
						prefix: 'icon-',
						inlineSvg: true
					}))
					.pipe(gulp.dest(config.assets.svg.output));
			}
		}))
		.pipe(svgmin())
		.pipe(gulp.dest(config.assets.svg.output));
});

// Copy image files into output folder
gulp.task('build:images', function() {
	return gulp.src(config.assets.images.input)
		.pipe(plumber())
		.pipe(imagemin({
            optimizationLevel: 5,
            progressive: true,
            interlaced: true
        }))
		.pipe(gulp.dest(config.assets.images.output));
});

gulp.task('jshint', function () {
	return gulp.src(config.js.files.input)
		.pipe(plumber())
		.pipe(jshint('.jshintrc'))
		.pipe(jshint.reporter(stylish))
		.pipe(jshint.reporter('fail'));
});

gulp.task('js:templates', function () {
	var filename = 'templates.js',
		condition = (environment !== 'dev'),
		jsDev = lazypipe()
			.pipe(gulp.dest, config.js.files.output),

		jsLive = lazypipe()
			.pipe(rename, { suffix: '.min' })
			.pipe(uglify)
			.pipe(gulp.dest, config.js.files.output);

	return gulp.src([config.html.tpl.common, config.html.tpl.modules])
		.pipe(plumber())
		.pipe(html2js({
			outputModuleName: 'templates-app',
			useStrict: true,
			base: 'src/'
		}))
		//.pipe(jade({pretty: true}))
		.pipe(concat(filename))
		.pipe(jsDev())
		.pipe(gulpif(condition, jsLive()))
		.on('error', gutil.log);
});

gulp.task('css', function () {
	var filename = 'styles.css',
		cleancss = new LessPluginCleanCSS({ advanced: true }),
		autoprefix = new LessPluginAutoPrefix({ browsers: ["last 2 versions"] }),
		condition = (environment !== 'dev');

	return gulp.src(config.less.input)
		.pipe(plumber())
		.pipe(sourcemaps.init())
		.pipe(concat(filename))
		.pipe(less({
			paths: ['src/less/'],
			plugins: (condition ? [autoprefix, cleancss] : [autoprefix])
		}))
		.pipe(prettify({indentSize: 4}))
		.pipe(sourcemaps.write('./'))
		.pipe(gulpif(condition, rename({ suffix: '.min' })))
		.pipe(gulp.dest(config.less.output))
		.on('error', gutil.log);
});

//// Convert index.jade into index.html.
gulp.task('html', function () {
    var condition = environment !== 'dev',
		input, inputs,
		appending = '';

	if(condition) {
		input = [];
		inputs = [
			'/admin/js/' + config.js.vendor.output,
			'/admin/js/templates.js',
			'/admin/js/' + pkg.name + '-v' + pkg.version + '.js'
		];
	}
	else {
		input = config.js.files.input;
		inputs = [
			'http://localhost:35729/livereload.js',
			'/admin/js/' + config.js.vendor.output,
			'/admin/js/templates.js'
		];
	}
	for(var f in inputs) {
		appending += '<script type="text/javascript" src="' + inputs[f] + '"></script>';
	}

	sources = gulp.src(input, {read: false});
    return gulp.src(config.html.input)
		// inject common files
		.pipe(sInject.before('<!-- inject:js -->', appending))
		// inject each file for dev debug
		.pipe(inject(sources, {
			relative: false,
			removeTags: true,
			empty: true,
			transform: function(filepath, file, index, length, targetFile){
				return '<script type="text/javascript" src="' + filepath.replace('/src/', '/admin/js/') + '"></script>';
			}
		}))
		.pipe(prettify({
			braceStyle: "collapse",
			indentSize: 2,
			preserveNewlines: true,
			unformatted: ["a", "sub", "sup", "b", "i", "u"]
		}))
		.pipe(htmlmin({
			removeComments: true,
			preserveLineBreaks: true,
			removeRedundantAttributes: true,
			collapseWhitespace: true
		}))
        .pipe(rename(config.html.output))
        .pipe(gulp.dest('../laravel/resources/views/admin/'));
});

gulp.task('watch', function () {
	livereload.listen();
	watch(config.js.files.input)
		.on('change', function(file) {
			gulp.start('jshint');
			gulp.start('js:files');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('jshint');
			gulp.start('js:files');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('jshint');
			gulp.start('js:files');
			gulp.start('refresh');
		});

	watch(config.js.vendor.input)
		.on('change', function(file) {
			gulp.start('js:vendor');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('js:vendor');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('js:vendor');
			gulp.start('refresh');
		});

	watch([config.html.tpl.common, config.html.tpl.modules])
		.on('change', function(file) {
			gulp.start('js:templates');
			gulp.start('html');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('js:templates');
			gulp.start('html');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('js:templates');
			gulp.start('html');
			gulp.start('refresh');
		});
	watch([config.html.input])
		.on('change', function(file) {
			gulp.start('html');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('html');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('html');
			gulp.start('refresh');
		});


	watch(config.assets.images.input)
		.on('change', function(file) {
			gulp.start('build:images');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('build:images');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('build:images');
			gulp.start('refresh');
		});

	watch(config.assets.svg.input)
		.on('change', function(file) {
			gulp.start('build:svg');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('build:svg');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('build:svg');
			gulp.start('refresh');
		});

	watch('src/**/*.less')
		.on('change', function(file) {
			gulp.start('css');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('css');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('css');
			gulp.start('refresh');
		});
});

// Run livereload after file change
gulp.task('refresh', function () {
	livereload.changed('');
});
