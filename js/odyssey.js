(function($) {

//     $.post(OdysseyAjax.ajaxurl, {
//         action:'odyssey_get_json_post',
//     }, function(data) {
//         $('#imageTemplate').Chevron("render", $.parseJSON(data), function(rendering) {
//             $('#photo_frame').html(rendering);
//         });
//     });

    $(window).load(function () {
        setPhotoPositionAndInfo();
        $('#photo_frame').fadeIn(400);
    });


    function setPhotoPositionAndInfo() {
        var borderWidth = 5; // check in css file #photo_frame for consistency
        var frameWidth, frameHeight, resizedWidth, resizedHeight, offsetHeight;

        var photoInfosHeight = $('#photo_infos').height();
        var displayHeightArea = dE.clientHeight - $('header').height() - borderWidth * 2 - photoInfosHeight - 20;
        if (imageHeight < displayHeightArea) {
            resizedHeight = imageHeight;
            resizedWidth = imageWidth;
            if (imageHeight > displayHeightArea - photoInfosHeight / 2) { // let's see if we should elevate the image
                offsetHeight = photoInfosHeight / 2 - displayHeightArea + imageHeight;
            } else {
                offsetHeight = 0;
            }
        } else { // height smaller than the display area, let's resize the image
            resizedHeight = displayHeightArea;
            resizedWidth = resizedHeight * imageWidth / imageHeight;
            offsetHeight = photoInfosHeight / 2;
        }
        frameHeight = Math.round(resizedHeight);
        frameWidth = Math.round(resizedWidth);

        $('#photo_frame #img').css({
            'width': frameWidth,
            'height': frameHeight
        });
        $('#photo_frame').css({
            'margin-left': (dE.clientWidth - frameWidth  ) / 2 - borderWidth,
            'margin-top': (dE.clientHeight - frameHeight + $('header').height()) / 2 - borderWidth - offsetHeight
        });
    }

})(jQuery);
