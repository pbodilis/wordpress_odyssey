odyssey.comments = {
    status: {},
    post_comments_number: 0,
    
    bootstrap: function(e, post) {
        odyssey.comments.post_comments_number = post.comments_number;
    },
    init: function() {
        // update the panel on post update
        jQuery.subscribe('post.update', odyssey.comments.render);
        jQuery.subscribe('bootstrap', odyssey.comments.bootstrap);

        if (odyssey.comment_form_ajax_enabled == 'false') {
            return;
        }
        odyssey.comments.status = jQuery('#comment_status');
        
        jQuery(document).on('submit', '#commentform', function(e) {
            e.preventDefault();

            jQuery.ajax({
                type: 'post',
                url:        jQuery('#commentform').attr('action'),
                data:       jQuery('#commentform').serialize(),
                dataType:  'html',
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
                    
                    var parent_id = jQuery('#comment_parent').val();
                    var comment_list;
                    if (parent_id == 0) {
                        comment_list = jQuery('#responses ol.comment-list');
                    } else {
                        if (jQuery('#comment-' + parent_id + ' ol.children').length == 0) {
                            jQuery('#comment-' + parent_id).append('<ol class="children"></ol>');
                        }
                        comment_list = jQuery('#comment-' + parent_id + ' ol.children');
                    }
                    var comment = jQuery(data);
                    comment.hide().appendTo(comment_list).fadeIn();

                    // clear form
                    jQuery('#comment').val('');
                    jQuery('#cancel-comment-reply-link').click();

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
    render: function(e, post) {
        jQuery('#responses>ol.comment-list').html(post.comments);
        var comments_count = jQuery('li.comment').length;
        jQuery('h3.comments-title').html(comments_count + ' Comment' + ((comments_count != 1) ? 's' : ''));
    },
}

odyssey.comments.init();

