/**
 * Created by Clay on 19/04/15.
 */
(function (require) {

    'use strict';

    var gulp            = require('gulp'),
        browserSync     = require('browser-sync'),
        reload          = browserSync.reload,
        jade            = require('gulp-jade'),
        typescript      = require('gulp-tsc'),
        usemin          = require('gulp-usemin'),
        uglify          = require('gulp-uglify'),
        minifyHtml      = require('gulp-minify-html'),
        minifyCss       = require('gulp-minify-css'),
        rev             = require('gulp-rev'),
        sass            = require('gulp-sass'),
        imagemin        = require('gulp-imagemin'),
        pngquant        = require('imagemin-pngquant'),
        sourcemaps      = require('gulp-sourcemaps'),
        runSequence     = require('run-sequence'),
        minimist        = require('minimist'),
        del             = require('del'),
        svgmin          = require('gulp-svgmin'),
        sitemap         = require('gulp-sitemap'),
        data            = require('gulp-data'),
        path            = require('path'),
        gulpif          = require('gulp-if'),

        knownOptions    = {
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
            'html': './build/test/**/*.html',
            'img': './src/img/*',
            'svg': './src/svgs/*',
            'php': './src/php/*',
            'dependenciesjs': './bower_components/fontfaceobserver/fontfaceobserver.js',
            'dist': './build/test/'
        };

    // watch files for changes and reload
    gulp.task('serve', function () {
        browserSync({
            server: {
                baseDir: paths.build
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

        var fileExist;

        return gulp.src(paths.jadeViews)
            .pipe(data(function(file) {

                try {
                    fileExist = require('./src/json/' + path.basename(file.path) + '.json');
                } catch (error) {
                    fileExist = null;
                }

                if (fileExist) {
                    return fileExist;
                }
            }))
            .pipe(jade({
                pretty: true
            }))
            .pipe(gulp.dest(paths.dist))
            .pipe(reload({ stream: true }));
    });

    //Compiles typescript into js
    gulp.task('typescript', function () {
        return gulp.src(paths.ts)
            .pipe(typescript())
            .pipe(gulp.dest(paths.dist + 'js/'))
            .pipe(reload({ stream: true }));
    });

    gulp.task('sass', function () {
        gulp.src(paths.sassViews)
            .pipe(sass())
            .pipe(gulp.dest(paths.dist + 'css/'))
            .pipe(reload({ stream: true }));
    });

    gulp.task('img', function () {
        return gulp.src(paths.img)
            .pipe(imagemin({
                progressive: true,
                svgoPlugins: [{removeViewBox: false}],
                use: [pngquant()]
            }))
            .pipe(gulp.dest(paths.dist + 'img/'));
    });

    gulp.task('moveImg', function () {
        return gulp.src(paths.img)
            .pipe(gulp.dest(paths.dist + 'img/'));
    });

    //This should be use for prod build as it bundles css/js
    gulp.task('usemin', function () {
        return gulp.src('~/clone/build/test/**/*.html')
            .pipe(usemin({
                css: [minifyCss, rev],
                html: [ function () {return minifyHtml({ empty: true });} ],
                js: [uglify, rev],
                inlinejs: [ uglify ],
                inlinecss: [ minifyCss ]
            }))
            .pipe(gulp.dest(paths.dist));
    });

    gulp.task('svg', function () {
        return gulp.src([paths.svg])
            //.pipe(svgmin())
            .pipe(gulp.dest(paths.dist + 'svg/'));
    });

    gulp.task('php', function () {
        return gulp.src([paths.php])
            .pipe(gulp.dest(paths.dist + 'php/'));
    });

    gulp.task('dependenciesjs', function () {
        return gulp.src([paths.dependenciesjs])
            .pipe(gulp.dest(paths.dist + 'js/'));
    });

    gulp.task('sitemap', function () {
        gulp.src(paths.dist + '**/*.html')
            .pipe(sitemap({
                siteUrl: 'http://www.libertineconsultants.co.za'
            }))
            .pipe(gulp.dest(paths.dist));
    });

    gulp.task('build-dev', function () {

        runSequence(
            ['jade', 'typescript', 'sass', 'img', 'serve', 'svg', 'php', 'dependenciesjs']
        );
    });

    gulp.task('build-prod', function () {

        runSequence(
            'jade',
            'typescript',
            'sass',
            'img',
            'svg',
            'usemin',
            'sitemap',
            'php',
            'dependenciesjs'
        );
    });

    gulp.task('build', function () {

        runSequence(
            'build-dev'
        );
    });

    gulp.task('default', ['build']);

}(require));