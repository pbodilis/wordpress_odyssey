odyssey.history = {
	update: function(e) {
		post = odyssey.core.post;
        History.pushState({postID: post.postID}, 'state ' + post.postID, post.postUri);
	},
	popstate: function() {
		post = odyssey.core.post;
	    var state = History.getState(); // Note: We are using History.getState() instead of event.state
	    if (state.data.postID != odyssey.core.post.postID) {
     	    jQuery.publish('core.get', state.data.postID);
	    }
	}
};

jQuery.subscribe('post.update', odyssey.history.update);

History.Adapter.bind(window, 'popstate', odyssey.history.popstate);
   