odyssey.core = {
    posts: {},

    getCurrentPostId: function() {
        return odyssey.core.posts.currentID;
    },
    getCurrentPost: function() {
        var current = odyssey.core.posts[odyssey.core.posts.currentID];
        if (current.previousID) { current.previous = odyssey.core.posts[current.previousID]; }
        if (current.nextID)     { current.next     = odyssey.core.posts[current.nextID];     }
        return current;
    },

    /**
     * retrieves the post with the current id, or the latest one if none was given
     * along with the adjacent posts
     */
    retrievePosts: function(e, id) {
        if (odyssey.core.posts[id]) { // do we have the current post in cache ?
            odyssey.core.posts.currentID = id; // just set the current cursor to the given id

            odyssey.core.postNotifyAll(); // notify all views the current post needs to be displayed
        } else { // retrieve the post
            var ajaxArgs = {
                action:     'odyssey_get_json_post_and_adjacents',
                post_nonce: odyssey.post_nonce
            }
            if (typeof(id) !== 'undefined') {
                ajaxArgs.id = id;
            }
            jQuery.ajax({
                url:      odyssey.ajaxurl,
                dataType: 'json',
                data:     ajaxArgs,
            }).done(function(r) {
                odyssey.post_nonce = r.nonce;
                odyssey.core.updateCurrentPost(r.posts);
            });
        }
    },
    updateCurrentPost: function(p) {
        for (var i in p) {
            odyssey.core.posts[i] = p[i];
        }
        odyssey.core.postNotifyAll();
    },
    postNotifyAll: function() {
        jQuery.publish('post.update', odyssey.core.getCurrentPost());
        
        // now, while the current post is loaded by the rest of the application
        // get the post beyond the current adjacent's post.
        var current = odyssey.core.getCurrentPost();
        if (current.previous && current.previous.previousID) { odyssey.core.cachePost(current.previous.previousID); }
        if (current.next     && current.next.nextID)         { odyssey.core.cachePost(current.next.nextID); }
    },

    /**
     * retrieves the post with the given id
     */
    cachePost: function(id) {
        if (odyssey.core.posts[id]) {
        } else {
            var ajaxArgs = {
                action:     'odyssey_get_json_post',
                id:         id,
                post_nonce: odyssey.post_nonce
            }
            jQuery.ajax({
                url:      odyssey.ajaxurl,
                dataType: 'json',
                data:     ajaxArgs,
            }).done(function(r) {
                odyssey.core.posts[id] = r.post;
                odyssey.core.nonce     = r.nonce;
            });
        }
    },

    prevPost: function(e) {
        var current = odyssey.core.posts[odyssey.core.posts.currentID];
        if (current.previousID) {
            odyssey.core.retrievePosts(e, current.previousID);
        }
    },
    nextPost: function(e) {
        var current = odyssey.core.posts[odyssey.core.posts.currentID];
        if (current.nextID) {
            odyssey.core.retrievePosts(e, current.nextID);
        }
    }
};

jQuery.subscribe('core.get',      odyssey.core.retrievePosts);
jQuery.subscribe('core.previous', odyssey.core.prevPost);
jQuery.subscribe('core.next',     odyssey.core.nextPost);


