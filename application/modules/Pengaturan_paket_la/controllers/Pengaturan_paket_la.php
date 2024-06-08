<?php

/**
 *  -----------------------
 *	Pengaturan paket la Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Pengaturan_paket_la extends CI_Controller
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
		$this->load->model('Model_pengaturan_paket_la', 'model_pengaturan_paket_la');
		#load modal pengaturan cud
		$this->load->model('Model_pengaturan_paket_la_cud', 'model_pengaturan_paket_la_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function get_info_pengaturan_paket_la(){
		# get info pengaturan
		$data = $this->model_pengaturan_paket_la->get_info_pengaturan_paket_la($this->company_id);
		# filter
		if (count($data) == 0) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data pengaturan paket la tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data pengaturan paket la berhasil ditemukan.',
				'data' => $data,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// filter
	function updatePengaturanPaketLa() {
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('note_paket_la', '<b>Catatan Invoice Paket LA<b>', 'min_length[1]');
		$this->form_validation->set_rules('kurs', '<b>Kurs<b>', 'trim|required|xss_clean|min_length[1]|in_list[rupiah,dollar,riyal]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			#  receive data
			$data = array();
			$adaTandaTangan = false;
			# check upload is exist
			if (isset($_FILES['tanda_tangan']) and $_FILES['tanda_tangan']['size'] > 0) {
				$adaTandaTangan = true;
				# get tanda tangan name
				$tanda_tangan = $this->model_pengaturan_paket_la->get_tanda_tangan($this->company_id);
				if ($tanda_tangan != '') {
					$tanda_tangan_name = explode('.', $tanda_tangan)[0];
				} else {
					$tanda_tangan_name = md5(date('Ymdhis'));
				}
				# path
				$path = 'image/company/tanda_tangan/';
				# define config
				$config['upload_path'] = FCPATH . $path;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['file_name'] = $tanda_tangan_name;
				$config['max_size']   = 1024;
				$config['max_width']  = 300;
				$config['max_height'] = 80;
				$config['overwrite'] = TRUE;
				$this->load->library('upload', $config);
				$this->upload->overwrite 	= true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ( $this->upload->do_upload('tanda_tangan') ) {
					$fileData = $this->upload->data();
					$data['tanda_tangan'] = $fileData['file_name'];
					if ($tanda_tangan != '') {
						if ($fileData['file_name'] != $tanda_tangan) {
							$src = FCPATH . $path . $tanda_tangan;
							if (file_exists($src)) {
								unlink($src);
							}
						}
					}
				} else {
					$error = 1;
					$error_msg = $this->upload->display_errors();
				}
			}
			// error filter
			if( $error == 0 ) {
				$data['note_paket_la'] = base64_encode($this->input->post('note_paket_la'));
				$data['kurs'] = $this->input->post('kurs');
				if( ! $this->model_pengaturan_paket_la_cud->update_pengaturan_paket_la($data) ){
					$error = 1;
					$error_msg = 'Proses update pengaturan paket la gagal dilakukan.';
				}
			}
			# filter model pengaturan cud
			if ( $error == 0 ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data pengaturan paket la berhasil disimpan.',
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

}