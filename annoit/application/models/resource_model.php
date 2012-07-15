<?php
include_once('application/phpconsole/e4phpconsole.php');

class Resource_model extends CI_Model {
	
	//TODO 待整合
	function get_available_resources() {
		$resources = array();
		$logged_username = $this->session->userdata('username');
		$sql = "SELECT resource.id, title, upload_time, status FROM resource, user
					WHERE resource.user_id = user.id
					AND user.username = ? AND status = 1";
		$result = $this->db->query($sql, array($logged_username))->result_array();
		
		foreach ($result as $res) {
			$resources[] = $res;
		}
		
		$result = $this->db->query(
			'SELECT resource.id, title, upload_time, status FROM resource WHERE status = 2')->result_array();
			
		foreach ($result as $res) {
			$resources[] = $res;
		}
		
		return $resources;
	}
	//获取逻辑标注资源
	function get_1pass_resources() {
		$sql = "SELECT * FROM resource WHERE status = 1";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	/**
	 * 获取已提交的标注资源
	 * @2012.4.26 added by Ivan 
	 * TODO 待整合
	 */
	function get_commit_resources() {
		$sql = "SELECT * FROM resource WHERE status = 2";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	/**
	 * 获取所有标注资源
	 * @2012.4.26 added by Ivan 
	 * TODO 待整合
	 */
	function get_resources() {
		$sql = "SELECT * FROM resource";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	function get_by_id($id, $field) {
		
		$sql = "SELECT {$field} FROM resource WHERE id = ?";
		$query = $this->db->query($sql, array($id));
		
		$rows = $query->result_array();
		
		if ($field == '*') {
			return $rows[0];
		} else {
			return $rows[0][$field];
		}
	}
	
	function get_resource($res_id) {
		$sql = "SELECT content FROM resource WHERE id = ?";
		$query = $this->db->query($sql, array($res_id));
		$rows = $query->result_array();
		return $rows[0]['content'];
	}
	

	//获取该资源的unit列表
	function get_unit_list($res_id){
		$list = $this->db->query('SELECT * FROM unit WHERE resource_id = ?', $res_id)->result_array();
		return $list;
	}
	
	//未修改
	function get_normal_units_tree($res_id) {

		$list = $this->db->query('SELECT * FROM unit WHERE resource_id = ?', $res_id)->result_array();
		$this->load->library('tree');
		
		$top_level = 3;
		foreach ($list as $item) {
			if ($item['type'] < $top_level) {
				$top_level = $item['type'];
			}
		}
		
		$tree = $this->tree->get_instance(-1, 'root', array()); // initialize root node
		for ($level = $top_level; $level < 4; $level++) { // iterate each level
		
			$aquery = array();
			$units = get_nodes_by_level($tree, $level - 1, $top_level - 1); // get nodes of current level
			
			foreach ($list as $item) {
				if ($item['type'] == $level) {
					$aquery[] = $item;
				}
				// get nodes from list whose level = current level + 1
			}
						
			if ($level == $top_level) {
				foreach ($aquery as $row) {
					$tree->childs[] = $this->tree->get_instance($row['id'], $row['name'], array(), $level);
				}
			} else {
				foreach ($aquery as $row) {
					foreach ($units as $unit) {
						if ($row['parent_id'] == $unit->id) {
							$unit->childs[] = $this->tree->get_instance($row['id'], $row['name'], array(), $level);
						}
					}
				}
			}
		}
		return $tree;
	}

	//创建资源，并配置文件
	function create_resource($data){
		mb_internal_encoding('UTF-8');
		// resContent: $data['resContent'], 资源内容
		// resource_title: $data['resource_title'], 资源标题 
		// annatate_mode: $data['annatate_mode'],标注模式(NR,list)
		// isLogic: $data['isLogic'], 是否跳过逻辑标注
		// logic_user: $data['logic_user'],
		// split_symbol
		// ntype: $data['ntype'],	节点类型
		// attachAttr: $data['attachAttr'],	节点属性	
		// rtype: $data['rtype'],	关系类型
		// adjacentOffset: $data['adjacentOffset'], adjacentOffset之间的关系
		
		
		//判断文本类型
		$type = mb_detect_encoding($data['resContent'], 
			array("EUC-CN","Windows-1252","ISO-8859-1","ASCII","UTF-8","GB2312","GBK","BIG5"));
		if($type=="EUC-CN"){
			return false;
		}					
		$this->db->trans_begin();
							
		//判断标注模式
		$annatate_mode = 0;
		switch($data['annatate_mode']){
			case 'list':{
				$annatate_mode = 1;
				break;
			}case 'NR':{
				$annatate_mode = 0;
				break;
			}default: {
				$annatate_mode = 0;
			}
		}
		//判断是否需要跳过逻辑标注
		$status = 0;
		if($data['isLogic']=='yes'){
			$status = 1;
		}else{
			$status = 2;
		}		
		$resource_title = $data['resource_title'];
		$resContent = str_replace("\r","",$data['resContent']);
		$logic_user = $data['logic_user'];
			
		println("annatate_mode:   ".$data['annatate_mode']);
		if($data['annatate_mode']=='list'){
			$annatate_mode = 1;
		}else if($data['annatate_mode']=='NR'){
			$annatate_mode = 0;
		}
		
		$this->db->query('
				INSERT INTO resource(title, content, type, status, user_id) VALUES (?, ?, ?, ?, ?)',
				array($resource_title, $resContent, $annatate_mode, 1, $logic_user)
		);
		
		//获取当前资源的id
		$result = $this->db->query('
			select MAX(id) as mid from resource
		')->result_array();
		$curr_res_id = $result[0]['mid'];		
		
		//将id设置入session
		$this->session->set_userdata('res_id', $curr_res_id);
		$r_id = $this->session->userdata('res_id');
		println($r_id);
		
		$this->load->model('annotate_model');
		//如果跳过逻辑结构标注
		if($status == 2){			
			if($annatate_mode==0){
				//NR标注处理
				$logic_node_text = "逻辑节点";
				$logic_to = mb_strlen($resContent);
				if(mb_strlen($resContent)>10){
					$logic_node_text = mb_substr($resContent, 0, 10, 'UTF-8');
					$logic_to = 10;
				}
				$logic_node = array(
					'id' => 1,
					//'text' =>  $logic_node_text,
					'text' =>  $resource_title,
					'from' => 0,
					'to' => $logic_to,
					'attr' => 'nt_0',
					'attachAttr' => array(),
				);
				$nodes[] = $logic_node;
				
				// $this->load->model('annotate_model');
				// //插入logic_node开始：
				// $del_sql = 'DELETE FROM node_logic WHERE resource_id = ?';
				// $this->db->query($del_sql, array($curr_res_id));					
				// //update_logic_node										
				// $update_sql = "INSERT INTO node_logic
					// (title, resource_id, inner_id, offset_start, offset_end, real_start, real_end, type)
					// VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
				// $this->db->query($update_sql, array(
					// $resource_title, $curr_res_id, 1, 0, $logic_to, 0, $logic_to, 'nt_0'
				// ));		
				
			}else if($annatate_mode==1){
				//list标注处理
				$split_symbol = $data['split_symbol'];	
				println("分割符：     $split_symbol");
				$nodes = $this->explodeForLogicNodes($split_symbol, $resContent);
				println("分隔之后的长度：     ".count($nodes));
															
			}
			//插入逻辑节点
			$this->annotate_model->update_logic_nodes($nodes);			
			//确认逻辑节点
			$this->annotate_model->confirm_logic($curr_res_id);		
		}

		//根据资源的节点属性数量，决定该资源对应的ke表,若无对应的数据表，则创建之，ke1有5个属性，ke2有10个属性，以此类推
		$attr_count = 0;
		foreach($data['attachAttr'] as $attachAttr){
			if(count($attachAttr['attr'])>$attr_count){
				$attr_count = count($attachAttr['attr']);
			}
		}
		
		$ke_index = 1;
		do {
			if((($ke_index-1)*5)<$attr_count && $attr_count<=($ke_index*5)){
				break;
			}
			$ke_index++;
		} while (true);
		
		$ke_table_name = 'ke'.$ke_index;
		println("表名：      ".$ke_table_name);
		
		$this->load->dbforge();
		//把id放到$fields里总是失败，auto_increment总是出问题，所以先利用现成的方法添加id
		$this->dbforge->add_field('id'); 
		
		$fields = array(
                        'node_id' => array(
                                                 'type' => 'INT',
                                                  'null' => FALSE
                                          ),
                        'task_id' => array(
                                                 'type' => 'INT',
                                                  'null' => FALSE
                                          ),
                );
		for ($i=1; $i <= ($ke_index*5); $i++) {
			$name = 'attr'. $i;
			$fields[$name] = array(
									'type' => 'VARCHAR',
                                    'constraint' => 200, 
                                    'null' => FALSE
							);
		}		
		$this->dbforge->add_field($fields); 
		$this->dbforge->create_table($ke_table_name, TRUE); //true的意思是：if not exists
		
		$data['res_id'] = $curr_res_id;
		$data['ke_table'] = $ke_table_name;

		
		//生成资源对应的php,js配置文件
		$this->load->model('file_model');		
		$this->file_model->create_config_php($data);
		
		
		if ($this->db->trans_status() == FALSE) {
			$this->db->trans_rollback();
			return FALSE;			
		}else{
			$this->db->trans_commit();
		}
						
		return true;
	}

	//分隔字符串，并返回Logic_node格式的数组
	function explodeForLogicNodes($seperator,$str){
		mb_internal_encoding('UTF-8');	
							// 'id' => 1,
						// 'text' =>  $resource_title,
						// 'from' => 0,
						// 'to' => $logic_to,
						// 'attr' => 'nt_0',
						// 'attachAttr' => array(),
		$temp_str = explode($seperator,$str);
		$index = 0;
		$splits = array();
		foreach ($temp_str as $key => $temp) {
			$length = mb_strlen($temp);
			$start = mb_strpos($str, $temp, $index);
			$end = $start + $length;
			$text = $temp;
			if($length>10){
				$text = mb_substr($temp, 0, 10, 'UTF-8');
			}
			$splits[] = array(
				'id' => ($key+1),
				'text' => $text,
				'from' => $start,
				'to' => $end,
				'attr' => 'nt_0',
				'attachAttr' => array()
			);
			$index = $end;
		}
		return $splits;
	}

}

/* End of file resource_model.php */
/* Location: ./application/models/resource_model.php */