<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Tree Helper
 * 
 * This helper contains a set of functions handling `tree' created by the `Tree' class
 * 
 * @author		ZhaoSusen
 * @since		Version 1.0.0
 */
include_once('application/phpconsole/e4phpconsole.php');
 
function print_tree($root) {
	if ($root->value == 'root') {
		foreach ($root->childs as $child) {
			print_tree($child);
		}
	} elseif (count($root->childs) == 0) {
		echo "<li>{$root->value}</li>";
	} else {
		echo "<li>{$root->value}<ul>";
		foreach ($root->childs as $child) {
			print_tree($child);
		}
		echo '</ul></li>';
	}
}

//每组任务数为limit个
function print_tree_as_list($tree, $limit){
	$CI =& get_instance();
	$CI->load->model('annotate_model');
	$list = $tree->childs;
	//$group_num为任务的数量
	$group_num = ceil(count($list)/$limit);
	
	echo "<ul>";	
	for($i=1;$i<=$group_num;$i++){
		//用该任务中第一项数据的标注状态代表整个任务的标注状态	
		$curr_id = $list[($i-1)*$limit]->id;
		//println('40 tree_helper: curr_id为:'.$curr_id);
		//println('41 tree_helper: tree_id为:'.$tree->id);
		$unit_state = $CI->annotate_model->get_unit_state($curr_id, $list[($i-1)*$limit]);
		$url = '';
		$link = '';
				
		switch ($unit_state) {
			case 0:
				$url = site_url('annotate/fetch_as_list/' . $curr_id);
				$link = "<span><a href=\"{$url}\">获取资源</a></span>";
				break;
			case 1:
				//在list标注里，child_unit是相邻的10个节点，而不是子节点
				$task = $CI->db->query('SELECT * FROM task WHERE unit_id = ?', $curr_id)->result_array();
				$url = site_url('annotate/list_task/' . $task[0]['id']);
				$link = '<span><span class="unit-state">已获取</span>' . "<a href=\"{$url}\">标注任务</a></span>";
				break;
			case 2:
				$link = '<span><span class="unit-state-na">不可标注</span></span>';
				break;
			case 3:
				$link = '<span><span class="unit-state-na">不可获取</span></span>';
				break;
			default:
				if ($unit_state >= 4) {
					$user_id = $unit_state - 4;
					$user = $CI->db->query('SELECT username FROM user WHERE id = ?', $user_id)->result_array();
					$user_name = $user[0]['username'];
					$link = '<span><span class="unit-state-na">由 ' . $user_name . ' 获取</span></span>';
				}
				break;
		}		
		echo "<li>任务{$i}:{$link}</li>";
		
	}
	echo "</ul>";
}


