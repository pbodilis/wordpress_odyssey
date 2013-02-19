odyssey.panel = {
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.panel.render);

        // click on the panel handle, or a "panel.toggle" event toggles the panel
        jQuery.subscribe('panel.toggle', odyssey.panel.toggle);
        jQuery(document).on('click', '#panel_handle', odyssey.panel.toggle);

        panelOut = odyssey.cookie.read('odyssey_theme_panelVisibility') == '1';
        jQuery('#panel').toggleClass('out', panelOut);
    },
    render: function(e, post) {
        // once rendering is done, insert the image
console.log(jQuery('header').height());
        post.panelHeight = document.documentElement.clientHeight - 30;

        jQuery('#photoblog_panel').Chevron('render', post, function(result) {
            jQuery('#panel').replaceWith(result);
        });
//         odyssey.panel.resize(e);
    },
    resize: function(e) {
        newPanelHeight = document.documentElement.clientHeight - jQuery('header').height();
        console.log(jQuery('#panel_scroll').height());
        jQuery('#panel_scroll').css('height', newPanelHeight);
    },
    toggle: function(e) {
        jQuery('#panel').toggleClass('out');
        panelOut = !panelOut;
        odyssey.cookie.create('odyssey_theme_panelVisibility', (panelOut ? '1' : '0'), 30);
    },
}


odyssey.panel.init();

// jQuery.subscribe('post.update', odyssey.panel.refresh);
jQuery(window).resize(odyssey.panel.resize);
// jQuery(window).load(odyssey.panel.resize);




