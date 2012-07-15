<h2><span class="pink">»</span>可用资源列表</h2>
<div id="list">
	<table id="resource_tbl">
		<tr class="tbl_head">
			<th class="tbl_id">资源编号</th>
			<th class="tbl_title">资源标题</th>
			<th class="tbl_status">资源状态</th>
			<th class="tbl_time">上传时间</th>
		</tr>
	<?php 
		$i=1;
		foreach ($list as $item): ?>
		<tr>
			<td><?php echo $item['id'] ?></td>
			<td>
				<a href="<?php echo site_url("user/show_resource/{$item['id']}"); ?>"><?php echo $item['title'] ?></a>
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
	<?php 
		$i++;
		endforeach;?>
	</table>
</div>
