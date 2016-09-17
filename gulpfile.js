var gulp = require('gulp');
var plumber = require('gulp-plumber');
var uglify = require("gulp-uglify");
var browserify = require('browserify');
var babelify = require('babelify');
var source = require('vinyl-source-stream');
var rename = require('gulp-rename');

gulp.task('list-dev', function() {
    return browserify('./jsx/list.jsx', {debug: true})
        .transform(babelify, {presets: ['es2015', 'react']})
        .bundle()
        .on("error", function (err) {
            console.log("Error : " + err.message);
        })
        .pipe(source('list.js'))
        .pipe(gulp.dest('./dest/'))
        .on('end', function () {
            return gulp.src(["./dest/list.js"])
                .pipe(plumber())
                .pipe(uglify({mangle: false}))
                .pipe(rename({
                    extname: '.min.js'
                }))
                .pipe(gulp.dest("./public/js/"))
        })
});

gulp.task('rsvp-dev', function() {
    return browserify('./jsx/rsvp.jsx', {debug: true})
        .transform(babelify, {presets: ['es2015', 'react']})
        .bundle()
        .on("error", function (err) {
            console.log("Error : " + err.message);
        })
        .pipe(source('rsvp.js'))
        .pipe(gulp.dest('./dest/'))
        .on('end', function () {
            return gulp.src(["./dest/rsvp.js"])
                .pipe(plumber())
                .pipe(uglify({mangle: false}))
                .pipe(rename({
                    extname: '.min.js'
                }))
                .pipe(gulp.dest("./public/js/"))
        })
});

gulp.task("default", ['list-dev', 'rsvp-dev']);
