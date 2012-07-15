<h2><span class="pink">»</span>列表标注</h2>
<ul id="unit-list">			
	<p>总共有<?php echo count($tree->childs); ?>项数据，共分为<?php echo ceil(count($tree->childs)/$limit); ?>个任务</p>
<?php 
//echo '<pre>'; var_dump($tree); echo '</pre>';
	print_tree_as_list($tree, $limit); 
?>
</ul>


