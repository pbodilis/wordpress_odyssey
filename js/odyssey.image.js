odyssey.image = {
    post: null,
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
        odyssey.image.post = post;
        odyssey.image.resize();
        jQuery('#photo_container').fadeIn(400);
    },
    get_image_position: function(image) {
        var dE = document.documentElement;

        var header_height = jQuery('header.headerbar').height();
        var fig_caption_height = 42; // guess the photo info height
        var border_width = 5; // check in css file #photo_container for consistency
        var resized_width, resized_height;

        if (jQuery('body').hasClass('admin-bar')) { // am I logged in?
            header_height += jQuery('#wpadminbar').height();
        }
        var display_height_area = dE.clientHeight - header_height * 2 - border_width * 2 - fig_caption_height - 20;

        if (image.height < display_height_area) { // the height for is image is big enough for the image to fit in
            resized_height = image.height;
            resized_width  = image.width;
        } else { // height smaller than the display area, let's resize the image
            resized_height = display_height_area;
            resized_width  = resized_height * image.width / image.height;
        }
        return {
            width:    Math.round(resized_width),
            height:   Math.round(resized_height),
            cheight:  Math.round(resized_height) + border_width * 2 + fig_caption_height,
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
        odyssey.image.post = post;
        if (post.image) {
            odyssey.image.do_render_image();
        } else if (post.video) {
            odyssey.image.do_render_video();
        } else {
            return;
        }
    },
    do_render_image: function(post) {
        var frame = odyssey.image.get_image_position(odyssey.image.post.image);

        var html = '';
        html += '<div id="photo_wrapper" class="" style="height: ' + frame.dEHeight + 'px;">';
        html += '  <figure id="photo_container" class="" style="width: ' + frame.width + 'px; height: ' + frame.cheight + 'px;">';
        html += '    <img src="' + odyssey.image.post.image.url + '" alt="' + odyssey.image.post.title + '"/>';
        html += '    <figcaption id="photo_infos">';
        html += '      <h2>' + odyssey.image.post.title + '</h2>';
        if (typeof odyssey.image.post.image.capture_date !== 'undefined') {
            html += '      <p>' + odyssey.image.post.image.capture_date + '</p>';
        }
        html += '    </figcaption>';

        html += '  </figure>';
        html += '</div>';

        // fadeout the image, and make the replacement appear in the callback
        jQuery('#photo_container img').fadeOut(200);
        // insert image
        jQuery('#photo_wrapper').replaceWith(html);
        jQuery('#photo_wrapper img').hide();
        jQuery('#photo_wrapper img').load(function() {
            jQuery(this).fadeIn(200);
        });
    },
    do_render_video: function(post) {
        var iframe = jQuery(odyssey.image.post.video.html);
        var image = {
            width: iframe.width(),
            height: iframe.height(),
        };
        var frame = odyssey.image.get_image_position(image);

        var html = '';
        html += '<div id="photo_wrapper" class="" style="height: ' + frame.dEHeight + 'px;">';
        html += '  <figure id="photo_container" class="" style="width: ' + frame.width + 'px; height: ' + frame.cheight + 'px;">';
        html += odyssey.image.post.video.html;
        html += '    <figcaption id="photo_infos">';
        html += '      <h2>' + odyssey.image.post.title + '</h2>';
        html += '    </figcaption>';

        html += '  </figure>';
        html += '</div>';

        // fadeout the image, and make the replacement appear in the callback
        jQuery('#photo_container').fadeOut(200);
        // insert image
        jQuery('#photo_wrapper').replaceWith(html);
        jQuery('#photo_wrapper iframe').hide();
        jQuery('#photo_wrapper iframe').load(function() {
            jQuery(this).fadeIn(200);
        });
    },
    resize: function(e) {
        if (odyssey.image.post) {
            if (odyssey.image.post.image) {
                frame = odyssey.image.get_image_position(odyssey.image.post.image);
            } else if (odyssey.image.post.video) {
                var iframe = jQuery(odyssey.image.post.video.html);
                var image = {
                    width: iframe.width(),
                    height: iframe.height(),
                };
                frame = odyssey.image.get_image_position(image);
            }

            jQuery('#photo_container').css({
                'width':  frame.width,
                'height': frame.cheight
            });
            jQuery('#photo_wrapper').css({
                'height': frame.dEHeight,
            });
        }
    },
}

odyssey.image.init();