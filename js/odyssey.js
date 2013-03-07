// let's load all mustache templates
jQuery("link[rel=template]").each(function(index, element) {
    var tpl = jQuery(this);
    jQuery.ajax({
        type: 'GET',
        url: tpl.attr('href'),
        success: function(response, status, request){
            ich.addTemplate(tpl.attr('id'), response);
        }
    });
});


window.onload = function() {
    odyssey.core.updateCurrentPost(jQuery.parseJSON(odyssey.posts));
};



// odyssey.init();