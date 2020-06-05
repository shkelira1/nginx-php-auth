'use strict';

let gulp = require('gulp'),
    watch = require('gulp-watch'),
    prefixer = require('gulp-autoprefixer'),
    uglify = require('gulp-uglify-es').default,
    sass = require('gulp-sass'),
    sourcemaps = require('gulp-sourcemaps'),
    rigger = require('gulp-rigger'),
    cssmin = require('gulp-minify-css'),
    imagemin = require('gulp-imagemin'),
    pngquant = require('imagemin-pngquant'),
    browserSync = require("browser-sync"),
    svgstore = require('gulp-svgstore'),
    svgmin = require('gulp-svgmin'),
    rename = require('gulp-rename'),
    reload = browserSync.reload;


//project map
let path = {
    public: {
        // html: 'public/_html/',
        js: 'auth.tva94.xyz/www/js/',
        style: 'auth.tva94.xyz/www/css/',
        img: 'auth.tva94.xyz/www/img/',
        // fonts: 'public/fonts/'
    },
    src: {
        // html: 'html-src/html/*.html',
        js: 'front-end/js/*.js',
        style: 'front-end/scss/**/*.scss',
        img: 'front-end/img/*.*',
        // fonts: 'html-src/fonts/*.*'
    },
    watch: {
        // html: 'html-src/html/*.html',
        js: 'front-end/js/*.js',
        style: 'front-end/scss/**/*.scss',
        img: 'front-end/img/*.*',
        // fonts: 'html-src/fonts/*.*'
    }
};


// let config = {
//     server: {
//         baseDir: "public/"
//     },
//     host: 'localhost',
//     port: 9100
// };


//webserver
// gulp.task('webserver', function () {
//     browserSync(config);
// });


//html
// gulp.task('html:build', function () {
//     gulp.src(path.src.html)
//         .pipe(rigger())
//         .pipe(gulp.dest(path.public.html))
//         .pipe(reload({stream: true}));
// });


//js


gulp.task('js:build', function () {
    gulp.src('front-end/js/main.js')
        .pipe(rigger())
        .pipe(uglify())
        .pipe(gulp.dest('auth.tva94.xyz/www/js/'))
        .pipe(reload({stream: true}));
});


//style
gulp.task('style:build', function () {
    gulp.src(path.src.style)
        .pipe(sass())
        .pipe(prefixer('last 5 version'))
        .pipe(cssmin())
        .pipe(gulp.dest(path.public.style))
        .pipe(reload({stream: true}));
});


//img
gulp.task('image:build', function () {
    gulp.src('front-end/img/*.{webp,svg,jpg,png}')
        .pipe(imagemin({
            progressive: true,
            svgPlugins: [{removeViewBox: false }],
            use: [pngquant()],
            interlaced: true,
            optimizationLevel: 5
        }))
        .pipe(gulp.dest('auth.tva94.xyz/www/img/'))
        .pipe(reload({stream: true}));
});


//fonts
// gulp.task('fonts:build', function() {
//     gulp.src(path.src.fonts)
//         .pipe(gulp.dest(path.public.fonts))
// });


//build all components project
gulp.task('build', ['style:build', 'image:build', 'js:build']);


//watch
gulp.task('watch', function(){
    // watch([path.watch.html], function(event, cb) {
    //     gulp.start('html:build');
    // });
    watch([path.watch.style], function(event, cb) {
        gulp.start('style:build');
    });
    watch([path.watch.js], function(event, cb) {
        gulp.start('js:build');
    });
    watch([path.watch.img], function(event, cb) {
        gulp.start('image:build');
    });
    // watch([path.watch.fonts], function(event, cb) {
    //     gulp.start('fonts:build');
    // });
});


//gulp default
gulp.task('default', ['build', 'watch']);
