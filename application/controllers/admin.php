<?php

class Admin extends CI_Controller {
	function __construct() {
		parent::__construct();
		logged_in_as('administrator');
	}
	//管理员首页
	function index() {
		$data['content'] = 'admin/admin_index';
		$this->load->view('admin/admin_panel_template', $data);
	}
	//创建资源并配置
	function resource_conf() {
		$users = $this->db->query('SELECT * FROM user WHERE role = 0')->result_array();
		$user_options = array();
		foreach ($users as $user) {
			$user_options[$user['id']] = $user['username'];
		}		
		$data['data'] = array('user_options' => $user_options);		
		$data['content'] = 'admin/resource_conf';
		$data['scripts'] = array('jquery-1.5.1.min.js','resource_conf.js');
		$this->load->view('admin/admin_panel_template', $data);
	}	
	//创建资源
	function upload_resource() {
		$users = $this->db->query('SELECT * FROM user WHERE role = 0')->result_array();
		$options = array();
		foreach ($users as $user) {
			$options[$user['id']] = $user['username'];
		}
		//标注方式选择
		$type_options = array('主题图标注','列表标注');
		
		$this->load->view('admin/admin_panel_template', 
			array(
				'data' => array('options' => $options, 'type_options' => $type_options),
				'content' => 'admin/upload_form'
			)
		);
	}
	//创建资源，上传文件
	function upload() {
		
		$config['upload_path'] = 'uploaded_resources/';
		$config['file_name'] = $this->session->userdata('session_id') . '.txt';
		$config['allowed_types'] = 'txt';
		$config['max_size'] = '2048';
  
		$this->load->library('upload', $config);
		$this->load->helper('file');
		//header('Content-type: text/html; charset=utf-8');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('upload_info', array('type' => 'error', 'info' => '请输入必要的信息！'));
			redirect('admin/upload_resource');
		}
		
