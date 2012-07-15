<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


function logged_in_as($role) {
	$CI =& get_instance();
	$logged_role = $CI->session->userdata('logged_role');
	
	if (!isset($logged_role) || strlen($logged_role) == 0 || $role != $logged_role) {
		
			redirect('login');
			die();
			
	}
}

function is_logged_in_as($role) {
	$CI =& get_instance();
	$logged_role = $CI->session->userdata('logged_role');
	
	if ($logged_role == $role) {
		return true;
	}
	
	return false;
}
