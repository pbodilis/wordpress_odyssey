(function($) {
    $.post(OdysseyAjax.ajaxurl, {
        action:'odyssey_get_json_post',
    }, function(data) {
        $.Mustache.load(OdysseyAjax.ajaxtpl + 'image.html').done(function () {
            $('#photo_frame').mustache('image', $.parseJSON(data), { method: 'html' });
        });
    });

})(jQuery);
