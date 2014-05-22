module.exports = function(grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    phpunit: {
        classes: {
          dir: '',
        },
        options: {
          bin: 'vendor/bin/phpunit',
          color: true,
        }
    },
    watch: {
      tests: {
        files: ['src/**/*.php', 'tests/**/*Test.php'],
        tasks: ['phpunit'],
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-phpunit');

  grunt.registerTask('default', ['watch']);

};
