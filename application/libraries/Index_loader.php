<?php

/**
 *  -----------------------
 *	Index Admin Loader library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Index_loader
{

	private $data = array();
	private $dataString = '';
	private $dataInt = '';
	private $settingProperty = array();
	private $companyCode;

	function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('ModelRead/ModelAdmin', 'modelAdmin');
	}

	/**
	 * settingProperty function
	 * Define property too get in database
	 */
	public function settingProperty($array)
	{
		$this->settingProperty = $array;
	}

	/**
	 * company code function
	 * Define property company code
	 */
	// public function codeCompany($value){
	// 		$this->companyCode = $value;
	// }

	/**
	 * AddData function
	 * merge data from input
	 */
	public function addData($param)
	{
		if (is_array($param)) {
			foreach ($param as $key => $value) {
				$this->data[$key] = $value;
			}
		}
	}

	/**
	 * Setting
	 * get data setting by property setting variable
	 */
	public function Setting()
	{
		$this->data = $this->CI->modelAdmin->getProperty($this->settingProperty);
	}

	public function CompanyData($value){ // code input
		$feedBack = $this->CI->modelAdmin->getCompanyData($value);
		$this->data['title'] = $feedBack['title'];
		$this->data['icon'] = $feedBack['icon'];
		return $feedBack['logo'];
	}

	/**
	 * Menu
	 * get data menu
	 */
	public function menus()
	{
		// get modul access from session
		$modulAccess = $this->CI->session->userdata('group_access');
		// define menu access
		$this->data['group_access'] = $this->CI->modelAdmin->getModulAccess($modulAccess);
	}

	/**
	 * Info Profil
	 * get data info profil
	 */
	public function infoProfil()
	{
		$user_id = $this->CI->session->userdata($this->CI->config->item('apps_name'))['user_id'];
		$this->data['info_profil'] = $this->CI->modelAdmin->getInfoProfil($user_id);
	}

	/**
	 * Response Model Settings
	 * @return ( default Array ), String, Int
	 */
	public function Response($type = 'Array')
	{
		if ($type == 'Array') {
			return $this->CI->systems->arrayVerifiator($this->data);
		} elseif ($type == 'String') {
			return $this->dataString;
		} elseif ($type == 'Int') {
			return $this->dataint;
		}
	}
}
