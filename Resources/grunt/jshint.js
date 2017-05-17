'use strict';

module.exports = {

  options: {
    jshintrc: true,
  },
  grunt: ['Gruntfile.js', 'grunt/*.js'],
  app: ['<%= paths.src %>/js/**/*.js']

};
