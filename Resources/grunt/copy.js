'use strict';

module.exports = {

  fonts: {
    files: [
      {
        expand: true,
        cwd: '<%= paths.src %>/fonts/',
        src: ['**/*'],
        dest: '<%= paths.dist %>/fonts/'
      },
      {
        expand: true,
        cwd: '<%= paths.bower %>/components-font-awesome/fonts/',
        src: ['**/*'],
        dest: '<%= paths.dist %>/fonts/'
      }
    ]
  }

};
