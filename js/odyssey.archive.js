odyssey.archive = {
    init: function() {
        jQuery('#archives_menu .menu_content:not(.extended)').hide();
        
        jQuery(document).on('click', '.menu_extend', function(e) {
            var menu = jQuery(this).parent().parent();

            var to_compress = menu.parent().find('.extended');
            to_compress.slideUp();
            to_compress.removeClass('extended');

            var mcontent = menu.find('.menu_content');
            mcontent.addClass('extended');
            mcontent.slideDown();
        });
    }
}

odyssey.archive.init();

