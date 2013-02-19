odyssey.panel = {
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.panel.render);

        // click on the panel handle, or a "panel.toggle" event toggles the panel
        jQuery.subscribe('panel.toggle', odyssey.panel.toggle);
        jQuery(document).on('click', '#panel_handle', odyssey.panel.toggle);
    },
    render: function(e, post) {
        // once rendering is done, insert the image
        jQuery('#photoblog_panel').Chevron('render', post, function(result) {
            jQuery('#panel').replaceWith(result);
        });
        var dE = document.documentElement;
        newPanelHeight = dE.clientHeight - jQuery('#header').height();
        jQuery('#panel_scroll').css('height', newPanelHeight);
    },

    toggle: function(e) {
        jQuery('#panel').toggleClass('out');
        panelOut = !panelOut;
        createCookie('odyssey_theme_panelVisibility', (panelOut ? '1' : '0'), 30);
    },
}


odyssey.panel.init();

// jQuery.subscribe('post.update', odyssey.panel.refresh);
jQuery(window).resize(odyssey.panel.resize);


panelOut = readCookie('odyssey_theme_panelVisibility') == '1';
jQuery('#panel').toggleClass('out', true);





// cookies! yummy
function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}
