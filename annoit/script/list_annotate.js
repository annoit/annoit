/*
 * @author  赵苏森 (http://susutou.com)
 * @date    2011.3
 * @version 1.0.11 (重要变更.功能增加.bug修复)
 * 
 * Update Logs
 * 
 * - 1.1.11: 功能增加: 层叠标注时使用多种颜色显示
 * - 1.2.11: 功能增加: 多种颜色得以区分，并且在选中时可以以高亮显示
 * - 1.2.13: Bug修复:  增加属性选单; 修正getAdjacentNodes函数, 增加getCurrentId函数
 * - 1.2.14: Bug Fix: more
 * - 1.3.14 @ April 25th, 2011:
 *   修改getAdjacentNodesById(), 使相关节点的取值范围为同类节点
 * - 1.4.14 @ April 25th, 2011:
 *   增加附加属性标注功能
 * - 1.5.15 @ April 27th, 2011:
 *   Bug fix & 增加若干功能
 * - 1.6.15 @ May 20th, 2011:
 *   Bug fix
 * 
 * 
 * - 1.6.16: @ 2012.4.17 by Ivan
 *   功能增加：tag标注
 *   Bug fix: getCurrentId函数修改，之前的getCurrentId函数导致初始标注时id选择错误（修正）
 * 
 */

//var rtype = ['前趋于...', '后继于...', '从属于...', '包含...', '未知关系'];
//var ntype = ['术语', '知识元'];
//var nodeList = new Array();
//var general_id = 0;  由annotate.php传入，涉及到node表的inner_id

var rmatrix = new Array();
for (var i = 0; i < rtype.length; i++) {
	rmatrix.push({id: i, type: rtype[i], relation: []});
}

var mousePos = {};
var oldMousePos = {};

String.prototype.limit = function() {
	var length = 16;
	if (this.length > length) {
		return this.slice(0, length) + '...';
	}
	return this.slice(0, length);
}

//获取两个节点关系Relation的ID
function getRelationId(relation, a, b) {
	for (var i = 0; i < relation.length; i++) {
		if (relation[i].x == a && relation[i].y == b) {
			return i + 1;
		}
	}
	return false;
}
//更新两个节点的关系
function updateRelation(relation, a, b) {
	for (var i = 0; i < rmatrix.length; i++) {
		var del_id = getRelationId(rmatrix[i].relation, a, b);		
		//console.log("del_id:  "+del_id);
		if (del_id) {
			//删除旧关系
			rmatrix[i].relation.splice(del_id - 1, 1);			
			break;
		}
	}
	//插入新关系
	relate(relation, a, b);
}

// 定义了gadget类，用于生成各类html组件，待再次封装
gadget = new Object();

gadget.button = function (id, value) {
	var html = '';
	html += '<input type="button" id="' + id + '" value="' + value + '" />';
	return html;
}

gadget.textfield = function (id, value) {
	var html = '';
	html += '<input type="text" id="' + id + '" value="' + value + '" />';
	return html;
}

gadget.label = function (id, value) {
	return '<label for="' + id + '">' + value + '</label>';
}

gadget.li = function (id, value) {
	var html;
	if (id != '') {
		html = '<li id="' + id + '">';
	} else {
		html = '<li>';
	}
	html += value;
	html += '</li>';
	return html;
}

gadget.ul = function (id, list) {
	var id_part = (id == '' ? '' : ' id="' + id + '"');
	var html = '<ul' + id_part + '>';
	for (i = 0; i < list.length; i++) {
		html += this.li(list[i].id, list[i].value);
	}
	html += '</ul>';
	return html;
}
// list. value, text, selected
gadget.select = function (id, list) {
	var id_part = (id == '' ? '' : ' id="' + id + '"');
	var html = '<select' + id_part + ' class="fixed">';
	for (i = 0; i < list.length; i++) {
		if (list[i].selected == true) {
			html += '<option value="' + list[i].value + '" selected="selected">';
		} else {
			html += '<option value="' + list[i].value + '">';
		}
		html += list[i].text.limit();
		html += '</option>';
	}
	html += '</select>';
	//alert(html);
	return html;
}

