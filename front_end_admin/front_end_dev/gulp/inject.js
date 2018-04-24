'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');
var gulpsync = require('gulp-sync')(gulp);
var $ = require('gulp-load-plugins')();
var browserSync = require('browser-sync');
var _ = require('lodash');

gulp.task('injectBefore', ['htmlInclude', 'scripts', 'styles']);

gulp.task('inject', gulpsync.sync(['injectBefore',[
    'index', 'login','shoreInfo','roleManager'
]]))

gulp.task('index', function() {
    var s_app = gulp.src([
        path.join(conf.paths.tmp, '/serve/styles/app.css'),
        path.join(conf.paths.tmp, '/serve/styles/index.css'),
        path.join(conf.paths.tmp, '/serve/scripts/moment.min.js'),
        path.join(conf.paths.tmp, '/serve/scripts/bootstrap-datepicker.min.js'),
        path.join(conf.paths.tmp, '/serve/libs/libs.js'),
        path.join(conf.paths.tmp, '/serve/scripts/app.js'),
        path.join(conf.paths.tmp, '/serve/scripts/index.js'),
    ], {read: false});
    var injectOptions = {
        ignorePath: [conf.paths.src, path.join(conf.paths.tmp, '/serve')],
        addRootSlash: true,
        starttag: '<!-- inject:{{ext}} -->'
    };
    return gulp.src(path.join(conf.paths.tmp, '/serve/html/index.html'))
        .pipe($.inject(s_app, injectOptions))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/html/')))
        .pipe(browserSync.reload({
            stream: true
        }));
})

gulp.task('login', function() {
    var s_app = gulp.src([
        path.join(conf.paths.tmp, '/serve/styles/app.css'),
        path.join(conf.paths.tmp, '/serve/styles/login.css'),
        path.join(conf.paths.tmp, '/serve/libs/libs.js'),
        path.join(conf.paths.tmp, '/serve/scripts/app.js'),
        path.join(conf.paths.tmp, '/serve/scripts/login.js'),
    ], {read: false});
    var injectOptions = {
        ignorePath: [conf.paths.src, path.join(conf.paths.tmp, '/serve')],
        addRootSlash: true,
        starttag: '<!-- inject:{{ext}} -->'
    };
    return gulp.src(path.join(conf.paths.tmp, '/serve/html/login.html'))
        .pipe($.inject(s_app, injectOptions))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/html/')))
        .pipe(browserSync.reload({
            stream: true
        }));
})

gulp.task('shoreInfo', function() {
    var s_app = gulp.src([
        path.join(conf.paths.tmp, '/serve/styles/app.css'),
        path.join(conf.paths.tmp, '/serve/styles/store_info.css'),
        path.join(conf.paths.tmp, '/serve/libs/libs.js'),
        path.join(conf.paths.tmp, '/serve/scripts/app.js'),
        path.join(conf.paths.tmp, '/serve/scripts/store_info.js'),
    ], {read: false});
    var injectOptions = {
        ignorePath: [conf.paths.src, path.join(conf.paths.tmp, '/serve')],
        addRootSlash: true,
        starttag: '<!-- inject:{{ext}} -->'
    };
    return gulp.src(path.join(conf.paths.tmp, '/serve/html/store_info.html'))
        .pipe($.inject(s_app, injectOptions))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/html/')))
        .pipe(browserSync.reload({
            stream: true
        }));
})

gulp.task('roleManager', function() {
    var s_app = gulp.src([
        path.join(conf.paths.tmp, '/serve/styles/app.css'),
        path.join(conf.paths.tmp, '/serve/styles/role-manager.css'),
        path.join(conf.paths.tmp, '/serve/libs/libs.js'),
        path.join(conf.paths.tmp, '/serve/scripts/app.js'),
        path.join(conf.paths.tmp, '/serve/scripts/role_manager.js'),
    ], {read: false});
    var injectOptions = {
        ignorePath: [conf.paths.src, path.join(conf.paths.tmp, '/serve')],
        addRootSlash: true,
        starttag: '<!-- inject:{{ext}} -->'
    };
    return gulp.src(path.join(conf.paths.tmp, '/serve/html/role_manager.html'))
        .pipe($.inject(s_app, injectOptions))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/html/')))
        .pipe(browserSync.reload({
            stream: true
        }));
})
