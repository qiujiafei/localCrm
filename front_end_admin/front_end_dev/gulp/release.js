'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');
var stylediff = require('stylediff');
var fs = require('fs');

var $ = require('gulp-load-plugins')();

var _ = require('lodash');

var sassOptions = {
    style: 'expanded'
};

gulp.task('release', ['release-global', 'release-custom','release-supply', 'release-admin', 'release-business','release-wechat'], function () {
    (function _recoverFs() {
        fs.rename(path.join(conf.paths.bower, '/bootstrap-sass/assets/stylesheets/bootstrap-origin.scss'), path.join(conf.paths.bower, '/bootstrap-sass/assets/stylesheets/_bootstrap.scss'), function (err) {
            if (err) {
                throw err;
            }
        });
        fs.rename(path.join(conf.paths.src, '/scss/bootstrap-customed.scss'), path.join(conf.paths.src, '/scss/_bootstrap.scss'), function (err) {
            if (err) {
                throw err;
            }
        })
        fs.unlink(path.join(conf.paths.src, '/scss/bootstrap-origin.css'));
        fs.unlink(path.join(conf.paths.src, '/scss/bootstrap-customed.css'));
        fs.unlink(path.join(conf.paths.src, '/scss/bootstrap-diff.scss'));
    })();
});

// release for global
gulp.task('release-global', ['release-global-css'])

gulp.task('release-global-css', ['diff-css'], function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/global/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('global', 'css')));
});





// release for admin
gulp.task('release-admin', ['release-admin-css', 'release-admin-nanjing-css', 'release-admin-iframe-css', 'release-admin-count-css', 'release-admin-info-css', 'release-admin-service-css', 'release-admin-activity-css', 'release-admin-fund-css', 'release-admin-others'])

gulp.task('release-admin-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/admin/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('admin', 'css')));
});

gulp.task('release-admin-iframe-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/adminIframe/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminIframe', 'css')));
});

gulp.task('release-admin-count-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/adminCount/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminCount', 'css')));
});

gulp.task('release-admin-info-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/adminInfo/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminInfo', 'css')));
});


gulp.task('release-admin-service-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/adminService/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminService', 'css')));
});

gulp.task('release-admin-activity-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/adminActivity/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminActivity', 'css')));
});

gulp.task('release-admin-nanjing-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/adminNanjing/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminNanjing', 'css')));
});

gulp.task('release-admin-fund-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/adminFund/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminFund', 'css')));
});

gulp.task('release-admin-others', function () {
    return gulp.src([
            path.join(conf.paths.src, '/images/**/*')
        ], {
            "base": path.join(conf.paths.src, "/")
        })
        .pipe(gulp.dest(conf.paths.release('admin', 'others')));
})

// release for custom
gulp.task('release-custom', ['release-custom-css', 'release-account-css', 'release-custom-others', 'release-customTemp-css', 'release-custom-quality-css', 'release-custom-membrane-css'])

gulp.task('release-custom-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/custom/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('custom', 'css')));
});

gulp.task('release-account-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/account/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('account', 'css')));
});

gulp.task('release-customTemp-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/customTemp/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('customTemp', 'css')));
});

gulp.task('release-custom-quality-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/customQuality/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('customQuality', 'css')));
});

gulp.task('release-custom-membrane-css', function () {
    return gulp.src([
        path.join(conf.paths.src, '/scss/release/custom-membrane/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('custom-membrane', 'css')));
});

gulp.task('release-custom-others', function () {
    return gulp.src([
            path.join(conf.paths.src, '/images/**/*')
        ], {
            "base": path.join(conf.paths.src, "/")
        })
        .pipe(gulp.dest(conf.paths.release('custom', 'others')));
})

// release for supply
gulp.task('release-supply', ['release-supply-vender', 'release-supply-css', 'release-supply-others'])

gulp.task('release-supply-vender', function () {
    return gulp.src([
            path.join(conf.paths.src, '/js/kindeditor/**/*')
        ], {
            'base': path.join(conf.paths.src, '/js/')
        })
        .pipe(gulp.dest(conf.paths.release('supply', 'vender')));
});

gulp.task('release-supply-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/supply/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('supply', 'css')));
});

gulp.task('release-supply-others', function () {
    return gulp.src([
            path.join(conf.paths.src, '/images/**/*')
        ], {
            "base": path.join(conf.paths.src, "/")
        })
        .pipe(gulp.dest(conf.paths.release('supply', 'others')));
})

//release for business

gulp.task('release-business', ['release-business-css', 'release-business-bank-css', 'release-business-data-css', 'release-business-temp-css', 'release-business-leader-css', 'release-business-site-css', 'release-business-account-css', 'release-business-others','release-business-quality-css', 'release-business-membrane-css']);

gulp.task('release-business-membrane-css', function(){
    return gulp.src([
        path.join(conf.paths.src + '/scss/release/business-membrane/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('business-membrane', 'css')));
})

gulp.task('release-business-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/business/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('business', 'css')));
});

gulp.task('release-business-leader-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/businessLeader/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('businessLeader', 'css')));
});

gulp.task('release-business-site-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/businessSite/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('businessSite', 'css')));
});


gulp.task('release-business-account-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/businessAccount/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('businessAccount', 'css')));
});

gulp.task('release-business-quality-css', function () {
    return gulp.src([
        path.join(conf.paths.src, '/scss/release/businessQuality/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('businessQuality', 'css')));
});

gulp.task('release-business-temp-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/businessTemp/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('businessTemp', 'css')));
});

gulp.task('release-business-data-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/businessData/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('businessData', 'css')));
});

gulp.task('release-business-bank-css', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/businessBank/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('businessBank', 'css')));
});

