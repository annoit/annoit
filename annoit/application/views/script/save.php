$(document).ready(function() {
	$('#submit').click(function() {
		$.ajax({
			url: '<?php echo site_url('annotate/update_logic_nodes'); ?>',
			type: 'POST',
			data: {nodes: nodeList, ajax: 1},
			beforeSend: function() {
				$('#timestamp').html('保存中...');
			},
			success: function(msg) {
				if ($('#timestamp').css('display') == 'inline') {
					$('#timestamp').html(msg).css({'display': 'none'}).fadeIn(1000);
				}
			}
		});
	});
});