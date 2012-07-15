<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link href="<?php echo base_url();?>css/user_general.css" rel="stylesheet" type="text/css" media="screen" />
<?php
if (isset($scripts)):
	foreach ($scripts as $script):
?>
	<script type="text/javascript" src="<?php echo base_url() . 'script/' . $script;?>"></script>
<?php
	endforeach;
endif;
?>
	<title>管理员页面模板</title>
</head>
<body id="admin-panel">
	<div id="header">
		<div id="logo">
			<img src="<?php echo base_url();?>img/logo/a.gif" height="40" />
			<img src="<?php echo base_url();?>img/logo/n.gif" height="40" />
			<img src="<?php echo base_url();?>img/logo/n.gif" height="40" />
			<img src="<?php echo base_url();?>img/logo/o.gif" height="40" />
			<img src="<?php echo base_url();?>img/logo/i.gif" height="40" />
			<img src="<?php echo base_url();?>img/logo/t.gif" height="40" />
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
				<li class="first"><a href="<?php echo site_url('admin'); ?>" alt="管理员首页">管理员首页</a></li>				
				<li><a href="<?php echo site_url('admin/new_member'); ?>" alt="用户管理">用户管理</a></li>
				<li><a href="<?php echo site_url('admin/resource_conf'); ?>" alt="资源管理">资源管理</a></li>
				<!--<li><li><a href="<?php echo site_url('admin/confirm_logic_list'); ?>" alt="确认1pass标注结果">逻辑标注审核</a></li>
				<li><a href="<?php echo site_url('admin/upload_resource'); ?>" alt="上传资源">上传资源</a></li>
				<li><a href="<?php echo site_url('admin/list_user'); ?>" alt="用户管理">用户管理</a></li>
				<li><a href="<?php echo site_url('admin/progress_monitor'); ?>" alt="进度监测">进度监测</a></li>-->
			</ul>
		</div>
	</div>
	<div id="main">
		<div id="news">
			<h2><span class="pink">»</span>工作通告</h2>
			<p>这里是一些经常性更新的工作信息，有助于您更好的使用本系统完成工作。</p>
			<p>测试</p>
			<dl>
				<dt>系统概览<span class="date">发布于 11年3月25日</span></dt>
				<dd>术语与知识元标注</dd>
			</dl>
			<dl>
				<dt>使用提示<span class="date">发布于 11年3月24日</span></dt>
				<dd>1.上传资源</dd>
				<dd>2.等待用户标注逻辑结构</dd>
				<dd>3.确认逻辑结构</dd>
				<dd>4.用户标注</dd>
			</dl>
		</div>
		<div id="maincontent">
			<?php
				if (isset($data)) {
					$this->load->view($content, $data);
				} else {
					$this->load->view($content);
				}
			?>
			</div>
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