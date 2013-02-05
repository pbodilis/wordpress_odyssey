(function($) {

    $.post(OdysseyAjax.ajaxurl, {
        action:'odyssey_get_json_post',
    }, function(data) {
        $('#imageTemplate').Chevron("render", $.parseJSON(data), function(rendering) {
            $('#photo_frame').html(rendering);
        });
    });
})(jQuery);
