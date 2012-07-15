/**
 * @author Ivan
 */
var NRTemplatesName = ["主题图"];
var NRTemplates = [
{
	rtype: ['同义', '上位', '整体', '前序', '示例', '类比', '未知关系'],
	ntype: ['术语', '知识元'],
	adjacentOffset: 5,
	attachAttr: [
	{
		type: 'nt_1',
		attr: [
			{
				title: 'ke_type',
				//该title与annotate_model中的ke_type_index相对应，修改时请注意，下同
				name: '知识元类型',
				form: 'select fixed',
				set: ['定义类', '分类类', '实例类', '演化类', '属性类', '方法类', '区别类', '其他类']
				//对应的数据库中的ke_type从1至8
			},
			{
				title: 'core_term',
				name: '核心术语',
				form: 'select within',
				set: []
			},
			{
				title: 'term_set',
				name: '术语集合',
				form: 'select within',
				set: []
			},
			{
				title: 'tags',
				name: '标签',
				//form: 'select tag_button',
				form: 'select multi_classified',
				set: ['中国','美国','法国','德国','俄罗斯','几内亚']
				//对应数据库中的tags从0开始
			}
		]
	}]
}
];

var resContent = "测试";
var resource_title ="测试";
var annatate_mode = "NR";
var isLogic = "no";
var logic_user = "";
var resPath = "";
var split_symbol = ";";


var ntype = [];	//节点类型
var attachAttr = [];	//节点属性	
var rtype = [];	//关系类型
var adjacentOffset = 0; //adjacentOffset之间的关系
var errorInfo = [];//错误信息 


var data= [];



//上传文件检查
function uploadCheck(){
	// var filepath = $(obj).val();
	// console.log($(obj).val());
	resContent = "";
	resPath = "";
	
	var file = document.getElementById("fileUpload").files[0];
	if(file==null){
		$("#fileStatus").text("请上传utf8格式的txt文件");
		return false;		
	};
	var reader = new FileReader();		     
	reader.readAsText(file);
	
	reader.onloadend = function(e){
		var contents = e.target.result;
        console.log( "Got the file \n"
              +"name: " + file.name + "\n"
              +"type: " + file.type + "\n"
              +"size: " + file.size + " bytes\n"
              + "starts with: " + contents
        );  
		if(file.type!="text/plain"){
			$("#fileStatus").text("请上传utf8格式的txt文件");
			return false;
		}else{
			resPath = $("#fileUpload").val();
			resContent = contents;
			$("#fileStatus").text("");
		}
	}	
	return true;
}

function titleCheck(obj){
	resource_title = $("#resource_title").val();
	if(resource_title.length<=0){
		$("#titleStatus").text("请输入资源标题");		
		return false;
	}else{
		$("#titleStatus").text("");		
		return true;
	}
}


//模式选择判断
function modeChange(obj){
	var selectVal = $("#NR_logic_mode").val();
	switch(selectVal){
		case "yes": {
			$("#logic_user_div").show();
			$("#split_div").hide();
			break;
		}
		case "no": {
			$("#logic_user_div").hide();
			if($("#annatate_mode").val()=='list'){
				$("#split_div").show();
			}else{
				$("#split_div").hide();
			}
			break;
		}
	}
}
//是否进行文本逻辑结构标注判断
function logicChange(obj){
	var selectVal = $("#NR_logic_mode").val();
	switch(selectVal){
		case "yes": {
			$("#logic_user_div").show();
			$("#split_div").hide();
			break;
		}
		case "no": {
			$("#logic_user_div").hide();
			if($("#annatate_mode").val()=='list'){
				$("#split_div").show();
			}else{
				$("#split_div").hide();
			}
			break;
		}
	}
}


