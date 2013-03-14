odyssey.is_post=function() {
    return (typeof odyssey.posts != 'undefined');
};

if (odyssey.is_post()) {
    // let's load all mustache templates
    jQuery('link[rel=template]').each(function(index, element) {
        var tpl = jQuery(this);
        jQuery.ajax({
            type: 'GET',
            async: false, // don't do anything before all templates are loaded TODO: find better
            url: tpl.attr('href'),
            success: function(response, status, request){
                ich.addTemplate(tpl.attr('id'), response);
            }
        });
    });


    // wait for the document to be ready
    jQuery(document).ready(function() {
        odyssey.core.update_current_post(jQuery.parseJSON(odyssey.posts));
    });
} else {
}



// odyssey.init();