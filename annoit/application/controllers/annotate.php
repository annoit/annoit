<?php
include_once('application/phpconsole/e4phpconsole.php');

class Annotate extends CI_Controller {
	function __construct() {
		parent::__construct();
		logged_in_as('user');
		mb_internal_encoding('UTF-8');
	}
	
	function index() {
		redirect('user');
	}
	
	function logic($res_id) {
		$this->session->set_userdata('res_id', $res_id);
		$this->load->model('resource_model');
		$data['res_content'] = $this->resource_model->get_by_id($res_id, 'content');
		$data['active_scripts'] = array(site_url('annotate/get_existed_nodes_to_js/' . $res_id));
		//根据逻辑标注选择载入的脚本
		$res_type = $this->resource_model->get_by_id($res_id, 'type');
		switch($res_type){
			case 0: {
				$logic_conf = 'logic_conf.js';
				break;
			}
			case 1: {
				$logic_conf = 'list_logic_conf.js';
				break;
			}
			default: {
				$logic_conf = 'logic_conf.js';
			}
		}
		$data['scripts'] = array('jquery-1.5.1.min.js', $logic_conf , 'annotate.js', 'unload.js');
		$this->load->view('annotate/annotate_template', $data);
	}
	
	function get_existed_nodes_to_js($res_id = -1) {
		header('Content-Type: text/javascript; charset=utf-8');
		$this->load->model('annotate_model');
		if ($res_id == -1) {
			echo 'var general_id = 0;';			
			echo 'var isTagged = false;';
			echo 'var nodeList = [];';
		} else {
			$json = $this->annotate_model->get_node_list_as_json($res_id);
			echo 'var general_id = ' . $json['total'] . ';';
			echo 'var isTagged = ' . $json['tagged'] . ';';
			echo 'var nodeList = ' . $json['json'] . ';';			
			//println("1传出的general_id为".$json['total']);
			
		}
	}
	
	function get_nodes_json($task_id = -1) {
		header('Content-Type: text/javascript; charset=utf-8');
		$this->load->model('annotate_model');
		echo 'var task_id = '.$task_id.';';
		if ($task_id == -1) {
			echo 'var general_id = 0;';
			echo 'var isTagged = false;';			
			echo 'var nodeList = [];';
		} else {
			$json = $this->annotate_model->get_nodes_as_json($task_id);
			echo 'var isTagged = ' . $json['tagged'] . ';';	
			echo 'var general_id = ' . $json['total'] . ';';					
			//println("49行 nodeList: ".$json['json']);
			echo 'var nodeList = ' . $json['json'] . ';';
			//println("2传出的general_id为".$json['total']);
		}
		
	}
	//将unit信息传入js中
	function get_units_json($task_id){
		header('Content-Type: text/javascript; charset=utf-8');
		$this->load->model('annotate_model');
		if ($task_id == -1) {
			echo 'var units = [];';
		} else {
			$units = $this->annotate_model->get_units($task_id);
			$json = json_encode($units);
			echo 'var units = '.$json.';';		
		}		
	}
	
	
	function get_relations_json($task_id = -1) {
		header('Content-Type: text/javascript; charset=utf-8');
		$this->load->model('annotate_model');
		if ($task_id == -1) {
			
		} else {
			$json = $this->annotate_model->get_relations_as_json($task_id);
			echo "var relationData = {$json};";
		}
	}
	
	function get_existed_all_to_js($unit_id) {
		header('Content-Type: text/javascript; charset=utf-8');
		/*
		$this->load->model('annotate_model');
		if ($res_id == -1) {
			echo 'var nodeList = [];';
			echo 'var general_id = 0;';
			echo 'var isTagged = false;';
		} else {
			$json = $this->annotate_model->get_node_list_as_json($res_id);
			echo 'var nodeList = ' . $json['json'] . ';';
			echo 'var general_id = ' . $json['total'] . ';';
			echo 'var isTagged = ' . $json['tagged'] . ';';
		}*/
		echo 'var nodeList = [];';
		echo 'var general_id = 0;';
		echo 'var isTagged = false;';
	}
	
	function fetch_helper_js() {
		header('Content-Type: text/javascript; charset=utf-8');
		$url = site_url('annotate/check_assignment/');
		echo <<<JS
$(document).ready(function() {
	$('#unit-list a[id^=aquire_]').each(function() {
		$(this).click(function() {
			var id = $(this).attr('id');
			$.ajax({
				url: '{$url}'
			});
		});
	});
});

JS;
	}
	
