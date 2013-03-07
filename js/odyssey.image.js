odyssey.image = {
    image: null,

    init: function() {
        jQuery.subscribe('post.update', odyssey.image.render);
        jQuery(window).resize(odyssey.image.resize);
    },

    get_photo_frame_position: function(image) {
        // those values should be extracted from the css
        // var photo_infos_height = jQuery('#photo_infos').height();
        var photo_infos_height = 42;
        var header_height     = jQuery('header.headerbar').height();
//         var header_height     = 30;
        if (jQuery('body').hasClass('admin-bar')) { // am I logged in?
            header_height += jQuery('#wpadminbar').height();
        }
        // panel handle width
        var panel_handle_width = jQuery('#panel_handle').width();

        // check in css file #photo_frame for consistency
        var border_width = 5;

        var dE = document.documentElement;

        var frame_width, frame_height, resized_width, resized_height, offset_height;

//         var photo_infos_height = jQuery('#photo_infos').height();
        var photo_infos_height = 40; // guess on the photo info height
        var display_height_area = dE.clientHeight - header_height - border_width * 3 - photo_infos_height - 20;

        if (image.height < display_height_area) {
            resized_height = image.height;
            resized_width = image.width;
            if (image.height > display_height_area - photo_infos_height / 2) { // let's see if we should elevate the image
                offset_height = photo_infos_height / 2 - display_height_area + image.height;
            } else {
                offset_height = 0;
            }
        } else { // height smaller than the display area, let's resize the image
            resized_height = display_height_area;
            resized_width = resized_height * image.width / image.height;
            offset_height = photo_infos_height / 2;
        }
        frame_height = Math.round(resized_height);
        frame_width  = Math.round(resized_width);
        return {
            width:  frame_width,
            height: frame_height,
            left:   (dE.clientWidth - frame_width + panel_handle_width) / 2 - border_width,
            top:    (dE.clientHeight - frame_height + header_height) / 2 - border_width - offset_height
        };
    },
    render: function(e, post) {
        odyssey.image.image = post.image;
        post.frame = odyssey.image.get_photo_frame_position(post.image);
        var rendering = ich.render_image(post);
        // fadeout the image, and make the replacement appear in the callback
        jQuery('#photo_frame').fadeOut(200, function() {
            // insert image
            jQuery('#photo_frame').replaceWith(rendering);
            jQuery('#photo_frame').fadeIn(400);
        });
    },
    resize: function(e) {
        frame = odyssey.image.get_photo_frame_position(odyssey.image.image);

        jQuery('#photo_frame #img').css({
            'width':  frame.width,
            'height': frame.height
        });
        jQuery('#photo_frame').css({
            'left': frame.left,
            'top':  frame.top
        });
    },
}

odyssey.image.init();