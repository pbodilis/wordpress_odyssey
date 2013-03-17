odyssey.archive = {
    init: function() {
        jQuery('#archives_menu .menu_content').hide();
        
        jQuery(document).on('click', '.menu_extend', function(e) {
            jQuery('.extended').toggleClass('extended', false);
            jQuery('#archives_menu .menu_content').slideUp();
            var menu = jQuery(this).parent().parent();
            menu.toggleClass('extended', true);
            var mcontent = menu.find('.menu_content');
            mcontent.slideDown();
        });
    }
}

odyssey.archive.init();

