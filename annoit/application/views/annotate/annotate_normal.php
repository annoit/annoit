<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="<?php echo base_url();?>css/user_general.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="<?php echo base_url();?>/css/annotate.css" rel="stylesheet" type="text/css" media="screen" />
<?php
if (isset($active_scripts)):
	foreach ($active_scripts as $path):
?>
	<script type="text/javascript" src="<?php echo $path; ?>"></script>
<?php
	endforeach;
endif;
?>


<?php
if (isset($config_scripts)):
	foreach ($config_scripts as $script):
?>
	<script type="text/javascript" src="<?php echo base_url().$script; ?>"></script>
<?php
	endforeach;
endif;
?>



<?php
if (isset($scripts)):
	foreach ($scripts as $script):
?>
	<script type="text/javascript" src="<?php echo base_url() . 'script/' . $script; ?>"></script>
<?php
	endforeach;
endif;
?>

	<script type="text/javascript">
		<?php $this->load->view('script/normal_save'); ?>
	</script>
	<title>用户页面模板</title>
</head>
<body id="annotate-panel">
	<div id="header">
		<div id="logo">
			<img src="<?php echo base_url();?>img/logo/a.gif" height="40" /><img src="<?php echo base_url();?>img/logo/n.gif" height="40" /><img src="<?php echo base_url();?>img/logo/n.gif" height="40" /><img src="<?php echo base_url();?>img/logo/o.gif" height="40" /><img src="<?php echo base_url();?>img/logo/i.gif" height="40" /><img src="<?php echo base_url();?>img/logo/t.gif" height="40" />
		</div>
		<div id="logged-info">
			<ul>
				<li>以<a href="<?php echo site_url('user'); ?>" alt="用户主页"><?php echo $this->session->userdata('username'); ?></a>登入</li>
				<li><a href="#" alt="#">FAQ</a></li>
				<li><a href="<?php echo site_url('login/logout'); ?>" alt="logout">登出</a></li>
			</ul>
		</div>
		<div id="toolbar">
			<ul>
				<li class="first"><a href="<?php echo site_url('user'); ?>" alt="用户首页">用户首页</a></li>
				<li><a href="<?php echo site_url('user/resource_list'); ?>" alt="可用资源列表">可用资源列表</a></li>
				<!--<li><a href="<?php echo site_url('user/get_resource'); ?>" alt="资源获取">资源获取</a></li>
				<li><a href="<?php echo site_url('user/progress_monitor'); ?>" alt="进度监测">进度监测</a></li>-->
			</ul>
		</div>
	</div>
	<div id="main">
		
		<div id="annotate_tool">
			<input type="button" id="submit" value="保存" />
	  		<span id="timestamp" class="time"></span>
		</div>
		<div id="list" class="right_list"></div>
	    <div id="article_area">
	    	<h2><span class="pink"><?php echo $res_title; ?> »</span><?php echo $unit_name; ?></h2>
			<div id="tool" class="invis"></div>
			<pre id="text_area"><?php echo $unit_content; ?></pre>
		</div>
	</div>
	<div id="footer">
		<div id="copyright">
			Copyright &copy; 2011 Text Mining Group.
			Powered by <a href="http://codeigniter.org" alt="CodeIgniter">CodeIgniter</a> &amp;
			<a href="http://jquery.com">jQuery</a>.
		</div>
	</div>
</body>
</html>