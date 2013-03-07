// let's load all mustache templates
jQuery('link[rel=template]').each(function(index, element) {
    var tpl = jQuery(this);
    jQuery.ajax({
        type: 'GET',
        async: false,
        url: tpl.attr('href'),
        success: function(response, status, request){
            ich.addTemplate(tpl.attr('id'), response);
        }
    });
});


// jQuery(window).load(function() {
    odyssey.core.updateCurrentPost(jQuery.parseJSON(odyssey.posts));
// });



// odyssey.init();