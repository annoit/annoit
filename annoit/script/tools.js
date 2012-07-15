/*
 * annotate.js中独立功能的函数,留以后用
 */


String.prototype.limit = function() {
	var length = 16;
	if (this.length > length) {
		return this.slice(0, length) + '...';
	}
	return this.slice(0, length);
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

//保存
function saveAttachAttr(id) {
	if (/*hasAttachAttr(nodeList[id].attr) && */$('#attach_fields')) {
		nodeList[id].attachAttr = [];
		
		//将输入框组件的title+value存入节点的attachAttr中
		$('#attach_fields span input#input').each(function() {
			//alert($(this).val() + '-' + $(this).attr('id'));
			nodeList[id].attachAttr.push({
				title: $(this).attr('id').split('-')[1],
				value: $(this).val()
			});			
		});
		
		//应根据组件类别存储，不应该用id=attach-ke_type来做选择器（不够general）
		$('#attach_fields span select#attach-ke_type').each(function() {
			//alert($(this).val());
			if ($(this).val() != 'none') {
				nodeList[id].attachAttr.push({
					title: $(this).attr('id').split('-')[1],
					value: parseInt($(this).val().slice(8), 10)
				});
			}else{
				nodeList[id].attachAttr.push({
					title: $(this).attr('id').split('-')[1],
					value: null
				})
				console.log("ke_type未选时的保存value",parseInt($(this).val().slice(8), 10));
				console.log("ke_type未选时的保存title",$(this).attr('id').split('-')[1]);
			}
		});
		//将多选框组件的title+value存入节点的attachAttr中
		$('#attach_fields span select[multiple="multiple"]').each(function() {
			if ($(this).val() != 'none') {
				var attrlist = $(this).val() || [];
				for (var i = 0; i < attrlist.length; i++) {
					attrlist[i] = parseInt(attrlist[i].split('-')[2], 10);
				}				
				nodeList[id].attachAttr.push({
					title: $(this).attr('id').split('-')[1],
					value: attrlist.join(',')
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
				console.log("tag_group:",tag_group);
				console.log("title:",tag_group.split('-')[1]);
				console.log("attrlist:",attrlist.join(','));
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


//获取该节点范围内的节点数组
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

//获取该节点的所有属性
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