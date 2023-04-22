import gulp from 'gulp';
import concat from 'gulp-concat';
import autoprefixer from 'autoprefixer';
import debug from 'gulp-debug';
import cssnano from 'cssnano';
import sourcemaps from 'gulp-sourcemaps';
import gulpIf from 'gulp-if';
import del from 'del';
import postCss from 'gulp-postcss';
import postCssReporter from 'postcss-reporter';
import sass from 'gulp-dart-sass';
import rename from "gulp-rename";

const isProd = process.env.NODE_ENV === 'prod';

const paths = {
    distRoot: './assets/',
    distCss: './assets/css/',
    distImages: "./assets/img/",
};

export const clean = () => {
    return del(['assets/css/**',]);
};

export const img = () => {
    return gulp.src([
        "./src/img/**/*"
    ])
        .pipe(gulp.dest(paths.distImages))
};

export const css = () => {

    const plugins = [
        postCssReporter(),
        autoprefixer(),
    ];

    if (isProd) {
        plugins.push(cssnano());
    }

    return gulp.src(['src/scss/admin.scss'])
        .pipe(gulpIf(!isProd, sourcemaps.init()))
        .pipe(sass())
        .pipe(postCss(plugins))
        .on('error', console.error)
        .pipe(debug())
        .pipe(rename(function (path) {
            path.extname = ".min.css";
        }))
        .pipe(gulpIf(!isProd, sourcemaps.write('.')))
        .pipe(gulp.dest(paths.distCss))
};

export const watch = () => {
    gulp.watch(['src/scss/**/*',], {}, gulp.parallel(css));
};

export const build = gulp.series(clean, gulp.parallel(css,));

export const dev = gulp.series(build, watch);

export default gulp.series(dev);