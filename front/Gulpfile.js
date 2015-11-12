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
	minify = require('gulp-minify-css'),
    html2js = require('gulp-html2js'),
	gulpif = require('gulp-if'),
	vinylPaths = require('vinyl-paths'),
    concat = require('gulp-concat'),
    ngmin = require('gulp-ngmin'),
    uglify = require('gulp-uglify'),
	svgmin = require('gulp-svgmin'),
	svgstore = require('gulp-svgstore'),
    htmlmin = require('gulp-htmlmin'),
    imagemin = require('gulp-imagemin'),
    cache = require('gulp-cache'),
    livereload = require('gulp-livereload'),
    wrap = require('gulp-wrap'),
    runSequence = require('run-sequence'),
	markdown = require('gulp-markdown'),
	fileinclude = require('gulp-file-include'),
	sourcemaps = require('gulp-sourcemaps')
    connectPhp = require('gulp-connect-php'),
    inject = require('gulp-inject'),
    connect = require('connect'),
	pkg = require('./package.json'),
	environment = 'dev',
	config = require('./build.config.js');

// FACADES
gulp.task('default', ['jshint', 'js:vendor', 'build:images', 'build:svg']);
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
gulp.task('js:vendor', ['clean:dist'], function() {
	var condition = (environment !== 'dev'),
		jsDev = lazypipe()
			.pipe(header, config.banner.full, { pkg: pkg })
			.pipe(gulp.dest, config.js.files.output),

		jsLive = lazypipe()
			.pipe(rename, { suffix: '.min' })
			//.pipe(vinylPaths(del))
			.pipe(uglify)
			.pipe(header, config.banner.min, { pkg: pkg })
			.pipe(gulp.dest, config.js.files.output);

	return gulp.src(config.js.vendor.input)
		.pipe(plumber())
		.pipe(sourcemaps.init())
		.pipe(concat(config.js.vendor.output))
		.pipe(jsDev())
		.pipe(gulpif(condition, jsLive()))
		.pipe(sourcemaps.write('./'))
		.on('error', gutil.log);
});

