odyssey.core = {
    posts: {},

    init: function() {
        jQuery.subscribe('core.get',             odyssey.core.go_to_post);
        jQuery.subscribe('core.previous',        odyssey.core.previous);
        jQuery.subscribe('core.next',            odyssey.core.next);
        jQuery.subscribe('core.random',          odyssey.core.random);
        jQuery.subscribe('core.comments.reload', odyssey.core.reload_comments);
    },

    get_current_post_id: function() {
        return odyssey.core.posts.current_ID;
    },
    get_current_post: function() {
        return odyssey.core.posts[odyssey.core.posts.current_ID];
    },

    /**
     * retrieves the post with the current id, or the latest one if none was given
     * along with the adjacent posts
     */
    go_to_post: function(e, id, adjacent) {
        adjacent  = adjacent || 'both';
        if (odyssey.core.posts[id]) { // do we have the current post in cache ?
            // if it's caching, move the state to pending
            if (odyssey.core.posts[id] === 'caching') odyssey.core.posts[id] = 'pending';
            // if it's pending, nothing to do
            if (odyssey.core.posts[id] === 'pending') return;
            odyssey.core.posts.current_ID = id; // just set the current cursor to the given id
            odyssey.core.publish_update(); // notify all views the current post needs to be displayed
            
            // cache the adjacent of this post if we don't have it already
            var current = odyssey.core.get_current_post();
            if (current.previous_ID && typeof odyssey.core.posts[current.previous_ID] == 'undefined') {
                odyssey.core.posts[current.previous_ID] = 'caching';
                odyssey.core.retrieve_post(current.previous_ID, 'previous', odyssey.core.cache_posts);
            }
            if (current.next_ID && typeof odyssey.core.posts[current.next_ID] == 'undefined') {
                odyssey.core.posts[current.next_ID] = 'caching';
                odyssey.core.retrieve_post(current.next_ID, 'next', odyssey.core.cache_posts);
            }
        } else { // retrieve the post
            if ('random' != id) {
                odyssey.core.posts[id] = 'pending';
            }
            odyssey.core.retrieve_post(id, adjacent, odyssey.core.update_current_post);
        }
    },
    retrieve_post: function(id, adjacent, retrieve_post_callback) {
        var ajaxArgs = {
            action:     'odyssey_get_json_post_and_adjacents',
            adjacent:   adjacent || 'both',
            post_nonce: odyssey.post_nonce
        }
        if (typeof(id) !== 'undefined') {
            ajaxArgs.id = id;
        }
        jQuery.ajax({
            url:      odyssey.ajaxurl,
            dataType: 'json',
            data:     ajaxArgs/*,*/
//             beforeSend: function() {
//                 jQuery.publish('post.loading');
//             },
//             complete: function() {
//                 jQuery.publish('post.loaded');
//             }
        }).done(function(r) {
            if (!r) return;
            odyssey.post_nonce = r.post_nonce;
            retrieve_post_callback(r.posts);
        });
    },
    bootstrap: function(p) {
        odyssey.core.posts = p;
        jQuery.publish('bootstrap', odyssey.core.get_current_post());
    },
    update_current_post: function(p) {
        for (var i in p) {
            odyssey.core.posts[i] = p[i];
        }
        odyssey.core.publish_update();
    },
    cache_posts: function(p) {
        for (var i in p) {
            if (odyssey.core.posts[i] === 'pending') {
                odyssey.core.current_ID = i;
            }
            if (i != 'current_ID') {
                odyssey.core.posts[i] = p[i];
            }
        }
    },
    publish_update: function() {
        var current = odyssey.core.get_current_post();
        jQuery.publish('post.update', current);
    },
    reload_comments: function(e) {
        odyssey.core.posts[id].comments = jQuery('#responses>ol.comment-list').html();
    },
    previous: function(e) {
        var current = odyssey.core.posts[odyssey.core.posts.current_ID];
        if (current.previous_ID) {
            odyssey.core.go_to_post(e, current.previous_ID, 'previous');
        }
    },
    next: function(e) {
        var current = odyssey.core.posts[odyssey.core.posts.current_ID];
        if (current.next_ID) {
            odyssey.core.go_to_post(e, current.next_ID, 'next');
        }
    },
    random: function(e) {
        odyssey.core.go_to_post(e, 'random');
    }
};

odyssey.core.init();
