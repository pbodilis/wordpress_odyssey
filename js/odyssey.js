(function($) {
    $(window).load(function () {
        $.publish('/window/first', odyssey.post);
    });

    $(window).resize(function () {
        $.publish('/window/resize', odyssey.post);
    });

})(jQuery);


// odyssey.init();