// list. value, text, selected
gadget.selectRelation = function (id, list) {
	var id_part = (id == '' ? '' : ' id="' + id + '"');
	var html = '<select' + id_part + ' class="relation">';
	for (i = 0; i < list.length; i++) {
		if (list[i].selected == true) {
			html += '<option value="' + list[i].value + '" selected="selected">';
		} else {
			html += '<option value="' + list[i].value + '">';
		}
		html += list[i].text.limit();
		html += '</option>';
	}
	html += '</select>';
	//alert(html);
	return html;
}

gadget.selectChange = function (id, list) {
	var id_part = (id == '' ? '' : ' id="' + id + '"');
	var html = '<select' + id_part + ' class="fixed" onchange="generateAttachDiv(-1)">';
	for (i = 0; i < list.length; i++) {
		if (list[i].selected == true) {
			html += '<option value="' + list[i].value + '" selected="selected" >';
		} else {
			html += '<option value="' + list[i].value + '">';
		}
		html += list[i].text.limit();
		html += '</option>';
	}
	html += '</select>';
	//alert(html);
	return html;
}



gadget.multipleselect = function (id, list) {
	var id_part = (id == '' ? '' : ' id="' + id + '"');
	var html = '<select' + id_part + ' multiple="multiple">';
	for (i = 0; i < list.length; i++) {
		if (list[i].selected == true) {
			html += '<option value="' + list[i].value + '" selected="selected">';
		} else {
			html += '<option value="' + list[i].value + '">';
		}
		html += list[i].text.limit();
		html += '</option>';
	}
	html += '</select>';
	//alert(html);
	return html;
}
gadget.selectwithin = function (id, list) {
	var id_part = (id == '' ? '' : ' id="' + id + '"');
	var html = '<select' + id_part + ' multiple="multiple" class="within">';
	for (i = 0; i < list.length; i++) {
		if (list[i].selected == true) {
			html += '<option value="' + list[i].value + '" selected="selected">';
		} else {
			html += '<option value="' + list[i].value + '">';
		}
		html += list[i].text.limit();
		html += '</option>';
	}
	html += '</select>';
	//alert(html);
	return html;
}

function hasAttachAttr(type) {
	if (attachAttr.length > 0) {
		for (i = 0; i < attachAttr.length; i++) {
			if (attachAttr[i].type == type) {
				// alert("hasAttachAttr");
				return true;
			}
		}
	return false;
	}
}
//get attr From attachAttr By type
function getAttachAttrByType(type) {
	if (attachAttr.length > 0) {
		for (i = 0; i < attachAttr.length; i++) {
			if (attachAttr[i].type == type) {
				return attachAttr[i].attr;
			}
		}
	}
}
//get Tags List From attachAttr By type, edit by Ivan @2012.4.12
function getTagsByType(type){
	if(attchAttr.length > 0){
		for (i = 0; i < attachAttr.length; i++) {
			if (attachAttr[i].type == type) {
				return attachAttr[i].tags;
			}
		}
	}
}
//获取
function getAttachAttrValue(id, type) {
	//alert(id);
	if (nodeList[id].attachAttr.length > 0) {
		for (var i = 0; i < nodeList[id].attachAttr.length; i++) {
			if (nodeList[id].attachAttr[i].title == type) {
				return nodeList[id].attachAttr[i].value;
			}
		}
	}
	return false;
}

function idInAttrList(id, value) {
	
	try {
		var attrList = value.split(',');
		for (var i = 0; i < attrList.length; i++) {
			if (id == attrList[i]) {
				return true;
			}
		}
	} catch (e) {
		return false;
	}
	return false;
}
//根据AttachAttr中的attr生成无值面板
function generateAttachAttrB(attach) {
	var code = '';
	switch (attach.form) {
		case 'select within':
			code += '<span class="ritem">';
			code += gadget.label(attach.title, attach.name);
			code += '</span>';
			break;
			
		//多选框，用于选取attachAttr[].attr[].set集合中的选项
		case 'select multi_classified':
			var list = [];
			var isset;
			for(var i=0;i<attach.set.length;i++){
				isset = false;
				list.push({
					value: 'attach-' + attach.title + '-' + i,
					text: attach.set[i],
					selected: isset
				});
			}
			code += '<span class="ritem">';		
			code += gadget.label(attach.title, attach.name);	
			code += gadget.multipleselect('attach-' + attach.title, list);
			code += '</span>';
			break;			
			
		//单选分类
		case 'select fixed':
			var list = [];
			//alert(attach.title);
			for (var i = 0; i < attach.set.length; i++) {
				var isset = false;
				list.push({
					value: attach.title + '_' + (i + 1),
					text: attach.set[i],
					selected: isset
				});
			}
			code += '<span class="ritem">';
			code += gadget.label(attach.title, attach.name);
			code += gadget.select('attach-' + attach.title, list);
			code += '</span>';
			break;
			
		//输入框
		case 'input':
			var text = '';
			code += '<span class="ritem">';
			code += gadget.label(attach.title, attach.name);
			code += gadget.textfield('attach-' + attach.title, text);
			code += '</span>';
			break;
	}
	return code;
}


