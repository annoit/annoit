<div id="itembar">
	<ul>
		<li><span class="pink">»</span></li>
		<li><a href="<?php echo site_url('admin/resource_list'); ?>" alt="资源查看">资源查看</a></li>
		<li><a href="<?php echo site_url('admin/upload_resource'); ?>" alt="上传资源"><span class="selected">上传资源</span></a></li>
		<li><a href="<?php echo site_url('admin/confirm_logic_list'); ?>" alt="逻辑标注审核">逻辑标注审核</a></li>
		<li><a href="<?php echo site_url('admin/confirm_commit_list'); ?>" alt="正式标注审核">正式标注审核</a></li>
		<li><a href="<?php echo site_url('admin/resource_conf'); ?>" alt="创建">创建</a></li>
	</ul>	
</div>

<?php
// <h2><span class="pink">»</span>上传资源</h2>
?>

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
	echo form_open_multipart('admin/upload');
	echo form_label('资源标题', 'title');
	echo form_input('title', '');
	echo form_label('资源文件', 'resource_file');
	echo form_upload('userfile', '选择资源文件');
	echo form_label('标注方式选择', 'type');
	echo form_dropdown('type', $type_options);
	echo form_label('逻辑标注用户', 'logic_user');
	echo form_dropdown('users', $options);
		
	echo form_submit('submit', '确认上传');
	echo form_close();
	//var_dump($options);
	?>
</div><!-- end login_form-->