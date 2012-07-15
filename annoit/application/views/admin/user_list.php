<div id="itembar">
	<ul>
		<li><span class="pink">»</span></li>
		<li><a href="<?php echo site_url('admin/new_member'); ?>" alt="新建用户">新建用户</a></li>
		<li><a href="<?php echo site_url('admin/show_users'); ?>" alt="查看用户列表"><span class="selected">查看用户列表</span></a></li>
	</ul>	
</div>

<div id="list">
	<table id="resource_tbl">
		<tr class="tbl_head">
			<th class="tbl_id">用户id</th>
			<th class="tbl_title">用户名</th>
			<th class="tbl_status">邮件地址</th>
			<th class="tbl_time">用户类型</th>
		</tr>
	<?php foreach ($data as $item): ?>
		<tr>
			<td><?php echo $item['id']; ?></td>
			<td>
				<?php echo $item['username']; ?>
			</td>
			<td><?php echo $item['email_address']; ?></td>
			<td>
				<?php if ($item['role'] == 0): ?>
					用户
				<?php elseif ($item['role'] == 1): ?>
					管理员
				<?php endif;?>
			</td>
		</tr>
	<?php endforeach;?>			
	</table>	
</div>
