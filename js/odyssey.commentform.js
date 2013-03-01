odyssey.commentform = {
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.commentform.render);

        jQuery(document).on('submit', '#comment_form', function(e) {
            e.preventDefault();
//             return false;
//         jQuery('#comment_form').submit(function() {
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
                dataType: 'json',
                error: function(xhr, textStatus, errorThrown){
                    if(xhr.status==500){
                        var response=xhr.responseText;
                        var text=response.split('<p>')[1].split('</p>')[0];
                        jQuery('#comment_status').html('<p class="wdpajax-error" >'+text+'</p>');
                    }
                    else if(xhr.status==403){
                        jQuery('#comment_status').html('<p class="wdpajax-error" >Stop!! You are posting comments too quickly.</p>');
                    }
                    else{
                        if(textStatus=='timeout')
                            jQuery('#comment_status').html('<p class="wdpajax-error" >Server timeout error. Try again.</p>');
                        else
                            jQuery('#comment_status').html('<p class="wdpajax-error" >Unknown error</p>');
                    }
                },
                success: function(data, textStatus){
                    if (data == 'success')
                        jQuery('#comment_status').html('<p class="ajax-success" >Thanks for your comment. We appreciate your response.</p>');
                    else
                        jQuery('#comment_status').html('<p class="ajax-error" >Please wait a while before posting your next comment</p>');
                        jQuery('#comment_form textarea[name=comment]').val('');
                }
            }).done(function(r) {
                console.log(r);
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




