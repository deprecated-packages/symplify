// install: npm install
// run: gulp

var gulp = require('gulp');
var watch = require('gulp-watch');
var exec = require('child_process').exec;
var log = require('gulplog');

gulp.task('default', function () {
    // Generate current version
    log.info('Generating...');
    exec('vendor/bin/statie generate source', function (err, stdout, stderr) {
        stdout ? log.info(stdout.trim()) : null;
        stderr ? log.error("Error:\n" + stderr.trim()) : null;

        // Run local server, open localhost:8000 in your browser
        // needs to listen on 0.0.0.0 to work in Docker on windows
        exec('php -S 0.0.0.0:8000 -t output');
        log.info('Local PHP server started at "http://localhost:8000", open browser to see it.');
    });

    // For the second arg see: https://github.com/floatdrop/gulp-watch/issues/242#issuecomment-230209702
    return watch(['source/**/*', '!**/*___jb_tmp___'], function () {
        log.info('Regenerating...');
        exec('vendor/bin/statie generate source', function (err, stdout, stderr) {
            stdout ? log.info(stdout.trim()) : null;
            stderr ? log.error("Error:\n" + stderr.trim()) : null;
        });
    });
});
