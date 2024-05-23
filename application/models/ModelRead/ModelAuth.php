<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ModelAuth extends CI_Model
{

	private $response;
	private $feedBack = array();

	// database authentication process
	public function authentication()
	{
		$this->db->select('u.user_id, u.personal_id, u.username, u.password, p.fullname, g.group_access, g.nama_group')
			->from('base_users AS u')
			->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
			->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
			->where('u.username', $this->input->post('username'));
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			$res = $q->row();
			if (password_verify($this->input->post('password') . '_' . $this->systems->getSalt(), $res->password)) {
				$this->feedBack = array(
					'error' => false,
					'error_msg' => 'Login Berhasil Dilakukan.',
					'sessions' => array(
						'isLogin' => true,
						'user_id' => $res->user_id,
						'personal_id' => $res->personal_id,
						'fullname' => $res->fullname,
						'nama_group' => $res->nama_group,
						'group_access' => unserialize($res->group_access)
					)
				);
				// input log
				$this->syslog->write_log_auth($res->fullname . ' Melakukan Login ', $res->user_id);
			} else {
				$this->feedBack = array(
					'error' 	=> true,
					'error_msg' => 'Pasword Tidak Valid.'
				);
			}
		} else {
			$this->feedBack = array(
				'error' 	=> true,
				'error_msg' => 'Username Tidak Ditemukan.'
			);
		}
	}

	public function Clear_response()
	{
		$this->response = '';
	}

	public function Get_salt()
	{
		$q 	= 	$this->db->select('setting_value')
			->from('base_setting')
			->where('setting_name', 'salt')
			->get();
		if ($q->num_rows() > 0) {
			$this->response = $q->row()->setting_value;
		}
	}

	/**
	 * Response
	 * @return String, Int
	 */
	function Response()
	{
		return $this->response;
	}

	/**
	 * FeedBack
	 * @return ( default Array )
	 */
	function FeedBack()
	{
		return $this->feedBack;
	}
}