//配置节点时，单选和多选出现选项集合输入框
function attrFormChange(obj) {
	//select的id形式为1_1_form,代表第一个节点的第一个属性的form
	var selectVal = $(obj).val();
	// var sets = $(obj).attr("id").split('_');
	// var set_id = sets[0] + '_' + sets[1] + '_' +  'set';
	// var selector = "#"+set_id;
	//使用相对的方式查找，待改
	var set = $(obj).parent().siblings().first().next().next().next().next().children();	
	switch(selectVal){
		case "input": {
			set.hide();
			break;
		}
		case "select fixed": {
			set.show();
			break;
		}
		case "select within": {
			set.hide();
			break;
		}		
		case "select multi_classified": {
			set.show();
			break;
		}
	}
}
//向Table添加tr
function addAttr(obj){
	//id从0开始	
	var vTable = $(obj).parent().parent().parent().parent();
	// var nodeId = vTable.attr("id").split('_')[0];
	// var trSize = vTable.children().children().size()-1;
	var newRow = getTableRow();
	vTable.append(newRow);
}
//获取新添加的tr,方法：获取父table，然后添加行
function getTableRow(){
	//1_3代表第2个节点的第4个属性
	//var attrId = nodeId + '_' + (trSize);
	var html = '<tr class="attrLine">';
			//html +=	'<td>属性'+ (trSize+1) +'</td>';
			html +=	'<td width="40px">属性:</td>';
			html += '<td width="120px"><input type="text" class="attrTitle"></td>';
			html += '<td width="120px"><input type="text" class="attrName"></td>';
			html += '<td width="90px"><select class="attrForm" onchange="attrFormChange(this)">';
				html += '<option value="select fixed" selected="true">单选</option>';
				html += '<option value="select multi_classified">多选</option>';
				html += '<option value="select within">多选(内部)</option>';				
				html += '<option value="input">输入</option>';
			html += '</select></td>';
			html += '<td width="120px"><input type="text" class="attrSet"></td>';
			html += '<td width="30px"><a class="deleteAttr" onclick="deleteAttr(this)">×</a></td>';		
		html += '</tr>';
	return html;
}
//删除属性
function deleteAttr(obj){
	var vTr = $(obj).parent().parent();
	vTr.remove();
}
//添加节点
function addNode(){
	var nodeConf = $("#nodeConf");
	var newNodeDiv = getNodeDiv();
	nodeConf.append(newNodeDiv);
}
function getNodeDiv(){
	var html = '';
	html += '<div class="nodeDiv">';
		html += '<div class="nodeHead">';
			html += '<a class="hideNode" onclick="hideNodeDiv(this)">-</a>';				
			html += '<a class="showNode" onclick="showNodeDiv(this)">+</a>';						
			html += '<label class="nodeL">节点</label>';						
		html += '</div>';					
		html += '<div class="nodePanel">';							
			html += '<label>名称：</label>';			
			html += '<input type="text" class="nodeName">';		
			html += '<a class="deleteNode" onclick="deleteNode(this)">×</a>';			
			html += '<div class="attrPanel">';										
				html += '<label class="attrL">属性设置</label>';		
				html += '<table class="attrTable">';					
					html += '<tr class="attrHead">';			
						html += '<th width="40px"><a class="addAttr" onclick="addAttr(this)">+</a></th>';			
						html += '<th width="120px">英文名</th>';						
						html += '<th width="120px">中文名</th>';			
						html += '<th width="90px">标注方式</th>';				
						html += '<th width="120px">选项集<br>(英文逗号隔开)</th>';						
						html += '<th width="30px"></th>';			
					html += '</tr>';	
					html += '<tr class="attrLine">';			
						html += '<td width="40px">属性:</td>';			
						html += '<td width="120px"><input type="text" class="attrTitle"></td>';						
						html += '<td width="120px"><input type="text" class="attrName"></td>';									
						html += '<td width="90px">';
							html += '<select class="attrForm" onchange="attrFormChange(this)">';	
								html += '<option value="select fixed" selected="true">单选</option>';
								html += '<option value="select multi_classified">多选</option>';
								html += '<option value="select within">多选(内部)</option>';
								html += '<option value="input">输入</option>';
							html += '</select>';
						html += '</td>';
						html += '<td width="120px"><input type="text" class="attrSet"></td>';						
						html += '<td width="30px"><a class="deleteAttr" onclick="deleteAttr(this)">×</a></td>';																	
					html += '</tr>';						
				html += '</table>';	
			html += '</div>';					
		html += '</div>';	
	html += '</div>';					
	return html;
}
//删除节点
function deleteNode(obj){
	var nodeDiv = $(obj).parents(".nodeDiv");
	nodeDiv.remove();
}
//折叠单个节点配置板
function hideNodeDiv(obj){
	var nodePanel = $(obj).parent().siblings().first();
	nodePanel.hide("normal");
	$(obj).hide();
	$(obj).siblings().first().show();
}
//展开单个节点配置板
function showNodeDiv(obj){
	var nodePanel = $(obj).parent().siblings().first();
	nodePanel.show("normal");
	$(obj).hide();
	$(obj).siblings().first().show();
}
//添加关系选项
function addOption(){
	var relOption = $("#relOptions");
	var newOptionDiv = "";
	
	newOptionDiv += '<div class="relOption">';
		newOptionDiv += '<label>选项名称:</label>';
		newOptionDiv += '<input type="text" class="relOptionName">';
		newOptionDiv += '<a class="deleteAttr" onclick="delOption(this)">×</a>';
	newOptionDiv += '</div>';
		
	relOption.append(newOptionDiv);
}
function delOption(obj){
	var relOption = $(obj).parents(".relOption");
	relOption.remove();	
}

