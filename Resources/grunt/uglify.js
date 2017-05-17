'use strict';

module.exports = {

  options: {
    sourceMap: true,
    sourceMapIn: '<%= paths.temp %>/js/main.js.map'
  },

  dist: {
    files: {
      '<%= paths.dist %>/js/main.min.js': '<%= paths.temp %>/js/main.js'
    }
  }

};
