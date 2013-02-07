(function($) {
    $.extend({
        core: {
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
                }).done($.core.updateLocalPost);
            },
            updateLocalPost: function(p) {
                $.core.post = p;
                $.publish('post.update');
            },

            nextPost: function() {
            },
            prevPost: function() {
            }
        }
    });
})(jQuery);

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