//检查第一步输入的信息
function checkInfo(){
	split_symbol = ";";
	
	if($("#annatate_mode").val()=='list'){
		if($("#NR_logic_mode").val()=='no'){			
			if($("#split_symbol").val().length<=0){
				$("#splitStatus").text("请输入划分符");
				return false;
			}else{
				split_symbol = $("#split_symbol").val();
			}
		}
	}	
	
	annatate_mode = $("#annatate_mode").val();
	isLogic = $("#NR_logic_mode").val();
	logic_user = $("#logic_user").val();			
	if(uploadCheck()==true&&titleCheck()==true){
		return true;
	}else{
		return false;
	}

	//文件检查+title检查
	var file = document.getElementById("fileUpload").files[0];
	if(file==null||file.type!="text/plain"){
		$("#fileStatus").text("请上传utf8格式的txt文件");
		return false;
	}else{
		$("#fileStatus").text("");
	}	
	if(resource_title.length<=0){
		$("#titleStatus").text("请输入资源标题");
		return false;
	}else{
		$("#titleStatus").text("");
	}	
	
	return true;	
}


//获取输入的数据
function checkConf(){
	data = [];	
	ntype = [];	//节点类型
	attachAttr = [];	//节点属性	
	rtype = [];	//关系类型
	adjacentOffset = 0; //adjacentOffset之间的关系
	errorInfo = [];//错误信息 
	
	//节点名称检查	(放到下面$(".nodeDiv").each里总是提示$(this).find(".nodeName").val()是undefined)
	$(".nodeName").each(function(){
		var i=1;
		if($(this).val().length==0){
			errorInfo.push("请输入节点"+i+"的名称");			
		}
		i++;
	});
		
	$(".nodeDiv").each(function(){
		var type = 'nt_' + ntype.length;
		//保存节点名称		
		ntype.push($(this).find(".nodeName").val());				
		var attr = [];
			
		$(this).find("tr").not(".attrHead").each(function(){
			
			//输入检查
			if($(this).find(".attrTitle").val().length==0){
				errorInfo.push("请输入节点"+ntype.length+"-属性"+attr.length+"的英文名");
			}
			if($(this).find(".attrName").val().length==0){
				errorInfo.push("请输入节点"+ntype.length+"-属性"+attr.length+"的中文名");
			}
			if($(this).find(".attrSet").val().length==0){
				if($(this).find(".attrForm").val()!="input"&&$(this).find(".attrForm").val()!="select within"){
					errorInfo.push("请输入节点"+ntype.length+"-属性"+attr.length+"的选项集");
				}
			}else{
				var attrSet = $(this).find(".attrSet").val().split(',');	
				if(attrSet.length==1){
					errorInfo.push("节点"+ntype.length+"-属性"+attr.length+"的选项数量为1，请以英文逗号隔开");
				}			
			}
			var attrSet = $(this).find(".attrSet").val().split(',');
			
			//单个属性保存入attr中						
			attr.push({
				title: $(this).find(".attrTitle").val(),
				name:  $(this).find(".attrName").val(),
				form:  $(this).find(".attrForm").val(),
				set:   attrSet
			});
						
		});	
		//节点所有属性保存入attachAttr中	
		if($(this).find("tr").not(".attrHead").length>0){
			attachAttr.push({
			type: type,
			attr: attr
		});		
		}
	});	
	//关系距离检查
	adjacentOffset = $("#adjacentOffset").val();
	if(isNumber(adjacentOffset)==false){
		errorInfo.push("关系距离请输入正整数");
	}
	//关系选项名称检查
	$(".relOptionName").each(function(){
		if($(this).val().length<=0){
			errorInfo.push("关系选项缺失");
		}else{
			rtype.push($(this).val());
		}		
	});	
	data.push({
		ntype: ntype,	//节点类型
		attachAttr: attachAttr,	//节点属性	
		rtype: rtype,	//关系类型
		adjacentOffset: adjacentOffset, //adjacentOffset之间的关系
	});
	return errorInfo;
}

