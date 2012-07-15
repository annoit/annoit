<?php

class Membership_model extends CI_Model {
	
	/**
	 * Validater
	 * 
	 * This function is used to validate users' credentials
	 * 
	 * @access private
	 */
	function validate() {
		$this->db->where('username', $this->input->post('username'));
		$this->db->where('password', $this->input->post('password'));
		$query = $this->db->get('user');
		
		if ($query->num_rows == 1) {
			
			return true;
			/*
			$username = $this->input->post('username');
			$is_logged_in = $this->db->query(
				'SELECT is_logged_in FROM membership WHERE username = ?',
				$username)->result_array();
			
			if ($is_logged_in[0]['is_logged_in'] == 0) {
				$this->db->query('UPDATE membership SET is_logged_in = 1 WHERE username = ?', $username);
				return true;
			}
			*/
		}
		
		return false;
	}
	
	function set_logout() {
		if ($this->session->userdata('username')) {
			$username = $this->session->userdata('username');
			$this->db->query('UPDATE u SET is_logged_in = 0 WHERE username = ?', $username);
		}
	}
	
	function get_role() {
		$role_list = array(0 => 'user', 1 => 'administrator');
		$this->db->select('role')->from('user')->where('username', $this->input->post('username'));
		$result = $this->db->get()->result_array();
		return $role_list[$result[0]['role']];
	}

	function get_current_id() {
		$this->db->select('id')->from('user')->where('username', $this->input->post('username'));
		$result = $this->db->get()->result_array();
		return $result[0]['id'];
	}
	//创建用户
	function create_member() {		
		$new_member_insert_data = array(
			// 'first_name' => $this->input->post('first_name'),
			// 'last_name' => $this->input->post('last_name'),
			'email_address' => $this->input->post('email_address'),			
			'username' => $this->input->post('username'),
			'password' => $this->input->post('psw'),
			'role' => $this->input->post('user_type'),
			'is_logged_in' => 1					
		);				
		if($insert = $this->db->insert('user', $new_member_insert_data)){
			return true;
		}else return false;		
	}
	
	function get_user_list(){
		$result = $this->db->query('select id, username, email_address, role from user')->result_array();
		$data['data'] = $result;
		return $data;
	}
		
}