gulp.task('release-business-others', function () {
    return gulp.src([
            path.join(conf.paths.src, '/images/**/*')
        ], {
            "base": path.join(conf.paths.src, "/")
        })
        .pipe(gulp.dest(conf.paths.release('business', 'others')));
})

// compare to get diff part of customed bootstrap file
gulp.task('diff-css', ['output-css-bootstrap-origin', 'output-css-bootstrap-customed'], function () {
    var css1 = fs.readFileSync(path.join(conf.paths.src, '/scss/bootstrap-origin.css'), 'utf8');
    var css2 = fs.readFileSync(path.join(conf.paths.src, '/scss/bootstrap-customed.css'), 'utf8');
    stylediff(css2, css1, function (err, out) {
        fs.writeFileSync(path.join(conf.paths.src, '/scss/bootstrap-diff.scss'), out);
    });
})
gulp.task('output-css-bootstrap-origin', function () {
    fs.rename(path.join(conf.paths.bower, '/bootstrap-sass/assets/stylesheets/_bootstrap.scss'), path.join(conf.paths.bower, '/bootstrap-sass/assets/stylesheets/bootstrap-origin.scss'), function (err) {
        if (err) {
            throw err;
        }
    })
    return gulp.src([
            path.join(conf.paths.bower, '/bootstrap-sass/assets/stylesheets/bootstrap-origin.scss')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(path.join(conf.paths.src, '/scss/')));
});
gulp.task('output-css-bootstrap-customed', function () {
    fs.rename(path.join(conf.paths.src, '/scss/_bootstrap.scss'), path.join(conf.paths.src, '/scss/bootstrap-customed.scss'), function (err) {
        if (err) {
            throw err;
        }
    })
    return gulp.src([
            path.join(conf.paths.src, '/scss/bootstrap-customed.scss')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(path.join(conf.paths.src, '/scss/')));
});

// no diff
gulp.task('release-no-diff', ['release-global-no-diff', 'release-custom', 'release-supply', 'release-admin', 'release-business','release-wechat']);

gulp.task('release-global-no-diff', ['release-global-css-no-diff'])

gulp.task('release-global-css-no-diff', function () {
    return gulp.src([
            path.join(conf.paths.src, '/scss/release/global/**/*')
        ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer()).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('global', 'css')));
});


//release for wechat
gulp.task('release-wechat', ['release-app-css', 'release-app-images', 'release-app-membrane-css','release-app-temp-css','release-app-account-css', 'release-app-customization']);

gulp.task('release-app-css', function () {
    return gulp.src([
        path.join(conf.paths.src, '/wechat/scss/release/app/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer({browsers: ['iOS 7']})).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('mobile', 'css')));
});

gulp.task('release-app-account-css', function () {
    return gulp.src([
        path.join(conf.paths.src, '/wechat/scss/release/app-account/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer({browsers: ['iOS 7']})).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('app-account', 'css')));
});

gulp.task('release-app-customization', function () {
    return gulp.src([
        path.join(conf.paths.src, '/wechat/scss/release/app-customization/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer({browsers: ['iOS 7']})).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('app-customization', 'css')));
});

gulp.task('release-app-temp-css', function () {
    return gulp.src([
        path.join(conf.paths.src, '/wechat/scss/release/app-temp/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer({browsers: ['iOS 7']})).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('app-temp', 'css')));
});

gulp.task('release-app-membrane-css', function () {
    return gulp.src([
        path.join(conf.paths.src, '/wechat/scss/release/app-membrane/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer({browsers: ['iOS 7']})).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('app-membrane', 'css')));
});


gulp.task('release-app-images', function () {
    return gulp.src([
        path.join(conf.paths.src, '/wechat/images/**/*')
    ], {
        "base": path.join(conf.paths.src, "/wechat/")
    })
        .pipe(gulp.dest(conf.paths.release('mobile', 'others')));
});


gulp.task('release-admin-wechat', ['release-admin-wechat-css'])


gulp.task('release-admin-wechat-css', function () {
    return gulp.src([
        path.join(conf.paths.src, '/wechat/scss/release/adminWechat/**/*')
    ])
        .pipe($.sass(sassOptions)).on('error', conf.errorHandler('Sass'))
        .pipe($.autoprefixer({browsers: ['iOS 7']})).on('error', conf.errorHandler('Autoprefixer'))
        .pipe(gulp.dest(conf.paths.release('adminWechat', 'css')));
});