//根据AttachAttr中的attr生成标注面板
function generateAttachAttr(id, attach) {
	var code = '';
	//alert(attach.form);
	switch (attach.form) {
		//多选框，用于选择知识元包含的核心术语
		case 'select within':
			var nodeInRange = getNodeInRange(id);
			if (nodeInRange.length == 0) {
				return '';
			}
			var list = [];
			/*
			var list = [{
				value: 'none',
				text: '请选择...',
				selected: false
			}];
			*/
			for (var i = 0; i < nodeInRange.length; i++) {
				var isset = false;
				//alert(getAttachAttrValue(id, attach.title));
				if (getAttachAttrValue(id, attach.title) &&
					idInAttrList(nodeInRange[i].id, getAttachAttrValue(id, attach.title))) {
					//alert(id + '-' + getAttachAttrValue(id, attach.title));
					//alert();
					isset = true;
				}
				list.push({
					value: 'attach-' + attach.title + '-' + nodeInRange[i].id,
					text:  nodeInRange[i].text,
					selected: isset
				});
			}
			code += '<span class="ritem">';
			code += gadget.label(attach.title, attach.name);
			code += gadget.multipleselect('attach-' + attach.title, list);
			code += '</span>';
			break;
			
		//多选框，用于选取attachAttr[].attr[].set集合中的选项
		case 'select multi_classified':
			var list = [];
			var isset;
			for(var i=0;i<attach.set.length;i++){
				isset = false;
				if(getAttachAttrValue(id, attach.title) &&
					idInAttrList(i, getAttachAttrValue(id, attach.title))){
					isset = true;
				}
				list.push({
					value: 'attach-' + attach.title + '-' + i,
					text: attach.set[i],
					selected: isset
				});
			}
			code += '<span class="ritem">';		
			code += gadget.label(attach.title, attach.name);	
			code += gadget.multipleselect('attach-' + attach.title, list);
			code += '</span>';
			break;
		
		//多选button，用于选取attachAttr[].attr[].set集合中的选项
		case 'select tag_button':
			var list = [];	//存储attr[].set集合中的选项
			var isset;	//该选项是否已被选择
												
			//alert("289行生成Buttons时Nodeid="+id);
			for(var i=0;i<attach.set.length;i++){
				isset = false;
				//仿其他组件，isset的判断是多余的
				if(getAttachAttrValue(id, attach.title) &&
					idInAttrList(i, getAttachAttrValue(id, attach.title))){
					isset = true;
					//alert("设置true时Nodeid="+id+"   getAttach:"+getAttachAttrValue(id, attach.title));
				}
				list.push({
					value: 'attach-' + attach.title + '-' + i,
					text: attach.set[i],
					selected: isset
				});
			}
//			alert("生成id:attach-"+ attach.title);
			code += '<span class="ritem">';		
			code += gadget.label(attach.title, attach.name);	
			code += gadget.tags('attach-' + attach.title, list);
			code += '</span>';			
				
			break;
			
			
		//单选分类
		case 'select fixed':
			var list = [];
			//alert(attach.title);
			for (var i = 0; i < attach.set.length; i++) {
				var isset = false;
				if (getAttachAttrValue(id, attach.title) == i + 1) {
					isset = true;
				}
				list.push({
					value: attach.title + '_' + (i + 1),
					text: attach.set[i],
					selected: isset
				});
			}
			code += '<span class="ritem">';
			code += gadget.label(attach.title, attach.name);
			code += gadget.select('attach-' + attach.title, list);
			code += '</span>';
			break;
			
		//输入框
		case 'input':
			var text = getAttachAttrValue(id, attach.title) ? getAttachAttrValue(id, attach.title) : '';
			code += '<span class="ritem">';
			code += gadget.label(attach.title, attach.name);
			code += gadget.textfield('attach-' + attach.title, text);
			code += '</span>';
			break;
	}
	return code;
}

