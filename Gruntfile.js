'use strict';
module.exports = function(grunt) {

    // load all grunt tasks matching the `grunt-*` pattern
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({

        // watch for changes and trigger sass, jshint, uglify and livereload
        watch: {
            sass: {
                files: ['templates/default/scss/**/*.{scss,sass}', 'assets/admin/scss/**/*.{scss,sass}', 'assets/frontend/scss/**/*.{scss,sass}'],
                tasks: ['sass', 'autoprefixer', 'jshint', 'uglify']
            },
            js: {
                files: '<%= jshint.all %>',
                tasks: ['jshint', 'uglify']
            }
        },

        // sass
        sass: {
            dist: {

                files: {
                    'assets/admin/css/style.css': 'assets/admin/scss/style.scss',

                    /* 'assets/admin/css/metaboxes/file.css': 'assets/admin/scss/metaboxes/file.scss',
                    'assets/admin/css/metaboxes/image-select.css': 'assets/admin/scss/metaboxes/image-select.scss',
                    'assets/admin/css/metaboxes/image.css': 'assets/admin/scss/metaboxes/image.scss',
                    'assets/admin/css/metaboxes/plupload-image.css': 'assets/admin/scss/metaboxes/plupload-image.scss',
                    'assets/admin/css/metaboxes/select-advanced.css': 'assets/admin/scss/metaboxes/select-advanced.scss',
                    'assets/admin/css/metaboxes/select.css': 'assets/admin/scss/metaboxes/select.scss',
                    'assets/admin/css/metaboxes/wysiwyg.css': 'assets/admin/scss/metaboxes/wysiwyg.scss',
                    'assets/admin/css/playlist.css': 'assets/admin/scss/playlist.scss',
                    'assets/admin/css/playlist-modal.css': 'assets/admin/scss/playlist-modal.scss', */

                    'assets/admin/css/metaboxes/style.css': 'assets/admin/scss/metaboxes/style.scss',

                    'assets/frontend/css/video-js.css': 'assets/frontend/scss/video-js.scss',

                    'templates/default/css/style.css': 'templates/default/scss/style.scss',
                    'templates/default/css/grid.css': 'templates/default/scss/grid.scss',
                    'templates/default/css/font-awesome.css': 'templates/default/scss/font-awesome.scss',
                }
            }
        },

        //css sprites
        sprites: {

            sprite: {
                src: ['templates/default/img/sprites-source/*.png'],
                css: 'templates/default/scss/_sprites-source.scss',
                map: 'templates/default/img/sprite.png',
                classPrefix: 'bg',
                margin: 45
            }

        },

        // autoprefixer
        autoprefixer: {
            options: {
                browsers: ['last 2 versions', 'ie 9', 'ios 6', 'android 4'],
                map: true
            },
            files: {
                expand: true,
                flatten: true,
                cwd: 'templates/default/css/',
                src: 'templates/default/css/{,*/}*.css',
                dest: 'templates/default/css/'
            },
        },

        // javascript linting with jshint
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                "force": true
            },
            all: [
                'Gruntfile.js',
                'templates/default/js/source/*.js',
                'assets/admin/js/source/*.js',
                'assets/frontend/js/source/*.js',
            ]
        },

        // uglify to concat, minify, and make source maps
        uglify: {
            plugins: {

                files: {
                    'templates/default/js/plugins.min.js': [
                        'templates/default/js/vendor/jquery.cookie.js',
                        'templates/default/js/vendor/jquery.jcarousel.js',
                        'templates/default/js/vendor/bootstrap.tabs.js',
                        'templates/default/js/vendor/fitvid.js',
                        'templates/default/js/vendor/readmore.min.js',

                    ]
                }
            },
            main: {

                files: {
                    'templates/default/js/main.min.js': [
                        'templates/default/js/source/likes.js',
                        'templates/default/js/source/main.js'
                    ]
                }
            },

            videojs: {

                files: {
                    'assets/frontend/js/video-js.js': [
                        'bower_components/video.js/dist/video.js',
                        'bower_components/videojs-vimeo/vjs.vimeo.js',
                        'bower_components/videojs-youtube/dist/Youtube.js',
                        'assets/frontend/js/source/video-central.js'
                    ]
                }

            }, //careful here. Minified video-js doesn't work properly

            admin_js: {

                files: {
                    'assets/admin/js/import.min.js':    'assets/admin/js/source/import.js',
                    'assets/admin/js/sort.min.js':      'assets/admin/js/source/sort.js',
                    'assets/admin/js/meta.min.js':      'assets/admin/js/source/meta.js',
                    'assets/admin/js/debug-bar.min.js': 'assets/admin/js/source/debug-bar.js',
                    'assets/admin/js/mce-playlist-view.js': 'assets/admin/js/source/mce-playlist-view.js',
                    'assets/admin/js/modal-playlist-view.js': 'assets/admin/js/source/modal-playlist-view.js',
                    'assets/admin/js/playlist.js': 'assets/admin/js/source/playlist.js'
                }
            }

        },

        // image optimization
        imagemin: {
            dist: {
                options: {
                    optimizationLevel: 7,
                    progressive: true,
                    interlaced: true
                },
                files: [{
                    expand: true,
                    cwd: 'assets/frontend/images/',
                    src: ['assets/frontend/*.{png,jpg,gif}'],
                    dest: 'assets/frontend/images/'
                }, {
                    expand: true,
                    cwd: 'assets/admin/images/',
                    src: ['assets/admin/*.{png,jpg,gif}'],
                    dest: 'assets/admin/images/'
                }, {
                    expand: true,
                    cwd: 'templates/default/img/',
                    src: ['templates/default/*.{png,jpg,gif}'],
                    dest: 'templates/default/img/'
                }]
            }
        },

        checktextdomain: {
            options: {
                correct_domain: false,
                text_domain: 'video_central',
                keywords: [
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    '_n:1,2,4d',
                    '_ex:1,2c,3d',
                    '_nx:1,2,4c,5d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },
            files: {
                src: '**/*.php',
                expand: true
            }
        },

        addtextdomain: {
            options: {
                textdomain: 'video_central', // Project text domain.
            },

            target: {
                files: {
                    src: ['*.php', '**/*.php', '!node_modules/**']
                }
            }
        },

        makepot: {
            target: {
                options: {
                    domainPath: 'languages',
                    mainFile: 'video-central.php',
                    potFilename: 'video_central-en_US.po',
                    processPot: function(pot) {
                        pot.headers['report-msgid-bugs-to'] = 'frank@radiumthemes.com';
                        pot.headers['language-team'] = 'RadiumThemes <http://radiumthemes.com>';
                        pot.headers['Last-Translator'] = 'Franklin Gitonga <frank@radiumthemes.com>';
                        return pot;
                    },
                    type: 'wp-plugin'
                }
            }
        },

        devUpdate: {
          main: {
              options: {
                  updateType: 'prompt', //just report outdated packages
                  reportUpdated: false, //don't report up-to-date packages
                  semver: false, // update regardless of package.json
                  packages: {
                      devDependencies: true, //only check for devDependencies
                      dependencies: false
                  },
                  packageJson: null, //use matchdep default findup to locate package.json
                  reportOnlyPkgs: [] //use updateType action on all packages
              }
          }
      }

    });

    // register task
    grunt.registerTask('build', ['sass', 'sprites', 'autoprefixer', 'jshint', 'uglify', 'watch']);
    grunt.registerTask('build-lang', ['checktextdomain', 'makepot']);
    grunt.registerTask('update-packages', ['devUpdate']);

};
