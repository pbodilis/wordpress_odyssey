odyssey.navigation = {
    init: function() {
        if (odyssey.is_post()) {
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
            jQuery(document).on('click', '#random', function(e) {
                jQuery.publish('core.random');
                e.preventDefault();
                return false;
            });
        }
    }
};

odyssey.navigation.init();