function getNodeInRange(id) {
	var start = nodeList[id].from;
	var end = nodeList[id].to;
	var list = [];
	for (var i = 0; i < nodeList.length; i++) {
		if (nodeList[i].from >= start && nodeList[i].to <= end && i != id) {
			list.push(nodeList[i]);
		}
	}
	return list;
}
function saveAttachAttr(id) {
	if (/*hasAttachAttr(nodeList[id].attr) && */$('#attach_fields')) {
		nodeList[id].attachAttr = [];
		
		//将输入框组件的title+value存入节点的attachAttr中
		$('#attach_fields span input#input').each(function() {
			//alert($(this).val() + '-' + $(this).attr('id'));
			nodeList[id].attachAttr.push({
				title: $(this).attr('id').split('-')[1],
				value: $(this).val(),
				form: 'input'
			});			
		});		

		$('#attach_fields span select').each(function() {
			//alert($(this).val());
			if($(this).hasClass("fixed")){
				//console.log("我是fixed:　　" + $(this).attr('id').split('-')[1]);
				if ($(this).val() != 'none') {
					nodeList[id].attachAttr.push({
						title: $(this).attr('id').split('-')[1],
						value: parseInt($(this).val().slice(8), 10),
						form: 'select fixed'
					});
				}else{
					nodeList[id].attachAttr.push({
						title: $(this).attr('id').split('-')[1],
						value: null,
						form: 'select fixed'
					})
					//console.log("ke_type未选时的保存value",parseInt($(this).val().slice(8), 10));
					//console.log("ke_type未选时的保存title",$(this).attr('id').split('-')[1]);
				}
			}
		});		
		
		//将多选框组件的title+value存入节点的attachAttr中
		$('#attach_fields span select[multiple="multiple"]').each(function() {
			if ($(this).val() != 'none') {
				var attrlist = $(this).val() || [];
				for (var i = 0; i < attrlist.length; i++) {
					attrlist[i] = parseInt(attrlist[i].split('-')[2], 10);
				}
				//判断是within还是普通多选
				var attrForm = 'select multi_classified';
				if($(this).hasClass("within")){
					attrForm = 'select within';
				}
				nodeList[id].attachAttr.push({
					title: $(this).attr('id').split('-')[1],
					value: attrlist.join(','),
					form: attrForm
				});
			}			
		});	
		//将多选tag_buttons组件的title+value存入节点的attachAttr中
		$('#attach_fields span.tag_buttons').each(function() {		
			var taglist = $(this).children("input.taglist");
			var tag_group = $(this).attr('id');//tag_group = attach-tags
				
			var attrlist = Array();
			//用tag_group != null过滤掉tag_buttons下的buttons
			if(tag_group != null){
				for(i=0; i<taglist.length; i++){
					if($(taglist[i]).hasClass("active")){
						attrlist.push($(taglist[i]).attr("tid"));
					}
				}
				nodeList[id].attachAttr.push({					
					title: tag_group.split('-')[1], 
					value: attrlist.join(',')
				});
			}
		});	
	} else {
		//nodeList[id].attr = null;
	}
}

function getRelatedList(id, attr) {
	var select_code = '';
	if (nodeList.length > 1) {
		var aList = getAdjacentNodesById(adjacentOffset, id, attr);
		if (aList.list.length > 0) {
			var slist = [{
				value: 'none',
				text: '请选择...',
				selected: true
			}];
			for (var i = 0; i < rmatrix.length; i++) {
				slist.push({
					value: 'r_' + rmatrix[i].id,
					text:  rmatrix[i].type,
					selected: false
				});
			}
			select_code += '<span id="related-list">';
			for (var i = 0; i < aList.list.length; i++) {
				select_code += '<span class="ritem">';
				select_code += gadget.select('nr_' + aList.list[i].oi, slist);
				select_code += '<span class="tagtip">' + aList.list[i].ai.text.limit() + '</span>';
				select_code += '</span>';
			}
			select_code += '</span>';
		}
	}
	return select_code;
}

