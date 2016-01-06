var gulp = require('gulp'),
	runSequence = require('run-sequence'),
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
	connect = require('connect'),
	replace = require('gulp-replace-task'),
	pkg = require('./package.json'),
	environment,
	dotenv = require('dotenv').config({path: '../laravel/.env'}),
	config = require('./build.config.js');

gulp.task('test', function () {
	gutil.log(config.placeholders);
});

// FACADES
gulp.task('build', function(callback) {
	runSequence('clean:dist',
		'jshint',
		['js:vendor', 'js:templates', 'css', 'fonts', 'images', 'svg'],
		'js:files',
		'html',
		callback);
});

gulp.task('production', function(callback) {
	runSequence(['clean:dist'],
		'jshint',
		['js:vendor', 'js:templates', 'css', 'fonts', 'images', 'svg'],
		'js:files',
		'compile:js',
		'compile:clean',
		'html',
		callback);
});

gulp.task('default', ['env']);

// run a server and a watcher
gulp.task('dev', ['env', 'server', 'watch']);

// TASKS
// set the environment
gulp.task('env', function() {
	environment = process.env.APP_ENV;
	gutil.log('Environment: ' + environment);
	if(environment === 'local') {
		gulp.start('build');
	} else {
		gulp.start('production');
	}
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
	return gulp.src(config.js.vendor.input)
		.pipe(plumber())
		.pipe(concat(config.js.vendor.output))
		.pipe(gulp.dest(config.js.files.output))
		.on('error', gutil.log);
});

// Process app's JS into app.js.
gulp.task('js:files', function () {
	var condition = (environment === 'local'),
		filename = 'app.js',
		jsLive,
		banner = '(function ( window, angular, undefined ) {\n';
	banner +=  '\'use strict\';\n';
	banner +=  '<%= contents %>';
	banner +=  '})( window, window.angular );';
	jsLive = lazypipe()
		.pipe(concat, filename)
		.pipe(wrap, banner);

	return gulp.src(config.js.files.input)
		.pipe(plumber())
		.pipe(replace({
			patterns: config.placeholders
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
		.pipe(gulpif(!condition, jsLive()))
		.pipe(gulp.dest(config.js.files.output))
		.on('error', gutil.log);
});

gulp.task('compile:js', function () {
	var filename = pkg.name + '-v' + pkg.version + '.js';

	return gulp.src([
		config.js.files.output + config.js.vendor.output,
		config.js.files.output + config.html.tpl.output,
		config.js.files.output + 'app.js'
	])
		.pipe(plumber())
		.pipe(header(config.banner.min, { pkg: pkg }))
		.pipe(concat(filename))
		.pipe(rename({
			suffix: '.min'
		}))
		.pipe(uglify({
			outSourceMap: filename + '.map'
		}))
		.pipe(gulp.dest(config.js.files.output));
});

gulp.task('compile:clean', function () {
	var filename = pkg.name + '-v' + pkg.version + '.min.js';

	return del.sync([
		config.js.files.output + '*.js',
		'!' + config.js.files.output,
		'!' + config.js.files.output + filename
	], { force: true});
});

// start a server localhost in php
gulp.task('server', function (next) {
	connectPhp.server({
		//hostname: 'localhost',
		port: config.port,
		base: config.paths.server_path
	});
});

gulp.task('svg', function () {
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

// Copy fonts files into output folder
gulp.task('fonts', function() {
	return gulp.src(config.assets.fonts.input)
		.pipe(gulp.dest(config.assets.fonts.output));
});

// Copy image files into output folder
gulp.task('images', function() {
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

gulp.task('gulpfile', function () {
	return gulp.src('./Gulpfile.js')
		.pipe(plumber())
		.pipe(jshint('.jshintrc'))
		.pipe(jshint.reporter(stylish))
		.pipe(jshint.reporter('fail'));
});

gulp.task('js:templates', function () {
	return gulp.src([config.html.tpl.common, config.html.tpl.modules])
		.pipe(plumber())
		.pipe(html2js({
			outputModuleName: 'templates-app',
			useStrict: true,
			base: 'src/'
		}))
		.pipe(concat(config.html.tpl.output))
		.pipe(gulp.dest(config.js.files.output))
		.on('error', gutil.log);
});

gulp.task('css', function () {
	var filename = 'styles.css',
		cleancss = new LessPluginCleanCSS({ advanced: true }),
		autoprefix = new LessPluginAutoPrefix({ browsers: ["last 2 versions"] }),
		condition = (environment === 'local');

	return gulp.src(config.less.input)
		.pipe(plumber())
		.pipe(concat(filename))
		.pipe(prettify({indentSize: 4}))
		.pipe(sourcemaps.init())
		.pipe(less({
			paths: ['src/less/'],
			plugins: (condition ? [autoprefix] : [autoprefix, cleancss])
		}))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest(config.less.output))
		.on('error', gutil.log);
});

//// Convert index.jade into index.html.
gulp.task('html', function () {
	var condition = environment !== 'local',
		input, inputs, sources,
		appending = '';

	if(condition) {
		input = [];
		inputs = [
			'/js/' + pkg.name + '-v' + pkg.version + '.min.js'
		];
	}
	else {
		input = config.js.files.input;
		inputs = [
			'http://localhost:4001/livereload.js',
			'/js/' + config.js.vendor.output,
			'/js/templates.js'
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
				return '<script type="text/javascript" src="' + filepath.replace('/src/', '/js/') + '"></script>';
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
		.pipe(gulp.dest('../public_html/admin/'));
});

gulp.task('watch', function () {
	livereload.listen(4001);

	watch('./Gulpfile.js')
			.on('change', function (file) {
				gulp.start('gulpfile');
			});

	watch(config.js.files.input)
			.on('change', function (file) {
				runSequence('jshint', 'js:files', 'refresh');
			})
			.on('add', function (file) {
				runSequence('jshint', 'js:files', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('jshint', 'js:files', 'refresh');
			});

	watch(config.js.vendor.input)
			.on('change', function (file) {
				runSequence('js:vendor', 'refresh');
			})
			.on('add', function (file) {
				runSequence('js:vendor', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('js:vendor', 'refresh');
			});

	watch([config.html.tpl.common, config.html.tpl.modules])
			.on('change', function (file) {
				runSequence('js:templates', 'html', 'refresh');
			})
			.on('add', function (file) {
				runSequence('js:templates', 'html', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('js:templates', 'html', 'refresh');
			});

	watch([config.html.input])
			.on('change', function (file) {
				runSequence('html', 'refresh');
			})
			.on('add', function (file) {
				runSequence('html', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('html', 'refresh');
			});

	watch(config.assets.images.input)
			.on('change', function (file) {
				runSequence('images', 'refresh');
			})
			.on('add', function (file) {
				runSequence('images', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('images', 'refresh');
			});

	watch(config.assets.svg.input)
			.on('change', function (file) {
				runSequence('svg', 'refresh');
			})
			.on('add', function (file) {
				runSequence('svg', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('svg', 'refresh');
			});

	watch(config.assets.fonts.input)
			.on('change', function (file) {
				runSequence('fonts', 'refresh');
			})
			.on('add', function (file) {
				runSequence('fonts', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('fonts', 'refresh');
			});

	watch(['src/**/*.less', 'src/**/*.css'])
			.on('change', function (file) {
				runSequence('css', 'refresh');
			})
			.on('add', function (file) {
				runSequence('css', 'refresh');
			})
			.on('unlink', function (file) {
				runSequence('css', 'refresh');
			});
});

// Run livereload after file change
gulp.task('refresh', function () {
	livereload.changed('');
});
