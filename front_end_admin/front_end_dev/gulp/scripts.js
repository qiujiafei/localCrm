'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');

var $ = require('gulp-load-plugins')({
    pattern: ['gulp-*', 'uglify-save-license', 'del']
});

var browserSync = require('browser-sync');

gulp.task('scripts', ['scripts-bower', 'scripts-each'], function () {
    return gulp.src([
            path.join(conf.paths.src, '/js/common.js')
        ])
        .pipe($.sourcemaps.init())
        .pipe($.concat('common.js'))
        // .pipe($.sourcemaps.write('maps'))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/scripts/')))
        .pipe(browserSync.reload({
            stream: true
        }))
        .pipe($.size())
});

gulp.task('scripts-bower', function () {
    return gulp.src([
            path.join(conf.paths.bower, '/bootstrap-select/dist/js/bootstrap-select.js'),
            path.join(conf.paths.bower, '/bootstrap-datepicker/dist/js/bootstrap-datepicker.js'),
            path.join(conf.paths.src, '/js/juicer-min.js')
        ])
        .pipe($.sourcemaps.init())
        .pipe($.concat('libs.js'))
        // .pipe($.sourcemaps.write('maps'))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/libs/')))
        .pipe(browserSync.reload({
            stream: true
        }))
        .pipe($.size())
})

gulp.task('scripts-each', function() {
    return gulp.src([
            path.join(conf.paths.src, '/js/each/**/*.js'),
            path.join(conf.paths.src, '/js/app.js')
        ])
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/scripts/')))
        .pipe(browserSync.reload({
            stream: true
        }))
        .pipe($.size())
})
