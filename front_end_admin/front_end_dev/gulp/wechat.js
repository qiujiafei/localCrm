var gulp = require('gulp');
var path = require('path');
var conf = require('./conf');
var fileinclude = require('gulp-file-include');
var $ = require('gulp-load-plugins')();

var browserSync = require('browser-sync');
var proxyMiddleware = require('http-proxy-middleware');
var util = require('util');

function isOnlyChange(event) {
    return event.type === 'changed';
}

function browserSyncInit(baseDir, browser) {
    browser = browser === undefined
        ? 'default'
        : browser;

    var routes = null;
    if (baseDir === conf.paths.wechat || (util.isArray(baseDir) && baseDir.indexOf(conf.paths.wechat) !== -1)) {
        routes = {
            // '/bower_components': 'bower_components'
        };
    }

    var server = {
        baseDir: baseDir,
        reloadDelay: 500,
        routes: routes
    };

    browserSync.instance = browserSync.init({
        // open: "external", // open with ip
        open: "local",
        notify: false,
        startPath: '/html/index.html',
        server: server,
        browser: browser
    });
}

gulp
    .task('serve-wechat', ['watch-wechat'], function () {
        browserSyncInit([
            path.join(conf.paths.tmp, '/serve'),
            conf.paths.wechat
        ]);
    });

gulp.task('watch-wechat', [
    'wechat-inject', 'wechat-img'
], function () {
    gulp
        .watch([
            path.join(conf.paths.wechat, '/scss/**/*.css'),
            path.join(conf.paths.wechat, '/scss/**/*.scss'),
            path.join(conf.paths.wechat, '/images/*')
        ], function (event) {
            if (event.type === 'changed') {
                gulp.start('wechat-styles');
                gulp.start('wechat-img');
            } else {
                gulp.start('wechat-inject');
            }
        });

    gulp.watch(path.join(conf.paths.wechat, '/js/**/*.js'), function (event) {
        if (isOnlyChange(event)) {
            gulp.start('wechat-scripts');
        } else {
            gulp.start('wechat-inject');
        }
    });

    gulp.watch([
        path.join(conf.paths.wechat, '/html/**/*.html'),
        path.join(conf.paths.wechat, '/html/*.inc')
    ], function (event) {
        if (isOnlyChange(event)) {
            gulp.start('wechat-htmlInclude');
            gulp.start('wechat-inject');
        } else {
            gulp.start('wechat-inject');
        }
    });
});

// image
gulp.task('wechat-img', function () {
    var fileFilter = $.filter(function (file) {
        return file
            .stat
            .isFile();
    });

    return gulp.src([
        path.join(conf.paths.wechat, '/**/*'),
            path.join('!' + conf.paths.wechat, '/**/*.{html,css,js,scss,inc}')
        ])
        .pipe(fileFilter)
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve')));
});

// html
gulp.task('wechat-htmlInclude', function (done) {
    return gulp
        .src(path.join(conf.paths.wechat, '/html/**/*.html'))
        .pipe(fileinclude({prefix: '@@', basepath: '@file'}))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/html/')))
        // .pipe(browserSync.reload({ stream: true }))
        .pipe($.size())
});

// script
gulp.task('wechat-scripts', ['wechat-scripts-lib'], function () {
    return gulp.src([
        path.join(conf.paths.wechat, '/js/zepto.min.js'),
        path.join(conf.paths.wechat, '/js/fastclick.js'),
        path.join(conf.paths.wechat, '/js/weui.min.js'),
            path.join(conf.paths.wechat, '/js/swipeSlide.min.js')
        ])
        .pipe($.sourcemaps.init())
        .pipe($.concat('app.js'))
        .pipe($.sourcemaps.write('maps'))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/scripts/')))
        .pipe(browserSync.reload({stream: true}))
        .pipe($.size())
});

gulp.task('wechat-scripts-lib', function () {
    return gulp
        .src([// put out-sourced (like bower) scripts here
        path.join(conf.paths.wechat, '/js/gallary-carousels.js')])
        .pipe($.sourcemaps.init())
        .pipe($.concat('libs.js'))
        .pipe($.sourcemaps.write('maps'))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/libs/')))
        .pipe(browserSync.reload({stream: true}))
        .pipe($.size())
})

gulp.task('wechat-styles', function () {
    var sassOptions = {
        style: 'expanded'
    };

    return gulp.src([path.join(conf.paths.wechat, '/scss/app.scss')])
    // .pipe($.inject(injectFiles, injectOptions))
        .pipe($.sourcemaps.init())
        .pipe($.sass(sassOptions))
        .on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer())
        .on('error', conf.errorHandler('Autoprefixer'))
        .pipe($.sourcemaps.write())
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/styles/')))
        .pipe(browserSync.reload({stream: true}));
});

gulp.task('wechat-inject', [
    'wechat-htmlInclude', 'wechat-scripts', 'wechat-styles'
], function () {
    var injectStyles = gulp.src([
        path.join(conf.paths.tmp, '/serve/styles/*.css'),
        path.join(conf.paths.tmp, '/serve/libs/*.js'),
        path.join(conf.paths.tmp, '/serve/scripts/*.js')
        // path.join('!' + conf.paths.tmp, '/serve/vendor.css'), path.join('!' +
        // conf.paths.tmp, '/serve/vendor.js')
    ], {read: false});

    var injectOptions = {
        ignorePath: [
            conf.paths.src, path.join(conf.paths.tmp, '/serve')
        ],
        addRootSlash: true
    };

    return gulp
        .src(path.join(conf.paths.tmp, '/serve/html/**/*.html'))
        .pipe($.inject(injectStyles, injectOptions))
        .pipe(gulp.dest(path.join(conf.paths.tmp, '/serve/html/')))
        .pipe(browserSync.reload({stream: true}));
});
