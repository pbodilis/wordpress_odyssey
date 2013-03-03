odyssey.header = {
    init: function() {
        jQuery(document).on('click', '.color a', function(e) {
//             jQuery('body.custom-background').css('background-color', jQuery(this).css('background-color'));
            jQuery('.color a').each(function(i, v) {
                jQuery('body').toggleClass(v.className, false);
            });
            jQuery('body').toggleClass(this.className, true);
            odyssey.cookie.create('odyssey_theme_color', this.className, 30);
        });
        jQuery.subscribe('post.loading', odyssey.header.loading);
        jQuery.subscribe('post.loaded',  odyssey.header.loaded);
    },
    loading: function() {
        jQuery('header .menu.loading').show();
    },
    loaded: function() {
        jQuery('header .menu.loading').hide();
    },
};

odyssey.header.init();
