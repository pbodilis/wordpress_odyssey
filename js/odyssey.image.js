odyssey.image = {
    image: null,
    timeout: null,

    init: function() {
        if (odyssey.is_post()) {
            jQuery.subscribe('post.update', odyssey.image.render);
            jQuery.subscribe('bootstrap', odyssey.image.bootstrap);
            jQuery(window).resize(odyssey.image.resize);
        }
    },
    bootstrap: function(e, post) {
        jQuery('#photo_container').hide();
        if (post.image) {
            odyssey.image.image = post.image;
            odyssey.image.resize();
        }
        jQuery('#photo_container').fadeIn(400);
    },
    get_post_main_position: function(image) {
        // those values should be extracted from the css
        var photo_infos_height = 0;
        var header_height     = jQuery('header.headerbar').height();

        if (jQuery('body').hasClass('admin-bar')) { // am I logged in?
            header_height += jQuery('#wpadminbar').height();
        }
        // panel handle width
        var panel_handle_width = jQuery('#panel_handle').width();

        // check in css file #photo_container for consistency
        var border_width = 5;

        var dE = document.documentElement;

        var resized_width, resized_height;

        var photo_infos_height = 42; // guess on the photo info height
        var display_height_area = dE.clientHeight - header_height * 2 - border_width * 2 - photo_infos_height - 20;

        if (image.height < display_height_area) {
            resized_height = image.height;
            resized_width  = image.width;
        } else { // height smaller than the display area, let's resize the image
            resized_height = display_height_area;
            resized_width  = resized_height * image.width / image.height;
        }
        return {
            width:  Math.round(resized_width),
            height: Math.round(resized_height),
            dEHeight: dE.clientHeight - header_height * 2
        };
    },
    render: function(e, post) {
        if (odyssey.image.timeout == null) {
            clearTimeout(odyssey.image.timeout);
        }
        odyssey.image.timeout = setTimeout(odyssey.image.do_render, 1, post)
    },
    do_render: function(post) {
        if (post.image) {
            odyssey.image.image = post.image;
            post.frame = odyssey.image.get_post_main_position(post.image);
        }

        var rendering = ich.render_image(post);
        // fadeout the image, and make the replacement appear in the callback
        jQuery('#photo_wrapper').fadeOut(200, function() {
            // insert image
            jQuery('#photo_wrapper').replaceWith(rendering);
            jQuery('#photo_wrapper').fadeIn(400);
        });
    },
    resize: function(e) {
        frame = odyssey.image.get_post_main_position(odyssey.image.image);

        jQuery('#photo_container').css({
            'width':  frame.width,
        });
        jQuery('#page img').css({
            'width':  frame.width,
            'height': frame.height
        });
        jQuery('#photo_wrapper').css({
            'height': frame.dEHeight,
        });
    },
}

odyssey.image.init();