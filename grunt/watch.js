'use strict';

module.exports = {

  css: {
    files: ['<%= paths.src %>/scss/**/*.scss'],
    tasks: ['test-css', 'build-css']
  },

  js: {
    files: ['<%= paths.src %>/js/*.js'],
    tasks: ['test-js', 'build-js']
  },

  images: {
    files: ['<%= paths.src %>/images/**/*'],
    tasks: ['build-images']
  },

  config: {
    files: ['grunt/*.js'],
    tasks: ['build']
  }

};
