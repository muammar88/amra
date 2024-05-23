<?php

/**
 *  -----------------------
 *	Log library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Serpul
{

	private $response;
	private $url;

	// function __construct()
	// {
	// 	$this->serpul = &get_instance();
	// }

	// function account(){
	// 	$this->serpul->url = '/account';
	// }

	// function getCategoryPrabayar(){
	// 	$this->serpul->url = '/prabayar/category';
	// }

	function send(){
		$curl = curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => $this->config->item('serpul_main_url').$this->serpul->url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			  'Accept: application/json',
			  'Authorization: '. $this->serpul->config->item('serpul_api_key')
			),
		));
		$this->serpul->response = curl_exec($curl);
		curl_close($curl);
	}

	// function response(){
	// 	return $this->serpul->response;
	// }

}