//验证输入是否是数字
function isNumber(str){ 
	if(str.length<=0){
		return false;
	}
    var mynumber="0123456789"; 
    for(var i=0;i<str.length;i++){ 
        var c=str.charAt(i); 
        if(mynumber.indexOf(c)==-1){ 
            return false; 
        } 
    } 
    return true; 
}

function loadTemplate(obj){
	var templateName = $(obj).attr("name");
	for(var i=0;i<NRTemplatesName.length;i++){
		if(NRTemplatesName[i]==templateName){
			var template = NRTemplates[i];
			break;
		}
	}
	
	var ntype = template["ntype"];
	var attachAttr = template["attachAttr"];	
	var rtype = template["rtype"];
	var adjacentOffset = template["adjacentOffset"]; //adjacentOffset之间的关系	

	for(var i=0;i<ntype.length-$(".nodeDiv").length;i++){
		addNode();
	}

	var ntype_index=0;		
	
	// for (var i=0; i < attachAttr.length; i++) {
		// var type = attachAttr[i].type;
		// console.log(type);
	// };
		
	$(".nodeDiv").each(function(){		
		var hasAttr = false;
		var typeNum = "nt_"+(ntype_index);
		//节点名称设置		
		$(this).find(".nodeName").attr("value",ntype[ntype_index]);
		//节点属性设置
		var attr = [];
		for (var i=0; i < attachAttr.length; i++) {
			if(typeNum == attachAttr[i].type){
				attr = attachAttr[i].attr;
				hasAttr = true;
				//console.log(attr);
				break;
			}
		};
		if(hasAttr==true){
			var time = attr.length-$(this).find(".attrLine").length;
			for (var j=0; j < time; j++) {
				$(this).find(".addAttr").click();
			}
			var attrIndex= 0 ;	
			$(this).find("tr").not(".attrHead").each(function(){	
				$(this).find(".attrTitle").attr("value",attr[attrIndex]["title"]);
				$(this).find(".attrName").attr("value",attr[attrIndex]["name"]);
				$(this).find(".attrForm").attr("value",attr[attrIndex]["form"]);
				if(attr[attrIndex]["form"]=="select within"||attr[attrIndex]["form"]=="input"){
					$(this).find(".attrSet").hide();	
				}else{
					$(this).find(".attrSet").attr("value",attr[attrIndex]["set"]);			
				}							
				attrIndex++;			
			});	
		}
		ntype_index++;
	});	
	
	//关系距离设置
	$("#adjacentOffset").attr("value",adjacentOffset);
	var time = rtype.length-$(".relOptionName").length;
	for (var i=0; i < time; i++) {
	  	addOption();
	};
	var rel_index = 0;
	//关系选项名称检查
	$(".relOptionName").each(function(){
		$(this).attr("value",rtype[rel_index]);		
		rel_index++;
	});	
}


