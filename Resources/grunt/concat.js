'use strict';

module.exports = {

  dist: {
    options: {
      sourceMap: true,
    },
    src: [
      '<%= paths.bower %>/jquery/dist/jquery.js',
      '<%= paths.bower %>/tether/dist/js/tether.js',
      '<%= paths.bower %>/bootstrap/js/dist/alert.js',
      '<%= paths.bower %>/bootstrap/js/dist/collapse.js',
      '<%= paths.bower %>/bootstrap/js/dist/dropdown.js',
      '<%= paths.bower %>/bootstrap/js/dist/modal.js',
      '<%= paths.bower %>/bootstrap/js/dist/tooltip.js',
      '<%= paths.bower %>/bootstrap/js/dist/popover.js',
      '<%= paths.bower %>/bootstrap/js/dist/tab.js',
      '<%= paths.bower %>/bootstrap/js/dist/util.js',
      '<%= paths.src %>/js/components/dynamic-content.js',
      '<%= paths.src %>/js/components/link.js',
      '<%= paths.src %>/js/main.js'
    ],
    dest: '<%= paths.temp %>/js/main.js'
  }

};