// start a server localhost in php
gulp.task('server', function (next) {
	connectPhp.server({
		//hostname: 'localhost',
		port: config.port,
		base: config.paths.output
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
		.pipe(jshint())
		.pipe(jshint.reporter(stylish))
		.pipe(jshint.reporter('fail'));
});

gulp.task('watch', function () {
	livereload.listen();
	watch(config.js.files.input)
		.on('change', function(file) {
			gulp.start('default');
			gulp.start('refresh');
		}).on('add', function(file) {
			gulp.start('default');
			gulp.start('refresh');
		}).on('unlink', function(file) {
			gulp.start('default');
			gulp.start('refresh');
		});
});

// Run livereload after file change
gulp.task('refresh', function () {
	livereload.changed();
});

//// Process app's JS into app.js.
//gulp.task('js:app', function () {
//
//    return gulp.src(files.js.app)
//        .pipe(jshint('.jshintrc'))
//        .pipe(jshint.reporter('default'))
//        //.pipe(ngmin())
//        //.pipe(concat('app.js'))
//        //.pipe(wrap('(function ( window, angular, undefined ) {\n'
//        //+ '\'use strict\';\n'
//        //+ '<%= contents %>'
//        //+ '})( window, window.angular );'))
//        .pipe(gulp.dest(files.js.buildDest));
//});
//
//// Cache src/modules templates into templates-modules.js.
//gulpJSTemplates('modules');
//
//// Cache src/common templates into templates-common.js.
//gulpJSTemplates('common');
//
//// Process Less files into main.css.
//gulp.task('css', function () {
//    return gulp.src(files.less.main)
//        .pipe(concat('main.less'))
//        .pipe(less())
//        .pipe(gulp.dest(files.less.buildDest));
//});
//
//gulp.task('vendor_css', function () {
//    return gulp.src(files.css.vendor_files)
//        .pipe(concat('vendor.css'))
//        .pipe(gulp.dest(files.css.buildDest));
//});
//
//// Convert index.jade into index.html.
//gulp.task('html', function () {
//    var sources = gulp.src(files.js.app , {read: false});
//
//    return gulp.src(files.html.index)
//        .pipe(inject(sources, {
//            relative: false,
//            transform: function(filepath, file, index, length, targetFile){
//
//                return '<script type="text/javascript" src="/js/' + filepath.replace('/src/', '') + '"></script>';
//            }
//        }))
//        .pipe(rename(files.html.buildDestFileName))
//        .pipe(gulp.dest(files.html.buildDest));
//});
//
//// Process images.
//gulp.task('img', function () {
//    return gulp.src(files.img.src)
//        .pipe(cache(imagemin({
//            optimizationLevel: 5,
//            progressive: true,
//            interlaced: true
//        })))
//        .pipe(gulp.dest(files.img.buildDest));
//});
//
//// Compile CSS for production.
//gulp.task('compile:css', function () {
//    return gulp.src('build/**/*.css')
//        .pipe(minifyCSS({keepSpecialComments: 0}))
//        .pipe(gulp.dest(productionDir));
//});
//
//// Compile JS for production.
//gulp.task('compile:js', function () {
//    return gulp.src('build/**/*.js')
//        .pipe(uglify())
//        .pipe(gulp.dest(productionDir));
//});
//
//// Compile HTML for production.
//gulp.task('compile:html', function () {
//    return gulp.src('build/**/*.htm*')
//        .pipe(htmlmin({collapseWhitespace: true}))
//        .pipe(gulp.dest(productionDir));
//});
//
//// Prepare images for production.
//gulp.task('compile:img', function () {
//    return gulp.src('build/img/**')
//        .pipe(gulp.dest(productionDir + '/img'));
//});
//
//// Clean build directory.
//gulpClean('build');
//
//// Clean production directory.
//gulpClean(productionDir);
//
//// Clean build and production directories.
//
//
//// Build files for local development.
//gulp.task('build', function (callback) {
//    runSequence(
//        'clean:build',
//        [
//            'js:vendor',
//            'js:app',
//            'js:templates-common',
//            'js:templates-modules',
//            'css',
//            'vendor_css',
//            'html',
//            'img'
//        ],
//        callback);
//});
//
//// Process files and put into directory ready for production.
//gulp.task('compile', function (callback) {
//    runSequence(
//        ['build', 'clean:' + productionDir],
//        [
//            'compile:js',
//            'compile:css',
//            'compile:html',
//            'compile:img'
//        ],
//        callback);
//});
//
//
//// Watch task
//gulp.task('watch:files', [], function () {
//    gulp.watch('build.config.js', ['js:vendor']);
//
//    gulp.watch(files.js.app, ['js:app']);
//
//    gulp.watch(files.html.tpls.modules, ['js:templates-modules']);
//
//    gulp.watch(files.html.tpls.common, ['js:templates-common']);
//
//    gulp.watch([
//        'src/less/**/*.less',
//        'src/common/**/*.less',
//        'src/modules/**/*.less'
//    ], ['css']);
//
//    gulp.watch(files.html.index, ['html']);
//
//    gulp.watch(files.img.src, ['img']);
//
//    // Livereload
//    var server = livereload();
//
//    gulp.watch('../public_html/**/*', function (event) {
//        server.changed(event.path);
//    });
//});
//
//// Build, run server watch for changes.
//gulp.task('watch', function (callback) {
//    runSequence(
//        'watch:files',
//        'serverphp',
//        callback);
//});
//
//// Same as watch:files.
//gulp.task('default', ['watch:files']);
//
///**
// * Generate tasks for Angular JS template caching
// *
// * @param {string} folder
// * @return stream
// */
//function gulpJSTemplates(folder) {
//    gulp.task('js:templates-' + folder, function () {
//        return gulp.src(files.html.tpls[folder])
//            //.pipe(jade({pretty: true}))
//            .pipe(html2js({
//                outputModuleName: 'templates-' + folder,
//                useStrict: true,
//                base: 'src/' + folder
//            }))
//            .pipe(concat('templates-' + folder + '.js'))
//            .pipe(gulp.dest(files.js.buildDest));
//    });
//}
//
///**
// * Generate cleaning tasks.
// *
// * @param {string} folder
// * @return stream
// */
//function gulpClean(folder) {
//    gulp.task('clean:' + folder, function () {
//		rimraf('./folder', cb);
//    });
//}

// Run server.
