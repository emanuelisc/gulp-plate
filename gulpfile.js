/**
 * Gulpfile.
 *
 * Gulp v4 with WordPress.
 *
 * @author Emanuelis Capas
 * @version 2.0.0
 */



/*
    Dependencies
*/

// General
const { src, dest, watch, series, parallel } = require('gulp');
const debug = require('gulp-debug');
const browsersync = require('browser-sync').create();
const notify = require('gulp-notify');
const rename = require('gulp-rename');
const gulp = require('gulp');
const bump = require('gulp-wp-bump');
const sourcemaps = require('gulp-sourcemaps');
// Css related
const sasslint = require('gulp-sass-lint');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
// Js related
const lineec = require('gulp-line-ending-corrector'); // Consistent Line Endings for non UNIX systems. Gulp Plugin for Line Ending Corrector (A utility that makes sure your files have consistent line endings)
const uglify = require('gulp-uglify'); // Minifies JS files
// Deployment related
var ftp = require('vinyl-ftp');
var gutil = require('gulp-util');
// Image realted plugins.
var imagemin = require('gulp-imagemin'); // Minify PNG, JPEG, GIF and SVG images with imagemin.

/*
    Global project variables
*/
var project = 'Gulp Plate'; // Project Name.
var projectURL = 'http://plate.local'; // Local project URL of your already running WordPress site. Could be something like local.dev or localhost:8888.
var localDomain = 'localhost:3000';

var cssSrc = './src/css/*.scss'; // Path to .scss files.
var jsSrc = './src/js/*.js'; // Path to .js files.

var cssDest = './assets/css/'; // Path to place the compiled css files.
var jsDest = './assets/js/'; // Path to place the compiled js files.

var bumpFile = './assets.php'; // Script and styles enque file, need to be separate to avoid infinite loop while bumping
var phpFiles = ['./**/*.php', '!' + bumpFile]; // Path to all PHP files, ignore Script and styles enque file to avoid infinite loop while bumping

var timeOffset = 180;

/*
    Deployment variabes
*/

var dev_deployment_location = '/site/wwwroot/wp-content/themes/pigeon/';
var prod_deployment_location = '/site/wwwroot/wp-content/themes/pigeon/';


var deployConfigFile = './deploy.json';



// Init BrowserSync.
function browserSync(cb) {
    browsersync.init({
        proxy: projectURL,
        socket: {
            domain: localDomain
        }
    });
    cb();
}

/*
    Styles functions
*/

// Watch Sass files
function watchScss() {
    gulp.watch(cssSrc).on("change", function(scssFile) {
        scssFile = scssFile.replace(/\\/g, "/"); // change backslash to forwardslash for src to recognize file name and it's dir
        processScss(scssFile) // Pass individual changed file to scss pipeline
    });
}

// Compile CSS from Sass.
function processScss(scssfile) {
    return src(scssfile)
        // .pipe(sasslint({
        //   configFile: '.sass-lint.yml'
        // }))
        // .pipe(sasslint.format())
        // .pipe(sasslint.failOnError())
        .pipe(sourcemaps.init()) // Initiate sourcemaps
        .pipe(sass({ outputStyle: 'compressed' })) // Convert scss to css and minify
        .pipe(autoprefixer(['last 15 versions', '> 1%', 'ie 8', 'ie 7'])) // Add prefixes for previous versions
        .pipe(rename({ suffix: '.min' })) // Add .min suffix to the file name
        .pipe(debug())
        .pipe(bump(bumpFile)) // Bump css file version in enque php file
        .pipe(notify("Css updated")) // Notify about completed process
        .pipe(sourcemaps.write('/maps')) // Write maps
        .pipe(dest(cssDest)) // Write new css files
        .pipe(browsersync.reload({ stream: true })) // Stream css updates to browsers
}

// Run through all scss files to create minified files, and bump all versions
const allScss = function(cb) {
    return src(cssSrc)
        // .pipe(sasslint({
        //   configFile: '.sass-lint.yml'
        // }))
        // .pipe(sasslint.format())
        // .pipe(sasslint.failOnError())
        .pipe(sourcemaps.init())
        .pipe(sass({ outputStyle: 'compressed' }))
        .pipe(autoprefixer(['last 15 versions', '> 1%', 'ie 8', 'ie 7']))
        .pipe(rename({ suffix: '.min' }))
        .pipe(bump(bumpFile)) // Change css version in assets.php
        .pipe(sourcemaps.write('/maps'))
        .pipe(dest(cssDest));
    notify("All scss processed!");
    cb();
};