function print_tree_with_link($root) {
	$CI =& get_instance();
	$CI->load->model('annotate_model');
	
	if ($root->value == 'root') { // root node
		foreach ($root->childs as $child) {
			print_tree_with_link($child);
		}
	} else {
		
		$unit_state = $CI->annotate_model->get_unit_state($root->id, $root);
		$link = '';
		
		switch ($unit_state) {
			case 0:
				$a_id = 'aquire_' . $root->id;
				$url = site_url('annotate/fetch/' . $root->id);
				$link = "<span><a id=\"{$a_id}\" href=\"{$url}\">获取资源</a></span>";
				break;
			case 1:
				$task = $CI->db->query('SELECT * FROM task WHERE unit_id = ?', $root->id)->result_array();
				$url = site_url('annotate/task/' . $task[0]['id']);
				$link = '<span><span class="unit-state">已获取</span>' . "<a href=\"{$url}\">标注任务</a></span>";
				break;
			case 2:
				$link = '<span><span class="unit-state-na">不可标注</span></span>';
				break;
			case 3:
				$link = '<span><span class="unit-state-na">不可获取</span></span>';
				break;
			default:
				if ($unit_state >= 4) {
					$user_id = $unit_state - 4;
					$user = $CI->db->query('SELECT username FROM user WHERE id = ?', $user_id)->result_array();
					$user_name = $user[0]['username'];
					$link = '<span><span class="unit-state-na">由 ' . $user_name . ' 获取</span></span>';
				}
				break;
		}
		if (count($root->childs) == 0) { // leaf nodes
			/*
				$result = $CI->db->query('SELECT * FROM unit WHERE id = ?', $root->id)->result_array();
				$is_assigned = $result[0]['is_assign'];
				
				//$is_assigned = $CI->annotate_model->unit_is_assigned($root);
				
				if ($is_assigned == 0) {
					
					$a_id = 'aquire_' . $root->id;
					$url = site_url('annotate/fetch/' . $root->id);
					$link = "<span><a id=\"{$a_id}\" href=\"{$url}\">获取资源</a></span>";
				} else {
					$uresult = $CI->db->query(
						'SELECT user_id FROM task, unit WHERE unit.id = task.unit_id AND unit.id = ?',
						$root->id)->result_array();
					$user_id = $uresult[0]['user_id'];
					$user = $CI->db->query('SELECT username FROM user WHERE id = ?', $user_id)->result_array();
					$user_name = $user[0]['username'];
					if ($user_id == $CI->session->userdata('user_id')) {
						$url = site_url('annotate/unit/' . $root->id);
						$link = '<span><span class="unit-state">已获取</span>' . "<a href=\"{$url}\">标注资源</a></span>";
					} else {
						$link = '<span><span class="unit-state-na">由 ' . $user_name . ' 获取</span></span>';
					}
					
				}
			 */
			echo "<li>{$root->value}{$link}</li>";
				
		} else { // non-leaf nodes
				
			//$result = $CI->db->query('SELECT * FROM unit WHERE id = ?', $root->id)->result_array();
			//$is_assigned = $result[0]['is_assign'];
			/*
			$is_assigned = $CI->annotate_model->unit_is_assigned($root);
			
			$result = $CI->db->query('SELECT * FROM unit WHERE id = ?', $root->id)->result_array();
			$single_is_assigned = $result[0]['is_assign'];
			//var_dump($is_assigned);
			
			
			if ($is_assigned == 0 && $single_is_assigned == 0) {
				$a_id = 'aquire_' . $root->id;
				$url = site_url('annotate/fetch/' . $root->id);
				$link = "<span><a id=\"{$a_id}\" href=\"{$url}\">获取资源</a></span>";
			} else {
				$uresult = $CI->db->query(
					'SELECT user_id FROM task, unit WHERE unit.id = task.unit_id AND unit.id = ?',
					$root->id)->result_array();
					
				if (count($uresult) > 0) {
					$user_id = $uresult[0]['user_id'];
					$user = $CI->db->query('SELECT username FROM user WHERE id = ?', $user_id)->result_array();
					$user_name = $user[0]['username'];
					if ($user_id == $CI->session->userdata('user_id')) {
						$url = site_url('annotate/unit/' . $root->id);
						$link = '<span><span class="unit-state">已获取</span>' . "<a href=\"{$url}\">标注资源</a></span>";
					} else {
						$link = '<span><span class="unit-state-na">由 ' . $user_name . ' 获取</span></span>';
					}
				} else {
					$link = '<span><span class="unit-state-na">不可获取</span></span>';
				}
				
			}
			//$link .= !$CI->annotate_model->unit_is_assigned($root) ? 'true' : 'false';
			
			 * 
			 */
			echo "<li>{$root->value}{$link}<ul>";
			foreach ($root->childs as $child) {
				print_tree_with_link($child);
			}
			echo '</ul></li>';
		}
	}
}

function get_nodes_by_level($root, $level, $top_level = 0) {
	static $nodes;
	if ($level == $top_level) {
		$nodes[] = $root;
	} else {
		foreach ($root->childs as $child) {
			get_nodes_by_level($child, $level - 1, $top_level);
		}
	}
	return $nodes;
}

function get_child_units($root) {
	static $units;
	$units[] = array('id' => $root->id);
	
	if (count($root->childs) != 0) {
		foreach ($root->childs as $child) {
			get_child_units($child);
		}
	}
	return $units;
}

function get_node_by_id($root, $id) {
	static $node;
	//println('treehelper里 root为：'.$root);
	if ($root->id == $id) {
		$node = $root;
	} elseif (count($root->childs) != 0) {
		foreach ($root->childs as $child) {
			get_node_by_id($child, $id);
		}
	} else {
		return false;
	}
    //println('treehelper里 root为：'.$root.'   返回的 node为：'.$node);	
	return $node;
}