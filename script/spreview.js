/**
 * @author zhaosusen
 */
$(document).ready(function() {
	$('#structure-preview').click(function() {
		/*
		$.ajax({
				url: 'http://localhost/demo/admin/logic_preview/',
				type: 'POST',
				beforeSend: function() {
					$('#preview-wait').html('获取中...');
				},
				success: function(json) {
					
					if ($('#preview-wait').css('display') == 'inline') {
						$('#preview-wait').html(msg).css({'display': 'none'}).fadeIn(1000);
					}
				}
		});
		*/
		if ($('#preview-area').css('display') == 'none') {
			$('#preview-area').fadeIn().slideDown();
		} else {
			$('#preview-area').fadeOut().slideUp();
		}
	});
});
