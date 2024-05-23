<?php

/**
 *  -----------------------
 *	System library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Systems
{

	private $data = array();

	function __construct()
	{
		$this->system = &get_instance();
	}

	/**
	 * 	Get Salt
	 * 	@return Session Salt
	 */
	public function Hash_session()
	{
		if (!$this->session->Salt) {
			$this->system->session->set_userdata(array('Salt' => $this->_getSalt()));
		}
	}

	/* encription */
	public function Encrypt($value)
	{
		if ($this->session->Salt) {
			return password_hash($value . '_' . $this->session->Salt, PASSWORD_DEFAULT, array('cost' => 10));
		} else {
			return password_hash($value . '_' . $this->getSalt(), PASSWORD_DEFAULT, array('cost' => 10));
		}
	}

	/* get salt from database */
	public function getSalt()
	{
		$this->system->db->select('setting_value')
			->from('base_setting')
			->where('setting_name', 'salt');
		$q = $this->system->db->get();
		if ($q->num_rows() > 0) {
			return $q->row()->setting_value;
		} else {
			return '123456';
		}
	}

	/**
	 * Verifacator Array
	 * @return Array
	 */
	public function arrayVerifiator($array)
	{
		if (is_array($array)) {
			return $array;
		} else {
			return array();
		}
	}

	/**
	 * 	Get Response
	 * 	@return Array
	 */
	public function Response()
	{
		return $this->system->data;
	}
}
