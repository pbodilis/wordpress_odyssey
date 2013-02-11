odyssey.core = {
    post: null,

    getPost: function(e, id) {
        var ajaxArgs = {
            action: 'odyssey_get_json_post',
        }
        if (typeof(id) !== 'undefined') {
            ajaxArgs.id = id;
        }
        jQuery.ajax({
            url:      odyssey.ajaxurl,
            dataType: 'json',
            data:     ajaxArgs,
        }).done(odyssey.core.updateLocalPost);
    },
    updateLocalPost: function(p) {
        odyssey.core.post = p;
        jQuery.publish('post.update');
    },

    prevPost: function(e) {
        if (odyssey.core.post.previous) {
        	odyssey.core.getPost(e, odyssey.core.post.previous.postID);
        }
    },
    nextPost: function(e) {
        if (odyssey.core.post.next) {
        	odyssey.core.getPost(e, odyssey.core.post.next.postID);
        }
    }
};

jQuery.subscribe('core.get',      odyssey.core.getPost);
jQuery.subscribe('core.previous', odyssey.core.prevPost);
jQuery.subscribe('core.next',     odyssey.core.nextPost);


