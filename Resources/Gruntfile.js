'use strict';

module.exports = function (grunt) {

  var options = {

    // External configs
    pkg: grunt.file.readJSON('package.json'),

    // Paths
    paths: {
      src: '.',
      dist: 'public',
      npm: 'node_modules',
      bower: 'bower_components',
      temp: '.tmp',
    },

    // Development
    devUrl: 'localhost:8000',
    devBrowser: 'google chrome',

  };

  require('time-grunt')(grunt);

  require('load-grunt-config')(grunt, { config: options, jitGrunt: true });

  // See the `grunt/` directory for individual task configurations.
};
