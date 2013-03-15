odyssey.archive = {
    init: function() {
        jQuery('.accordion li > .sub_menu').hide();
        
        jQuery(document).on('click', '.menu_extend', function(e) {
            jQuery('.accordion li > .sub_menu').slideUp();
            jQuery(this).parent().find('.sub_menu').slideDown();
        });
    }
}

odyssey.archive.init();

