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
		if (getRelationId(rmatrix[i].relation, a, b)) {
			var del_id = getRelationId(rmatrix[i].relation, a, b);
			//alert(del_id);
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
//tags标签块
gadget.tags = function (id, list) {
	var id_part = (id == '' ? '' : ' id="' + id + '"');
//	var html = '<span id="tag_span" style="display:block;width:210px" id='+id_part+'>';
	var html = '<span class="tag_buttons" ' + id_part + ' style="display:block;width:210px" >';
	for (i = 0; i < list.length; i++) {
		html += gadget.tag_button(i,list[i].text,list[i].selected);
	}
	html+='</span>';
	//alert(html);
	return html;
}

gadget.tag_button = function (id, value, selected) {
	var html = '';
	html += '<input class="taglist" type="button" value="'+value+'" onClick=$(this).toggleClass(\'active\'); style="margin:2px;" tid="'+id+'" />';
	if(selected == true){

	}
	// html += '<input type="button" id="' + id + '" value="' + value + '" />';
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
				console.log(attachAttr[i].attr);
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
				// console.log("nodeInrange:   "+nodeInRange[i].id);
				// console.log(id);		
				// console.log(attach.title);				
				// console.log(getAttachAttrValue(id, attach.title));
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
			code += gadget.selectwithin('attach-' + attach.title, list);
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
			list.push({
				value: 'none',
				text: '请选择...',
				selected: false
			});
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
		
		// $('#attach_fields span select#attach-ke_type').each(function() {
			// //alert($(this).val());
			// if ($(this).val() != 'none') {
				// nodeList[id].attachAttr.push({
					// title: $(this).attr('id').split('-')[1],
					// value: parseInt($(this).val().slice(8), 10),
					// form: 'select fixed'
				// });
			// }else{
				// nodeList[id].attachAttr.push({
					// title: $(this).attr('id').split('-')[1],
					// value: null,
					// form: 'select fixed'
				// })
				// //console.log("ke_type未选时的保存value",parseInt($(this).val().slice(8), 10));
				// //console.log("ke_type未选时的保存title",$(this).attr('id').split('-')[1]);
			// }
		// });
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
				//alert(attrlist.join(','));
			}			
			//alert($(this).val());
			/*
			if ($(this).val() != 'none') {
				nodeList[id].attachAttr.push({
					title: $(this).attr('id').split('-')[1],
					value: parseInt($(this).val().split('-')[2], 10)
				});
			}*/
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
				//console.log("tag_group:",tag_group);
				//console.log("title:",tag_group.split('-')[1]);
				//console.log("attrlist:",attrlist.join(','));
				nodeList[id].attachAttr.push({					
					title: tag_group.split('-')[1], 
					value: attrlist.join(',')
				});
			}

			// alert("tag_group不空，为"+$(this).attr('id'));				
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
//var q = getDistinctList(l);
//整理所有节点，新建 span tag-Sid_id
function hltest() {
	var dlist = getDistinctList(nodeList);
	var areaList = [];
	for (var i = 0; i < dlist.length - 1; i++) {
		var temp = [];
		for (var j = 0; j < nodeList.length; j++) {
			if (nodeList[j].from <= dlist[i].offset &&
				nodeList[j].to >= dlist[i + 1].offset) {
				temp.push(j);
			}
		}
		if (temp.length > 0) {
			areaList.push({
				from:  dlist[i].offset,
				to:	   dlist[i + 1].offset,
				cover: temp,
				level: temp.length
			});
		}
	}
	//alert(areaList.length);
	var str = $('#text_area').text();
	//alert(str);
	var t = '';
	if (areaList.length == 0) {
		t = str;
	} else if (areaList.length > 0) {
		var spanclass = '';
		t = '';
		t += str.slice(0, areaList[0].from);
		for (var j = 0; j < areaList.length - 1; j++) {
			if (areaList[j].level <= 6) {
				spanclass = ' class="level' + areaList[j].level + '"';
			} else {
				spanclass = ' class="level4"';
			}
			t += '<span id="tag-' + j + '_' + areaList[j].cover.join('_') + '"' + spanclass + '>';
			t += str.slice(areaList[j].from, areaList[j].to);
			t += '</span>';
			t += str.slice(areaList[j].to, areaList[j + 1].from);
		}
		if (areaList[areaList.length - 1].level <= 4) {
			spanclass = ' class="level' + areaList[areaList.length - 1].level + '"';
		} else {
			spanclass = ' class="level4"';
		}
		t += '<span id="tag-' + (areaList.length - 1) + '_' + areaList[areaList.length - 1].cover.join('_') + '"' + spanclass + '>';
		t += str.slice(areaList[areaList.length - 1].from, areaList[areaList.length - 1].to);
		t += '</span>';
		t += str.slice(areaList[areaList.length - 1].to, str.length);
	}
	//rr();
	$('#text_area').html(t);
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

//标注选择框的初始化
function initialize() {
	var input = GetSelection().text;
	if (input != '') {
		// aList = getAdjacentNodes(3, getCaret('text_area').start);
		var slist = [];
		for (var i = 0; i < ntype.length; i++) {
			slist.push({
				value: 'nt_' + i,
				text:  ntype[i],
				selected: false
			});
		}
		var html = gadget.select('type_select', slist);
		html += gadget.button('do_tag', '标注');
		html += '<span id="tag_note">节点未标注</span>';
		var toolkit = $('#tool');
		toolkit.html(html)
		.css({
			'top':  mousePos.y + 5 + 'px',
			'left': mousePos.x + 5 + 'px'
		}).fadeIn();
		oldMousePos = mousePos;
	}
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

//标注选定的文本
function annotate() {
	var selection = GetSelection();
	var input = selection.text;
	if (input != '') {
		var current_attr = $('#type_select option').filter(':selected').attr('value');
		
		//console.log("current_attr:",current_attr);;
		
		var current = {
			id:   0,
			text: input,
			from: getCaret('text_area').start,
			to:   getCaret('text_area').end,
			attr: current_attr,
			attachAttr: []
		};
		
		if (nodeList.has(current)) {
			alert('该节点已存在于标注列表中！');
		} else {
			
			general_id++;//传来的general_id有时不是最大的 TODO
			current.id = general_id;
			
			//console.log("annotate()刚开始,general_id++之后：",general_id);
			
			nodeList.push(current);
			nodeList.sort(function(a, b) {
				if (a.from > b.from) {
					return 1;
				}
				if (a.from < b.from) {
					return -1;
				}
				return 0;
			});
			$('#tag_note').text('节点已标注');
			if (nodeList.length > 1 || hasAttachAttr(current_attr)) {
				var select_code = '';

				//console.log("标注时当前id=",getCurrentId());
				//console.log("标注节点的内含id=",nodeList[getCurrentId()].id);
				console.log("746行：   "+current_attr);
				if (hasAttachAttr(current_attr)) {
					var current_attach = getAttachAttrByType(current_attr);				
					select_code += '<span id="attach_fields">';
					for (var i = 0; i < current_attach.length; i++) {
						//alert(current_attach[i].form);						
						select_code += generateAttachAttr(getCurrentId(), current_attach[i]);
					}
					select_code += '</span>';
				}								

				//生成“关系选择区”
				if (nodeList.length > 1) {
					var aList = getAdjacentNodesById(adjacentOffset, getCurrentId(), current_attr);
					if (aList.list.length > 0) {
						var current_id = current.id;
						//alert("699行，current_id="+current_id)
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
				select_code += gadget.button('tag_relation', '保存');
				$('#tool').append(select_code);
				
				activate_tagButtons(getCurrentId(),"tags");																
				
				$('#type_select').change(function() {
					//alert();
				//alert($(this).attr('value'));
					var select_attr = '';
					$('#type_select > option').filter(':selected').each(function() {
						//alert($(this).attr('value'));
						select_attr = $(this).attr('value');
					});
					//alert(getRelatedList(current_sid, select_attr));
					if ($('#related-list')) {
						$('#related-list').replaceWith(getRelatedList(getCurrentId(), select_attr));
					}
				});
				
				$('#tag_relation').click(function() {
					
					nodeList[getCurrentId()].attr = $('#type_select option').filter(':selected').attr('value');
					saveAttachAttr(getCurrentId());
					
					$('#tool select').each( function() {
						if ($(this).attr('id') != 'type_select') {
							var selector = '#' + $(this).attr('id') + '>option';
							var target_id = parseInt($(this).attr('id').slice(3), 10);
							$(selector).each(function() {
								if ($(this).attr('value') != 'none' && $(this).attr('selected') == true) {
									for (var i = 0; i < rmatrix.length; i++) {
										if ($(this).attr('value') == 'r_' + rmatrix[i].id) {
											relate(rmatrix[i].relation, current_id, target_id);
											break;
										}
									}
								}
							});
						}
					});
				});
			}
		}
		displayList();
		hltest();
		highlight();
	}
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

function rr() {
	if (document.selection) {
		document.selection.empty();
	} else if (window.getSelection) {
		window.getSelection().removeAllRanges();
	}
}
//高亮文本
function highlight() {
	$('#list dl dt').each( function() {
		$(this).mouseover( function() {
			$(this).css({'cursor': 'pointer'});
			//$('#text_area span[id*=_' + test_id + '_]').toggleClass('highlight');
			//$('#text_area span[id$=_' + test_id + ']').toggleClass('highlight');
			$('#text_area span[id*=_' + $(this).attr('id').slice(2) + '_]').toggleClass('highlight');
			$('#text_area span[id$=_' + $(this).attr('id').slice(2) + ']').toggleClass('highlight');
			
		});
		$(this).mouseout( function() {
			$('#text_area span[id*=_' + $(this).attr('id').slice(2) + '_]').toggleClass('highlight');
			$('#text_area span[id$=_' + $(this).attr('id').slice(2) + ']').toggleClass('highlight');
		});
	});
	
	$('#text_area span').each( function() {
		$(this).mouseover( function() {
			$(this).css({
				'cursor': 'pointer'
			});
		});
		$(this).mouseover(function() {
			var test_list = $(this).attr('id').slice(4).split('_');
			var test_id = -1;
			if (test_list.length == 2) {
				test_id = parseInt(test_list[1], 10);
			} else {
				var min_len = 65535;
				for (var i = 1; i < test_list.length; i++) {
					var s2id = parseInt(test_list[i], 10);
					if (nodeList[s2id].to - nodeList[s2id].from < min_len) {
						min_len = nodeList[s2id].to - nodeList[s2id].from;
						test_id = s2id;
					}
				}
			}
			$('#text_area span[id*=_' + test_id + '_]').toggleClass('highlight');
			$('#text_area span[id$=_' + test_id + ']').toggleClass('highlight');
			// fix the bug of multi-highlight
		});
		$(this).mouseout(function() {
			var test_list = $(this).attr('id').slice(4).split('_');
			var test_id = -1;
			if (test_list.length == 2) {
				test_id = parseInt(test_list[1], 10);
			} else {
				var min_len = 65535;
				for (var i = 1; i < test_list.length; i++) {
					var s2id = parseInt(test_list[i], 10);
					if (nodeList[s2id].to - nodeList[s2id].from < min_len) {
						min_len = nodeList[s2id].to - nodeList[s2id].from;
						test_id = s2id;
					}
				}
			}
			$('#text_area span[id*=_' + test_id + '_]').toggleClass('highlight');
			$('#text_area span[id$=_' + test_id + ']').toggleClass('highlight');
		});
		$(this).click(function() {
			var test_list = $(this).attr('id').slice(4).split('_');
			console.log('test_list未split前=',$(this).attr('id'));
			var test_id = -1;
			if (test_list.length == 2) {
				test_id = parseInt(test_list[1], 10);
			} else {
				var min_len = 65535;
				for (var i = 1; i < test_list.length; i++) {
					var s2id = parseInt(test_list[i], 10);
					if (nodeList[s2id].to - nodeList[s2id].from < min_len) {
						min_len = nodeList[s2id].to - nodeList[s2id].from;
						test_id = s2id;
					}
				}
			}
			var current_sid = test_id;
			console.log('test_id=',test_id);
			var select_code = '';
			var current_attr = nodeList[current_sid].attr;
			var slist = [];
			for (var i = 0; i < ntype.length; i++) {
				var isSelected = false;
				if (nodeList[current_sid].attr == ('nt_' + i)) {
					isSelected = true;
				}
				slist.push({
					value: 'nt_' + i,
					text:  ntype[i],
					selected: isSelected
				});
			}
			select_code += gadget.select('type_select', slist);
			select_code += '<span id="tag_note">节点已标注</span>';
			select_code += '<span id="attach_fields">';
			
			//console.log('点击节点id=',current_sid);
			//console.log('点击节点内部id=',nodeList[current_sid].id);
			
			if (hasAttachAttr(current_attr)) {
					var current_attach = getAttachAttrByType(current_attr);
					for (var i = 0; i < current_attach.length; i++) {
						select_code += generateAttachAttr(current_sid, current_attach[i]);
					}
			}
			select_code += '</span>';				
			//点击修改时的“关系选择区”
			var aList = getAdjacentNodesById(adjacentOffset, current_sid, nodeList[current_sid].attr);
			if (aList.list.length > 0) {
				var current_id = nodeList[current_sid].id;
				select_code += '<span id="related-list">';
				for (var i = 0; i < aList.list.length; i++) {
					// generate selection list
					var slist = [{
						value: 'none',
						text: '请选择...',
						selected: false
					}];
					for (var j = 0; j < rmatrix.length; j++) {
						slist.push({
							value: 'r_' + rmatrix[j].id,
							text:  rmatrix[j].type,
							selected: false
						});
					}
					var flag = false;
					for (var j = 0; j < rmatrix.length; j++) {
						if (rmatrix[j].relation.has_r({x: nodeList[current_sid].id, y: aList.list[i].oi})) {
							slist[j + 1].selected = true;
							flag = true;
							break;
						}
					}
					if (!flag) {
						slist[0].selected = true;
					}
					// ^ temporary solution
					select_code += '<span class="ritem">';
					select_code += gadget.select('nr_' + aList.list[i].oi, slist);
					select_code += '<span class="tagtip">' + aList.list[i].ai.text.limit() + '</span>';
					select_code += '</span>';
				}
				select_code += '</span>';
			}
			select_code += gadget.button('tag_relation', '保存');
			$('#tool').html(select_code);
			
			//TODO 目前这个只适合title=tags的情况，比较单一			
			activate_tagButtons(current_sid,"tags");
			
			$('#type_select').change(function() {
				//alert($(this).attr('value'));
				var select_attr = '';
				$('#type_select > option').filter(':selected').each(function() {
					//alert($(this).attr('value'));
					select_attr = $(this).attr('value');
					if (hasAttachAttr(select_attr)) {
						var current_attach = getAttachAttrByType(select_attr);
						var attachcode = '';
						for (var i = 0; i < current_attach.length; i++) {
							attachcode += generateAttachAttr(current_sid, current_attach[i]);
						}
						attachcode = '<span id="attach_fields">' + attachcode + '</span>';
						if ($('#attach_fields')) {
							$('#attach_fields').remove();
							$(attachcode).insertAfter($('#tag_note'));
						} else {
							$(attachcode).insertAfter($('#tag_note'));
						}
					} else {
						if ($('#attach_fields')) {
							$('#attach_fields').remove();
						}
					}
				});
				//alert(getRelatedList(current_sid, select_attr));
				if ($('#related-list')) {
					$('#related-list').replaceWith(getRelatedList(current_sid, select_attr));
				}
				
			});
			
			$('#tag_relation').click(function() {
				//alert();
				saveAttachAttr(current_sid);
				
				nodeList[current_sid].attr = $('#type_select option').filter(':selected').attr('value');
				$('#tool select').each( function() {
					if ($(this).attr('id') != 'type_select') {
						var selector = '#' + $(this).attr('id') + '>option';
						var target_id = parseInt($(this).attr('id').slice(3), 10);
						$(selector).each(function() {
							if ($(this).attr('value') != 'none' && $(this).attr('selected') == true) {
								for (var i = 0; i < rmatrix.length; i++) {
									if ($(this).attr('value') == 'r_' + rmatrix[i].id) {
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
				displayList();
				hltest();
				highlight();
			});
			$('#tool').css({
				'top':  mousePos.y + 5 + 'px',
				'left': mousePos.x + 5 + 'px'
			}).fadeIn();
			
		});
	});
}

//tag_buttons中已选择项的显示
function activate_tagButtons(nodeId, title){

	//TODO 输入title=tags 需改得更通用
	//TODO $("input.taglist")选择的是该节点所有的taglist的button，需改得更通用
	
	var taglist = $("input.taglist");
	var index;			
	var selectedTags = getAttachAttrValue(nodeId, title);
	//selectedTags格式为2,3,5或false
	if(selectedTags!=false){
		//console.log("tag的按钮attrList，selectedTags=",selectedTags);
		for (var i = 0; i<selectedTags.split(',').length; i++){
			index = parseInt(selectedTags.split(',')[i]);
			$(taglist[index]).addClass("active");
		}
	}
	
	//新插入的节点内的id: nodeList[].id都为0			
	//console.log("点击的节点，当前id=",nodeId);
	//console.log("点击的节点，内部id=",nodeList[nodeId].id);				
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
				htmlpcs += '<dt id="h_' + i + '">节点内容: <em>' + nodeList[i].id + ':' + nodeList[i].text.limit() + '</em>' + button + '</dt>';
				htmlpcs += '<dd>偏移量: <b>' + nodeList[i].from + '</b> to <b>' + nodeList[i].to + '</b></dd>';
			}
		}
		
		htmlpcs += '</dl>';
	}

	$('#list').html(htmlpcs);
	if (nodeList.length > 0 && $('#list').css('display') == 'none') {
		$('#list').fadeIn();
	} else if (nodeList.length == 0) {
		$('#list').fadeOut();
	}
	
	$('#list > span').click(function() {
		$(this).next().toggle();
		$(this).toggleClass('minus');
	});
	
	$('#list input').each( function() {
		if ($(this).attr('id').slice(0, 6) == 'remove') {
			$(this).click( function() {
				if (confirm('真的要删除该节点吗？')) {
					removeNode(parseInt($(this).attr('id').slice(7), 10));
					hltest();
					highlight();
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
	hltest();
	highlight();
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

function GetSelection() {
	var range_text = '';
	if ($.browser.msie) {
		selection = document.selection;
		range = selection.createRange();
		range_text = range.text;
	} else {
		try {
			selection = window.getSelection();
			range = selection.getRangeAt(0);
			range_text = range.toString();
		} catch(e) {
			return {range: null, text: ''};
		}
	}
	return {range: range, text: range_text};
}

function getCaret(id) {
	var selectionStart = 0;
	var selectionEnd = 0;
	var el = document.getElementById(id);

	if( document.selection ) {
		var range_dupe = document.selection.createRange();
		var stored_range = range_dupe.duplicate();
		stored_range.moveToElementText( el );
		stored_range.setEndPoint( 'EndToEnd', range_dupe );
		selectionStart = stored_range.text.length - range_dupe.text.length;
		selectionEnd = selectionStart + range_dupe.text.length;
	} else {
		range = GetSelection().range;
		var endChar = range.toString().length;
		var stored_range = range.cloneRange();
		stored_range.collapse(true);
		stored_range.setStart(el, 0);
		selectionStart = stored_range.toString().length;
		selectionEnd = selectionStart + endChar;
	}
	return {start: selectionStart, end: selectionEnd};
};

//接受controllers->annotate.php的传参
$(document).ready( function() {
	
	if (isTagged) {
		var content = $('#text_area').text();
		for (var i = 0; i < nodeList.length; i++) {
			nodeList[i].text = content.slice(nodeList[i].from, nodeList[i].to);
		}
		for (var i = 0; i < rmatrix.length; i++) {
			for (var j = 0; j < relationData[i].length; j++) {
				rmatrix[i].relation.push({
					x: relationData[i][j].x,
					y: relationData[i][j].y
				});
			}
		}
		displayList();
		hltest();
		highlight();
	}
	$('#text_area').mousemove(mouseMove);
	$('#text_area').mousedown( function() {
		if ($('#tool').css('display') != 'none') {
			clear();
		}
	});
	$('#text_area').mouseup( function() {
		if (GetSelection().text.length > 0) {
			$('#text_area span').unbind('click');
		}
		initialize();
		$('#do_tag').click( function() {
			annotate();
			return false;
		});
	});
	/*
	$('#list').mouseover( function() {
		if ($('#tool').css('display') != 'none') {
			clear();
		}
	});*/
});
