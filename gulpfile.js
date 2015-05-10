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

        knownOptions = {
            string: 'env',
            default: {
                env: process.env.NODE_ENV || 'prod'
            }
        },

        options     = minimist(process.argv.slice(2), knownOptions),

        paths       = {
            'jadeTemplates': './src/jade/**/*.jade',
            'ts': './src/ts/**/*.ts',
            'sass': './src/scss/**/*.scss',
            'build': './build/',
            'html': './build/**/*.html',
            'img': './src/img/*',
            'dist': './build/'
        };

    if (options.env === 'prod') {
        paths.dist  = './dist/';
        paths.img   = paths.build + 'img/*';
    }

    // watch files for changes and reload
    gulp.task('serve', function () {
        browserSync({
            server: {
                baseDir: paths.dist
            }
        });

        gulp.watch(paths.jadeTemplates, ['jade']);
        gulp.watch(paths.ts, ['typescript']);
        gulp.watch(paths.scss, ['sass']);
    });

    gulp.task('watchSass', function () {
        gulp.watch(paths.scss, ['sass']);
    });

    //Compiles jade template into html
    gulp.task('jade', function () {
        return gulp.src(paths.jadeTemplates)
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
        gulp.src(paths.sass)
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

    gulp.task('removeBuild', function () {
        del([paths.build], function (err, deletedFiles) {
            console.log('Files deleted:', deletedFiles.join(', '));
        });
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

    gulp.task('build-dev', ['jade', 'typescript', 'sass', 'img', 'serve']);

    gulp.task('build', function () {

        if (options.env === 'prod') {
            return runSequence(
                'build-dev',
                'usemin',
                'moveImg',
                'removeBuild'
            );
        }

        runSequence(
            'build-dev'
        );
    });

    gulp.task('default', ['build']);

}(require));