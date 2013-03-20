odyssey.history = {
    postID: null,

    init: function() {
        if (odyssey.is_post()) {
            jQuery.subscribe('post.update', odyssey.history.update);
            History.Adapter.bind(window, 'popstate', odyssey.history.popstate);
        }
    },

    update: function(e, post) {
        odyssey.history.postID = post.ID;
        History.pushState({postID: post.ID}, post.title, post.url);
    },
    popstate: function() {
        var state = History.getState(); // Note: We are using History.getState() instead of event.state
        if (state.data.postID != odyssey.history.postID) {
            jQuery.publish('core.get', state.data.postID);
        }
    }
};

odyssey.history.init();