		if (!$this->upload->do_upload()) {
			$this->session->set_flashdata('upload_info', array('type' => 'error', 'info' => $this->upload->display_errors()));
			redirect('admin/upload_resource');
			//$this->load->view('upload_form', $error);
		} else {
			//echo 'yes!';
			$content = read_file('uploaded_resources/' . $this->session->userdata('session_id') . '.txt');
			delete_files('uploaded_resources/');
			$this->db->query('
				INSERT INTO resource(title, content, author, status, user_id, type) VALUES (?, ?, ?, ?, ?, ?)',
				array($this->input->post('title'), $content, $this->session->userdata('username'), 1, 
					$this->input->post('users'),$this->input->post('type')));
			$this->session->set_flashdata('upload_info', array('type' => 'successful', 'info' => '上传资源成功！'));
			redirect('admin/upload_resource');
			//$this->load->view('upload_success', $data);
		}
	}
	//逻辑标注审核
	function confirm_logic_list() {
		$this->load->model('resource_model');
		$data['data']['list'] = $this->resource_model->get_1pass_resources();
		$data['content'] = 'admin/confirm_logic_list';
		$this->load->view('admin/admin_panel_template', $data);
	}
	/**
	 * 正式标注审核
	 * @2012.4.26 added by Ivan 
	 * TODO
	 */
	function confirm_commit_list() {
		$this->load->model('resource_model');
		$data['data']['list'] = $this->resource_model->get_commit_resources();
		$data['content'] = 'admin/confirm_commit_list';
		$this->load->view('admin/admin_panel_template', $data);
	}
	/**
	 * 查看所有资源
	 * @2012.4.26 added by Ivan 
	 * TODO
	 */
	function resource_list(){
		$this->load->model('resource_model');
		$data['data']['list'] = $this->resource_model->get_resources();
		$data['content'] = 'admin/resource_list';
		$this->load->view('admin/admin_panel_template', $data);
	}
	/**
	 * 载入新建用户页面
	 * @2012.4.26 added by Ivan 
	 * TODO
	 */
	function new_member(){
		$options = array();
		$options[0] = '用户';
		$options[1] = '管理员';
		$this->load->view('admin/admin_panel_template', 
			array(
				'data' => array('options' => $options),
				'content' => 'admin/new_member_form'
			)
		);
	}
	/**
	 * 创建新用户
	 * @2012.4.26 added by Ivan 
	 * TODO
	 */
	function create_member(){

		$this->load->library('form_validation');
		$this->form_validation->set_rules('username', 'Username', 'trim|required');
		$this->form_validation->set_rules('psw', 'Psw', 'trim|required');
		$this->form_validation->set_rules('email_address', 'Email_address', 'trim|required');
		
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('upload_info', array('type' => 'error', 'info' => '请输入必要的信息！'));
			redirect('admin/new_member');
		}
		$this->load->model('membership_model');
		if(!$this->membership_model->create_member()){
			$this->session->set_flashdata('upload_info', array('type' => 'error', 'info' => '请输入必要的信息！'));
			redirect('admin/new_member');
		}else{
			$this->session->set_flashdata('upload_info', array('type' => 'successful', 'info' => '创建用户成功！'));
			redirect('admin/new_member');
		}
	}
	
	
	function show_logic($res_id) {
		mb_internal_encoding('UTF-8');
		$this->session->set_userdata('res_id', $res_id);
		$this->load->model('annotate_model');
		$q = $this->annotate_model->get_logic_nodelist($res_id);
		$data['content'] = 'admin/display_node_list';
		$data['data']['list'] = $q['nodes'];
		$data['data']['resource'] = $q['res'];
		$calist = $this->annotate_model->get_logic_relations($q['nodes'], mb_strlen($q['res']['content']));
		$data['data']['calist'] = $calist;
		$data['data']['tree'] = $this->annotate_model->generate_logic_tree($calist);
		$data['scripts'] = array('jquery-1.5.1.min.js', 'spreview.js');
		$this->load->view('admin/admin_panel_template', $data);
	}
	
	function logic_preview() {
		$res_id = $this->session->userdata('res_id');
		$this->load->model('annotate_model');
		$list = $this->annotate_model->get_logic_relations($res_id);
		$tree = $this->annotate_model->generate_tree($res_id);
		echo json_encode($list);
	}
	//确认逻辑标注 
	function confirm_logic($res_id) {
		$this->load->model('annotate_model');
		$this->load->model('resource_model');
		$this->annotate_model->confirm_logic($res_id);
		$confirmed = $this->resource_model->get_by_id($res_id, 'status');
		if ($confirmed == 2) {
			$data['data']['confirmed'] = true;
		} else {
			$data['data']['confirmed'] = false;
		}
		$data['content'] = 'admin/logic_confirmed';
		$this->load->view('admin/admin_panel_template', $data);
	}
	//显示用户列表
	function show_users(){
		$this->load->model('membership_model');
		$data = $this->membership_model->get_user_list();
		$data['content'] = 'admin/user_list';
		$this->load->view('admin/admin_panel_template',$data);
	}	

	//新建资源，并配置
	function new_resource_conf(){
		
		// $this->output->enable_profiler(TRUE);		
		// $this->benchmark->mark('code_start');
		
		// $this->load->model('file_model');		
		// $this->file_model->create_config_php($this->input->post('data'));	
		// include_once('resource_config/1.php');		
		// println("可载入： ".$config_res_title);	
		
				//根据节点属性的最大数量，决定该资源对应的ke表
				
		// $data = $this->input->post('data');		
		// $attr_count = 0;
		// foreach($data['attachAttr'] as $attachAttr){
			// if(count($attachAttr['attr'])>$attr_count){
				// $attr_count = count($attachAttr['attr']);
			// }
		// }
		// println('最大属性该数量count：    '.$attr_count);
		// $ke_table_name = 'ke';
		// $ke_index = 1;
		// do {
			// if((($ke_index-1)*5)<$attr_count&&$attr_count<=($ke_index*5)){
				// break;
			// }
			// $ke_index++;
		// } while (true);
// 		
		// println("ke_index:     ".$ke_index);
		
		if($this->input->post('ajax')){
			$conf_data = $this->input->post('data');
			$this->load->model('resource_model');
						
			$type = mb_detect_encoding($conf_data['resContent'], 
				array("EUC-CN","Windows-1252","ISO-8859-1","ASCII","UTF-8","GB2312","GBK","BIG5"));
				
			if($type=="EUC-CN"){
				//传回信息
				// $data = array(
					// 'time' => gmdate('G:i:s', time() + 3600 * 8), 
					// 'status' => false,
					// 'info' => "创建资源失败，资源是ansi格式，出现乱码");
				// echo json_encode($data);
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "请检查文本编码格式 @ {$time}";
			}else if($this->resource_model->create_resource($conf_data)){
				//传回信息
				// $data = array(
					// 'time' => gmdate('G:i:s', time() + 3600 * 8), 
					// 'status' => true,
					// 'info' => "成功创建资源");
				// echo json_encode($data);				
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "创建成功 @ {$time}";	
				
			}else{
				//传回信息
				// $data = array(
					// 'time' => gmdate('G:i:s', time() + 3600 * 8), 
					// 'status' => false,
					// 'info' => "创建资源失败");
				// echo json_encode($data);		
				$time = gmdate('G:i:s', time() + 3600 * 8);
				echo "创建失败 @ {$time}";	
			}						
		}
		
		// $this->benchmark->mark('code_end');
		// echo $this->benchmark->elapsed_time('code_start', 'code_end');				
		
		
	}


}