function getDistinctList(list) {
	var dlist = [];
	for (var i = 0; i < list.length; i++) {
		dlist.push({
			offset: list[i].from,
			type: 0
		});
		dlist.push({
			offset: list[i].to,
			type: 1
		});
	}
	dlist.sort(function(a, b) {
		if (a.offset > b.offset) {
			return 1;
		}
		if (a.offset < b.offset) {
			return -1;
		}
		return 0;
	});
	for (var i = 0; i < dlist.length - 1; i++) {
		if (dlist[i].offset == dlist[i + 1].offset) {
			dlist.splice(i, 1);
			i--;
		}
	}
	return dlist;
}


function sort() {
	for (i = 0; i < nodeList.length - 1; i++) {
		for (j = i + 1; j < nodeList.length; j++) {
			if (nodeList[i].from > nodeList[j].from) {
				t = nodeList[i];
				nodeList[i] = nodeList[j];
				nodeList[j] = t;
			}
		}
	}
}
function getCurrentId() {
	//alert("getCurrentId中NodeList长度为："+nodeList.length);
		
	for (var i = 0; i < nodeList.length; i++) {
		if (nodeList[i].id == general_id) {
			//console.log(i);
			//console.log("getCurrentId()函数中，nodeList[上面的i].id=",general_id);
			return i;
		}
	}
	//return nodeList.length-1;
}

function mouseMove(ev) {
	ev = ev || window.event;
	mousePos = mouseCoords(ev);
}

function mouseCoords(ev) {
	if (ev.pageX || ev.pageY) {
		return {x: ev.pageX, y: ev.pageY};
	}
	return {
		x: ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y: ev.clientY + document.body.scrollTop - document.body.clientTop
	};
}

function valid(t) {
	for (i = 0; i < nodeList.length; i++) {
		o = nodeList[i];
		if (t.from <= o.from && t.to >= o.to) {
			return false;
		}
		if (t.from >= o.from && t.to <= o.to) {
			return false;
		}
		if (t.from < o.to && o.to < t.to) {
			return false;
		}
		if (t.from < o.from && o.from < t.to) {
			return false;
		}
	}
	return true;
}

function getUnitIdByOffset(start, end){
	for(var i=0;i<units.length;i++){
		if((units[i]['from'] == start) && (units[i]['to'] == end)){
			return i;
		}
	}
	return -1;	
}
function getNodeIdByOffset(start, end){
	for(var i=0;i<nodeList.length;i++){
		if((nodeList[i]['from'] == start) && (nodeList[i]['to'] == end)){
			return i;
		}
	}
	return -1;	
}
function getAttrById(id){
	for(var i=0;i<nodeList.length;i++){		
		if((nodeList[i]['id'] == id)){
			return nodeList[i]['attr'];
		}
	}
	return -1;	
}


function generateRelationDiv(node_id,current_attr){
	var select_code = '';
	//生成“关系选择区”
	if (nodeList.length > 1) {
		if(node_id==-1){
			var aList = getAdjacentNodes(adjacentOffset, current_attr);			
		}else{
			var aList = getAdjacentNodesById(adjacentOffset, node_id, current_attr);	
		}
		if (aList.list.length > 0) {
			var current_id = node_id;
			select_code += '<span id="related-list">';
			for (var i = 0; i < aList.list.length; i++) {				
				var slist = [{
					value: 'none',
					text: '请选择...',
					selected: true
				}];
				for (var t = 0; t < rmatrix.length; t++) {
					slist.push({
						value: 'r_' + rmatrix[t].id,
						text:  rmatrix[t].type,
						selected: false
					});
				}		
				
				console.log("1:   "+aList.list[i].oi);
				if(node_id!=-1){
					var flag = false;
					for (var j = 0; j < rmatrix.length; j++) {					
						if (rmatrix[j].relation.has_r({x: nodeList[node_id].id, y: aList.list[i].oi})) {
							slist[j + 1].selected = true;
							flag = true;
							break;
						}
					}
					if (!flag) {
						slist[0].selected = true;
					}						
				}				
				select_code += '<span class="ritem">';
				select_code += gadget.selectRelation('nr_' + aList.list[i].oi, slist);
				select_code += '<span class="tagtip">' + aList.list[i].ai.text.limit() + '</span>';
				select_code += '</span>';
			}
			select_code += '</span>';
		}
	}
	return select_code;
}


