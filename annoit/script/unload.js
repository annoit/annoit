/**
 * @author zhaosusen
 */
$(document).ready(function() {
	var rl = $('#list');
	var top = 110;
	//alert(parseInt($('body').css('height'), 10) - $(window).height());
	$(window).scroll(function() {
		var offset = $(document).scrollTop();
		if (offset > top) {
			rl.css({
				'top' : '0px'
			});
		} else {
			rl.css({
				'top' : (top - offset) + 'px'
			});
		}
	});
	
	window.onbeforeunload = function() {
		return '页面即将关闭，确认离开吗？';
	}
	
});
