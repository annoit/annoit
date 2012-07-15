
$(document).ready(function() {
	$('#submitRes').click(function() {
		$.ajax({
			url: "<?php echo site_url('admin/new_resource_conf'); ?>",
			type: 'POST',			
			data: {
				data: {
					resPath: resPath,
					resContent: resContent,
					resource_title: resource_title,
					annatate_mode: annatate_mode,
					isLogic: isLogic,
					logic_user: logic_user,
					split_symbol: split_symbol,	
					ntype: ntype,	//节点类型
					attachAttr: attachAttr,	//节点属性	
					rtype: rtype,	//关系类型
					adjacentOffset: adjacentOffset, //adjacentOffset之间的关系
				},
				ajax: 1
			},
			beforeSend: function() {
				$('#submitRes').attr({'disabled': 'disabled'});
				$('#timestamp').html('保存中...');
			},
			success: function(msg) {
				if ($('#timestamp').css('display') == 'inline') {
					$('#timestamp').html(msg).css({'display': 'none'}).fadeIn(1000);
				}
				$('#submitRes').attr({'disabled': null});
				if(msg.indexOf('创建成功')>=0){
					$("#preview").hide();
					$("#finish").show();
				}
				console.log("传送完成");	
			}
		});
	});
});