function generateAttachDiv(node_id){
	var select_code = '';
	var current_attr = $('#type_select').val();
	$('#attachDiv').html('');
	
	if (hasAttachAttr(current_attr)) {
		var current_attach = getAttachAttrByType(current_attr);
			select_code += '<span id="attach_fields">';
			for (var i = 0; i < current_attach.length; i++) {
				//alert(current_attach[i].form);
				if(node_id==-1){
					select_code += generateAttachAttrB(current_attach[i]);
				}else{
					select_code += generateAttachAttr(node_id,current_attach[i]);
				}				
			}
			select_code += '</span>';
	}				
	$('#attachDiv').append(select_code);
	//添加关系变化
	$('#relationDiv').html('');	
	$('#relationDiv').append(generateRelationDiv(node_id,current_attr));		
}


function la_initialize(current_attr){
//	var html = gadget.select('type_select', slist);
//	html += gadget.button('do_tag', '切换类型');
	var slist = [];	
	var ntype_id = current_attr.split('_')[1];
	for (var i = 0; i < ntype.length; i++) {
		var selected = false;
		if(ntype_id==i){
			selected = true;
		}
		slist.push({
			value: 'nt_' + i,
			text:  ntype[i],
			selected: selected
		});
	}
	var html = gadget.label('type_select_name', '节点类型选择');
	html += gadget.selectChange('type_select', slist);
	
	var toolkit = $('#tool');
	toolkit.html(html)
		.css({
			'top':  mousePos.y + 5 + 'px',
			'left': mousePos.x + 5 + 'px'
		}).fadeIn();

	var select_code = '';	
	select_code += '<div id="attachDiv">';
	select_code += '</div>';
	select_code += '<div id="relationDiv">';
	select_code += '</div>';	
	$('#tool').append(select_code);
	oldMousePos = mousePos;		
}

function saveUnit(obj){
	id = $(obj).attr('id').split('_')[2];
	var current = {
		id:   0,
		text: units[id]['text'],
		from: units[id]['from'] - units[0]['from'],
		to:   units[id]['to'] - units[0]['from'],
		attr: 'nt_0',
		attachAttr: []	
	};
	if (nodeList.has(current)) {
		var current_id = getNodeIdByOffset(current.from, current.to);
		var current_attr = $('#type_select option').filter(':selected').attr('value');
		nodeList[current_id].attr = current_attr;
		//保存节点属性
		saveAttachAttr(current_id);	

		console.log("791行: " + current_id);			
		saveRelation(nodeList[current_id].id);
	}else{
		general_id++;
		current.id = general_id;	
							
		nodeList.push(current);					
		sort();
		//修改标注状态
		var selector = '#la_note_' + id;
		$(selector).text('已标注');		
		
		var current_attr = $('#type_select option').filter(':selected').attr('value');
		console.log("首次标注：   "+current_attr);
		//保存节点类型					
		nodeList[getCurrentId()].attr = current_attr;
		//保存节点属性
		saveAttachAttr(getCurrentId());
		saveRelation(nodeList[getCurrentId()].id);
	}
	displayList();
}

function saveRelation(current_id){	
	$('#tool select').each( function() {
		if ($(this).hasClass('relation')) {
			var selector = '#' + $(this).attr('id') + '>option';
			var target_id = parseInt($(this).attr('id').slice(3), 10);
			//console.log("selector   "+ selector);
			$(selector).each(function() {
				if ($(this).attr('value') != 'none' && $(this).attr('selected') == true) {
					for (var i = 0; i < rmatrix.length; i++) {
						//console.log("$(this).attr('value'):    "+ $(this).attr('value'));
						if ($(this).attr('value') == 'r_' + rmatrix[i].id) {
							console.log(nodeList);
							console.log("插入id："+current_id);
							console.log("插入target_id："+target_id);
							updateRelation(rmatrix[i].relation, current_id, target_id);
							break;
						}
					}
				} else if ($(this).attr('value') == 'none' && $(this).attr('selected') == true) {
					//alert();
					for (var i = 0; i < rmatrix.length; i++) {
						removeRelation(rmatrix[i].relation, current_id, target_id);
					}
				}
			});
		}
	});			
}

