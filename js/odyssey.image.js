(function($) {
    $.subscribe('/window/first', function(e, post) {
        setPhotoPositionAndInfo(post.image);
        $('#photo_frame').fadeIn(400);
    });

    $.subscribe('/window/resize', function(e, post) {
        setPhotoPositionAndInfo(post.image);
    });

    function setPhotoPositionAndInfo(image) {
        var dE = document.documentElement;

        var borderWidth = 5; // check in css file #photo_frame for consistency
        var frameWidth, frameHeight, resizedWidth, resizedHeight, offsetHeight;

        var photoInfosHeight = $('#photo_infos').height();
        var displayHeightArea = dE.clientHeight - $('header').height() - borderWidth * 2 - photoInfosHeight - 20;
        if (image.height < displayHeightArea) {
            resizedHeight = image.height;
            resizedWidth = image.width;
            if (image.height > displayHeightArea - photoInfosHeight / 2) { // let's see if we should elevate the image
                offsetHeight = photoInfosHeight / 2 - displayHeightArea + image.height;
            } else {
                offsetHeight = 0;
            }
        } else { // height smaller than the display area, let's resize the image
            resizedHeight = displayHeightArea;
            resizedWidth = resizedHeight * image.width / image.height;
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
