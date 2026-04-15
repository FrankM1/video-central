module.exports = function(grunt) {
    'use strict';

    require('jit-grunt')(grunt, {
        usebanner: 'grunt-banner',
        replace: 'grunt-text-replace',
        postcss: '@lodder/grunt-postcss'
    });

    grunt.loadNpmTasks('grunt-wp-i18n');

    // require it at the top and pass in the grunt instance
	require('time-grunt')(grunt);

    var pkgInfo = grunt.file.readJSON( 'package.json' );

    grunt.initConfig({
        pkg: grunt.file.readJSON( 'package.json' ),

        // watch for changes and trigger sass, jshint, uglify and livereload
        watch: {
            sass: {
                files: ['templates/default/scss/**/*.{scss,sass}', 'assets/admin/scss/**/*.{scss,sass}', 'assets/frontend/scss/**/*.{scss,sass}'],
                tasks: ['sass', 'postcss', 'eslint', 'uglify']
            },
            scripts: {
                files: [
                    'assets/admin/js/source/**/*.js'
                ],
                tasks: [ 'scripts' ]
            }
        },

        // sass (Dart Sass via grunt-sass)
        sass: {
            options: {
                implementation: require( 'sass' ),
                sourceMap: false
            },
            dist: {
                files: {
                    'assets/admin/css/style.css': 'assets/admin/scss/style.scss',
                    'assets/admin/css/playlist.css': 'assets/admin/scss/playlist.scss',
                    'assets/admin/css/metaboxes/style.css': 'assets/admin/scss/metaboxes/style.scss',
                    'assets/frontend/css/video-js.css': 'assets/frontend/scss/video-js.scss',
                    'templates/default/css/style.css': 'templates/default/scss/style.scss',
                    'templates/default/css/grid.css': 'templates/default/scss/grid.scss',
                    'templates/default/css/font-awesome.css': 'templates/default/scss/font-awesome.scss'
                }
            }
        },

        postcss: {
			dev: {
				options: {
					map: true,

					processors: [
						require( 'autoprefixer' )( {
							overrideBrowserslist: [ 'last 5 versions' ]
						} )
					]
				},
				files: [ {
					src: [
						'templates/default/css/*.css',
						'!templates/default/css/*.min.css'
					]
				} ]
			},
			minify: {
				options: {
					processors: [
						require( 'autoprefixer' )( {
							overrideBrowserslist: [ 'last 5 versions' ]
						} ),
						require( 'cssnano' )( {
							preset: [ 'default', { reduceIdents: false } ]
						} )
					]
				},
				files: [ {
					expand: true,
					src: [
						'templates/default/css/*.css',
						'!templates/default/css/*.min.css'
					],
					ext: '.min.css'
				} ]
			}
        },

        browserify: {
            options: {
                browserifyOptions: {
                    debug: true
                }
            },

            dist: {
                files: {
                    'assets/admin/js/video-central.js': [
                        'assets/admin/js/source/playlist/editor.js'
                    ],
                    'assets/admin/js/playlist-edit.js': [
                        'assets/admin/js/source/playlist/playlist-edit.js'
                    ]
                },
                options: pkgInfo.browserify
            }

        },

        // javascript linting with eslint
        eslint: {
            options: {
                overrideConfigFile: 'eslint.config.mjs',
                quiet: true
            },
            target: [
                'Gruntfile.js',
                'templates/default/js/source/*.js',
                'assets/admin/js/source/*.js',
                'assets/frontend/js/source/*.js'
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
                        'templates/default/js/vendor/readmore.min.js'
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
                    'assets/admin/js/import.min.js'         : 'assets/admin/js/source/import.js',
                    'assets/admin/js/meta.min.js'           : 'assets/admin/js/source/meta.js',
                    'assets/admin/js/debug-bar.min.js'      : 'assets/admin/js/source/debug-bar.js',
                    'assets/admin/js/mce-playlist-view.js'  : 'assets/admin/js/source/playlist/mce-playlist-view.js',
                    'assets/admin/js/modal-playlist-view.js': 'assets/admin/js/source/playlist/modal-playlist-view.js',
                }
            }

        },

        addtextdomain: {
            options: {
                textdomain: 'video_central',
                updateDomains: true
            },
            target: {
                files: {
                    src: [
                        '*.php',
                        '**/*.php',
						'!node_modules/**',
						'!build/**',
						'!tests/**',
						'!.github/**',
						'!vendor/**',
						'!*~'
                    ]
                }
            }
		},

        makepot: {
            target: {
                options: {
                    domainPath: 'languages',
                    mainFile: 'video-central.php',
                    potFilename: 'en_US.po',
                    processPot: function(pot) {
                        pot.headers['report-msgid-bugs-to'] = 'frank@radiumthemes.com';
                        pot.headers['language-team'] = 'RadiumThemes <http://radiumthemes.com>';
                        pot.headers['Last-Translator'] = 'Franklin Gitonga <frank@radiumthemes.com>';
                        return pot;
                    },
                    updateTimestamp: true,
                    type: 'wp-plugin'
                }
            }
        }

    });

    grunt.registerTask( 'i18n', [
        'addtextdomain',
        'makepot'
    ] );

    grunt.registerTask( 'scripts', [
        'eslint',
        'browserify',
        'uglify'
    ] );

    grunt.registerTask( 'styles', [
        'sass',
        'postcss'
    ] );

    // register task
    grunt.registerTask( 'default', [
        'i18n',
        'styles',
        'scripts',
        'watch'
    ] );

};
