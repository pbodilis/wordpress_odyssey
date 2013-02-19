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
    }
};

odyssey.navigation.init();
