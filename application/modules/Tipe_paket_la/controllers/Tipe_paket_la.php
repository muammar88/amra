<?php

/**
 *  -----------------------
 *	Tipe paket la Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Tipe_paket_la extends CI_Controller
{

	private $company_code;
	private $company_id;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_tipe_paket_la', 'model_tipe_paket_la');
		# model fasilitas cud
		$this->load->model('Model_tipe_paket_la_cud', 'model_tipe_paket_la_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function daftar_tipe_paket_la()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_tipe_paket_la->get_total_daftar_tipe_paket_la($search);
			$list 	= $this->model_tipe_paket_la->get_index_daftar_tipe_paket_la($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar tipe paket la tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar tipe paket la berhasil ditemukan.',
					'total' => $total,
					'data' => $list,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	function get_info_tipe_paket_la()
	{
		$error = 0;
		# get list fasilitas
		$fasilitas = $this->model_tipe_paket_la->get_list_fasilitas();
		if (count($fasilitas) == 0) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data tipe paket la tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data tipe paket la berhasil ditemukan.',
				'fasilitas' => $fasilitas,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function _ck_id_tipe_paket_la_exist()
	{
		if ($this->input->post('id')) {
			if ($this->model_tipe_paket_la->check_tipe_paket_id_la($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_id_tipe_paket_la_exist', 'ID Tipe Paket la tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	function _ck_id_fasilitas_tipe_paket_la($fasilitas_id)
	{
		if ($this->model_tipe_paket_la->check_fasilitas_id_tipe_paket_la_exist($fasilitas_id)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_id_fasilitas_tipe_paket_la', 'ID Fasilitas ID tidak ditemukan.');
			return FALSE;
		}
	}

	function _ck_pax_tipe_paket_la($pax)
	{
		if ($pax > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_pax_tipe_paket_la', 'Pax tidak boleh kosong.');
			return FALSE;
		}
	}

	function proses_addupdate_tipe_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Tipe Paket LA<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_tipe_paket_la_exist');
		$this->form_validation->set_rules('nama_tipe_paket_la', '<b>Nama Tipe Paket<b>', 'trim|required|xss_clean|min_length[1]');
		# fasilitas
		foreach ($this->input->post('fasilitas') as $keyFasilitas => $valFasilitas) {
			$this->form_validation->set_rules("fasilitas[" . $keyFasilitas . "]", "Fasilitas", 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_id_fasilitas_tipe_paket_la');
		}
		# pax
		foreach ($this->input->post('pax') as $keyPax => $valPax) {
			$this->form_validation->set_rules("pax[" . $keyPax . "]", "Pax", 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_pax_tipe_paket_la');
		}
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			#  receive data
			$fasilitas = $this->input->post('fasilitas');
			$uniq_fasilitas = array_unique($fasilitas);
			if (count($fasilitas) == count($uniq_fasilitas)) {
				$pax = $this->input->post('pax');

				$data = array();
				$data['paket_type_name'] = $this->input->post('nama_tipe_paket_la');
				$data['last_update'] = date('Y-m-d');

				$data_fasilitas = array();
				if ($this->input->post('id')) {
					foreach ($fasilitas as $key => $value) {
						$data_fasilitas[] = array(
							'company_id' => $this->company_id,
							'facilities_la_id' => $value,
							'paket_type_id' => $this->input->post('id'),
							'pax' => $pax[$key]
						);
					}
					if (!$this->model_tipe_paket_la_cud->update_tipe_paket_la($this->input->post('id'), $data, $data_fasilitas)) {
						$error = 1;
						$error_msg = 'Proses update tipe paket la gagal dilakukan.';
					}
				} else {
					$data['company_id'] = $this->company_id;
					$data['input_date'] = date('Y-m-d');
					foreach ($fasilitas as $key => $value) {
						$data_fasilitas[] = array(
							'company_id' => $this->company_id,
							'facilities_la_id' => $value,
							'pax' => $pax[$key]
						);
					}
					if (!$this->model_tipe_paket_la_cud->insert_tipe_paket_la($data, $data_fasilitas)) {
						$error = 1;
						$error_msg = 'Proses insert tipe paket la gagal dilakukan.';
					}
				}
			} else {
				$error = 1;
				$error_msg = 'Fasilitas tidak boleh duplikat.';
			}
			# filter feedBack
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data tipe paket berhasil disimpan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	function get_info_edit_tipe_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Tipe Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_tipe_paket_la_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# get list fasilitas
			$fasilitas = $this->model_tipe_paket_la->get_list_fasilitas();
			# get data info edit tipe paket la
			$feedBack = $this->model_tipe_paket_la->get_info_edit_tipe_paket_la($this->input->post('id'));
			# filter feedBack
			if (count($feedBack) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data tipe paket berhasil ditemukan.',
					'data' => $feedBack,
					'fasilitas' => $fasilitas,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data tipe paket tidak ditemukan',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	function delete_tipe_paket_la()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Id Tipe Paket LA<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_tipe_paket_la_exist');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# filter feedBack
			if (!$this->model_tipe_paket_la_cud->delete_tipe_paket_la($this->input->post('id'))) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data tipe paket gagal dihapus',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data tipe paket berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				// define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}
}
