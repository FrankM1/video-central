var Video_Central = Video_Central || {}; // global storage for the Video_Central site;

Video_Central.ajax = Video_Central.ajax || {};

//run the ajax request
Video_Central.ajax.request = function(obj, type) {

    jQuery.ajax({
        type: "POST",
        url: Video_Central.util.get_saved_option('ajaxurl'),
        cache: true,
        data: {
            action: "ajax_posts_list",
            block_id: obj.id,
            current_page: obj.current_page,
            block_type: obj.block_type,
            filter: obj.filter,
            filter_id: obj.filter_id,
            trigger: obj.trigger
        },
        success: function(data, textStatus, jqXHR) {

            console.log(data);
        },
        error: function(jqXHR, textStatus, errorThrown) {
        }
    });

};
