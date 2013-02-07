//(function(o) {
//    o.post = jQuery.parseJSON(o.postStr);
//}(odyssey));

(function($) {
	$.ajax({
		url: odyssey.ajaxurl,
		dataType: 'json',
		data:{
            'action': 'odyssey_get_json_post',
        },
	}).done(function() {
		var post = $.parseJSON(post);
		$.publish('post.update', post);
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