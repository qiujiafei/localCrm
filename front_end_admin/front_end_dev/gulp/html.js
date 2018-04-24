'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');
var fileinclude = require('gulp-file-include');

var $ = require('gulp-load-plugins')({
  pattern: ['gulp-*', 'uglify-save-license', 'del']
});

var browserSync = require('browser-sync');

//用于在html文件中直接include文件
gulp.task('htmlInclude', function (done) {
    return gulp.src(path.join(conf.paths.src, '/html/**/*.html'))
        .pipe(fileinclude({
          prefix: '@@',
          basepath: '@file'
        }))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/html/')))
        // .pipe(browserSync.reload({ stream: true }))
        .pipe($.size())
});