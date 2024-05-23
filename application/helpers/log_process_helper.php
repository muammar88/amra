<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

function write_log($log_msg){
	$ci =& get_instance();
	$ci->load->database();
	
	$log_data['log_msg'] 		= $log_msg;
	$log_data['log_ip'] 		= $ci->input->ip_address();
	$log_data['input_date'] 	= date('Y-m-d H:i:s');
	$log_data['user_id'] 		= $ci->session->user_id;

	$insert = $ci->db->insert('base_system_log', $log_data);

}