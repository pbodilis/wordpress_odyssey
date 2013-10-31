odyssey.is_post=function() {
    return (typeof odyssey.posts != 'undefined');
};

if (odyssey.is_post()) {
    // wait for the document to be ready
    jQuery(document).ready(function() {
        odyssey.core.bootstrap(odyssey.posts);
    });
} else {
}



// odyssey.init();