//list'标注'键响应
function list_annotate(id) {

	var input = units[id]['text'];	
	if(input != ''){		
		var current = {
			id:   0,
			text: input,
			from: units[id]['from'] - units[0]['from'],
			to:   units[id]['to'] - units[0]['from'],
			attr: 'nt_0',
			attachAttr: []	
		};
		
		if (nodeList.has(current)) {
			console.log('该节点已存在于标注列表中！');
			var current_id = getNodeIdByOffset(current['from'],current['to']);
			var current_attr = nodeList[current_id].attr;
			console.log("getNodeIdByOffset得到的attr为： " + current_attr);
			//生成标注板
			la_initialize(current_attr);	
			generateAttachDiv(current_id);		
			var select_code = '';	
			select_code += '<input type="button" id="unit_save_' + id + '" value="保存 " onclick="saveUnit(this)"/>';			
			$('#tool').append(select_code);
			
		} else {
			console.log('该节点第一次标注！');				
			//生成标注板	
			la_initialize(current.attr);				
			generateAttachDiv(-1);
			var select_code = '';	
	
			select_code += '<input type="button" id="unit_save_' + id + '" value="保存 " onclick="saveUnit(this)"/>';	
			$('#tool').append(select_code);
		}
	}
	displayList();
}

function relate(relation, a, b) {
	var current = {
		x: a,
		y: b
	};
	if (relation.has_r(current)){
		alert('该关系已经存在于关系列表中！');
	} else {
		relation.push(current);
	}
}

function removeRelation(relation, a, b) {
	var index = 0, flag = false;
	for (i = 0; i < relation.length; i++) {
		if (relation[i].x == a && relation[i].y == b) {
			index = i;
			flag = true;
			break;
		}
	}
	if (flag) { relation.splice(index, 1); }
}

function getAdjacentNodesById(range, id, type) {
	if (range == 0) return {list: []};
	
	var aList = new Array();
	var precount = 0, forcount = 0;
	
	if (id != 0) {
		for (var i = id - 1; i >= 0; i--) {
			if (nodeList[i].attr == type) {
				precount++;
				aList.push({
					ai: nodeList[i],
					oi: nodeList[i].id,
					order: i
				});
				if (precount >= range) {
					break;
				}
			}
		}
	}
	
	if (id != nodeList.length) {
		for (var i = id + 1; i < nodeList.length; i++) {
			if (nodeList[i].attr == type) {
				forcount++;
				aList.push({
					ai: nodeList[i],
					oi: nodeList[i].id,
					order: i
				});
				if (forcount >= range) {
					break;
				}
			}
		}
	}
	
	aList.sort(function(a, b) {
		if (a.order > b.order) {
			return 1;
		}
		if (a.order < b.order) {
			return -1;
		}
		return 0;
	});
	
	/*
	for (var i = 0; i < nodeList.length; i++) {
		if ((i > id - range - 1) && (i < id + range + 1) && (i != id)) {
			aList.push({
				ai: nodeList[i],
				oi: nodeList[i].id
			});
		}
	}
	*/
	return {list: aList, idx: id};
}

function getAdjacentNodes(range, type) {
	if (range == 0) return {list: []};	
	var aList = new Array();
	var precount = 0, forcount = 0;
	
	for(var i = 0; i < nodeList.length; i++){
		if (nodeList[i].attr == type) {
			forcount++;
			aList.push({
				ai: nodeList[i],
				oi: nodeList[i].id,
				order: i
			});
			if (forcount >= range) {
				break;
			}
		}		
	}	
	aList.sort(function(a, b) {
		if (a.order > b.order) {
			return 1;
		}
		if (a.order < b.order) {
			return -1;
		}
		return 0;
	});
	
	return {list: aList};
}


