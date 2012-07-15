<h2><span class="pink">»</span>资源“<?php echo $resource['title']; ?>”逻辑节点列表</h2>
<div id="list">
	<table id="resource_tbl">
		<tr class="tbl_head">
			<th class="tbl_id">节点编号</th>
			<th class="tbl_title">节点标题</th>
			<th class="tbl_status">节点结构</th>
			<th class="tbl_time">父节点编号</th>
		</tr>
	<?php for ($i = 0; $i < count($calist) - 1; $i++): $item = $calist[$i]; ?>
		<tr>
			<td><?php echo $item['id'] ?></td>
			<td>
				<a href="#"><?php echo $item['title'] ?></a>
			</td>
			<td><span class="structure-<?php echo $item['type']; ?>">
				<?php if ($item['type'] == 'nt_0'): ?>
					章
				<?php elseif ($item['type'] == 'nt_1'): ?>
					节
				<?php elseif ($item['type'] == 'nt_2'): ?>
					小节
				<?php elseif ($item['type'] == 'nt_3'): ?>
					小小节
				<?php endif; ?>
			</span></td>
			<td><?php echo $item['parent_id']; ?></td>
		</tr>
	<?php endfor;?>
	</table>
</div>
<!--
<div id="display-structure">
	<input id="structure-preview" type="button" value="树形结构预览" />
</div>
-->
<h2><span class="pink">»</span>资源“<?php echo $resource['title']; ?>”逻辑结构预览</h2>
<div id="preview-area">
	<!--
	<table id="preview_tbl">
		<tr class="tbl_head">
			<th class="tbl_id">节点编号</th>
			<th class="tbl_title">节点标题</th>
			<th class="tbl_parentid">父节点编号</th>
		</tr>
	<?php for ($i = 0; $i < count($calist) - 1; $i++): $item = $calist[$i]; ?>
		<tr>
			<td><?php echo $item['id'] ?></td>
			<td><?php echo $item['title'] ?></td>
			<td><?php echo $item['parent_id'] ?></td>
		</tr>
	<?php endfor;?>
	</table>
	-->
	<ul>
	<?php print_tree($tree); ?>
	</ul>
	<span id="confirm_tool">
		<a href="<?php echo site_url('admin/confirm_logic/' . $resource['id']); ?>" alt="确认逻辑关系">
			确认逻辑关系
		</a>
	</span>
</div>