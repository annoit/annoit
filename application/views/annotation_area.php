<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>标注系统标注演示</title>
	<link href="<?php echo base_url();?>/css/tool.css" rel="stylesheet" type="text/css" media="screen" /> 
	<script type="text/javascript" src="<?php echo base_url();?>/script/jquery-1.5.1.min.js"></script>
	<!--
	<script type="text/javascript" src="<?php echo site_url('site/get_existed_nodes_to_js/' . $this->session->userdata('res_id')); ?>"></script>
	-->
	<script type="text/javascript" src="<?php echo base_url();?>/script/annotate.js"></script>
<script type="text/javascript">
/* <![ CDATA[ */
$(document).ready(function() {
	$('#submit').click(function() {
		$.ajax({
			url: '<?php echo site_url('site/update_data'); ?>',
			type: 'POST',
			data: {nodes: nodeList, relations: rmatrix, ajax: 1},
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
/* ]]> */
</script>
  </head>
  <body>
  	<div id="all">
	  	<h1>Annotation Page::<span class="pink">Demo</span></h1>
	  	<div id="toolbar">
	  		<span class="username"><?php echo $this->session->userdata('username'); ?></span>您好，这里是标注的测试页面。
	  		<a href="<?php echo site_url('site/annotate_area'); ?>" alt="返回资源列表">返回资源列表</a>
	  		<?php echo anchor('login/logout', '登出系统'); ?>
	  		<input type="button" id="submit" value="保存" />
	  		<span id="timestamp" class="time"></span>
	  	</div>
	  	<div id="list" class="right_list"></div>
	    <div id="article_area">
			<div id="tool" class="invis"></div>
			<pre id="text_area"><?php echo $content; ?></pre>
		</div>
	</div>
  </body>
</html>