//下一步上一步操作
function oneNext(){
	if(checkInfo()){
		$("#resourceDiv").hide();
		$("#confDiv").show();		
	}
}
function twoBack(){
	$("#confDiv").hide();
	$("#resourceDiv").show();
}
function twoNext(){	
	var errorInfo = checkConf();
	if(errorInfo.length==0){
		$("#confError").text("");	
		$("#confDiv").hide();
		previewConf();
		$("#preview").show();			
	}else{
		$("#confError").text(errorInfo[0]);
	}
	console.log("错误信息：\n"+errorInfo);
}
function threeBack(){
	$("#preview").hide();
	$("#confDiv").show();
}

function previewConf(){			
	
	var content = resContent;
	if(resContent.length>200){
		content = resContent.slice(0,200)+'...';
	}	
	var a_mode = '';
	if(annatate_mode=='NR'){
		a_mode = '节点关系';		
	}else{
		a_mode = '列表标注';	
	}	
	var isLogic_p = '';
	if(isLogic=='yes'){
		isLogic_p = "是;    逻辑标注用户："+logic_user;
	}else{
		isLogic_p = "否";
		if(annatate_mode=='list'){
			isLogic_p = "否;    正则划分符："+split_symbol;
		}
	}		
	
	var ntype_p = '';
	for(var i=0;i<ntype.length;i++){
		if(i==(ntype.length-1)){
			ntype_p += ntype[i];
		}else{
			ntype_p += ntype[i]+";   ";
		}		
	}
	var rtype_p =  "共"+rtype.length+"项:    ";
	for(var i=0;i<rtype.length;i++){
		if(i==(rtype.length-1)){
			rtype_p += rtype[i];
		}else{
			rtype_p += rtype[i]+";   ";
		}		
	}	
		
	$("#res_title_p").text(resource_title);
	$("#res_content_p").text(content);
	$("#annatate_mode_p").text(a_mode);
	$("#isLogic_p").text(isLogic_p); 
	$("#node_p").text(ntype_p); 
	$("#relation_p").text(rtype_p);
	$("#adjacentOffset_p").text(adjacentOffset);
	
	html = getNodePreview();
	$("#preview_table").append(html);
}


function getNodePreview(){
	console.log(attachAttr);
	var html = '';
	
	for(var i=0;i<ntype.length;i++){
		html += '<tr>';
			html += '<td  class="left_item">';
				html += ntype[i]+"的属性";
			html += '</td>';
			html += '<td>';
			for (var j=0; j < attachAttr.length; j++) {
				console.log("attachAttr[j].type:   "+attachAttr[j].type);
				var type = attachAttr[j].type;
				if(type.split('_')[1]==i){
					for(var k=0;k<attachAttr[j].attr.length;k++){
						html += '<div>';
							html += '属性'+(k+1)+':   ';	
						html += '</div>';
						html += '<div class="attr_p">';	
							html += '英文名称：'+attachAttr[j]['attr'][k].title;	
						html += '</div>';		
						html += '<div class="attr_p">';		
							html += '中文名称：'+attachAttr[j]['attr'][k].name;
						html += '</div>';	
						html += '<div class="attr_p">';			
							html += '标注方式：'+attachAttr[j]['attr'][k].form;	
						html += '</div>';		
							if(attachAttr[j]['attr'][k].form!='select within' && attachAttr[j]['attr'][k].form!='input'){
								html += '<div class="attr_p">';		
									html += '选相集合：'+attachAttr[j]['attr'][k].set;	
								html += '</div>';			
							}																			
						html += '</div>';	
					}
					break;
				}
			};
			html += '</td>';
		html += '</tr>';
	}
	return html;
}



$(document).ready(function() {
	//css中display:none不起作用...
	$("#split_div").hide();
});