	/**
	 * Nodes and Relations Annotation
	 * 
	 * render the normal annotation page, and display tree structure
	 */
	function normal($res_id) {
		// load essential models and set resource id into session
		$this->session->set_userdata('res_id', $res_id);
		$this->load->model('resource_model');
		$this->load->model('annotate_model');
		
		$res_type = $this->resource_model->get_by_id($res_id, 'type');
		switch($res_type){
			//列表式任务显示
			case 1: {
				$data['content'] = 'resource/list_annotate';
				$data['scripts'] = array('jquery-1.5.1.min.js', 'normal_conf.js' , 'list_annotate.js');
				
				//$result = $this->resource_model->get_unit_list($res_id);
				$data['data']['list'] = $this->resource_model->get_unit_list($res_id);
				$data['data']['tree'] = $this->resource_model->get_normal_units_tree($res_id);
				$data['data']['limit'] = 5;			
				break;
			}
			
			default: {
				$data['content'] = 'resource/unit_list';
				$data['scripts'] = array('jquery-1.5.1.min.js');
				$data['data']['tree'] = $this->resource_model->get_normal_units_tree($res_id);
			}
		}

		// get tree structure
		$data['active_scripts'] = array(site_url('annotate/fetch_helper_js/'));
		
		// load view
		$this->load->view('user/user_panel_template', $data);
	}
	
	function textjs() {
		$this->load->view('script/save');
	}
	
	function test() {
		$this->load->model('annotate_model');
		$this->annotate_model->get_nodes_as_json(2);
	}
	
	function update_all() {
		if ($this->input->post('ajax')) {
			$nodes = $this->input->post('nodes');
			$relations = $this->input->post('relations');
			$unit_id = $this->session->userdata('unit_id');
			if ($nodes == NULL) {
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "未标注任何内容 @ {$time}";
				return;
			}
			//var_dump($nodes);
			//var_dump($relations);
			//echo $unit_id . ';';
			$result = $this->db->query('SELECT * FROM unit WHERE id = ?', $unit_id)->result_array();
			$begin = $result[0]['begin_offset'];
			for ($i = 0; $i < count($nodes); $i++) {
				$nodes[$i]['id'] += 0;
				$nodes[$i]['from'] += $begin;
				$nodes[$i]['to'] += $begin;
				//$this->db->query('INSERT INTO node(resource_id, task_id, )');
			}
			//var_dump($relations);
			//$task = $this->db->query('SELECT * FROM task WHERE unit_id = ?', $unit_id)->result_array();
			//echo $task[0]['id'];
			$this->load->model('annotate_model');
			if ($this->annotate_model->update_normal_all($nodes, $relations)) {
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "保存成功 @ {$time}";
			} else {
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "保存失败 @ {$time}";
			}
		} else {
			$time = gmdate('G:i:s', time() + 3600 * 8);
			echo "未标注任何内容 @ {$time}";
		}
	}
	
	function update_logic_nodes() {
		if ($this->input->post('ajax')) {
			$nodes = $this->input->post('nodes');
			#var_dump($nodes);
			#$relations = $this->input->post('relations');
			$this->load->model('annotate_model');
			if ($this->annotate_model->update_logic_nodes($nodes)) {
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "保存成功 @ {$time}";
				#echo $this->session->userdata('res_id');
			} else {
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "保存失败 @ {$time}";
			}
		} else {
			$time = gmdate('G:i:s', time() + 3600 * 8);
			echo "未标注任何内容 @ {$time}";
		}
		
	}
	