//右侧节点列表
function displayList() {
	var htmlpcs = '';
	for (var j = 0; j < ntype.length; j++) {
		htmlpcs += '<span class="toggle">类别：' + ntype[j] + '</span>';
		htmlpcs += '<dl>';
		
		for (i = 0; i < nodeList.length; i++) {		
			if (nodeList[i].attr == 'nt_' + j) {
				var button = '<input type="button" value="移除该节点" id="remove_' + i + '" />';
				//htmlpcs += '<dt id="h_' + i + '">节点内容: <em>' + nodeList[i].id + ':' + nodeList[i].text.limit() + '</em>' + button + '</dt>';
				htmlpcs += '<dt id="h_' + i + '">节点内容: <em>' + nodeList[i].id + ':' + nodeList[i].text.limit() + '</em>' + button + '</dt>';
				htmlpcs += '<dd>偏移量: <b>' + nodeList[i].from + '</b> to <b>' + nodeList[i].to + '</b></dd>';
			}
		}
		
		htmlpcs += '</dl>';
	}

	$('#list').html(htmlpcs);
	$('#list').fadeIn();
	
	// if (nodeList.length > 0 && $('#list').css('display') == 'none') {
		// $('#list').fadeIn();
	// } else if (nodeList.length == 0) {
		// $('#list').fadeOut();
	// }
	
	$('#list > span').click(function() {
		$(this).next().toggle();
		$(this).toggleClass('minus');
	});
	
	$('#list input').each( function() {
		if ($(this).attr('id').slice(0, 6) == 'remove') {
			$(this).click( function() {
				if (confirm('真的要删除该节点吗？')) {		
								
					//edited 改变列表标注状态
					current_id = $(this).attr('id').slice(7);
					console.log("current_id: "+current_id);		
					current_unit_id = getUnitIdByOffset(nodeList[current_id].from,nodeList[current_id].to);
					var selector = '#la_note_' + current_unit_id;
					$(selector).text('未标注');					
					//end					
					removeNode(parseInt($(this).attr('id').slice(7), 10));					
				}
				return false;
			});
		}
	});
}

function clear() {
	toolkit = $('#tool');
	toolkit.fadeOut();
	displayList();
}

function lequal(a, b) {
	if (a.text == b.text && a.from == b.from && a.to == b.to && a.term == b.term) {
		return true;
	}
	return false;
}

function requal(a, b) {
	if (a.x == b.x && a.y == b.y) {
		return true;
	}
	return false;
}

function array_has(val) {
	for (var i = 0; i < this.length; i++) {
		if (lequal(this[i], val)) {
			return true;
		}
	}
	return false;
}

function array_hasr(val) {
	//if (this.length == 0) return false;
	for (var i = 0; i < this.length; i++) {
		if (requal(this[i], val)) {
			return true;
		}
	}
	return false;
}
function array_has_val(val) {
	for (var i = 1; i < this.length; i++) {
		if (this[i] == val) {
			return true;
		}
	}
	return false;
}
// add these 2 methods to the prototype methods of `Array'
Array.prototype.has = array_has;
Array.prototype.has_r = array_hasr;
Array.prototype.has_v = array_has_val;

function removeNode(i) {
	for (var j = 0; j < rmatrix.length; j++) {
		for (var k = 0; k < rmatrix[j].relation.length; k++) {
			if (rmatrix[j].relation[k].x == nodeList[i].id || rmatrix[j].relation[k].y == nodeList[i].id) {
				rmatrix[j].relation.splice(k, 1);
				k--;
			}
		}
	}
	nodeList.splice(i, 1);
	displayList();
}


function load_list(){
	//从annotate中获取nodeList
	var content = $('.text_area').text();
	for (var i = 0; i < nodeList.length; i++) {
		nodeList[i].text = content.slice(nodeList[i].from, nodeList[i].to);
		//console.log("nodeList[i].text:  "+nodeList[i].text);
		current_unit_id = getUnitIdByOffset(nodeList[i].from,nodeList[i].to);
		var selector = '#la_note_' + current_unit_id;
		$(selector).text('已标注');					
	}	

	for (var i = 0; i < rmatrix.length; i++) {
		for (var j = 0; j < relationData[i].length; j++) {
			rmatrix[i].relation.push({
				x: relationData[i][j].x,
				y: relationData[i][j].y
			});
		}
	}	
	
	
}

$(document).ready( function() {
	// console.log(units);
	// console.log(nodeList);
	console.log("isTagged:  "+isTagged);
	if (isTagged) {
		load_list();
		displayList();
	}
		
	$('.text_area').mousemove(mouseMove);
	
	$('#ul_area').mousedown( function() {
		if ($('#tool').css('display') != 'none') {
			clear();
		}
	});	
	
	$('.list_annotate_button').click( function(){
		var id = $(this).attr('id');
		unit_id = id.split('_')[2];
		list_annotate(unit_id);	
		
	});
});








