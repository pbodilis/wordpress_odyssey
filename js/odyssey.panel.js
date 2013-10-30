odyssey.panel = {
    init: function() {
        jQuery(document).on('click', '#content_title', odyssey.panel.toggle);

        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.panel.render);

        // click on the panel handle, or a "panel.toggle" event toggles the panel
        jQuery.subscribe('panel.toggle', odyssey.panel.toggle);
        jQuery(document).on('click', '#panel_handle', odyssey.panel.toggle);
    },
    render: function(e, post) {
        html = ich.render_content(post);
        jQuery('#content').replaceWith(html);
    },
    toggle: function(e) {
        jQuery('html, body').animate({
            scrollTop:jQuery('#content_title').offset().top
        }, 'slow', 'swing');
    },

}

odyssey.panel.init();
