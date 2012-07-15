<script type="text/javascript">
	<?php $this->load->view('script/conf_save'); ?>
</script>

<div id="itembar">
	<ul>
		<li><span class="pink">»</span></li>
		<li><a href="<?php echo site_url('admin/resource_list'); ?>" alt="资源查看">资源查看</a></li>
		<li><a href="<?php echo site_url('admin/confirm_logic_list'); ?>" alt="逻辑标注审核">逻辑标注审核</a></li>
		<li><a href="<?php echo site_url('admin/resource_conf'); ?>" alt="创建资源"><span class="selected">创建资源</span></a></li>
	</ul>	
</div>


<div>	
    <?php
		if ($this->session->flashdata('upload_info')):
			$upload_info = $this->session->flashdata('upload_info');
	?>
	<span class="callback_info <?php echo $upload_info['type']; ?>">
		<?php echo strip_tags($upload_info['info']); ?>
	</span>
	<?php
	endif;
	?>

	<div id="new_resourceDiv">
		<div id="resourceDiv" class="step" >
			<div>
				<label>上传资源:</label>
					<input id="fileUpload" type="file" onchange="uploadCheck()"/>
				<label class="erro_info" id="fileStatus"></label>					
			</div>		
			<div>
				<label>资源标题:</label>	
					<input type="text" id="resource_title" value="" onblur="titleCheck()">
				<label class="erro_info" id="titleStatus"></label>						
			</div>
			<div>
				<label>标注模式:</label>	
					<select id="annatate_mode" onchange="modeChange(this)">
						<option value="NR" selected="true">节点关系</option>
						<option value="list">列表标注</option>
					</select>
			</div>			
			<div>
				<label>人工标注文本逻辑结构?:</label>
					<select id="NR_logic_mode" onchange="logicChange(this)">
						<option value="yes" selected="true">是</option>
						<option value="no">否</option>
					</select>	
			</div>
			<div id="logic_user_div">
				<label>逻辑标注用户:</label>
					<select id="logic_user">
						<?php
							foreach ($user_options as $key => $user) {
						?>
								<option value="<?php echo $key; ?>" > <?php echo $user; ?></option>
						<?php
							}						
						?>
					</select>	
			</div>
			<div id="split_div">
				<label>正则划分符：</label>	
					<input type="text" id="split_symbol" value="">
				<label class="erro_info" id="splitStatus"></label>	
			</div>												
			<div class="stepButtons">
				<input type="button" id="next1" value="下一步" onclick="oneNext()">		
			</div>				
		</div>
		
		
						
		<div id="confDiv" class="step">			
			<h3 class="h3Title">节点和关系配置</h3>
			<h4>载入已有模板:</h4>
				<a class="NRTemplate" name="主题图" onclick="loadTemplate(this)">主题图</a>
			<h4>手工配置</h4>
			<div id="nodeConf">				
				<div class="nodeDiv">					
					<div class="nodeHead">
						<a class="hideNode" onclick="hideNodeDiv(this)">-</a>
						<a class="showNode" onclick="showNodeDiv(this)">+</a>
						<label class="nodeL">节点</label>						
					</div>			
					<div class="nodePanel">
						<label>名称：</label>
							<input type="text" class="nodeName">
							<a class="deleteNode" onclick="deleteNode(this)">×</a>
						<div class="attrPanel">
							<label class="attrL">属性设置</label>
							<table class="attrTable">
								<tr class="attrHead">
									<th width="40px"><a class="addAttr" onclick="addAttr(this)">+</a></th>
									<th width="120px">英文名</th>
									<th width="120px">中文名</th>
									<th width="90px">标注方式</th>
									<th width="120px">选项集<br>(英文逗号隔开)</th>
									<th width="30px"></th>
								</tr>
								<tr class="attrLine">
									<td width="40px">属性:</td>
									<td width="120px"><input type="text" class="attrTitle"></td>
									<td width="120px"><input type="text" class="attrName"></td>
									<td width="90px">
										<select class="attrForm" onchange="attrFormChange(this)">
											<option value="select fixed" selected="true">单选</option>
											<option value="select multi_classified">多选</option>
											<option value="select within">多选(内部)</option>
											<option value="input">输入</option>
										</select>
									</td>
									<td width="120px"><input type="text" class="attrSet"></td>
									<td width="30px"> <a class="deleteAttr" onclick="deleteAttr(this)">×</a></td>
								</tr>
							</table>
						</div>
					</div>
				</div>							
			</div>

			<input type="button" class="addConf" id="addNode" value="+" onclick="addNode()">		
			
			<div id="relation_conf">	
				<div class="nodeHead">
					<a class="hideNode" onclick="hideNodeDiv(this)">-</a>
					<a class="showNode" onclick="showNodeDiv(this)">+</a>					
					<label class="nodeL">关系</label>						
				</div>	
				<div class="relPanel">
					<div>
						<label>关系距离:</label>
						<input type="text" id="adjacentOffset">
						<label>正整数</label>
					</div>
					<div id="relOptions">						
						<div class="relOption">
							<label>选项名称:</label>
							<input type="text" class="relOptionName">
							<a class="deleteAttr" onclick="delOption(this)">×</a>
						</div>
					</div>
					<a class="addAttr" onclick="addOption()">+</a>																	
				</div>
			<span id="confError" class="erro_info"><span>	
			</div>


			<div class="stepButtons">	
				<input type="button" id="last2" value="上一步" onclick="twoBack()">		
				<input type="button" id="next2" value="下一步" onclick="twoNext()">		
			</div>					
		</div>
		<div id="finish" class="step">
			<h4>成功创建资源</h4>
		</div>
		
		
		<div id="preview" class="step">	
			<h3 class="h3Title">预览</h3>
			<div>
				<table id="preview_table">
					<tr>
						<th class="left_item">
							<strong>选项</strong>
						</th>
						<th>
							<strong>内容</strong>
						</th>
					</tr>
					<tr>
						<td class="left_item">资源标题</td>
						<td id="res_title_p"></td>
					</tr>
					<tr>
						<td class="left_item">资源内容</td>
						<td id="res_content_p"></td>
					</tr>
					<tr>
						<td class="left_item">标注模式</td>
						<td id="annatate_mode_p"></td>
					</tr>		
					<tr>
						<td class="left_item">关系</td>
						<td id="relation_p"></td>
					</tr>	
					<tr>
						<td class="left_item">关系距离</td>
						<td id="adjacentOffset_p"></td>
					</tr>
					<tr>
						<td class="left_item">节点</td>
						<td id="node_p"></td>
					</tr>																																
				</table>				
			</div>	
			<span id="timestamp" class="time"></span>
			<div class="stepButtons">	
				<input type="button" id="last3" value="上一步" onclick="threeBack()">		
				<input type="button" id="submitRes" value="完成"   >		
			</div>					
		</div>

	</div>
	
</div>