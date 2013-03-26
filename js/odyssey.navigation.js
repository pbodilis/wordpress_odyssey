odyssey.navigation = {
    init: function() {
        if (odyssey.is_post()) {
            jQuery.subscribe('post.update', odyssey.navigation.update_links);

            jQuery(document).on('click', 'nav .prev:not(.deactivated)', function(e) {
                jQuery.publish('core.previous');
                e.preventDefault();
                return false;
            });
            jQuery(document).on('click', 'nav .next:not(.deactivated)', function(e) {
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
        var prev = jQuery('.headerbar nav a.prev');
        var next = jQuery('.headerbar nav a.next');
        if (typeof post.previous_url != 'undefined') {
            prev.removeClass('deactivated');
            prev.attr('href', post.previous_url);
        } else {
            prev.addClass('deactivated');
            prev.attr('href', '');
        }
        if (typeof post.next_url != 'undefined') {
            next.removeClass('deactivated');
            next.attr('href', post.next_url);
        } else {
            next.addClass('deactivated');
            next.attr('href', '');
        }
    }
};

odyssey.navigation.init();
