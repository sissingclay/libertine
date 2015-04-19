/**
 * Created by Clay on 19/04/15.
 */
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
    pngquant    = require('imagemin-pngquant');

var paths       = {
    'jadeTemplates': './src/jade/**/*.jade',
    'ts': './src/ts/**/*.ts',
    'sass': './src/scss/**/*.scss',
    'dev': './build/dev/',
    'prod': './build/prod/',
    'html': './build/dev/**/*.html',
    'img': './src/img/*'
};


// watch files for changes and reload
gulp.task('serve', function () {
    browserSync({
        server: {
            baseDir: paths.dev
        }
    });

    gulp.watch(paths.jadeTemplates, ['jade']);
    gulp.watch(paths.ts, ['typescript']);
    gulp.watch(paths.scss, ['sass']);
});

//Compiles jade template into html
gulp.task('jade', function () {
    return gulp.src(paths.jadeTemplates)
        .pipe(jade({
            pretty: true
        }))
        .pipe(gulp.dest(paths.dev))
        .pipe(reload({ stream: true }));
});

//Compiles typescript into js
gulp.task('typescript', function () {
    return gulp.src(paths.ts)
        .pipe(typescript())
        .pipe(gulp.dest(paths.dev + 'js/'))
        .pipe(reload({ stream: true }));
});

gulp.task('sass', function () {
    gulp.src(paths.sass)
        .pipe(sass())
        .pipe(gulp.dest(paths.dev + 'css/'))
        .pipe(reload({ stream: true }));
});

gulp.task('img', function () {
    return gulp.src(paths.img)
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest(paths.dev + 'img/'));
});


//This should be use for prod build as it bundles css/js
gulp.task('usemin', function () {
    return gulp.src(paths.html)
        .pipe(usemin({
            css: [minifyCss(), 'concat', rev()],
            html: [minifyHtml({empty: true})],
            js: [uglify(), rev()]
        }))
        .pipe(gulp.dest(paths.prod));
});

gulp.task('build-dev', ['jade', 'typescript', 'sass', 'img', 'serve']);

gulp.task('default', function () {
    // place code for your default task here
});