odyssey.ich = ich;
        var tpl = jQuery('#photoblog_comments');
        jQuery.ajax({
            type: 'GET',
            url: tpl.attr('href'),
            async: false,
            success: function(response, status, request){
                odyssey.ich.addTemplate('render_comments', response);
            }
        });



odyssey.core.updateCurrentPost(jQuery.parseJSON(odyssey.posts));


// odyssey.init();