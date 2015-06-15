/**
 * Created by Clay on 19/04/15.
 */
(function (require) {

    'use strict';

    var gulp        = require('gulp'),
        browserSync = require('browser-sync'),
        reload      = browserSync.reload,
        jade        = require('gulp-jade'),
        typescript  = require('gulp-tsc'),
        usemin      = require('gulp-usemin'),
        uglify      = require('gulp-uglify'),
        minifyHtml  = require('gulp-minify-html'),
        minifyCss   = require('gulp-minify-css'),
        rev         = require('gulp-rev'),
        sass        = require('gulp-sass'),
        imagemin    = require('gulp-imagemin'),
        pngquant    = require('imagemin-pngquant'),
        sourcemaps  = require('gulp-sourcemaps'),
        runSequence = require('run-sequence'),
        minimist    = require('minimist'),
        gulpif      = require('gulp-if'),
        del         = require('del'),
        iconfontCss = require('gulp-iconfont-css'),
        iconfont    = require('gulp-iconfont'),

        knownOptions = {
            string: 'env',
            default: {
                env: process.env.NODE_ENV || 'prod'
            }
        },

        options     = minimist(process.argv.slice(2), knownOptions),

        paths       = {
            'jadeViews': './src/jade/views/**/*.jade',
            'jade': './src/jade/**/*.jade',
            'ts': './src/ts/**/*.ts',
            'sassViews': './src/scss/views/**/*.scss',
            'sass': './src/scss/**/*.scss',
            'build': './build/',
            'html': './build/**/*.html',
            'img': './src/img/*',
            'dist': './build/'
        };

    // watch files for changes and reload
    gulp.task('serve', function () {
        browserSync({
            server: {
                baseDir: paths.dist
            }
        });

        gulp.watch(paths.jade, ['jade']);
        gulp.watch(paths.ts, ['typescript']);
        gulp.watch(paths.sass, ['sass']);
    });

    gulp.task('watchSass', function () {
        gulp.watch(paths.sass, ['sass']);
    });

    gulp.task('watchTypescript', function () {
        gulp.watch(paths.ts, ['typescript']);
    });

    //Compiles jade template into html
    gulp.task('jade', function () {
        return gulp.src(paths.jadeViews)
            .pipe(jade({
                pretty: true
            }))
            .pipe(gulp.dest(paths.build))
            .pipe(reload({ stream: true }));
    });

    //Compiles typescript into js
    gulp.task('typescript', function () {
        return gulp.src(paths.ts)
            .pipe(typescript())
            .pipe(gulp.dest(paths.build + 'js/'))
            .pipe(reload({ stream: true }));
    });

    gulp.task('sass', function () {
        gulp.src(paths.sassViews)
            .pipe(sass())
            .pipe(gulp.dest(paths.build + 'css/'))
            .pipe(reload({ stream: true }));
    });

    gulp.task('img', function () {
        return gulp.src(paths.img)
            .pipe(imagemin({
                progressive: true,
                svgoPlugins: [{removeViewBox: false}],
                use: [pngquant()]
            }))
            .pipe(gulp.dest(paths.build + 'img/'));
    });

    gulp.task('moveImg', function () {
        return gulp.src(paths.img)
            .pipe(gulp.dest(paths.dist + 'img/'));
    });

    //This should be use for prod build as it bundles css/js
    gulp.task('usemin', function () {
        return gulp.src([paths.html, paths.build + 'img/*'])
            .pipe(usemin({
                css: [minifyCss(), 'concat', rev()],
                html: [minifyHtml({empty: true})],
                js: [uglify(), rev()]
            }))
            .pipe(gulp.dest(paths.dist));
    });

    gulp.task('iconFont', function () {

        var fontName    = 'lb-icons';

        gulp.src(['src/svgs/*.svg'])
            .pipe(iconfontCss({
                fontName: fontName,
                path: 'src/icons/_icons.css',
                targetPath: '../css/_icons.css',
                fontPath: '../fonts/',
                className: 'lb-icon',
                normalize: true
            }))
            .pipe(iconfont({
                fontName: fontName,
                appendCodepoints: true // recommended option
            }))
            .pipe(gulp.dest('build/fonts/'));
    });

    gulp.task('build-dev', ['jade', 'typescript', 'sass', 'img', 'serve', 'iconFont']);
    gulp.task('build-prod', ['jade', 'typescript', 'sass', 'img', 'iconFont']);

    gulp.task('build', function () {

        runSequence(
            'build-dev'
        );
    });

    gulp.task('default', ['build']);

}(require));