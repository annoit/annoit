<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="<?php echo base_url();?>/css/tool.css" rel="stylesheet" type="text/css" media="screen" /> 
    <title>可用资源列表</title>
  </head>
  <body>
  	<div id="all">
	  	<h1>Resource List::<span class="pink">Demo</span></h1>
	  	<div id="toolbar">
	  		<?php echo $bla; ?>
	  		<span class="username"><?php echo $this->session->userdata('username'); ?></span>您好，这里是您的资源页面。
	  		<?php echo anchor('login/logout', '登出系统'); ?>
	  	</div>
	  	<div id="list">
	  		<ul>
	  		<?php foreach ($list as $item): ?>
	  			<li>
	  				<a href="<?php echo site_url("site/get_resource/{$item['id']}"); ?>" rel="资源">
	  					资源编号：<?php echo $item['id']; ?>
	  				</a>
	  				<br />
	  				资源创建于：[<?php echo $item['created_date']; ?>]
	  				<br />
	  				资源状态：标注中
	  			</li>
	  		<?php endforeach;?>
	  		</ul>
	  		<ul>
				<?php print_tree($tree); ?>
	  		</ul>
	  	</div>
	</div>
  </body>
</html>