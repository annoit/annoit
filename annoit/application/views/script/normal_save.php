$(document).ready(function() {
	$('#submit').click(function() {
		$.ajax({
			url: '<?php echo site_url('annotate/update_all'); ?>',
			type: 'POST',
			data: {nodes: nodeList, relations: rmatrix, ajax: 1},
			beforeSend: function() {
				$('#submit').attr({'disabled': 'disabled'});
				$('#timestamp').html('保存中...');
			},
			success: function(msg) {
				if ($('#timestamp').css('display') == 'inline') {
					$('#timestamp').html(msg).css({'display': 'none'}).fadeIn(1000);
				}
				$('#submit').attr({'disabled': null});
				//alert(msg);
			}
		});
	});
});