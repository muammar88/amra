<?php

/**
 *  -----------------------
 *	Whatsapp library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Whatsapp_ops
{

	private $company_id;
	private $url;
	private $url_send_message;
	private $url_device_info;
	private $url_restart_device;
	private $url_check_message;
	private $api_key = '';
	private $device_key = '';
	private $system_device_key = '';
	private $system_whatsapp_number = '';
	private $whatsapp_number = '';
	private $response;
	public $destination_number = '';
	public $message = '';

	function __construct()
	{
		$this->CI = &get_instance();

		$this->company_id = $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'];

		// $this->url = 'https://wapisender.id/api/v1';
		$this->url = 'https://wapisender.id/api/v5';
		// $this->url_send_message = $this->url . '/send-message';
		// $this->url_device_info = $this->url . '/device-info';
		//$this->url_restart_device = $this->url . '/restart-device';
		$this->url_start_device = $this->url . '/device/start';
		$this->url_stop_device = $this->url . '/device/stop';

		// https://wapisender.id/api/v5/device/start
		// https://wapisender.id/api/v5/device/stop
		$this->url_check_message = $this->url . '/check-message';

		
		$this->url_send_message = $this->url . '/message/text';
		$this->url_device_info = $this->url . '/device/info';
		//$this->url_restart_device = '';
	}

	# define api
	function define_api(){
 		$this->CI->db->select('setting_value')
        	->from('base_setting')
        	->where('setting_name','api_key');
      	$q = $this->CI->db->get();
      	if( $q->num_rows() > 0 ) {
         	foreach ( $q->result() as $rows ) {
            	$this->api_key = $rows->setting_value;
         	}
      	}
	}

	# check api key
	function check_api_key(){
		return $this->api_key != '' ? true : false;
	}

	# define system device key
	function define_system_device_key(){
		$this->CI->db->select('setting_value')
        	->from('base_setting')
        	->where('setting_name','system_device_key');
      	$q = $this->CI->db->get();
      	if( $q->num_rows() > 0 ) {
         	foreach ( $q->result() as $rows ) {
            	$this->device_key = $rows->setting_value;
         	}
      	}
	}

	# define system whatsapp number
	function define_system_whatsapp_number() {
		$this->CI->db->select('setting_value')
        	->from('base_setting')
        	->where('setting_name','system_whatsapp_number');
      	$q = $this->CI->db->get();
      	if( $q->num_rows() > 0 ) {
         	foreach ( $q->result() as $rows ) {
            	$this->system_whatsapp_number = $rows->setting_value;
         	}
      	}
	}


	# define device key
	function define_device_key(){
		$this->CI->db->select('device_key, device_number')
         	->from('company')
         	->where('id', $this->company_id);
      	$q = $this->CI->db->get();
      	if( $q->num_rows() > 0 ) {
         	foreach ( $q->result() as $rows ) {
         		$this->device_key = $rows->device_key;
         		$this->whatsapp_number = $rows->device_number;
         	}
      	}
	}

	# check device key
	function check_device_key() {
		return $this->device_key != '' ? true : false;
	}

	# get device key
	function get_device_key(){
		return $this->device_key;	
	}

	# get system device key
	function get_system_device_key(){
		return $this->system_device_key;
	}

	# get system whatsapp number
	function get_system_whatsapp_number(){
		return $this->system_whatsapp_number;
	}

	# check whatsapp number
	function check_whatsapp_number() {
		return $this->whatsapp_number != '' ? true : false;
	}

	# get whatsapp number
	function get_whatsapp_number(){
		return $this->whatsapp_number;	
	}

	function get_api_key(){
		return $this->api_key;
	}

	public function get_info_device(){
        $payload = array();
        $payload['api_key'] = $this->api_key;
		$payload['device_key'] = $this->device_key; 
		$this->Posting($this->url_device_info, $payload);
    }

	# restart device
	function restart_device(){
		$param = array();
		$param['api_key'] = $this->api_key;
		$param['device_key'] = $this->device_key; 

		$this->Posting($this->url_stop_device, $param);
		$this->Posting($this->url_start_device, $param);

	}

	function check_nomor_tujuan($nomor_tujuan) {
		if( substr($nomor_tujuan,0, 1)  != '6' ){
			$nomor_tujuan = '62' . strval(substr($nomor_tujuan,1));
		}
		return $nomor_tujuan;
	}

	function send_message(){
		$param = array();
		$param['api_key'] = $this->api_key;
		$param['device_key'] = $this->device_key; 
		$param['destination'] = $this->check_nomor_tujuan( $this->destination_number ); 
		$param['message'] = $this->message; 

		$this->Posting($this->url_send_message, $param);

	}

	function Posting($url, $payload){
		$header = array();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

        $response = curl_exec($ch);
        $err      = curl_error($ch);

      	
        curl_close($ch);

        if ($err) {
        	$this->response = $err;
        } else {
        	$this->response = json_decode($response);
        }
	}

	function check_status_message($message_id){
		$param = array();
		$param['api_key'] = $this->api_key;
		$param['device_key'] = $this->device_key; 
		$param['message_id'] = $message_id; 
		# option
		$options = array(
		   'http' => array(
		       'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
		       'method'  => 'POST',
		       'content' => http_build_query($param)
		   )
		);
		$context  = stream_context_create( $options );
		$this->response = json_decode( file_get_contents( $this->url_check_message, false, $context ) );
	}

	# feedBack response
	function status_response(){
		return $this->response->status;
	}

	function response(){
		return $this->response;
	}
}