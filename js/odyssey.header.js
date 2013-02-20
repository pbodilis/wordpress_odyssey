odyssey.header = {
    init: function() {
        jQuery(document).on('click', '.color a', function(e) {
            jQuery('.color a').each(function(i, v) {
            jQuery('body').toggleClass(v.className, false);
        });
        jQuery('body').toggleClass(this.className, true);
            odyssey.cookie.create('odyssey_theme_color', this.className, 30);
        });
    }
};

odyssey.header.init();
