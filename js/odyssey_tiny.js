odyssey.is_post=function() {
    return (typeof odyssey.posts != 'undefined');
};


odyssey.archive = {
    init: function() {
        jQuery('#archives_menu .menu_content:not(.extended)').hide();
        
        jQuery(document).on('click', '.menu_extend', function(e) {
            // first, try to find the parent menu
            var menu = jQuery(this).parent().parent();

            // on this level, compress all extended elements
            var to_compress = menu.parent().find('.extended');
            to_compress.children('.menu_content').slideUp();
            to_compress.removeClass('extended');

            // if the menu isn't the compressed element, then extend the menu
            if (!menu.is(to_compress)) {
                menu.addClass('extended');
                var mcontent = menu.children('.menu_content');
                mcontent.slideDown();
            }
        });
    }
}

odyssey.archive.init();

odyssey.comments = {
    status: {},
    post_comments_number: 0,
    
    bootstrap: function(e, post) {
        odyssey.comments.post_comments_number = post.comments_number;
    },
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.comments.render);
        jQuery.subscribe('bootstrap', odyssey.comments.bootstrap);

        if (odyssey.comment_form_ajax_enabled == 'false') {
            return;
        }
        odyssey.comments.status = jQuery('#comment_status');
        
        jQuery(document).on('submit', '#commentform', function(e) {
            e.preventDefault();

            jQuery.ajax({
                type: 'post',
                url:        jQuery('#commentform').attr('action'),
                data:       jQuery('#commentform').serialize(),
                dataType:  'html',
                beforeSend: function(xhr, settings) {
                    odyssey.comments.status.html('Processing...');
                },
                error: function(xhr, test_status, error_thrown) {
                    if (500 == xhr.status) {
                        var response = xhr.responseText;
                        var text = response.split('<p>')[1].split('</p>')[0];
                        odyssey.comments.status.html(text);
                    } else if (403 == xhr.status) {
                        odyssey.comments.status.html('Stop! You are posting comments too quickly.');
                    } else {
                        if ('timeout' == test_status)
                            odyssey.comments.status.html('Server timeout error. Try again.');
                        else
                            odyssey.comments.status.html('Unknown error');
                    }
                },
                success: function(data, test_status, xhr) {
                    // update status
                    odyssey.comments.status.html('Comment posted!');
                    // display newly added comment
                    
                    var parent_id = jQuery('#comment_parent').val();
                    var comment_list;
                    if (parent_id == 0) {
                        comment_list = jQuery('#responses ol.comment-list');
                    } else {
                        if (jQuery('#comment-' + parent_id + ' ol.children').length == 0) {
                            jQuery('#comment-' + parent_id).append('<ol class="children"></ol>');
                        }
                        comment_list = jQuery('#comment-' + parent_id + ' ol.children');
                    }
                    var comment = jQuery(data);
                    comment.hide().appendTo(comment_list).fadeIn();

                    // clear form
                    jQuery('#comment').val('');
                    jQuery('#cancel-comment-reply-link').click();

                    // reload post comment
                    jQuery.publish('core.comments.reload');
                }
            });
        });
    },
    replyto: function(jthis, comment_id, author) {
        jQuery('.replying').toggleClass('replying', false);
        jthis.toggleClass('replying', true);

        jQuery('#comment_parent').val(comment_id);
        odyssey.comments.status.html('Replying to: ' + author + ' <a href="#" class="replyto_cancel">(cancel)</a>');
    },
    render: function(e, post) {
        jQuery('#responses>ol.comment-list').html(post.comments);
        var comments_count = jQuery('li.comment').length;
        jQuery('h3.comments-title').html(comments_count + ' Comment' + ((comments_count != 1) ? 's' : ''));
    },
}

odyssey.comments.init();

