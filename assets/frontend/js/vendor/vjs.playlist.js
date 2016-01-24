(function() {

    window.videojs.plugin('playlist', function(options) {

        var id = this.el().id;

        //assign variables
        var tracks = document.querySelectorAll("#" + id + "-vjs-playlist .vjs-track"),
            trackCount = tracks.length,
            player = this,
            currentTrack = tracks[0],
            index = 0,
            play = true,
            onTrackSelected = options.onTrackSelected;

        //manually selecting track
        for (var i = 0; i < trackCount; i++) {

            tracks[i].onclick = function() {
                trackSelect(this);
            };

        }

        // for continuous play
        if (typeof options.continuous === 'undefined' || options.continuous === true) {

            player.on("ended", function() {

                index++;
                if (index >= trackCount) {
                    index = 0;
                }
                tracks[index].click();

            }); // on ended
        }

        //track select function for onended and manual selecting tracks
        var trackSelect = function(track) {

            //get new src
            var src = track.getAttribute('data-src');
            index = parseInt(track.getAttribute('data-index')) || index;

            if (player.techName === 'youtube') {
                player.src([{
                    type: "video/youtube",
                    src: src
                }]);
            } else {

                if (player.el().firstChild.tagName === "AUDIO" || (typeof options.mediaType !== 'undefined' && options.mediaType === "audio")) {

                    player.src([{
                            type: "audio/mp4",
                            src: src + ".m4a"
                        }, {
                            type: "audio/webm",
                            src: src + ".webm"
                        }, {
                            type: "video/youtube",
                            src: src
                        }, {
                            type: "audio/ogg",
                            src: src + ".ogg"
                        }
                    ]);
                } else {
                    //console.log("video");
                    player.src([{
                            type: "video/mp4",
                            src: src + ".mp4"
                        }, {
                            type: "video/youtube",
                            src: src
                        }, {
                            type: "video/webm",
                            src: src + ".webm"
                        }
                    ]);
                }
            }

            if (play)  {
                player.play();
            }

            //remove 'currentTrack' CSS class
            for (var i = 0; i < trackCount; i++) {
                if (tracks[i].className.indexOf('currentTrack') !== -1) {
                    tracks[i].className = tracks[i].className.replace(/\bcurrentTrack\b/, 'nonPlayingTrack');
                }
            }
            //add 'currentTrack' CSS class
            track.className = track.className + " currentTrack";
            if (typeof onTrackSelected === 'function') {
                onTrackSelected.apply(track);
            }

        };

        //if want to start at track other than 1st track
        if (typeof options.setTrack !== 'undefined') {
            options.setTrack = parseInt(options.setTrack);
            currentTrack = tracks[options.setTrack];
            index = options.setTrack;
            play = false;
            //console.log('options.setTrack index'+index);
            trackSelect(tracks[index]);
            play = true;
        }
        if (window.location.hash) {
            var hash = window.location.hash.substring(9);
            play = false;
            trackSelect(tracks[hash]);
        }

        var data = {
            tracks: tracks,
            trackCount: trackCount,
            play: function() {
                return play;
            },
            index: function() {
                return index;
            },
            prev: function() {

                var j = index - 1;

                if (j < 0 || j > trackCount) {
                    j = 0;
                }

                trackSelect(tracks[j]);
            },
            next: function() {

                var j = index + 1;

                if (j < 0 || j > trackCount) {
                    j = 0;
                }

                trackSelect(tracks[j]);
            }
        };
        return data;
    });

})();
