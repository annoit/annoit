<div id="itembar">
	<ul>
		<li><span class="pink">»</span></li>
		<li><a href="<?php echo site_url('admin/resource_list'); ?>" alt="资源查看"><span class="selected">资源查看</span></a></li>
		<li><a href="<?php echo site_url('admin/confirm_logic_list'); ?>" alt="逻辑标注审核">逻辑标注审核</a></li>
		<li><a href="<?php echo site_url('admin/resource_conf'); ?>" alt="创建资源">创建资源</a></li>		
	</ul>	
</div>


<div id="list">
	<table id="resource_tbl">
		<tr class="tbl_head">
			<th class="tbl_id">资源编号</th>
			<th class="tbl_title">资源标题</th>
			<th class="tbl_status">资源状态</th>
			<th class="tbl_time">上传时间</th>
		</tr>
	<?php foreach ($list as $item): ?>
		<tr>
			<td><?php echo $item['id'] ?></td>
			<td>
				<a href="<?php echo site_url("admin/show_logic/{$item['id']}"); ?>"><?php echo $item['title'] ?></a>
			</td>
			<td><span class="status-<?php echo $item['status']; ?>">
				<?php if ($item['status'] == 0): ?>
					未分配
				<?php elseif ($item['status'] == 1): ?>
					逻辑标注中
				<?php elseif ($item['status'] == 2): ?>
					正式标注中
				<?php elseif ($item['status'] == 3): ?>
					标注完成
				<?php endif;?>
			</span></td>
			<td><?php echo substr($item['upload_time'], 0, 10); ?></td>
		</tr>
	<?php endforeach;?>
	</table>
</div>