// cookies! yummy
odyssey.cookie = {
    create: function(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = '; expires=' + date.toGMTString();
        } else {
            var expires = '';
        }
        document.cookie = name + '=' + value + expires + '; path=/';
    },

    read: function(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; ++i) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) == 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    },

    erase: function(name) {
        odyssey.cookie.create(name, '', -1);
    }
};
odyssey.core = {
    posts: {},

    init: function() {
        jQuery.subscribe('core.get',             odyssey.core.go_to_post);
        jQuery.subscribe('core.previous',        odyssey.core.previous);
        jQuery.subscribe('core.next',            odyssey.core.next);
        jQuery.subscribe('core.random',          odyssey.core.random);
        jQuery.subscribe('core.comments.reload', odyssey.core.reload_comments);
    },

    get_current_post_id: function() {
        return odyssey.core.posts.current_ID;
    },
    get_current_post: function() {
        return odyssey.core.posts[odyssey.core.posts.current_ID];
    },

    /**
     * retrieves the post with the current id, or the latest one if none was given
     * along with the adjacent posts
     */
    go_to_post: function(e, id, adjacent) {
        adjacent  = adjacent || 'both';
        if (odyssey.core.posts[id]) { // do we have the current post in cache ?
            // if it's caching, move the state to pending
            if (odyssey.core.posts[id] === 'caching') odyssey.core.posts[id] = 'pending';
            // if it's pending, nothing to do
            if (odyssey.core.posts[id] === 'pending') return;
            odyssey.core.posts.current_ID = id; // just set the current cursor to the given id
            odyssey.core.publish_update(); // notify all views the current post needs to be displayed

            // cache the adjacent of this post if we don't have it already
            var current = odyssey.core.get_current_post();
            if (current.previous_ID && typeof odyssey.core.posts[current.previous_ID] == 'undefined') {
                odyssey.core.posts[current.previous_ID] = 'caching';
                odyssey.core.retrieve_post(current.previous_ID, 'previous', odyssey.core.cache_posts);
            }
            if (current.next_ID && typeof odyssey.core.posts[current.next_ID] == 'undefined') {
                odyssey.core.posts[current.next_ID] = 'caching';
                odyssey.core.retrieve_post(current.next_ID, 'next', odyssey.core.cache_posts);
            }
        } else { // retrieve the post
            if ('random' != id) {
                odyssey.core.posts[id] = 'pending';
            }
            odyssey.core.retrieve_post(id, adjacent, odyssey.core.update_current_post);
        }
    },
    retrieve_post: function(id, adjacent, retrieve_post_callback) {
        var ajaxArgs = {
            action:     'odyssey_get_json_post_and_adjacents',
            adjacent:   adjacent || 'both',
            post_nonce: odyssey.post_nonce
        }
        if (typeof(id) !== 'undefined') {
            ajaxArgs.id = id;
        }
        jQuery.ajax({
            url:      odyssey.ajaxurl,
            dataType: 'json',
            data:     ajaxArgs/*,*/
//             beforeSend: function() {
//                 jQuery.publish('post.loading');
//             },
//             complete: function() {
//                 jQuery.publish('post.loaded');
//             }
        }).done(function(r) {
            if (!r) return;
            odyssey.post_nonce = r.post_nonce;
            retrieve_post_callback(r.posts);
        });
    },
    bootstrap: function(p) {
        odyssey.core.posts = p;
        jQuery.publish('bootstrap', odyssey.core.get_current_post());
    },
    update_current_post: function(p) {
        for (var i in p) {
            odyssey.core.posts[i] = p[i];
        }
        odyssey.core.publish_update();
    },
    cache_posts: function(p) {
        for (var i in p) {
            if (odyssey.core.posts[i] === 'pending') {
                odyssey.core.current_ID = i;
            }
            if (i != 'current_ID') {
                odyssey.core.posts[i] = p[i];
            }
        }
    },
    publish_update: function() {
        var current = odyssey.core.get_current_post();
        jQuery.publish('post.update', current);
    },
    reload_comments: function(e) {
        odyssey.core.posts[id].comments = jQuery('#responses>ol.comment-list').html();
    },
    previous: function(e) {
        var current = odyssey.core.posts[odyssey.core.posts.current_ID];
        if (current.previous_ID) {
            odyssey.core.go_to_post(e, current.previous_ID, 'previous');
        }
    },
    next: function(e) {
        var current = odyssey.core.posts[odyssey.core.posts.current_ID];
        if (current.next_ID) {
            odyssey.core.go_to_post(e, current.next_ID, 'next');
        }
    },
    random: function(e) {
        odyssey.core.go_to_post(e, 'random');
    }
};

odyssey.core.init();
odyssey.header = {
    init: function() {
        jQuery(document).on('click', '.color a', function(e) {
//             jQuery('body.custom-background').css('background-color', jQuery(this).css('background-color'));
            jQuery('.color a').each(function(i, v) {
                jQuery('body').toggleClass(v.className, false);
            });
            jQuery('body').toggleClass(this.className, true);
            odyssey.cookie.create('odyssey_theme_color', this.className, 30);
            e.preventDefault();
        });
        jQuery.subscribe('post.loaded',  odyssey.header.loaded);
    },
    loaded: function() {
        jQuery('header .menu.loading').hide();
    },
};

odyssey.header.init();
odyssey.history = {
    postID: null,
    
    init: function() {
        if (odyssey.is_post()) {
            jQuery.subscribe('post.update', odyssey.history.update);
            window.History.Adapter.bind(window, 'popstate', odyssey.history.popstate);
            window.History.Adapter.bind(window, 'hashchange', odyssey.history.popstate);
        }
    },

    update: function(e, post) {
        odyssey.history.postID = post.ID;
        window.History.pushState({postID: post.ID}, odyssey.blog_name + ' » ' + post.title, post.url);
    },

    popstate: function() {
        var state = window.History.getState(); // Note: We are using History.getState() instead of event.state
        if (state.data.postID != odyssey.history.postID) {
            jQuery.publish('core.get', state.data.postID);
        }
    }
};

