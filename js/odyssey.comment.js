odyssey.comments = {
    status: {},
    post_comments_number: 0,
    
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.comments.render);

        if (odyssey.comment_form_ajax_enabled == 'false') {
            return;
        }
        odyssey.comments.status = jQuery('#comment_status');
        
        jQuery(document).on('click', 'a.replyto_cancel', odyssey.comments.replyto_cancel);

        jQuery(document).on('submit', '#commentform', function(e) {
            e.preventDefault();

            jQuery.ajax({
                type: 'post',
                url:        jQuery('#commentform').attr('action'),
                data:       jQuery('#commentform').serialize(),
                dataType:  'json',
                beforeSend: function(xhr, settings) {
                    odyssey.comments.status.html('Processing...');
                },
                error: function(xhr, test_status, error_thrown) {
                    if (500 == xhr.status) {
                        var response = xhr.responseText;
                        var text = response.split('<p>')[1].split('</p>')[0];
                        odyssey.comments.status.html(text);
                    } else if (403 == xhr.status) {
                        odyssey.comments.status.html('Stop! You are posting comments too quickly.');
                    } else {
                        if ('timeout' == test_status)
                            odyssey.comments.status.html('Server timeout error. Try again.');
                        else
                            odyssey.comments.status.html('Unknown error');
                    }
                },
                success: function(data, test_status, xhr) {
                    // update status
                    odyssey.comments.status.html('Comment posted!');

                    // display newly added comment
                    var comment = ich.render_comments({'comments': data});
                    comment.hide();
                    jQuery('#comment_list_item_' + jQuery('#comment_parent').val() + '>ul.comments_list').append(comment);
                    comment.slideDown();
                    jQuery('#comment_title').html(++(odyssey.comments.post_comments_number) + ' Comment(s)');

                    // clear form
                    odyssey.comments.replyto_cancel();
                    jQuery('#comment').val('');

                    // reload post comment
                    jQuery.publish('core.comments.reload');
                }
            });
        });
    },
    replyto: function(jthis, comment_id, author) {
        jQuery('.replying').toggleClass('replying', false);
        jthis.toggleClass('replying', true);

        jQuery('#comment_parent').val(comment_id);
        odyssey.comments.status.html('Replying to: ' + author + ' <a href="#" class="replyto_cancel">(cancel)</a>');
    },
    replyto_cancel: function() {
        jQuery('.replying').toggleClass('replying', false);
        jQuery('#comment_parent').val(0);
        odyssey.comments.status.html('');
    },
    render: function(e, post) {
        odyssey.comments.replyto_cancel();
        jQuery('#comment_post_ID').val(post.ID);
        odyssey.comments.post_comments_number = post.comments_number;
        jQuery('#comment_title').html(odyssey.comments.post_comments_number + ' Comment(s)');

        jQuery('#comment_list_item_0').html(ich.render_comments({'comments': post.comments}));
    },
}

odyssey.comments.init();
