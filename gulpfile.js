const gulp = require("gulp");
const zip = require("gulp-zip").default || require("gulp-zip");
const del = require("del");
const browserSync = require('browser-sync').create();
const fs = require("fs");



gulp.task("clean", () => {
  return del(["bundled"]);
});

exports.bundle = () => {
  return gulp
    .src([
      "**/*",
      "!zip/**",
      "!bundled/**",
      "!node_modules/**",
      "!src/**",
      "!todo.txt",
      "!doc/**",
      "!composer.lock",
      "!.eslintrc.js",
      "!.gitignore",
      "!gulpfile.js",
      "!package.json",
      "!package-lock.json",
      "!readme.md",
      "!webpack.config copy.js",
      "!webpack.config.js",
    ])
    .pipe(gulp.dest("bundled/yt-player"));
};

exports.zip = () => {
  return gulp.src(["bundled/**"]).pipe(zip("yt-player.zip")).pipe(gulp.dest("zip"));
};



gulp.task("browser-sync", function () {
  browserSync.init({
    proxy: "localhost/freemius",
  });
  gulp.watch(["dist/*.js", "src/*.scss", "./*.php"]).on("change", () => {
    browserSync.reload();
  });
  gulp.watch(["inc/Block/*.php"]).on("change", () => {
    browserSync.reload();
  });
});
