<div id="itembar">
	<ul>
		<li><span class="pink">»</span></li>
		<li><a href="<?php echo site_url('admin/new_member'); ?>" alt="新建用户"><span class="selected">新建用户</span></a></li>
		<li><a href="<?php echo site_url('admin/show_users'); ?>" alt="查看用户列表">查看用户列表</a></li>
	</ul>	
</div>

<div id="upload_form">	
    <?php
	if ($this->session->flashdata('upload_info')):
		$upload_info = $this->session->flashdata('upload_info');
	?>
	<span class="callback_info <?php echo $upload_info['type']; ?>">
		<?php echo strip_tags($upload_info['info']); ?>
	</span>
	<?php
	endif;
	echo form_open_multipart('admin/create_member');
	echo form_label('username', 'username');
	echo form_input('username', '');
	// echo form_label('first_name', 'first_name');
	// echo form_input('first_name', '');
	// echo form_label('last_name', 'last_name');
	// echo form_input('last_name', '');
	echo form_label('密码', 'psw');
	echo form_input('psw', '');
	echo form_label('电子邮箱', 'email_address');
	echo form_input('email_address', '');
	echo form_label('用户类型', 'user_type');
	echo form_dropdown('user_type', $options);
	echo form_submit('submit', '确认创建');
	echo form_close();
	//var_dump($options);
	?>

</div><!-- end login_form-->