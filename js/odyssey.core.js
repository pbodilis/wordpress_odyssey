var odyssey = (function () {
    var post;

    // constructor
    var odyssey = function () {
    };

    // prototype
    odyssey.prototype = {
        constructor: odyssey,
        init: function (p) {
        	post = p;
        },
        getPost: function() {
        	return post;
        }
    };

    // return module
    return odyssey;
})();

