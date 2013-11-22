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
