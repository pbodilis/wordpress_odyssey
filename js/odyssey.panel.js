odyssey.panel = {
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.panel.render);

        // click on the panel handle, or a "panel.toggle" event toggles the panel
        jQuery.subscribe('panel.toggle', odyssey.panel.toggle);
        jQuery(document).on('click', '#panel_handle', odyssey.panel.toggle);
    },
    render: function(e, post) {
        jQuery('#photoblog_content').Chevron('render', post, function(result) {
            jQuery('#content').replaceWith(result);
        });
    },
    toggle: function(e) {
        jQuery('#panel').toggleClass('out');
    },
}


odyssey.panel.init();

// jQuery.subscribe('post.update', odyssey.panel.refresh);
// jQuery(window).load(function() {
//     panelOut = odyssey.cookie.read('odyssey_theme_panelVisibility') == '1';
//     jQuery('#panel').toggleClass('out', panelOut);
// });




