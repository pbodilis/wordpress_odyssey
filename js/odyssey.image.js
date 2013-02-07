odyssey.image = {
    getPhotoFramePosition: function (image) {
        // those values should be extracted from the css
        // var photoInfosHeight = jQuery('#photo_infos').height();
        var photoInfosHeight = 40;
        // var headerHeight     = jQuery('header').height();
        var headerHeight     = 30;

        // check in css file #photo_frame for consistency
        var borderWidth      = 5;

        var dE = document.documentElement;

        var frameWidth, frameHeight, resizedWidth, resizedHeight, offsetHeight;

//         var photoInfosHeight = jQuery('#photo_infos').height();
        var photoInfosHeight = 40; // guess on the photo info height
        var displayHeightArea = dE.clientHeight - headerHeight - borderWidth * 2 - photoInfosHeight - 20;

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

        return {
            width:      frameWidth,
            height:     frameHeight,
            marginLeft: (dE.clientWidth - frameWidth  ) / 2 - borderWidth,
            marginTop:  (dE.clientHeight - frameHeight + headerHeight) / 2 - borderWidth - offsetHeight
        };
    },
    render: function(e) {
        post = odyssey.core.post;
        post.frame = odyssey.image.getPhotoFramePosition(post.image);

        // once rendering is done, insert the image
        jQuery('#photoblog_image').Chevron('render', post, function(result) {
            jQuery('#photo_frame').replaceWith(result);
            jQuery('#photo_frame').fadeIn(400);
        });
    },
    resize: function(e) {
        post = odyssey.core.post;
        post.frame = odyssey.image.getPhotoFramePosition(post.image);

        jQuery('#photo_frame #img').css({
            'width': post.frame.width,
            'height': post.frame.height
        });
        jQuery('#photo_frame').css({
            'margin-left': post.frame.marginLeft,
            'margin-top': post.frame.marginTop
        });
    },
}
            
jQuery.subscribe('post.update',   odyssey.image.render);
jQuery(window).resize(odyssey.image.resize);
