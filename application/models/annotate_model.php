<?php
include_once('application/phpconsole/e4phpconsole.php');

class Annotate_model extends CI_Model {
	
	function get_available_resources() {
		// nothing
		$res_list = array();
		$logged_username = $this->session->userdata('username');
		$sql = "select resource.id, created_date, status from resource, user
					where resource.user_id = user.id
					and user.username = '{$logged_username}'";
		$query = $this->db->query($sql);
		
		foreach ($query->result_array() as $key => $row) {
			$res_list[$key]['id'] = $row['id'];
			$res_list[$key]['created_date'] = $row['created_date'];
			$res_list[$key]['status'] = $row['status'];
		}
		return $res_list;
	}
	function get_node_list_as_json($res_id) {
		$this->db
			->select('inner_id, offset_start, offset_end, type')
			->from('node_logic')
			->where('resource_id', $res_id);
		$rows = $this->db->get()->result_array();
		if (count($rows) == 0) {
			return array('json' => '[]', 'total' => 0, 'tagged' => 'false');
		}
		$nodes = array();
		$total = 0;
		$json_str = '[';
		for ($i = 0; $i < count($rows); $i++) {
			$params = array(
				'id' => $rows[$i]['inner_id'],
				'text' => '',
				'from' => $rows[$i]['offset_start'],
				'to' => $rows[$i]['offset_end'],
				'attr' => $rows[$i]['type']
			);
			$json_str .= '{';
			$json_str .= "id: {$params['id']},";
			$json_str .= "text: '{$params['text']}',";
			$json_str .= "from: {$params['from']},";
			$json_str .= "to: {$params['to']},";
			$json_str .= "attr: '{$params['attr']}'";
			$json_str .= '}';
			if ($i != count($rows) - 1) {
				$json_str .= ',';
			}
			//选择排序，保证$total是最大的
			if ($params['id'] > $total) {
				$total = $params['id'];
			}
		}
		$json_str .= ']';
		return array('json' => $json_str, 'total' => $total + 1, 'tagged' => 'true');
		//return json_encode($nodes);
	}

	//返回units
	function get_units($task_id) {
		$task = $this->db->query('SELECT * FROM task WHERE id = ?', $task_id)->result_array();
		$unit_ids = explode(',',$task[0]['list_id']);
		
		//获取资源内容
		$result = $this->db->query(
			'SELECT title, content FROM resource, unit WHERE unit.id = ? AND unit.resource_id = resource.id',
			$unit_ids[0])->result_array();
		$res_content = $result[0]['content'];
		$res_title = $result[0]['title'];
		//获取unit内容
		$units = array();
		for($i=0;$i<count($unit_ids);$i++){
			$units[$i]['id'] = $unit_ids[$i];
			$result = $this->db->query(
				'SELECT name, begin_offset, end_offset FROM unit WHERE id = ?', $unit_ids[$i])->result_array();
			$units[$i]['name'] = $result[0]['name'];
			$units[$i]['from'] = $result[0]['begin_offset'];
			$units[$i]['to'] = $result[0]['end_offset'];
			$units[$i]['text'] = mb_substr($res_content, $units[$i]['from'],
										$units[$i]['to'] - $units[$i]['from'], 'UTF-8');
			//println("unit_content:  ".$units[$i]['content']);
		}
		return $units;	
	}


