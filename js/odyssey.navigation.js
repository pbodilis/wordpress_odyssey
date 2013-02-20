odyssey.navigation = {
    init: function() {
        jQuery(document).on('click', 'nav .prev', function(e) {
            jQuery.publish('core.previous');
            e.preventDefault();
            return false;
        });
        jQuery(document).on('click', 'nav .next', function(e) {
            jQuery.publish('core.next');
            e.preventDefault();
            return false;
        });
        jQuery(document).on('click', '.color a', function(e) {
        	jQuery('.color a').each(function(i, v) {
        		jQuery('body').toggleClass(v.className, false);
        	});
        	jQuery('body').toggleClass(this.className, true);
            odyssey.cookie.create('odyssey_theme_color', this.className, 30);
        });
    }
};

odyssey.navigation.init();
