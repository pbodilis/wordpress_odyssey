odyssey.keyboard = {
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
