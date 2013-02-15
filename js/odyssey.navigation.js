odyssey.navigation = {
    init: function() {
        jQuery(document).on('click', 'nav .prev', function(event) {
            jQuery.publish('core.previous');
            event.preventDefault();
            return false;
        });
        jQuery(document).on('click', 'nav .next', function(event) {
            jQuery.publish('core.next');
            event.preventDefault();
            return false;
        });
    }
};

odyssey.navigation.init();