/*
    Scripts functions
*/

function watchJs() {
    gulp.watch(jsSrc).on("change", function(jsFile) {
        jsFile = jsFile.replace(/\\/g, "/"); // change backslash to forwardslash for src to recognize file name and it's dir
        processJs(jsFile) // Pass individual changed file to js pipeline
    });
}

function processJs(jsFile) {
    return src(jsFile)
        .pipe(sourcemaps.init())
        .pipe(lineec()) // Consistent Line Endings for non UNIX systems.
        .pipe(uglify()) // Minify js file
        .pipe(lineec()) // Consistent Line Endings for non UNIX systems.
        .pipe(rename({ suffix: '.min' }))
        .pipe(bump(bumpFile)) // Change css version in assets.php
        .pipe(notify({ message: 'JS updated! 🚀', onLast: true }))
        .pipe(sourcemaps.write('/maps'))
        .pipe(gulp.dest(jsDest))
        .pipe(browsersync.reload({ stream: true }))
}

// Run through all js files to create minified files, and bump all versions
const allJs = function(cb) {
    return src(jsSrc)
        .pipe(sourcemaps.init())
        .pipe(lineec()) // Consistent Line Endings for non UNIX systems.
        .pipe(uglify()) // Minify js file
        .pipe(lineec()) // Consistent Line Endings for non UNIX systems.
        .pipe(rename({ suffix: '.min' }))
        .pipe(bump(bumpFile)) // Change css version in assets.php
        .pipe(sourcemaps.write('/maps'))
        .pipe(gulp.dest(jsDest));
    notify("All js processed!");
    cb();
}


/*
    Php functions
*/

function watchPHP() {
    watch(
        phpFiles, { events: 'all', ignoreInitial: true },
        series(reload)
    );
}

function reload(cb) {
    browsersync.reload();
    cb();
}


/*
    DEPLOYMENT
*/


const deploy_development = function(cb) {
    var deployConfig = require(deployConfigFile);

    var args = process.argv.slice(3);
    var targetEnv = (args.length && args[0] == '--env') ? args[1] : 'development';
    var envOptions;

    // Check that environment options exists
    if (!deployConfig.env.hasOwnProperty(targetEnv)) {
        console.error(
            'No config for "%s1" environment.\nAvailable environments are: %a1.'
            .replace('%s1', targetEnv)
            .replace('%a1', Object.keys(deployConfig.env))
        );

        return;
    }

    envOptions = Object.assign({}, deployConfig.env[targetEnv], deployConfig.global);

    var conn = ftp.create({
        host: envOptions.ftpConfig.host,
        user: envOptions.ftpConfig.user,
        password: envOptions.ftpConfig.password,
        parallel: 1,
        log: gutil.log,
        debug: true,
        timeOffset: timeOffset,
        idleTimeout: 1000,
    });

    var globs = [
        './**',
        '!desktop.ini',
        '!node_modules/**',
        '!node_modules',
        '!gulpfile.js',
        '!gitignore.txt',
        '!assets/img/raw/**',
        '!assets/img/raw',
        '!package.json',
        '!deploy.json',
        '!package-lock.json',
        '!README.md'
    ];

    return gulp.src(globs, { base: '.', buffer: true })
        .pipe(conn.newer(dev_deployment_location)) // only upload newer files
        .pipe(conn.dest(dev_deployment_location)) // deploy
        .pipe(notify({ message: 'Files uploaded to Newslot! 🚀', onLast: true }))

    cb();
}

const clean_development = function(cb) {
    var deployConfig = require(deployConfigFile);

    var args = process.argv.slice(3);
    var targetEnv = (args.length && args[0] == '--env') ? args[1] : 'development';
    var envOptions;

    // Check that environment options exists
    if (!deployConfig.env.hasOwnProperty(targetEnv)) {
        console.error(
            'No config for "%s1" environment.\nAvailable environments are: %a1.'
            .replace('%s1', targetEnv)
            .replace('%a1', Object.keys(deployConfig.env))
        );

        return;
    }

    envOptions = Object.assign({}, deployConfig.env[targetEnv], deployConfig.global);

    var conn = ftp.create({
        host: envOptions.ftpConfig.host,
        user: envOptions.ftpConfig.user,
        password: envOptions.ftpConfig.password,
        parallel: 1,
        log: gutil.log,
        debug: true,
        timeOffset: timeOffset,
        idleTimeout: 1000,
    });

    return conn.clean(dev_deployment_location + '**', './', { base: dev_deployment_location })

    cb();
}

