'use strict';

module.exports = {

  'test-css': [
  ],

  'test-js': [
    'jshint',
    'jscs'
  ],

  test: [
    'test-css',
    'test-js'
  ],

  'build-css': [
    'sass',
    'postcss'
  ],

  'build-js': [
    'concat',
    'uglify'
  ],

  'build-images': [
    'svgmin',
    'imagemin'
  ],

  build: [
    'clean',
    'copy',
    'build-css',
    'build-js',
    'build-images'
  ],

  dev: [
    'test',
    'build',
    'watch'
  ],

  default: [
    'test',
    'build'
  ],

};
