'use strict';
module.exports = function(grunt) {

    // load all grunt tasks matching the `grunt-*` pattern
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({

        // watch for changes and trigger sass, jshint, uglify and livereload
        watch: {
            sass: {
                files: ['templates/default/scss/*.{scss,sass}', 'assets/admin/scss/*.{scss,sass}', 'assets/frontend/scss/*.{scss,sass}'],
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

                    'assets/admin/css/metaboxes/file.css': 'assets/admin/scss/metaboxes/file.scss',
                    'assets/admin/css/metaboxes/image-select.css': 'assets/admin/scss/metaboxes/image-select.scss',
                    'assets/admin/css/metaboxes/image.css': 'assets/admin/scss/metaboxes/image.scss',
                    'assets/admin/css/metaboxes/plupload-image.css': 'assets/admin/scss/metaboxes/plupload-image.scss',
                    'assets/admin/css/metaboxes/select-advanced.css': 'assets/admin/scss/metaboxes/select-advanced.scss',
                    'assets/admin/css/metaboxes/select.css': 'assets/admin/scss/metaboxes/select.scss',
                    'assets/admin/css/metaboxes/style.css': 'assets/admin/scss/metaboxes/style.scss',
                    'assets/admin/css/metaboxes/wysiwyg.css': 'assets/admin/scss/metaboxes/wysiwyg.scss',

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

            video_central: {

                files: {
                    'assets/frontend/js/video-central.min.js': [
                        'assets/frontend/js/source/video-central.js',
                    ]
                }
            },

            videojs: {

                files: {
                  	'assets/frontend/js/video-js.js': 'assets/frontend/js/vendor/video.dev.js',
                    'assets/frontend/js/video-js.plugins.min.js': [
                        'assets/frontend/js/vendor/videojs.persistvolume.js',
                        'assets/frontend/js/vendor/videojs.progressiveTips.js',
                        'assets/frontend/js/vendor/vjs.vimeo.js',
                		'assets/frontend/js/vendor/vjs.youtube.js',
                     ]
                }

            }, //careful here. Minified video-js doesn't work properly

            admin_js: {

                files: {
                  	'assets/admin/js/import.min.js': 'assets/admin/js/source/import.js',
                    'assets/admin/js/sort.min.js': 'assets/admin/js/source/sort.js',
                    'assets/admin/js/meta.min.js': 'assets/admin/js/source/meta.js',
                   	'assets/admin/js/debug-bar.min.js': 'assets/admin/js/source/debug-bar.js'
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
                },
                {
                    expand: true,
                    cwd: 'assets/admin/images/',
                    src: ['assets/admin/*.{png,jpg,gif}'],
                    dest: 'assets/admin/images/'
                },
                {
                    expand: true,
                    cwd: 'templates/default/img/',
                    src: ['templates/default/*.{png,jpg,gif}'],
                    dest: 'templates/default/img/'
                }]
            }
        }

    });

    // register task
    grunt.registerTask('build', ['sass', 'sprites', 'autoprefixer', 'jshint', 'uglify', 'watch']);

};
