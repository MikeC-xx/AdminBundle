'use strict';

module.exports = {

  options: {
    sourceMap: true,
    includePaths: ['<%= paths.bower %>']
  },
  dist: {
    files: {
      '<%= paths.temp %>/css/main.css': '<%= paths.src %>/scss/main.scss'
    }
  }

};
