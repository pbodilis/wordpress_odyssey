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
