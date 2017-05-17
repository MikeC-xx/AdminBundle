'use strict';

module.exports = {

  options: {
    processors: [
      require('pixrem')(),
      require('autoprefixer')({
        browsers: [
          'last 2 versions',
          'ie 9'
        ]
      }),
      require('cssnano')()
    ]
  },
  dist: {
    src: '<%= paths.temp %>/css/main.css',
    dest: '<%= paths.dist %>/css/main.min.css'
  }

};
