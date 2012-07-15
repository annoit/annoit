<h2><span class="pink">»</span>用户概况</h2>
<p>标注用户</p>
<h2><span class="pink">»</span>任务概况</h2>
<dl>
	<dt>最近的10个任务<span class="date">测试</span></dt>
	<dd id="task-list">
		<ul>
			<?php foreach ($task as $item): ?>
			<li><?php echo $item['name']; ?>
			<span><span class="task-state">资源长度 <?php echo $item['length']; ?></span>
			<a href="<?php echo site_url("annotate/task/{$item['id']}"); ?>">标注任务</a></span>
			</li>
			<?php endforeach; ?>
		</ul>
	</dd>
</dl>
