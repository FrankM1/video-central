/**
 * Video import form functionality
 *
 * @version 1.0.0
 */
(function($) {

    window.video_central_importMessages = {
        loading: 'Importing, please wait...',
        wait: 'Not done yet, still importing. You\'ll have to wait a bit longer.',
        importing: 'Importing',
        done: 'Import Complete'
    };

    $(document).ready(function() {

        window.Video_Central_Import = {

            init: function() {

                window.Video_Central_Import.Events();

                window.Video_Central_Import.Wizard();

                window.Video_Central_Import.Params_Ajax();

                window.Video_Central_Import.Import_Ajax();

                var current_position = $('.wizard-wrap').attr('data-step');

                if (current_position == 3) {

                    window.Video_Central_Import.Wizard_Step_Process(current_position - 1, 3);

                    var block_table = $('#blocks').height();
                    var list_table_height = $('.wp-list-table').height() + 170;

                    if (list_table_height > block_table) {
                        $('#blocks').height(list_table_height);
                    }

                }

            },

            Events: function() {

                // search criteria form functionality
                $('#video_central_feed').change(function() {

                    var val = $(this).val(),
                        ordVal = $('#video_central_order').val();

                    $('label[for=video_central_query]').html($(this).find('option:selected').attr('title') + ' :');

                    switch (val) {

                        case 'query':

                            $('tr.video_central_duration').show();

                            var hide = ['position', 'commentCount', 'duration', 'reversedPosition', 'title'],
                                show = ['relevance', 'rating'];

                            $.each(hide, function(i, el) {
                                $('#video_central_order option[value=' + el + ']').attr({
                                    'disabled': 'disabled'
                                }).css('display', 'none');
                            });

                            $.each(show, function(i, el) {
                                $('#video_central_order option[value=' + el + ']').removeAttr('disabled').css('display', '');
                            });

                            var hI = $.inArray(ordVal, hide);

                            if (-1 !== hI) {
                                $('#video_central_order option[value=' + hide[hI] + ']').removeAttr('selected');
                            }

                            break;

                        case 'user':

                        case 'playlist':

                            $('tr.video_central_duration').hide();

                            var show2 = ['position', 'commentCount', 'duration', 'reversedPosition', 'title'],
                                hide2 = ['relevance', 'rating'];

                            $.each(hide2, function(i, el) {
                                $('#video_central_order option[value=' + el + ']').attr({
                                    'disabled': 'disabled'
                                }).css('display', 'none');
                            });

                            $.each(show2, function(i, el) {
                                $('#video_central_order option[value=' + el + ']').removeAttr('disabled').css('display', '');
                            });

                            var hI2 = $.inArray(ordVal, hide2);

                            if (-1 !== hI2) {
                                $('#video_central_order option[value=' + hide2[hI2] + ']').removeAttr('selected');
                            }

                            break;

                    }

                }).trigger('change');

            },

            Wizard: function() {

                $("body").on("click", ".progress_bar .step.complete", function() {

                    var from = $(this).parent().find('.current').data('step');

                    var to = $(this).data('step');

                    var dir = "desc";

                    if (from < to) {
                        dir = "asc";
                    }

                    window.Video_Central_Import.Wizard_Step_Process(from, to, dir);

                });

            },

            Wizard_Move_to_Step: function(step, end, dir, step_speed) {

                var width = ((parseInt(step + 1) - 1) * 25);

                if (dir === 'asc') {

                    $("#step" + step).addClass('complete').removeClass('current');

                    $(".progress_bar").find('.current_steps').animate({
                        'width': width + '%'
                    }, step_speed, function() {

                        $("#step" + (step + 1)).removeClass('complete').addClass('current');

                        if (step + 1 < end) {
                            window.Video_Central_Import.Wizard_Move_to_Step((step + 1), end, dir, step_speed);
                        }

                    });

                } else if (dir === 'desc') {

                    $("#step" + step).removeClass('complete').removeClass('current');

                    $(".progress_bar").find('.current_steps').animate({
                        'width': width + '%'
                    }, step_speed, function() {

                        $("#step" + (step - 1)).removeClass('complete').addClass('current');

                        if (step - 1 > end) {
                            window.Video_Central_Import.Wizard_Move_to_Step((step - 1), end, dir, step_speed);
                        }

                    });

                } else {

                    $("#step" + step).removeClass('complete').removeClass('current');

                    //bug fix for starting over
                    $(".progress_bar").find('.current_steps').animate({
                        'width': '0%'
                    }, step_speed, function() {

                        $("#step" + (step - 1)).removeClass('complete').addClass('current');

                        if (step - 1 > end) {
                            window.Video_Central_Import.Wizard_Move_to_Step((step - 1), end, dir, step_speed);
                        }

                    });

                }

            },

            Wizard_Step_Process: function(from, to, dir) {

                $("html, body").animate({
                    scrollTop: 0
                }, "slow");

                if (typeof(dir) === 'undefined') {
                    dir = 'asc';
                }

                var old_move = '';
                var new_start = '';

                var speed = 500;

                if (dir === 'asc') {

                    old_move = '-';
                    new_start = '';

                } else if (dir === 'desc') {

                    old_move = '';
                    new_start = '-';

                }

                $('#block' + from).animate({
                    left: old_move + '100%'
                }, speed, function() {
                    $(this).css({
                        left: '100%',
                        'z-index': '2'
                    });
                });

                $('#block' + to).css({
                    'z-index': '3',
                    left: new_start + '100%'
                }).animate({
                    left: '0%'
                }, speed, function() {
                    $(this).css({
                        'z-index': '2'
                    });
                });

                if (Math.abs(from - to) === 1) {

                    // Next Step
                    if (from < to) {

                        $("#step" + from).addClass('complete').removeClass('current');

                    } else {

                        $("#step" + from).removeClass('complete').removeClass('current');

                    }

                    var width = ((parseInt(to) - 1) * 25);

                    $(".progress_bar").find('.current_steps').animate({
                        'width': width + '%'
                    }, speed, function() {
                        $("#step" + to).removeClass('complete').addClass('current');
                    });

                } else {

                    // Move to Step
                    var steps = Math.abs(from - to);
                    var step_speed = speed / steps;

                    if (dir === 'restart') {

                        window.Video_Central_Import.Wizard_Move_to_Step(from, to, 'restart', step_speed);

                    } else if (from < to) {

                        window.Video_Central_Import.Wizard_Move_to_Step(from, to, 'asc', step_speed);

                    } else {

                        window.Video_Central_Import.Wizard_Move_to_Step(from, to, 'desc', step_speed);

                    }

                }

            },

            Params_Ajax: function() {

                $('#video_central_load_feed_form').submit(function(e) {

                    var query = $('#video_central_query').val();

                    if (query === '') {
                        e.preventDefault();
                        $('#video_central_query, label[for=video_central_query]').addClass('video_central_error');
                    }


                });

                $('#video_central_query').keyup(function() {

                    var query = $(this).val();

                    if (query === '') {

                        $('#video_central_query, label[for=video_central_query]').addClass('video_central_error');

                    } else {
                        $('#video_central_query, label[for=video_central_query]').removeClass('video_central_error');
                    }
                });

                // form submit on search results
                var submitted = false;

                $('.ajax-submit-params').on('click', function(e) {

                    var query = $('#video_central_query').val();

                    if (query !== '') {

                        $('.import-global-loading').show().removeClass('video_central_error');

                    } else {

                        $('label[for=video_central_query]').addClass('video_central_error');

                    }

                }); //end submit

            },

            Import_Ajax: function() {

                // rename table action form action (which conflicts with ajax) to action_top
                $('.ajax-submit .tablenav.top .actions select[name=action]').attr({'name': 'action_top'});

                // form submit on search results
                var submitted = false;

                $('.ajax-submit').submit(function(e) {

                    e.preventDefault();

                    if (submitted) {

                        $('.video-central-ajax-response-task').html(window.video_central_importMessages.importing);

                        $('.video-central-ajax-response-progress').html(window.video_central_importMessages.wait);

                        return;
                    }

                    var dataString = $(this).serialize();

                    submitted = true;

                    $('.video-central-ajax-response-progress').removeClass('success error').addClass('loading').html(window.video_central_importMessages.loading);

                    $('.import-progress-inner').removeClass('success error done').addClass('loading');

                    //Get import progress
                    function CheckProgress() {

                        var progress;

                        $.ajax({
                            type: 'GET',
                            url: window.ajaxurl,
                            data: {
                                'action': 'video_central_import_progress'
                            },
                            dataType: 'JSON',
                            beforeSend: function() {}

                        }).done(function(response, textStatus, jqXHR) {

                            var progress = response;

                            if (typeof progress.current === 'undefined')  { progress.current = 0; }
                            if (typeof progress.total === 'undefined') { progress.total = 1; }

                            var progressWidth = Math.round((progress.current / progress.total) * 100);

                            progressWidth = progressWidth ? progressWidth : 0; //convert NaN to 0

                            $('.video-central-ajax-response-progress').html(progressWidth + '%');

                            $('.import-progress-inner').removeClass('loading').css('width', progressWidth + '%');

                        }); //end ajax call

                        return progress;

                    }

                    var CheckProgressintervalID = setInterval(function() {
                        new CheckProgress();
                    }, 5000);

                    $.ajax({

                        type: 'post',
                        url: window.ajaxurl,
                        data: dataString,
                        dataType: 'json',

                        success: function(response) {

                            if (response.success) {

                                $('.video-central-ajax-response-progress').removeClass('loading error').addClass('success').html(response.success);

                                $('.video-central-ajax-response-task').html(window.video_central_importMessages.done);

                                $('.import-progress-inner').addClass('success done').css('width', '100%');
                            }

                            if (response.error) {
                                $('.video-central-ajax-response-progress').removeClass('loading success').addClass('error').html(response.error);
                            }

                            clearInterval(CheckProgressintervalID);

                            submitted = false;
                        }

                    }); //end ajax

                }); //end submit

            }

        }; //end video central import

        window.Video_Central_Import.init();

    }); //end doc ready

}(jQuery));
