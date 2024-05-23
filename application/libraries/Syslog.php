<?php

/**
 *  -----------------------
 *	Log library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Syslog
{

	function __construct()
	{
		$this->syslog = &get_instance();
	}

	function write_log($content)
	{
		# get id
		$log_data = array();
		# receive session
		$sesi = $this->syslog->session->userdata($this->syslog->config->item('apps_name'));
		# define variable
		$log_data['log_ip'] = $this->syslog->input->ip_address();
		$log_data['input_date'] = date('Y-m-d H:i:s');
		$log_data['company_id'] = $sesi['company_id'];
		if ($sesi['level_akun'] == 'administrator') {
			$log_data['log_msg'] = 'Administrator ' . $content;
			$log_data['user_id'] = 0;
		} else {
			$log_data['log_msg'] = 'User ID : ' . $sesi['user_id'] . ' dengan Nama : ' . $sesi['fullname'] . ' ' . $content;
			$log_data['user_id'] = $sesi['user_id'];
		}
		$insert = $this->syslog->db->insert('base_system_log', $log_data);
	}
}
