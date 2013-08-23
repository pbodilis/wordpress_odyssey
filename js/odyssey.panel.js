odyssey.panel = {
    init: function() {
        // update the panel on post update
       jQuery.subscribe('post.update', odyssey.panel.render);

        // click on the panel handle, or a "panel.toggle" event toggles the panel
        jQuery.subscribe('panel.toggle', odyssey.panel.toggle);
        jQuery(document).on('click', '#panel_handle', odyssey.panel.toggle);
    },
    render: function(e, post) {
        jQuery('#content').replaceWith(ich.render_content(post));
    },
    toggle: function(e) {
        jQuery('#panel').toggleClass('out');
    },

// 	document.getElementById( 'top' ).scrollIntoView();
}

odyssey.panel.init();
