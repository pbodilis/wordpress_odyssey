odyssey.core = {
    post: null,

    getPost: function(id) {
        var ajaxArgs = {
            action: 'odyssey_get_json_post',
        }
        if (typeof(id) !== 'undefined') {
            ajaxArgs.id = id;
        }
        $.ajax({
            url:      odyssey.ajaxurl,
            dataType: 'json',
            data:     ajaxArgs,
        }).done(odyssey.core.updateLocalPost);
    },
    updateLocalPost: function(p) {
        odyssey.core.post = p;
        jQuery.publish('post.update');
    },

    nextPost: function() {
    },
    prevPost: function() {
    }
};

// 
// var odyssey = {
//     var post;
// 
//     // constructor
//     var odyssey = function () {
//     };
// 
//     // prototype
//     odyssey.prototype = {
//         constructor: odyssey,
//         init: function (p) {
//             post = p;
//         },
//         getPost: function() {
//             return post;
//         }
//     };
// 
//     // return module
//     return odyssey;
// };
// 
// (function($) {
//     $.extend({
//         /*
//             * cc means "conference common"
//             */
//         CC : {
// 
// })(jQuery);