odyssey.history.init();
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
        html += '<div id="photo_wrapper" class="' + odyssey.image.post.class + '" style="height: ' + frame.dEHeight + 'px;">';
        html += '  <figure id="photo_container" class="' + odyssey.image.post.class + '" style="width: ' + frame.width + 'px; height: ' + frame.cheight + 'px;">';
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
        html += '<div id="photo_wrapper" class="' + odyssey.image.post.class + '" style="height: ' + frame.dEHeight + 'px;">';
        html += '  <figure id="photo_container" class="' + odyssey.image.post.class + '" style="width: ' + frame.width + 'px; height: ' + frame.cheight + 'px;">';
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

odyssey.image.init();odyssey.keyboard = {
    init: function() {
        var txtFocus = false;
        jQuery('input, textarea').focus(function() {
            txtFocus = true;
        });
        jQuery('input, textarea').blur(function() {
            txtFocus = false;
        });
        jQuery(document).on('keydown', function(e) {
            if (!txtFocus) { // if typing text, do not trigger events !
                switch(e.which) {
                    case 32:
                        jQuery.publish('panel.toggle');
                        e.preventDefault();
                        return false;
                    case 37:
                        if (!odyssey.is_post()) {return;}

                        jQuery.publish('core.previous');
                        e.preventDefault();
                        return false;
                    case 39:
                        if (!odyssey.is_post()) {return;}

                        jQuery.publish('core.next');
                        e.preventDefault();
                        return false;
                }
            }
        });
    }
};

odyssey.keyboard.init();
odyssey.navigation = {
    init: function() {
        if (odyssey.is_post()) {
            jQuery.subscribe('post.update', odyssey.navigation.update_links);

            jQuery(document).on('click', 'nav .previous', function(e) {
                jQuery.publish('core.previous');
                e.preventDefault();
                return false;
            });
            jQuery(document).on('click', 'nav .next', function(e) {
                jQuery.publish('core.next');
                e.preventDefault();
                return false;
            });
            jQuery(document).on('click', '#random', function(e) {
                jQuery.publish('core.random');
                e.preventDefault();
                return false;
            });
        }
    },
    update_links: function(e, post) {
        var prev = jQuery('nav a.previous');
        var next = jQuery('nav a.next');
        if (typeof post.previous_url != 'undefined') {
            prev.attr('href', post.previous_url);
        } else {
            prev.attr('href', '');
        }
        if (typeof post.next_url != 'undefined') {
            next.attr('href', post.next_url);
        } else {
            next.attr('href', '');
        }
    }
};

odyssey.navigation.init();
odyssey.panel = {
    init: function() {
        jQuery(document).on('click', '#content_title', odyssey.panel.toggle);

        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.panel.render);

        // click on the panel handle, or a "panel.toggle" event toggles the panel
        jQuery.subscribe('panel.toggle', odyssey.panel.toggle);
        jQuery(document).on('click', '#panel_handle', odyssey.panel.toggle);
    },
    toggle: function(e) {
        jQuery('html, body').animate({
            scrollTop:jQuery('#content_title').offset().top
        }, 'slow', 'swing');
    },

    render: function(e, post) {
        html = '';
        html += '<section id="content" class="' + post.class + '">';
        html += '<article class="post_content">' + post.content + '</article>';

        if (Object.keys(post.categories).length > 0) {
            html += '<article class="post_categories">';
            html += '<ul>';
            jQuery.each(post.categories, function(cat_name, cat_url) {
                html += '<li><a href="' + cat_url + '">&#91;' + cat_name + '&#93;</a></li>';
            });
            html += '</ul>';
            html += '</article>';
        }

        if (post.image && Object.keys(post.image.exifs).length > 0) {
            html += '<article class="image_exifs">';
            html += '<ul>';
            jQuery.each(post.image.exifs, function(exif_name, exif_value) {
                html += '<li>' + exif_name + exif_value + '</li>';
            });
            html += '</ul>';
            html += '</article>';
        }

        if (typeof post.ratings !== 'undefined') {
            html += '<article class="post_rating">' + post.ratings + '</article>';
        }
        html += '</section>';
        jQuery('#content').replaceWith(html);
        jQuery('#page').attr('class', post.class);
        jQuery('#wrapper').attr('class', post.class);
        if (post.format === false) {
            jQuery('#content_title').html(post.title);
        } else {
            jQuery('#content_title').html('Info, rate &amp; Comments');
        }
    },
}

odyssey.panel.init();
if (odyssey.is_post()) {
    // wait for the document to be ready
    jQuery(document).ready(function() {
        odyssey.core.bootstrap(odyssey.posts);
    });
} else {
}



// odyssey.init();