	function fetch($unit_id) {
		$this->load->model('annotate_model');
		if ($this->annotate_model->fetch_unit($unit_id)) {
			echo 'available.';
		} else {
			echo 'non-available';
		}
		redirect('annotate/normal/' . $this->session->userdata('res_id'));
	}
	//list功能里，将unit分配给task
	function fetch_as_list($unit_id) {
		$this->load->model('annotate_model');
		$limit = 5;
		if ($this->annotate_model->fetch_unit_as_list($unit_id, $limit)) {
			echo 'available.';
		} else {
			echo 'non-available';
		}
		redirect('annotate/normal/' . $this->session->userdata('res_id'));
	}
	
	
	function task($task_id) {
		$config_res_path = 'resource_config/';
		$config_res_js_path = 'js_config/';
		
		//header('Content-type: text/html; charset=utf-8');
		$task = $this->db->query('SELECT * FROM task WHERE id = ?', $task_id)->result_array();
		$unit_id = $task[0]['unit_id'];
		
		$result = $this->db->query(
			'SELECT title, content, resource_id FROM resource, unit WHERE unit.id = ? AND unit.resource_id = resource.id',
			$unit_id)->result_array();
		$res_content = $result[0]['content'];
		$res_title = $result[0]['title'];
		$res_id = $result[0]['resource_id'];
		
		
		//载入资源js配置文件
		$config_res_js_name = $config_res_path.$config_res_js_path.$res_id.'_conf.js';
		if(!file_exists($config_res_js_name)){			
			$config_res_js_name = $config_res_path.$config_res_js_path.'template_conf.js';
		}
		println("配置js：   $config_res_js_name");
		
		
		$result = $this->db->query(
			'SELECT name, begin_offset, end_offset FROM unit WHERE id = ?', $unit_id)->result_array();
		$unit_name = $result[0]['name'];
		$start = $result[0]['begin_offset'];
		$end = $result[0]['end_offset'];

		//echo '<b>' . $res_title . ' - ' . $unit_name . '</b><hr />';
		$unit_content = mb_substr($res_content, $start, $end - $start, 'UTF-8');

		$this->session->set_userdata('task_id', $task_id);
		$this->session->set_userdata('unit_id', $unit_id);
		//$this->load->model('resource_model');
		//$data['res_content'] = $this->resource_model->get_by_id($res_id, 'content');
		//$data['active_scripts'] = array(site_url('annotate/get_existed_nodes_to_js/' . $res_id));
				
		$data['active_scripts'] = array(
			site_url('annotate/get_nodes_json/' . $task_id),
			site_url('annotate/get_relations_json/' . $task_id)
		);
		$data['scripts'] = array('jquery-1.5.1.min.js', 'annotate.js', 'unload.js');
		$data['config_scripts'] = array($config_res_js_name);
		$data['res_title'] = $res_title;
		$data['unit_name'] = $unit_name;
		$data['unit_content'] = $unit_content;
		$this->load->view('annotate/annotate_normal', $data);	
	}

	function list_task($task_id){
		$config_res_path = 'resource_config/';
		$config_res_js_path = 'js_config/';
				
		// $this->load->model('annotate_model');
		// $units = $this->annotate_model->get_units($task_id);
						
		$task = $this->db->query('SELECT * FROM task WHERE id = ?', $task_id)->result_array();

		$unit_ids = explode(',',$task[0]['list_id']);
		//获取资源内容
		$result = $this->db->query(
			'SELECT title, content, resource_id FROM resource, unit WHERE unit.id = ? AND unit.resource_id = resource.id',
			$unit_ids[0])->result_array();
		$res_content = $result[0]['content'];
		$res_title = $result[0]['title'];
		$res_id = $result[0]['resource_id'];
		
		//载入资源js配置文件
		$config_res_js_name = $config_res_path.$config_res_js_path.$res_id.'_conf.js';
		if(!file_exists($config_res_js_name)){			
			$config_res_js_name = $config_res_path.$config_res_js_path.'template_conf.js';
		}
		println("配置js：   $config_res_js_name");
				
		//获取unit内容
		$units = array();
		for($i=0;$i<count($unit_ids);$i++){
			$units[$i]['id'] = $unit_ids[$i];
			$result = $this->db->query(
				'SELECT name, begin_offset, end_offset FROM unit WHERE id = ?', $unit_ids[$i])->result_array();
			$units[$i]['name'] = $result[0]['name'];
			$units[$i]['begin_offset'] = $result[0]['begin_offset'];
			$units[$i]['end_offset'] = $result[0]['end_offset'];
			$units[$i]['content'] = mb_substr($res_content, $units[$i]['begin_offset'],
										$units[$i]['end_offset'] - $units[$i]['begin_offset'], 'UTF-8');
		}
		// foreach($unit_ids as $unit_id){
			// $result = $this->db->query(
				// 'SELECT name, begin_offset, end_offset FROM unit WHERE id = ?', $unit_id)->result_array();
			// $unit_name[] = $result[0]['name'];
			// $start = $result[0]['begin_offset'];
			// $end = $result[0]['end_offset'];
// 
			// //echo '<b>' . $res_title . ' - ' . $unit_name . '</b><hr />';
			// $unit_content[] = mb_substr($res_content, $start, $end - $start, 'UTF-8');
			// //println(mb_substr($res_content, $start, $end - $start, 'UTF-8'));
		// }
		
		$this->session->set_userdata('task_id', $task_id);
		$this->session->set_userdata('unit_id', $unit_ids);
		//获取之前标注的结果
		$data['active_scripts'] = array(
			site_url('annotate/get_nodes_json/' . $task_id),
			site_url('annotate/get_relations_json/' . $task_id),
			site_url('annotate/get_units_json/' . $task_id)
			//将units信息传入js文件中
		);
		
		$data['config_scripts'] = array($config_res_js_name);
		
		$data['scripts'] = array('jquery-1.5.1.min.js', 'list_annotate.js');
		$data['res_title'] = $res_title;
		$data['units'] = $units;
		$this->load->view('annotate/annotate_list', $data);	
		
	}

}
