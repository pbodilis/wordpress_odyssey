odyssey.commentform = {
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.commentform.render);

        if (odyssey.comment_form_ajax_enabled == 'false') {
            return;
        }
        
        jQuery(document).on('submit', '#commentform', function(e) {
            e.preventDefault();

            jQuery.ajax({
                type: 'post',
                url:        jQuery('#commentform').attr('action'),
                data:       jQuery('#commentform').serialize(),
                dataType:  'json',
                beforeSend: function(xhr, settings) {
                    jQuery('#comment_status').html('Processing...');
                },
                error: function(xhr, test_status, error_thrown) {
                    if (500 == xhr.status) {
                        var response = xhr.responseText;
                        var text = response.split('<p>')[1].split('</p>')[0];
                        jQuery('#comment_status').html(text);
                    } else if (403 == xhr.status) {
                        jQuery('#comment_status').html('Stop! You are posting comments too quickly.');
                    } else {
                        if ('timeout' == test_status)
                            jQuery('#comment_status').html('Server timeout error. Try again.');
                        else
                            jQuery('#comment_status').html('Unknown error');
                    }
                },
                success: function(data, test_status, xhr) {
                    jQuery('#comment_status').html('Comment posted!');
                    jQuery('#photoblog_comments').Chevron('render', data, function(result) {
console.log(result);
                        jQuery('#comments').append(result);
                    });
                }
            });
        });
    },
    render: function(e, post) {
        jQuery('#comment_post_ID').val(post.ID);

        jQuery('#photoblog_comments').Chevron('render', post, function(result) {
console.log(result);
            jQuery('#comments').replaceWith(result);
        });
    },
}

odyssey.commentform.init();

// jQuery.subscribe('post.update', odyssey.panel.refresh);
// jQuery(window).load(function() {
//     panelOut = odyssey.cookie.read('odyssey_theme_panelVisibility') == '1';
//     jQuery('#panel').toggleClass('out', panelOut);
// });




