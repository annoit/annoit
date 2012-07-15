<?php

class User extends CI_Controller {
	function __construct() {
		parent::__construct();
		logged_in_as('user');
	}
	
	function index() {
		$task = $this->db->query(
			'SELECT * FROM task WHERE user_id = ? ORDER BY allocate_time DESC LIMIT 0,10',
			$this->session->userdata('user_id'))->result_array();
		foreach ($task as &$item) {
			$unit = $this->db->query(
				'SELECT * FROM unit WHERE id = ?',
				$item['unit_id'])->result_array();
			$item['name'] = $unit[0]['name'];
			$item['length'] = $unit[0]['end_offset'] - $unit[0]['begin_offset'];
		}
		
		$data['content'] = 'user/user_index';
		$data['data']['task'] = $task;
		$this->load->view('user/user_panel_template', $data);
	}
	
	function resource_list() {
		$this->load->model('resource_model');
		$data['data']['list'] = $this->resource_model->get_available_resources();
		$data['content'] = 'resource/resource_list';
		$this->load->view('user/user_panel_template', $data);
	}
	
	function show_resource($res_id) {
		$this->session->set_userdata('res_id', $res_id);
		$this->load->model('resource_model');
		$data['data']['res_id'] = $res_id;
		$data['data']['res_status'] = $this->resource_model->get_by_id($res_id, 'status');
		$data['data']['res_title'] = $this->resource_model->get_by_id($res_id, 'title');
		$data['data']['res_type'] = $this->resource_model->get_by_id($res_id, 'type');
		$data['content'] = 'resource/resource_display';
		$this->load->view('user/user_panel_template', $data);
	}

}
