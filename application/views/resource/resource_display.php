<?php
	if($res_type==1) $type_name='列表式';
		else $type_name='节点和关系';
?>

<?php if ($res_status == 1): ?>

<h2><span class="pink">»</span>步骤一：逻辑结构标注</h2>
<div class="stepbox">
	对资源<span class="res-title"><?php echo $res_title; ?></span>进行<a href="<?php echo site_url('annotate/logic/' . $res_id); ?>">逻辑结构标注</a>
</div>
<h2 class="disabled"><span class="pink">»</span>步骤二：<?php echo($type_name); ?>标注</h2>
<div class="stepbox disabled">
	对资源<span class="res-title"><?php echo $res_title; ?></span>进行<i><?php echo($type_name); ?>标注</i>
</div>

<?php elseif ($res_status == 2): ?>

<h2 class="disabled"><span class="pink">»</span>步骤一：逻辑结构标注</h2>
<div class="stepbox disabled">
	对资源<span class="res-title"><?php echo $res_title; ?></span>进行<i>逻辑结构标注</i>
</div>
<h2><span class="pink">»</span>步骤二：<?php echo($type_name); ?>标注</h2>
<div class="stepbox">
	对资源<span class="res-title"><?php echo $res_title; ?></span>进行<a href="<?php echo site_url('annotate/normal/' . $res_id); ?>"> <?php echo($type_name); ?>标注</a>
</div>

<?php endif; ?>
