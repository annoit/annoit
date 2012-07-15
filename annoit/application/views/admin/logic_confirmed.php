<h2><span class="pink">»</span>逻辑确认</h2>
<?php if ($confirmed): ?>
<p>已确认！<a href="<?php echo site_url('admin'); ?>">返回</a></p>
<?php else: ?>
<p>确认不成功或者已确认过！<a href="<?php echo site_url('admin'); ?>">返回</a></p>
<?php endif; ?>
