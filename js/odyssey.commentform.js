odyssey.commentform = {
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.commentform.render);

        jQuery('#comment_form').submit(function() {
            //serialize and store form data in a variable
            var formdata = jQuery('#comment_form').serialize();
            //Add a status message
            jQuery('#comment_status').html('<p>Processing...</p>');
            //Extract action URL from commentform
            var formurl = jQuery('#comment_form').attr('action');
            //Post Form with data
            jQuery.ajax({
                type: 'post',
                url: formurl,
                data: formdata,
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    jQuery('#comment_status').html('<p class="wdpajax-error" >You might have left one of the fields blank, or be posting too quickly</p>');
                },
                success: function(data, textStatus){
                    if (data == 'success')
                        jQuery('#comment_status').html('<p class="ajax-success" >Thanks for your comment. We appreciate your response.</p>');
                    else
                        jQuery('#comment_status').html('<p class="ajax-error" >Please wait a while before posting your next comment</p>');
                        commentform.find('textarea[name=comment]').val('');
                }
            });
        });
    },
    render: function(e, post) {
        jQuery('#comment_post_ID').val(post.ID);
    },
}

odyssey.commentform.init();

// jQuery.subscribe('post.update', odyssey.panel.refresh);
// jQuery(window).load(function() {
//     panelOut = odyssey.cookie.read('odyssey_theme_panelVisibility') == '1';
//     jQuery('#panel').toggleClass('out', panelOut);
// });




