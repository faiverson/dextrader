var gulp = require('gulp'),
    clean = require('gulp-clean'),
    jshint = require('gulp-jshint'),
    less = require('gulp-less'),
    minifyCSS = require('gulp-minify-css'),
    html2js = require('gulp-html2js'),
    concat = require('gulp-concat'),
    ngmin = require('gulp-ngmin'),
    uglify = require('gulp-uglify'),
    htmlmin = require('gulp-htmlmin'),
    imagemin = require('gulp-imagemin'),
    cache = require('gulp-cache'),
    livereload = require('gulp-livereload'),
    wrap = require('gulp-wrap'),
    runSequence = require('run-sequence'),
    files = require('./build.config.js').files,
    rename = require('gulp-rename'),
    connectPhp = require('gulp-connect-php'),
    inject = require('gulp-inject'),
    connect = require('connect');

var productionDir = '_public', // production output directory (default: _public)
    port = require('./build.config.js').port;


// Concatenate vendor JS into vendor.js.
gulp.task('js:vendor', function () {
    return gulp.src(files.js.vendor)
        .pipe(concat('vendor.js'))
        .pipe(gulp.dest(files.js.buildDest));
});

// Process app's JS into app.js.
gulp.task('js:app', function () {

    return gulp.src(files.js.app)
        .pipe(jshint('.jshintrc'))
        .pipe(jshint.reporter('default'))
        //.pipe(ngmin())
        //.pipe(concat('app.js'))
        //.pipe(wrap('(function ( window, angular, undefined ) {\n'
        //+ '\'use strict\';\n'
        //+ '<%= contents %>'
        //+ '})( window, window.angular );'))
        .pipe(gulp.dest(files.js.buildDest));
});

// Cache src/modules templates into templates-modules.js.
gulpJSTemplates('modules');

// Cache src/common templates into templates-common.js.
gulpJSTemplates('common');

// Process Less files into main.css.
gulp.task('css', function () {
    return gulp.src(files.less.main)
        .pipe(concat('main.less'))
        .pipe(less())
        .pipe(gulp.dest(files.less.buildDest));
});

gulp.task('vendor_css', function () {
    return gulp.src(files.css.vendor_files)
        .pipe(concat('vendor.css'))
        .pipe(gulp.dest(files.css.buildDest));
});

// Convert index.jade into index.html.
gulp.task('html', function () {
    var sources = gulp.src(files.js.app , {read: false});
   console.log(sources);

    return gulp.src(files.html.index)
        .pipe(inject(sources, {
            relative: false,
            transform: function(filepath, file, index, length, targetFile){

                return '<script type="text/javascript" src="/js/' + filepath.replace('/src/', '') + '"></script>';
            }
        }))
        .pipe(rename(files.html.buildDestFileName))
        .pipe(gulp.dest(files.html.buildDest));
});

// Process images.
gulp.task('img', function () {
    return gulp.src(files.img.src)
        .pipe(cache(imagemin({
            optimizationLevel: 5,
            progressive: true,
            interlaced: true
        })))
        .pipe(gulp.dest(files.img.buildDest));
});

// Compile CSS for production.
gulp.task('compile:css', function () {
    return gulp.src('build/**/*.css')
        .pipe(minifyCSS({keepSpecialComments: 0}))
        .pipe(gulp.dest(productionDir));
});

// Compile JS for production.
gulp.task('compile:js', function () {
    return gulp.src('build/**/*.js')
        .pipe(uglify())
        .pipe(gulp.dest(productionDir));
});

// Compile HTML for production.
gulp.task('compile:html', function () {
    return gulp.src('build/**/*.htm*')
        .pipe(htmlmin({collapseWhitespace: true}))
        .pipe(gulp.dest(productionDir));
});

// Prepare images for production.
gulp.task('compile:img', function () {
    return gulp.src('build/img/**')
        .pipe(gulp.dest(productionDir + '/img'));
});

// Clean build directory.
gulpClean('build');

// Clean production directory.
gulpClean(productionDir);

// Clean build and production directories.
gulp.task('clean', function (callback) {
    runSequence(
        ['clean:build', 'clean:' + productionDir],
        callback
    );
});

// Build files for local development.
gulp.task('build', function (callback) {
    runSequence(
        'clean:build',
        [
            'js:vendor',
            'js:app',
            'js:templates-common',
            'js:templates-modules',
            'css',
            'vendor_css',
            'html',
            'img'
        ],
        callback);
});

// Process files and put into directory ready for production.
gulp.task('compile', function (callback) {
    runSequence(
        ['build', 'clean:' + productionDir],
        [
            'compile:js',
            'compile:css',
            'compile:html',
            'compile:img'
        ],
        callback);
});

// Run server.
gulp.task('server', ['build'], function (next) {
    var server = connect();
    server.use(connect.static('build')).listen(port, next);
});

// Run server.
gulp.task('serverPhp', ['build'], function (next) {
    connectPhp.server({ base: '../public_html'});
});

// Watch task
gulp.task('watch:files', [], function () {
    gulp.watch('build.config.js', ['js:vendor']);

    gulp.watch(files.js.app, ['js:app']);

    gulp.watch(files.html.tpls.modules, ['js:templates-modules']);

    gulp.watch(files.html.tpls.common, ['js:templates-common']);

    gulp.watch([
        'src/less/**/*.less',
        'src/common/**/*.less',
        'src/modules/**/*.less'
    ], ['css']);

    gulp.watch(files.html.index, ['html']);

    gulp.watch(files.img.src, ['img']);

    // Livereload
    var server = livereload();

    gulp.watch('../public_html/**/*', function (event) {
        server.changed(event.path);
    });
});


// Build, run server watch for changes.
gulp.task('watch', function (callback) {
    runSequence(
        'watch:files',
        'serverPhp',
        callback);
});

// Same as watch:files.
gulp.task('default', ['watch:files']);


/**
 * Generate tasks for Angular JS template caching
 *
 * @param {string} folder
 * @return stream
 */
function gulpJSTemplates(folder) {
    gulp.task('js:templates-' + folder, function () {
        return gulp.src(files.html.tpls[folder])
            //.pipe(jade({pretty: true}))
            .pipe(html2js({
                outputModuleName: 'templates-' + folder,
                useStrict: true,
                base: 'src/' + folder
            }))
            .pipe(concat('templates-' + folder + '.js'))
            .pipe(gulp.dest(files.js.buildDest));
    });
}

/**
 * Generate cleaning tasks.
 *
 * @param {string} folder
 * @return stream
 */
function gulpClean(folder) {
    gulp.task('clean:' + folder, function () {
        return gulp.src(folder, {read: false, force: true})
            .pipe(clean());
    });
}
