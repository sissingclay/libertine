const { src, dest, watch, series } = require('gulp');
const browserSync = require('browser-sync');
const data = require('gulp-data');
const pug = require('gulp-pug');
const sass = require('gulp-sass');
const pngquant = require('imagemin-pngquant');
const imagemin = require('gulp-imagemin');
const minifyCss = require('gulp-minify-css');
const usemin = require('gulp-usemin');
const rev = require('gulp-rev');
const minifyHtml = require('gulp-minify-html');
const uglify = require('gulp-uglify');
const sitemap = require('gulp-sitemap');
const ts = require('gulp-typescript');
const fs = require('fs');
var path = require('path');

const paths = {
    'jadeViews': './src/jade/views/**/*.pug',
    'jade': './src/jade/**/*.pug',
    'ts': './src/ts/**/*.ts',
    'sassViews': './src/scss/views/**/*.scss',
    'sass': './src/scss/**/*.scss',
    'build': './build/',
    'html': './build/**/*.html',
    'img': './src/img/*',
    'pdf': './src/pdf/*',
    'svg': './src/svgs/*',
    'php': './src/php/*',
    'dependenciesjs': ['./bower_components/fontfaceobserver/fontfaceobserver.js', './bower_components/picturefill/dist/picturefill.min.js'],
    'dist': './build/'
};

function pugBuild() {

    var fileExist;

    return src([paths.jadeViews, '!./src/jade/view/blog.pug'])
        .pipe(data(function(file) {

            try {
                fileExist = JSON.parse(fs.readFileSync('./src/json/' + path.basename(file.path) + '.json'));
            } catch (error) {
                fileExist = null;
            }

            if (fileExist) {
                return fileExist;
            }
        }))
        // .pipe(console.log('test', fileExist))
        .pipe(pug({
            pretty: true,
            data: fileExist
        }))
        .pipe(dest(paths.dist))
        .pipe(browserSync.reload({ stream: true }));
}

//Compiles typescript into js
function typescriptFC() {
    return src(paths.ts)
        .pipe(ts())
        .pipe(dest(`${paths.dist}js/`))
        .pipe(browserSync.reload({ stream: true }));
}

function scssFC() {
    return src(paths.sassViews)
        .pipe(sass())
        .pipe(dest(paths.dist + 'css/'))
        .pipe( browserSync.reload({ stream: true }));
}

function imgFC() {
    return src(paths.img)
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(dest(paths.dist + 'img/'));
}

function moveImg() {
    return src(paths.img)
        .pipe(dest(paths.dist + 'img/'));
};

function movePdf() {
    return src(paths.pdf)
        .pipe(dest(paths.dist + 'pdf/'));

};

function useminFC() {
    return src('./build/**/*.html')
        .pipe(usemin({
            css: [minifyCss, rev],
            html: [ function () {return minifyHtml({ empty: true });} ],
            js: [uglify, rev],
            inlinejs: [ uglify ],
            inlinecss: [ minifyCss ]
        }))
        .pipe(dest(paths.dist));
}

function svgFC() {
    return src([paths.svg])
        .pipe(dest(paths.dist + 'svg/'));
};

function phpFC() {
    return src([paths.php])
        .pipe(dest(paths.dist + 'php/'));
}

function dependenciesjsFC() {
    return src(paths.dependenciesjs)
        .pipe(dest(paths.dist + 'js/'));

}

function sitemapFC() {
    return src(paths.dist + '**/*.html')
        .pipe(sitemap({
            siteUrl: 'http://www.libertineconsultants.co.za'
        }))
        .pipe(dest(paths.dist));
}

// watch files for changes and reload
function serve() {
    browserSync({
        server: {
            baseDir: paths.build
        }
    });

    watch([paths.jade]).on('change', series('pugBuild'));
    watch([paths.ts]).on('change', series('typescriptFC'));
    watch([paths.sass]).on('change', series('scssFC'));
};

exports.serve = serve;
exports.pugBuild = pugBuild;
exports.typescriptFC = typescriptFC;
exports.scssFC = scssFC;
exports.moveImg = moveImg;
exports.imgFC = imgFC;
exports.movePdf = movePdf;
exports.useminFC = useminFC;
exports.svgFC = svgFC;
exports.phpFC = phpFC;
exports.dependenciesjsFC = dependenciesjsFC;
exports.sitemapFC = sitemapFC;

exports.buildDev = series(pugBuild, typescriptFC, scssFC, imgFC, movePdf, svgFC, phpFC, dependenciesjsFC, serve);
exports.default = series(pugBuild, typescriptFC, scssFC, imgFC, movePdf, svgFC, phpFC, dependenciesjsFC, useminFC);
