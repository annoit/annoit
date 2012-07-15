<?php

class Login extends CI_Controller {
	
	function index()
	{
		// 若用户已登陆，转向对应的页面
		if (is_logged_in_as('user')) {
			redirect('user');
		} else if (is_logged_in_as('administrator')) {
			redirect('admin');
		}
		// 未登陆，则显示登陆页面
		$data['main_content'] = 'login_form';
		$this->load->view('includes/template', $data);
		//echo $_SERVER['SERVER_ADDR'];
	}
	
	function validate_credentials()
	{
		$this->load->model('membership_model');
		$query = $this->membership_model->validate();
		
		if ($query) { // if the user's credential is valid
		
			$role = $this->membership_model->get_role(); // get role by user name
			$user_id = $this->membership_model->get_current_id();
			
			$data = array(
				'username' => $this->input->post('username'),
				'user_id' => $user_id,
				'is_logged_in' => true,
				'logged_role' => $role
			);
			
			$this->session->set_userdata($data); // store user info into session
			
			if ($role == 'user') {
				redirect('user');
				
			} else if ($role == 'administrator') {
				redirect('admin');
			}
			
		} else {
			$this->index();
		}
	}	
	
	function signup()
	{
		$data['main_content'] = 'signup_form';
		$this->load->view('includes/template', $data);
	}
	
	function create_member()
	{
		$this->load->library('form_validation');
		
		// field name, error message, validation rules
		$this->form_validation->set_rules('first_name', 'Name', 'trim|required');
		$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
		$this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
		
		
		if($this->form_validation->run() == FALSE)
		{
			$this->load->view('signup_form');
		}
		
		else
		{			
			$this->load->model('membership_model');
			
			if($query = $this->membership_model->create_member())
			{
				$data['main_content'] = 'signup_successful';
				$this->load->view('includes/template', $data);
			}
			else
			{
				$this->load->view('signup_form');			
			}
		}
		
	}
	
	function logout()
	{
		//$this->load->model('membership_model');
		//$this->membership_model->set_logout();
		$this->session->sess_destroy();
		$this->index();
	}

}