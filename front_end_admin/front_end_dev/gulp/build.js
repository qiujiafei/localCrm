'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');
var replace = require('gulp-replace');

var $ = require('gulp-load-plugins')({
    pattern: ['gulp-*', 'uglify-save-license', 'del']
});

gulp.task('html', ['inject'], function () {

    var htmlFilter = $.filter('**/*.html', {
        restore: true
    });
    var jsFilter = $.filter('**/*.js', {
        restore: true
    });
    var cssFilter = $.filter('**/*.css', {
        restore: true
    });
    var assets;

    return gulp.src(path.join(conf.paths.tmp, '/serve/html/*.html'))
        .pipe(assets = $.useref.assets())
        .pipe($.rev())
        .pipe(jsFilter)
        // .pipe($.sourcemaps.init())
        .pipe($.ngAnnotate())
        // .pipe($.uglify({
        //     preserveComments: $.uglifySaveLicense
        // })).on('error', conf.errorHandler('Uglify'))
        // .pipe($.sourcemaps.write('maps'))
        .pipe(jsFilter.restore)
        .pipe(cssFilter)
        // .pipe($.sourcemaps.init())
        .pipe($.minifyCss({
            processImport: false
        }))
        .pipe(replace('/images', '../images'))
        // .pipe($.sourcemaps.write('maps'))
        .pipe(cssFilter.restore)
        .pipe(assets.restore())
        .pipe($.useref())
        .pipe($.revReplace())
        .pipe(htmlFilter)
        // .pipe($.minifyHtml({
        //   empty: true,
        //   spare: true,
        //   quotes: true,
        //   conditionals: true
        // }))
        .pipe(replace('../images', './images'))
        .pipe(replace('/images', './images'))
        .pipe(replace('/html', ''))
        .pipe(replace('<link rel="stylesheet" href="/styles', '<link rel="stylesheet" href="styles'))
        .pipe(replace('<script src="/scripts', '<script src="scripts'))
        .pipe(htmlFilter.restore)
        .pipe(gulp.dest(path.join(conf.paths.dist, '/')))
        .pipe($.size({
            title: path.join(conf.paths.dist, '/'),
            showFiles: true
        }));
});


gulp.task('clean', function () {
    var targetRoot = path.resolve(conf.paths.dist)
    var tmpRoot = path.resolve(conf.paths.tmp)
    var dirs = [
        'fonts',
        'images',
        'scripts',
        'styles',
        '*.html'
    ]
    var paths = [ tmpRoot ]

    dirs.forEach(function (dir) {
        paths.push(path.join(targetRoot, dir))
    });

    return $.del(paths, { force: true });
});

gulp.task('build', ['html', 'move-src'], function () {
    var fileFilter = $.filter(function (file) {
        return file.stat.isFile();
    });

    return gulp.src([
            path.join(conf.paths.tmp, '/serve/scripts/vender/**/*'),
            path.join(conf.paths.tmp, '/serve/styles/vender/**/*'),
        ], {
            "base": path.join(conf.paths.tmp, "/serve/")
        })
        .pipe(fileFilter)
        .pipe(gulp.dest(path.join(conf.paths.dist, '/')));
});

gulp.task('move-src', function () {
    return gulp.src([
            path.join(conf.paths.src, '/images/**/*'),
            path.join(conf.paths.src, '/fonts/**/*'),
            path.join(conf.paths.src, '/js/**/*.json'),
        ], {
            "base": path.join(conf.paths.src, "/")
        })
        .pipe(gulp.dest(path.join(conf.paths.dist, '/')));
});
