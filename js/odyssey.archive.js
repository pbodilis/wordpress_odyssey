odyssey.archive = {
    init: function() {
        jQuery('#archives_menu .menu_content:not(.extended)').hide();
        
        jQuery(document).on('click', '.menu_extend', function(e) {
            // first, try to find the parent menu
            var menu = jQuery(this).parent().parent();

            // on this level, compress all extended elements
            var to_compress = menu.parent().find('.extended');
            to_compress.children('.menu_content').slideUp();
            to_compress.removeClass('extended');

            // if the menu isn't the compressed element, then extend the menu
            if (!menu.is(to_compress)) {
                menu.addClass('extended');
                var mcontent = menu.children('.menu_content');
                mcontent.slideDown();
            }
        });
    }
}

odyssey.archive.init();