const deploy_production = function(cb) {
    var deployConfig = require(deployConfigFile);

    var args = process.argv.slice(3);
    var targetEnv = (args.length && args[0] == '--env') ? args[1] : 'production';
    var envOptions;

    // Check that environment options exists
    if (!deployConfig.env.hasOwnProperty(targetEnv)) {
        console.error(
            'No config for "%s1" environment.\nAvailable environments are: %a1.'
            .replace('%s1', targetEnv)
            .replace('%a1', Object.keys(deployConfig.env))
        );

        return;
    }

    envOptions = Object.assign({}, deployConfig.env[targetEnv], deployConfig.global);

    var conn = ftp.create({
        host: envOptions.ftpConfig.host,
        user: envOptions.ftpConfig.user,
        password: envOptions.ftpConfig.password,
        parallel: 1,
        log: gutil.log,
        debug: true,
        timeOffset: timeOffset,
        idleTimeout: 1000,
    });

    var globs = [
        './**',
        '!desktop.ini',
        '!node_modules/**',
        '!node_modules',
        '!gulpfile.js',
        '!gitignore.txt',
        '!src/**',
        '!src',
        '!assets/img/raw/**',
        '!assets/img/raw',
        '!assets/css/*.map',
        '!assets/js/*.map',
        '!package.json',
        '!deploy.json',
        '!package-lock.json',
        '!README.md',
        '!lib/**',
        '!temp-uploads/**',
    ];

    return gulp.src(globs, { base: '.', buffer: true })
        .pipe(conn.newer(prod_deployment_location)) // only upload newer files
        .pipe(conn.dest(prod_deployment_location)) // deploy
        .pipe(notify({ message: 'Files uploaded to Production! 🚀', onLast: true }))

    cb();
}

const clean_production = function(cb) {
    var deployConfig = require(deployConfigFile);

    var args = process.argv.slice(3);
    var targetEnv = (args.length && args[0] == '--env') ? args[1] : 'production';
    var envOptions;

    // Check that environment options exists
    if (!deployConfig.env.hasOwnProperty(targetEnv)) {
        console.error(
            'No config for "%s1" environment.\nAvailable environments are: %a1.'
            .replace('%s1', targetEnv)
            .replace('%a1', Object.keys(deployConfig.env))
        );

        return;
    }

    envOptions = Object.assign({}, deployConfig.env[targetEnv], deployConfig.global);

    var conn = ftp.create({
        host: envOptions.ftpConfig.host,
        user: envOptions.ftpConfig.user,
        password: envOptions.ftpConfig.password,
        parallel: 1,
        log: gutil.log,
        debug: true,
        timeOffset: timeOffset,
        idleTimeout: 1000,
    });

    return conn.clean(prod_deployment_location + '**', './', { base: prod_deployment_location })

    cb();
}


// Export commands.
exports.default = parallel(browserSync, watchScss, watchJs, watchPHP);
exports.all = series(allScss, allJs); // when initializing project run through all scss and .js files and create minified versions and maps.
exports.deploy_dev = series(deploy_development, clean_development);
exports.deploy_prod = series(deploy_production, clean_production);



/**
 * Task: `images`.
 *
 * Minifies PNG, JPEG, GIF and SVG images.
 *
 * This task does the following:
 *     1. Gets the source of images raw folder
 *     2. Minifies PNG, JPEG, GIF and SVG images
 *     3. Generates and saves the optimized images
 *
 * This task will run only once, if you want to run it
 * again, do it with the command `gulp images`.
 */
// gulp.task( 'images', function() {
//  gulp.src( imagesSRC )
//    .pipe( imagemin( {
//          progressive: true,
//          optimizationLevel: 3, // 0-7 low-high
//          interlaced: true,
//          svgoPlugins: [{removeViewBox: false}]
//        } ) )
//    .pipe(gulp.dest( imagesDestination ))
//    .pipe( notify( { message: 'TASK: "images" Completed! 🚀', onLast: true } ) );
// });