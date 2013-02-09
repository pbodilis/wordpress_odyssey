odyssey.keyboard = {
	init: function() {
	    var txtFocus = false;
	    jQuery('input, textarea').focus(function() {
	        txtFocus = true;
	    });
	    jQuery('input, textarea').blur(function() {
	        txtFocus = false;
	    });
        jQuery(document).keydown(function(e) {
	        if (!txtFocus) { // if typing text, do not trigger events !
	            switch(e.which){
	                case 32:
	                    break;
	                case 37:
				        jQuery.publish('core.previous');
	                    break;
	                case 39:
				        jQuery.publish('core.next');
	                    break;
	            }
	        }
	    });
	}
};

odyssey.keyboard.init();