	function get_nodes_as_json($task_id) {
		//println("get_nodes_as_json");		
		$config_res_path = 'resource_config/';
		
		$db_nodes = $this->db->query('SELECT * FROM node WHERE task_id = ? ORDER BY offset_start', $task_id)->result_array();
		$task = $this->db->query('
			SELECT resource_id,begin_offset FROM unit,task WHERE task.id = ? AND unit.id = task.unit_id', $task_id)->result_array();
	
		$resource_id = $task[0]['resource_id'];
		
		//println("resource_id:   ".$resource_id);
		//载入资源配置文件
		$config_res_name = $config_res_path.$resource_id.'.php';
		$config_ke_table = 'ke';
		if(file_exists($config_res_name)){
			//通过include_once载入配置
			include_once($config_res_name);
		}else{
			include_once($config_res_path.'template.php');
		}
		
		//println("SELECT * FROM $config_ke_table WHERE task_id = $task_id");
		
		// hard coded
		$ke = $this->db->query("SELECT * FROM $config_ke_table WHERE task_id = ?", $task_id)->result_array();
		
		$begin = $task[0]['begin_offset'];
		$nodes = array();
		$total = 0;
		
		//println($config_attachAttr);
		
		//针对$config_attachAttr配置的含有属性的节点种类，读取相应字段
		
		foreach ($db_nodes as $db_node) {
			$ke_node_attr = array();		
			//println($db_node['id']."的db_nodeType:   ".$db_node['type']);	
			foreach ($ke as $ke_item) {
				if ($db_node['id'] == $ke_item['node_id']) {											
					foreach ($config_attachAttr as $config_node_attr) {
						$type = $config_node_attr['type'];					
						if($db_node['type']==$type){
							foreach ($config_node_attr['attr'] as $key => $config_attr) {								
								//属性名称
								//$attr_name = $config_attr['title'];	
								$attr_name = 'attr'.($key+1);
								//println($attr_name);
								if($ke_item[$attr_name] != NULL){
									$node_attr_value = $ke_item[$attr_name];
									//若是select within,需要进行id映射								
									if($config_attr['form']=='select within'){
										//TODO 此处用$db_nodes还是$db_nodes？待测试
										$node_attr_value = $this->transfer_joined_list($ke_item[$attr_name], $db_nodes, 1);
									}
									//println("attr_value:   $node_attr_value");
																	
									$node_attr[] = array(
											//'title' => $attr_name,
											'title' => $config_attr['title'],
											//'value' => $this->map($db_nodes, $ke_item['core_term'] + 0)
											'value' => $node_attr_value,
											'form' => $config_attr['form']
										);
								}
							}
							break;
						}				
					}
					if(count($node_attr)>0){
						$ke_node_attr = $node_attr;	
					}									
					break;
				}
			}
			$nodes[] = array(
				'id' => $db_node['inner_id'] + 0,
				'text' => '',
				'from' => $db_node['offset_start'] - $begin,
				'to' => $db_node['offset_end'] - $begin,
				'attr' => $db_node['type'],
				'attachAttr' => $ke_node_attr
			);
			//选择排序，选择最大的total(annotate.js页面的general_id,node表的inner_id)
			if($db_node['inner_id'] > $total){
				$total = $db_node['inner_id'];
			}	
		}

		//println("get_nodes_as_json结束");
		$json = json_encode($nodes);
		//println("json:   ".$json);
		return array('json' => $json, 'total' => $total, 'tagged' => 'true');
		
		// foreach ($db_nodes as $db_node) {
			// $ke_node_attr = array();		
			// foreach ($ke as $ke_item) {
				// if ($db_node['id'] == $ke_item['node_id']) { 										
					// if ($ke_item['ke_type'] != NULL) {
						// $ke_node_attr = array(
							// array(
								// 'title' => 'ke_type',
								// 'value' => $ke_item['ke_type'] + 0
							// ),
							// array(
								// 'title' => 'core_term',
								// //'value' => $this->map($db_nodes, $ke_item['core_term'] + 0)
								// 'value' => $this->transfer_joined_list($ke_item['core_term'], $db_nodes, 1)
							// ),
							// array(
								// 'title' => 'term_set',
								// 'value' => $this->transfer_joined_list($ke_item['term_set'], $db_nodes, 1)
							// ),
							// array(
								// 'title' => 'tags',
								// 'value' => $ke_item['tags']
							// )
						// );
					// } else {
						// $ke_node_attr = array(
							// array(
								// 'title' => 'core_term',
								// //'value' => $this->map($db_nodes, $ke_item['core_term'] + 0)
								// 'value' => $this->transfer_joined_list($ke_item['core_term'], $db_nodes, 1)
							// ),
							// array(
								// 'title' => 'term_set',
								// 'value' => $this->transfer_joined_list($ke_item['term_set'], $db_nodes, 1)
							// ),
							// array(
								// 'title' => 'tags',
								// 'value' => $ke_item['tags']
							// )
						// );
					// }
					// break;
				// }
			// }		
			// $nodes[] = array(
				// 'id' => $db_node['inner_id'] + 0,
				// 'text' => '',
				// 'from' => $db_node['offset_start'] - $begin,
				// 'to' => $db_node['offset_end'] - $begin,
				// 'attr' => $db_node['type'],
				// 'attachAttr' => $ke_node_attr
			// );
			// //选择排序，选择最大的total(annotate.js页面的general_id,node表的inner_id)
			// if($db_node['inner_id'] > $total){
				// $total = $db_node['inner_id'];
			// }
		// }		
	}
	
	function get_relations_as_json($task_id) {
		$db_relations = $this->db->query('
			SELECT * FROM relationship WHERE task_id = ?', $task_id)->result_array();
		$db_nodes = $this->db->query('
			SELECT * FROM node WHERE task_id = ? ORDER BY inner_id', $task_id)->result_array();
		
		$relations = array();
		$relation_num = 7;
		for ($i = 0; $i < $relation_num; $i++) {
			$relations[] = array();
		}
		
		foreach ($db_relations as $db_relation) {
			for ($i = 0; $i < $relation_num; $i++) {
				if ($db_relation['type'] == $i) {
					$relations[$i][] = array(
						'x' => $this->map($db_nodes, $db_relation['node1_id']),
						'y' => $this->map($db_nodes, $db_relation['node2_id'])
					);
				}
			}
		}
		return json_encode($relations);
	}
	
	function map($map, $key) {
		foreach ($map as $value) {
			if ($value['id'] == $key) {
				return $value['inner_id'] + 0;
			}
		}
	}
	
	function reverse_map($map, $key) {
		foreach ($map as $value) {
			if ($value['inner_id'] == $key) {
				return $value['id'] + 0;
			}
		}
		return FALSE;
	}
	
	function generate_logic_tree($list) {
		
		for ($i = 0; $i < count($list) - 1; $i++) {
			$list[$i]['type'] = substr($list[$i]['type'], 3) + 0;
		}
		
		$this->load->library('tree');

		$top_level = 3;
		for ($i = 0; $i < count($list) - 1; $i++) {
			if ($list[$i]['type'] < $top_level) {
				$top_level = $list[$i]['type'];
			}
		}
		
		$tree = $this->tree->get_instance(-1, 'root', array());
		
		for ($level = $top_level; $level < 4; $level++) {
			
			$aquery = array();
			$units = get_nodes_by_level($tree, $level - 1, $top_level - 1);
			//echo '<b>' . $level . '</b><pre>'; var_dump($units); echo '</pre>';

			for ($i = 0; $i < count($list) - 1; $i++) {
				if ($list[$i]['type'] == $level) {
					$aquery[] = $list[$i];
				}
			}
			//var_dump($aquery);
			
			if ($level == $top_level) {
				foreach ($aquery as $row) {
					$tree->childs[] = $this->tree->get_instance($row['id'], $row['title'], array(), $level);
				}
			} else {
				foreach ($aquery as $row) {
					foreach ($units as $unit) {
						if ($row['parent_id'] == $unit->id) {
							$unit->childs[] = $this->tree->get_instance($row['id'], $row['title'], array(), $level);
						}
					}
				}
			}
		}
		return $tree;
	}
	function get_unit_tree() {
		$this->load->library('tree');
		#$obj = $this->test->getInstance(512);
		#return $obj->a;
		/*
		$data = array(
			array('unit_id' => 0, 'unit_name' => '精华', 'parent_id' => -1, 'type' => 0),
			array('unit_id' => 1, 'unit_name' => '语法', 'parent_id' => -1, 'type' => 0),
			array('unit_id' => 2, 'unit_name' => '对象', 'parent_id' => -1, 'type' => 0),
			array('unit_id' => 3, 'unit_name' => 'Why Javascript?', 'parent_id' => 0, 'type' => 1),
			array('unit_id' => 4, 'unit_name' => '空白', 'parent_id' => 1, 'type' => 1),
			array('unit_id' => 5, 'unit_name' => '标识符', 'parent_id' => 1, 'type' => 1),
			array('unit_id' => 6, 'unit_name' => '字面量', 'parent_id' => 1, 'type' => 1),
			array('unit_id' => 7, 'unit_name' => '反射', 'parent_id' => 2, 'type' => 1),
			array('unit_id' => 8, 'unit_name' => '子节点1', 'parent_id' => 4, 'type' => 2),
			array('unit_id' => 9, 'unit_name' => '子节点2', 'parent_id' => 4, 'type' => 2),
			array('unit_id' => 10, 'unit_name' => '子节点3', 'parent_id' => 5, 'type' => 2),
			array('unit_id' => 11, 'unit_name' => '子节点4', 'parent_id' => 10, 'type' => 3)
		);*/
		$tree = $this->tree->get_instance(-1, 'root', array());
		
		for ($level = 0; $level < 4; $level++) {
			$aquery = array();
			$units = get_nodes_by_level($tree, $level);
			$this->db->select('id, name, parent_id')->from('unit')->where('type', $level);
			$query = $this->db->get();
			$aquery = $query->result_array();
			//foreach ($data as $item) {
			//	if ($item['type'] == $level) {
			//		$aquery[] = $item;
			//	}
			//}
			
			if ($level == 0) {
				foreach ($aquery as $row) {
					$tree->childs[] = $this->tree->get_instance($row['id'], $row['name'], array());
				}
			} else {
				foreach ($aquery as $row) {
					foreach ($units as $unit) {
						if ($row['parent_id'] == $unit->id) {
							$unit->childs[] = $this->tree->get_instance($row['id'], $row['name'], array());
						}
					}
				}
			}
		}
		return $tree;	
	}

	function hasAttachAttr($type) {
		$typelist = array(
			'nt_1' => array(
				'core_term', 'term_set'
			)
		);
		
		foreach ($typelist as $key => $value) {
			if ($key == $type) {
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	function transfer_joined_list($joined_list, $map, $mode = 0) {
		$split_list = explode(',', $joined_list);
		if ($mode == 0) {
			foreach ($split_list as &$item) {
				$item = $this->reverse_map($map, $item + 0);
			}
		} elseif ($mode == 1) {
			foreach ($split_list as &$item) {
				$item = $this->map($map, $item + 0);
			}
		}
		return implode(',', $split_list);
	}

	function update_normal_all($nodes, $relations) {
		mb_internal_encoding('UTF-8');
		$config_res_path = 'resource_config/';
				
		$task_id = $this->session->userdata('task_id');
		$unit_id = $this->session->userdata('unit_id');
		
		// delete old
		$this->db->trans_start();
		//$this->db->query('DELETE FROM node WHERE task_id = ?', $task_id);
		$exsisted_nodes = $this->db->query('SELECT * FROM node WHERE task_id = ? ORDER BY id', $task_id)->result_array();
		$total_ex_nodes = count($exsisted_nodes);
		
		$unit = $this->db->query(
			'SELECT * FROM unit WHERE id = ?', $unit_id)->result_array();
		$res_id = $unit[0]['resource_id'];
		
		//$result = $this->db->query('SELECT content FROM resource WHERE id = ?', $res_id)->result_array();
		//$content = str_replace("\r", "", $result[0]['content']);
		
		if (count($nodes) > 0) {
			if (count($nodes) <= $total_ex_nodes) { // current <= exsisted
				if (count($nodes) < $total_ex_nodes) {
					for ($i = $total_ex_nodes - 1; $i >= count($nodes); $i--) {
						$this->db->query('DELETE FROM node WHERE id = ?', $exsisted_nodes[$i]['id']);
					}
				}
				
				for ($i = 0; $i < count($nodes); $i++) {
					$node = $nodes[$i];
					$begin = $node['from'];
					$end = $node['to'];
					//$n1 = search_char($content, 0, $begin, "\n");
					//$n2 = search_char($content, $begin, $end, "\n");
					//$begin += $n1;
					//$end += $n1 + $n2;
					
					$update_sql = "UPDATE node
						set inner_id = ?, offset_start = ?, offset_end = ?, real_start = ?, real_end = ?, type = ?
						WHERE id = ?";
					$this->db->query($update_sql, array(
						$node['id'], $node['from'], $node['to'], $begin, $end, $node['attr'], $exsisted_nodes[$i]['id']
					));
				}
			} elseif (count($nodes) > $total_ex_nodes) { // current > exesisted
				for ($i = 0; $i < $total_ex_nodes; $i++) {
					$node = $nodes[$i];
					$begin = $node['from'];
					$end = $node['to'];
					
					$update_sql = "UPDATE node
						set inner_id = ?, offset_start = ?, offset_end = ?, real_start = ?, real_end = ?, type = ?
						WHERE id = ?";
					$this->db->query($update_sql, array(
						$node['id'], $node['from'], $node['to'], $begin, $end, $node['attr'], $exsisted_nodes[$i]['id']
					));
				}
				for ($i = $total_ex_nodes; $i < count($nodes); $i++) {
					$node = $nodes[$i];
					$begin = $node['from'];
					$end = $node['to'];
					//$n1 = search_char($content, 0, $begin, "\n");
					//$n2 = search_char($content, $begin, $end, "\n");
					//$begin += $n1;
					//$end += $n1 + $n2;
					
					$update_sql = "INSERT INTO node
						(resource_id, task_id, inner_id, offset_start, offset_end, real_start, real_end, type)
						VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
					$this->db->query($update_sql, array(
						$res_id, $task_id, $node['id'], $node['from'], $node['to'], $begin, $end, $node['attr']
					));
				}
			}
						
			//return;
			//$counter = 1;
			/*
			foreach ($nodes as $node) {
				
				$begin = $node['from'];
				$end = $node['to'];
				$n1 = search_char($content, 0, $begin, "\n");
				$n2 = search_char($content, $begin, $end, "\n");
				$begin += $n1;
				$end += $n1 + $n2;
				
				$update_sql = "INSERT INTO node
					(resource_id, task_id, inner_id, offset_start, offset_end, real_start, real_end, type)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
				$this->db->query($update_sql, array(
					$res_id, $task_id, $node['id'], $node['from'], $node['to'], $begin, $end, $node['attr']
				));
				//var_dump($node['attachAttr'] == null);
				//var_dump($node);
				//echo $task_id;
				//$counter++;
			}*/
		}
		$this->db->trans_complete();

		// $this->output->enable_profiler(TRUE);		
		// $this->benchmark->mark('code_start');
						
		//载入资源配置文件
		$config_res_name = $config_res_path.$res_id.'.php';
		
		if(file_exists($config_res_name)){
			//通过include_once载入配置
			include_once($config_res_name);
		}else{
			include_once($config_res_path.'template.php');
		}
		
		//hardcoded 需改
		$this->db->trans_start();
		//删掉之前的所有node，重新插入
		$this->db->query("DELETE FROM $config_ke_table WHERE task_id = ?", $task_id);		
		if (count($nodes) > 0) {
			$db_nodes = $this->db->query('SELECT * FROM node WHERE task_id = ?', $task_id)->result_array();
			foreach ($nodes as $node) {
				if ($node['attachAttr'] != null) {
					$node_data = array(
						'node_id' => $this->reverse_map($db_nodes, $node['id']),
						'task_id' => $task_id	
					);	

					//有时前台传回的格式是$node['attachAttr'][0]
					foreach ($node['attachAttr'] as $key => $node_attr) {
						//前台传回的attachAttr里的attr可能有缺失，所以得遍历一次配置文件
						//从配置文件中读取$config_attachAttr的格式
						foreach ($config_attachAttr as $config_node_attr) {
							$type = $config_node_attr['type'];
							//前台传回的nt_1存在$node['attr']而不是$node['type']里
							if($node['attr']==$type){
								foreach ($config_node_attr['attr'] as $key2 => $config_attr) {
									if($node_attr['title'] == $config_attr['title']){
										$attr_value = $node_attr['value'];										
										if($node_attr['form']=='select within'){
											//select within要转换坐标
											$attr_value = $this->transfer_joined_list($node_attr['value'],$db_nodes);
										}
										//属性名称
										$attr_name = 'attr'.($key2+1);
										println("attr_name - attr_value   :    ".$config_attr['title']." - $attr_value");
										$node_data[$attr_name] = $attr_value;
										break;
									}							
								}								
								break;
							}
						}
					}					
					//println($node_data);
					$this->db->insert($config_ke_table,$node_data);		
					println("插入finish");								
				}
			}
		}
		
		$this->db->trans_complete();
		// $this->benchmark->mark('code_end');
		// echo $this->benchmark->elapsed_time('code_start', 'code_end');		
		
		// //hardcoded 需改
		// $this->db->trans_start();
		// $this->db->query("DELETE FROM $config_ke_table WHERE task_id = ?", $task_id);
		// if (count($nodes) > 0) {			
			// $db_nodes = $this->db->query('SELECT * FROM node WHERE task_id = ?', $task_id)->result_array();
			// //var_dump($db_nodes);				
			// foreach ($nodes as $node) {
				// //if仅选择'nt_1'类型的节点				
				// if ($this->hasAttachAttr($node['attr']) && $node['attachAttr'] != null) {
// 									
					// //判断属性attachAttr的保存顺序和保存内容（有可能保存不全）,限定了节点类型，hardcoded	
					// $ke_type_index=-1;//这些属性应从文件里读取
					// $core_term_index=-1;
					// $term_set_index=-1;
					// $tags_index=-1;		
					// if($node['attr']=='nt_1'){
						// //println("node['attachAttr']长度：".count($node['attachAttr']));						
						// for($j=0;$j<count($node['attachAttr']);$j++){
							// //println("执行j：".$j);
							// if($node['attachAttr'][$j]['title'] == 'ke_type') {
								// $ke_type_index = $j;	
								// //println("ke_type_index = ".$ke_type_index);
							// }else if($node['attachAttr'][$j]['title'] == 'core_term') {
								// $core_term_index = $j;	
								// //println("core_term_index = ".$core_term_index);						
							// }else if($node['attachAttr'][$j]['title'] == "term_set") {
								// $term_set_index = $j;
							// }else if($node['attachAttr'][$j]['title'] == "tags") {
								// $tags_index = $j;							
							// }																						
						// }					
					// }			
					// println("ke_type:".$ke_type_index == -1 ? NULL : $node['attachAttr'][$ke_type_index]['value']);
					// $update_sql = "INSERT INTO $config_ke_table(node_id, task_id, ke_type, core_term, term_set, tags)
						// VALUES (?, ?, ?, ?, ?, ?)";
					// $this->db->query($update_sql, array(
						// $this->reverse_map($db_nodes, $node['id']),
						// $task_id,
						// //$this->reverse_map($db_nodes, $node['attachAttr'][1]['value']),
						// //$node['attachAttr'][0]['value']												
						// $ke_type_index == -1 ? NULL : $node['attachAttr'][$ke_type_index]['value'],
						// $core_term_index==-1? "" :$this->transfer_joined_list($node['attachAttr'][$core_term_index]['value'], $db_nodes),
						// $term_set_index==-1? "" :$this->transfer_joined_list($node['attachAttr'][$term_set_index]['value'], $db_nodes),
						// $tags_index == -1? "" :$node['attachAttr'][$tags_index]['value']
					// ));
				// }
			// }
			// //}
		// }		
		// $this->db->trans_complete();
		// $this->benchmark->mark('code_end');
		// echo $this->benchmark->elapsed_time('code_start', 'code_end');		
		
		//end
		
		// $this->db->trans_start();
		// // hardcoded here, however it will be altered later
		// $this->db->query('DELETE FROM ke WHERE task_id = ?', $task_id);
		// if (count($nodes) > 0) {			
				// $db_nodes = $this->db->query('SELECT * FROM node WHERE task_id = ?', $task_id)->result_array();
				// //var_dump($db_nodes);
				// foreach ($nodes as $node) {
				// if ($this->hasAttachAttr($node['attr']) && $node['attachAttr'] != null) {					
					// //foreach ($node['attachAttr'] as $attach_attr) {
					// //echo $this->reverse_map($db_nodes, $node['id']);
					// //var_dump($node['attachAttr']);
					// //echo $attach_attr[0]['value'] . '-' . $attach_attr[1]['value'];
					// // hardcoded here, however it will be altered later
					// $update_sql = "INSERT INTO ke(node_id, task_id, ke_type, core_term, term_set)
						// VALUES (?, ?, ?, ?, ?)";
					// $this->db->query($update_sql, array(
						// $this->reverse_map($db_nodes, $node['id']),
						// $task_id,
						// //$this->reverse_map($db_nodes, $node['attachAttr'][1]['value']),
						// //$node['attachAttr'][0]['value']
						// $node['attachAttr'][$ke_type_index]['value'] == 0 ? NULL : $node['attachAttr'][$ke_type_index]['value'],
						// $core_term_index==-1? "" :$this->transfer_joined_list($node['attachAttr'][$core_term_index]['value'], $db_nodes),
						// $term_set_index==-1? "" :$this->transfer_joined_list($node['attachAttr'][$term_set_index]['value'], $db_nodes)
					// ));
				// }
			// }
			// //}
		// }
		// $this->db->trans_complete();
		
		$this->db->trans_start();
		$this->db->query('DELETE FROM relationship WHERE task_id = ?', $task_id);

		if (count($relations) > 0) {
			//echo 'branch relation';
			//var_dump($relations);
			$db_nodes = $this->db->query('SELECT * FROM node WHERE task_id = ?', $task_id)->result_array();
			//var_dump($db_nodes);
			$db_relations = array();
			$type = 0;
			foreach ($relations as $relation) {
				//var_dump($relation['relation']);
				if ($relation['relation'] != NULL) {
					foreach ($relation['relation'] as $current_relation) {
						foreach ($db_nodes as $db_node) {
							if ($current_relation['x'] == $db_node['inner_id']) {
								$x = $db_node['id'];
							}
							if ($current_relation['y'] == $db_node['inner_id']) {
								$y = $db_node['id'];
							}
						}
						$db_relations[] = array(
							'node1_id' => $x,
							'node2_id' => $y,
							'type' => $type
						);
					}
				}
				$type++;
			}
			foreach ($db_relations as $db_relation) {
				$update_sql = "INSERT INTO relationship
					(task_id, node1_id, node2_id, type)
					VALUES (?, ?, ?, ?)";
				$this->db->query($update_sql, array(
					$task_id, $db_relation['node1_id'], $db_relation['node2_id'], $db_relation['type']
				));
			}
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() == FALSE) {
			return FALSE;
		}
		return TRUE;
	}

	/*
	 * Update Logic Annotation Results
	 */
	function update_logic_nodes($nodes) {
		mb_internal_encoding('UTF-8');
		
		// $this->output->enable_profiler(TRUE);		
		// $this->benchmark->mark('code_start');
		
		
		$res_id = $this->session->userdata('res_id');
		$this->db->trans_start();
		
		$del_sql = 'DELETE FROM node_logic WHERE resource_id = ?';
		$this->db->query($del_sql, array($res_id));
		
		
		//$result = $this->db->query('SELECT content FROM resource WHERE id = ?', $res_id)->result_array();
		//$content = str_replace("\r", "", $result[0]['content']);
		
		if (count($nodes) > 0) {
			$counter = 1;
			foreach ($nodes as $node) {
				
				//$begin = $node['from'];
				//$end = $node['to'];
				//$n1 = search_char($content, 0, $begin, "\n");
				//$n2 = search_char($content, $begin, $end, "\n");
				//$begin += $n1;
				//$end += $n1 + $n2;
				
				$update_sql = "INSERT INTO node_logic
								(title, resource_id, inner_id, offset_start, offset_end, real_start, real_end, type)
								VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
				/*
				$this->db->query($update_sql, array(
					$node['text'], $res_id, $counter, $node['from'], $node['to'], $begin, $end, $node['attr']
				));*/
				$this->db->query($update_sql, array(
					$node['text'], $res_id, $node['id'], $node['from'], $node['to'], $node['from'], $node['to'], $node['attr']
				));
				
				$counter++;
			}
		}
			
		$this->db->trans_complete();
		
		
		// $this->benchmark->mark('code_end');
		// echo $this->benchmark->elapsed_time('code_start', 'code_end');				
		
		
		if ($this->db->trans_status() == FALSE) {
			return FALSE;
		}
		println("update_logic_nodes：我可是返回true了");
		return TRUE;
	}
	
	function confirm_logic($res_id) {
		mb_internal_encoding('UTF-8');
		$list = $this->get_logic_nodelist($res_id);
		$calist = $this->get_logic_relations($list['nodes'], mb_strlen($list['res']['content']));
		$this->load->model('resource_model');
		
		if ($this->resource_model->get_by_id($res_id, 'status') == 1) {
			$this->db->trans_start();
			$this->db->query('UPDATE resource SET status = 2 WHERE id = ?', $res_id);
			for ($i = 0; $i < count($calist) - 1; $i++) {
				$item = $calist[$i];
				println("unit开插");
				$this->db->query(
					'INSERT INTO unit(name, type, resource_id, logic_id, parent_id, begin_offset, end_offset)
					VALUES(?, ?, ?, ?, ?, ?, ?)',
					array($item['title'], substr($item['type'], 3) + 0, $res_id, $item['id'], $item['parent_id'], $item['real_start'], $item['sematic_end']));
			}
			
			$result = $this->db->query('SELECT * FROM unit WHERE resource_id = ?', $res_id)->result_array();
			foreach ($result as $row) {
				$this->db->query('UPDATE unit SET parent_id = ? WHERE parent_id = ?',
					array($row['id'], $row['logic_id']));
			}
			$this->db->trans_complete();
		}

	}
	
	function get_logic_nodelist($res_id) {
		$this->load->model('resource_model');
		$res = $this->resource_model->get_by_id($res_id, '*');
		$sql = 'SELECT * FROM node_logic WHERE resource_id = ?';
		$query = $this->db->query($sql, array($res_id));
		$nodes = $query->result_array();
		return array('res' => $res, 'nodes' => $nodes);
	}

	function get_logic_relations($nodes, $length) {
		$sec = $nodes;
		$sec[] = array('real_start' => $length, 'type' => 'nt_0');
		for ($i = 0; $i < count($sec) - 1; $i++) {
			/*
			if ($i < count($sec) - 1) {
				$j = $i + 1;
				while ($sec[$j]['level'] > $sec[$i]['level'] && $j < count($sec) - 1) {
					$j++;
				}
				$lbound = $sec[$j]['start'];
			} else {
				$lbound = $length;
			}*/
			for ($j = $i + 1; $j < count($sec); $j++) {
				if ($sec[$j]['type'] <= $sec[$i]['type']) {
					break;
				}
			}
			
			$lbound = $sec[$j]['real_start'];
			
			$sec[$i]['sematic_end'] = $lbound;
			//echo $j . $sec[$i]['title'] . ' - ' . $structure[$sec[$i]['level']] . ' - ' . $sec[$i]['offset'] . ' ~ ' . $lbound . '<br />';
		}
		
		for ($i = 0; $i < count($sec) - 1; $i++) {
			$j = $i;
			for ($j = $i; $j >= 0; $j--) {
				if ($sec[$j]['type'] < $sec[$i]['type']) {
					break;
				}
			}
			//echo $j . ' is the parent of ' . $i . '<br />';
			if (isset($sec[$j]['id'])) {
				$sec[$i]['parent_id'] = $sec[$j]['id'];
			} else {
				$sec[$i]['parent_id'] = -1;
			}
		}
		
		return $sec;
	}
	
	function update_data($nodes, $relations) {
		//return false;
		$res_id = $this->session->userdata('res_id');
		
		$del_sql = "delete from node
					where resource_id = {$res_id}";
		
		$this->db->query($del_sql);
		//$exsisted_nodes = array();
		//$exsisted_db_id = array();
		/*
		if (count($nodes > 1)) {
			for ($i = 0; $i < count($nodes); $i++) {
				$where_list = array(
					'resource_id' => $res_id,
					'offset_start' => $nodes[$i]['from'],
					'offset_end' => $nodes[$i]['to']
				);
				$update_data = array(
					'inner_id' => $i
				);
				$this->db->select('id')->from('node')->where($where_list);
				if ($this->db->update('node', $update_data)) {
					$exsisted_nodes[] = $i;
					$this->db->select
				}
			}
			foreach ($exsisted_nodes as $eid) {
				$this->db->
			}
		}*/
		if (count($nodes) > 1) {
			foreach ($nodes as $node) {
				//$update_sql = "insert into node(resource_id, inner_id, offset_start, offset_end)
				//				values ({$res_id}, {$node['id']}, {$node['from']}, {$node['to']})";
				$update_sql = "insert into node(resource_id, inner_id, offset_start, offset_end, type)
								values (?, ?, ?, ?, ?)";
				$this->db->query($update_sql, array(
					$res_id, $node['id'], $node['from'], $node['to'], $node['attr']
				));
			}
		}
		return true;
	}
/*
	function unit_is_assigned($root) {
		static $is_assigned = TRUE;
		if (count($root->childs) == 0) {
			$result = $this->db->query('SELECT is_assign FROM unit WHERE id = ?', $root->id)->result_array();
			if ($result[0]['is_assign'] == 1) {
				$uresult = $this->db->query(
					'SELECT user_id FROM task, unit WHERE unit.id = task.unit_id AND unit.id = ?',
				$root->id)->result_array();
				$user_id = $uresult[0]['user_id'];
				if ($user_id != $this->session->userdata('user_id')) {
					$is_assigned = FALSE;
					//echo '*' . $root->value;
					//echo $user_id . '---' . $this->session->userdata('user_id') . '*';
				}
			}
		} else {
			foreach ($root->childs as $child) {
				$this->unit_is_assigned($child);
			}
		}
		return $is_assigned;
	}
*/

	function unit_child_is_assigned($root) {
		$stack = array();
		array_push($stack, $root);
		while (count($stack) != 0) {
			$currNode = array_pop($stack);
			if (count($currNode->childs) != 0) {
				foreach ($currNode->childs as $child) {
					array_push($stack, $child);
				}
			}
			
			$result = $this->db->query(
				'SELECT is_assign FROM unit WHERE id = ?', $currNode->id)->result_array();
			
			if (count($result) != 0){
				if ($result[0]['is_assign'] == 1) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}


/*
 * Get Unit State by unit_id
 * 
 * description:
 * state 0: unit not assigned
 * state 1: unit assigned to current user, top unit
 * state 2: unit assigned to current user, child unit
 * state 3: unit assigned to another user
 */
	function get_unit_state($unit_id, $tree = null) {
		// some initializing job
		
		$current_user_id = $this->session->userdata('user_id');
		
		$top_unit_query = $this->db->query(
			'SELECT * FROM task WHERE unit_id = ?', $unit_id);
		$is_top_unit = $top_unit_query->num_rows();
		
		$child_unit_query = $this->db->query(
			'SELECT * FROM task WHERE (child_id like ?) OR (child_id like ?)
				OR (child_id like ?)',
			array($unit_id . ',%', '%,' . $unit_id, '%,' . $unit_id . ',%'));
		$is_child_unit = $child_unit_query->num_rows();
		// end initializing
		if ($is_top_unit == 1) {
			$top_unit = $top_unit_query->result_array();
			if ($top_unit[0]['user_id'] == $current_user_id) {
				return 1;
			} else {
				return 4 + $top_unit[0]['user_id'];
			}
		} elseif ($is_child_unit == 1) {
			$child_unit = $child_unit_query->result_array();
			if ($child_unit[0]['user_id'] == $current_user_id) {
				return 2;
			} else {
				return 4 + $child_unit[0]['user_id'];
			}
		} else {
			//println('ann_model 830行：tree为：'.$tree->id);
			//tree的id不为空
			$root = get_node_by_id($tree, $unit_id);
			//var_dump($root);
			if ($this->unit_child_is_assigned($root)) {
				return 3;
				// end here
			}
		}
		return 0;
	}
	
	function fetch_unit($unit_id) {
		$this->load->model('resource_model');
		
		
		$result = $this->db->query('SELECT resource_id FROM unit WHERE id = ?', $unit_id)->result_array();
		$res_id = $result[0]['resource_id'];
		
		$tree = $this->resource_model->get_normal_units_tree($res_id);
		$root = get_node_by_id($tree, $unit_id);
		$nodes = get_child_units($root);
		
		//$is_assigned = $this->unit_is_assigned($root);
		$unit_state = $this->get_unit_state($unit_id);//这儿为什么不出bug???
		
		var_dump($is_assigned);
		
		if ($unit_state == 0) {
			
			$children = array();
			
			// transaction begins
			$this->db->trans_start();
			// $this->db->query('LOCK TABLES unit WRITE');
			// $this->db->query('LOCK TABLES unit READ');
			// $this->db->query('LOCK TABLES task WRITE');
			
			foreach ($nodes as $node) {
				$result = $this->db->query('
					SELECT is_assign FROM unit WHERE id = ? FOR UPDATE', $node['id'])->result_array();
				$node_assigned = $result[0]['is_assign'];
				if ($node_assigned == 0) {
					// set unit state
					$this->db->query(
						'UPDATE unit SET is_assign = 1 WHERE id = ?', $node['id']);
					// set task
				}
				if ($node['id'] != $unit_id) {
					$children[] = $node['id'];
				}
			}
			$child_id = implode(',', $children);
			$this->db->query(
				'INSERT INTO task(unit_id, user_id, child_id, state) VALUES(?, ?, ?, ?)',
				array($unit_id, $this->session->userdata('user_id'), $child_id, 1));
				
			//$this->db->query('UNLOCK TABLES');
			$this->db->trans_complete();
			// transaction ends
			if ($this->db->trans_status() == FALSE) {
				return FALSE;
			}
		} else {
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * list标注功能,给task分配unit
	 */
	function fetch_unit_as_list($unit_id, $limit){
		$this->load->model('resource_model');
		
		$result = $this->db->query('SELECT resource_id FROM unit WHERE id = ?', $unit_id)->result_array();
		$res_id = $result[0]['resource_id'];
		//获取相邻的$limit个unit
		$sql = 'SELECT * FROM unit WHERE id >= '.$unit_id.' and resource_id ='.$res_id.' limit '.$limit;
		$units = $this->db->query($sql)->result_array();
		var_dump($units);
		
		$this->db->trans_start();
		foreach ($units as $unit) {
			//此处可用get_unit_state查错	
			// set unit state
			$this->db->query('UPDATE unit SET is_assign = 1 WHERE id = ?', $unit['id']);
			//list标注里，任务分配的是平行等级的limit个unit
			$children[] = $unit['id'];				
		}
		$child_id = implode(',',$children);
		//set task
		$this->db->query(
			'INSERT INTO task(unit_id, user_id, list_id, state) VALUES(?, ?, ?, ?)',
			array($unit_id, $this->session->userdata('user_id'), $child_id, 1));
					
		$this->db->trans_complete();
		if ($this->db->trans_status() == FALSE) {
			return FALSE;
		}
		return TRUE;
	}


}