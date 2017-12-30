module.exports = function(grunt) {
    'use strict';

    require('jit-grunt')(grunt, {
        usebanner: 'grunt-banner',
        replace: 'grunt-text-replace'
    });

    // require it at the top and pass in the grunt instance
	require('time-grunt')(grunt);

    var remapify = require( 'remapify' ),
        pkgInfo = grunt.file.readJSON( 'package.json' );

    grunt.initConfig({
        pkg: grunt.file.readJSON( 'package.json' ),

        // watch for changes and trigger sass, jshint, uglify and livereload
        watch: {
            sass: {
                files: ['templates/default/scss/**/*.{scss,sass}', 'assets/admin/scss/**/*.{scss,sass}', 'assets/frontend/scss/**/*.{scss,sass}'],
                tasks: ['sass', 'postcss', 'jshint', 'uglify']
            },
            scripts: {
                files: [
                    'assets/admin/js/source/**/*.js'
                ],
                tasks: [ 'scripts' ]
            }
        },

        // sass
        sass: {
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

        postcss: {
			dev: {
				options: {
					map: true,

					processors: [
						require( 'autoprefixer' )( {
							browsers: 'last 5 versions'
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
							browsers: 'last 5 versions'
						} ),
						require( 'cssnano' )( {
							reduceIdents: false
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
                },
                preBundleCB: function( bundle ) {
                    bundle.plugin( remapify, [
                        {
							cwd: 'assets/admin/js/source/playlist',
							src: '**/*.js',
							expose: 'video-central-playlist'
						}
                    ] );
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

        // Extract sourcemap to separate file
        exorcise: {
            bundle: {
                options: {},
                files: {
                    'templates/default/js/main.js.map': [ 'templates/default/js/main.js' ]
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
                correct_domain: true,
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
            files: [ {
                src: [
                    '**/*.php',
                    '!node_modules/**',
                    '!build/**',
                    '!tests/**',
                    '!.github/**',
                    '!vendor/**',
                    '!*~'
                ],
                expand: true
            } ]
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

    grunt.registerTask( 'i18n', [
        'checktextdomain',
        'makepot' 
    ] );

    grunt.registerTask( 'scripts', [
        'jshint',
        'browserify',
        //'exorcise',
        'uglify'
    ] );

    grunt.registerTask( 'styles', [
        'sass',
        'postcss'
        //'sprites'
    ] );

    // register task
    grunt.registerTask( 'default', [ 
        'i18n',
        'styles',
        'scripts',
        'watch'
    ] );

    grunt.registerTask( 'update-packages', ['devUpdate']);

};
