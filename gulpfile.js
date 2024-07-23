
"use strict";

const gulp = require('gulp');
const twig = require('gulp-twig');
const data = require('gulp-data');
const fs = require('fs');

// Compile Twig templates to HTML
gulp.task('templates', function() {
    return gulp.src(['./templates/**/*.twig', '!templates/_*.twig', '!templates/base.html.twig'], { dot: true })
        .pipe(data(function(file) {
            return JSON.parse(fs.readFileSync('./data/set.json'));
        }))
        .pipe(twig({
            errorLogToConsole: true,
            extname: false,
            filters: [
                {
                    name: "mb_split",
                    func: function (args) {
                        return [...args];
                    }
                },
                {
                    name: "u",
                    func: function (args) {
                        return args.codePointAt(0).toString(16).padStart(4, '0');
                    }
                }
            ]
        }))
        .pipe(gulp.dest('./')); // output the rendered files to the "dist" directory
});
