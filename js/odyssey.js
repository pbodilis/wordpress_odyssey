(function($) {
/*    $(window).load(function () {
        $.publish('post.update', odyssey.post);
    });
*/
    $(window).resize(function () {
        $.publish('/window/resize', odyssey.post);
    });

})(jQuery);


// odyssey.init();