odyssey.commentform = {
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.commentform.render);

        jQuery(document).on('submit', '#commentform', function(e) {
            e.preventDefault();

            jQuery.ajax({
                type: 'post',
                url:        jQuery('#commentform').attr('action'),
                data:       jQuery('#commentform').serialize(),
                dataType:  'json',
                beforeSend: function(xhr, settings) {
                    jQuery('#comment_status').html('<p>Processing...</p>');
                },
                error: function(xhr, test_status, errorThrown) {
                    if (500 == xhr.status) {
                        var response = xhr.responseText;
                        var text = response.split('<p>')[1].split('</p>')[0];
                        jQuery('#comment_status').html('<p class="ajax-error" >'+text+'</p>');
                    } else if (403 == xhr.status) {
                        jQuery('#comment_status').html('<p class="ajax-error" >Stop!! You are posting comments too quickly.</p>');
                    } else {
                        if ('timeout' == test_status)
                            jQuery('#comment_status').html('<p class="ajax-error" >Server timeout error. Try again.</p>');
                        else
                            jQuery('#comment_status').html('<p class="ajax-error" >Unknown error</p>');
                    }
                },
                success: function(data, test_status) {
                    if ('success' == data)
                        jQuery('#comment_status').html('<p class="ajax-success" >Thanks for your comment. We appreciate your response.</p>');
                    else
                        jQuery('#comment_status').html('<p class="ajax-error" >Please wait a while before posting your next comment</p>');
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




