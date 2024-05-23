<?php

/**
 *  -----------------------
 *	Slider Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Slider extends CI_Controller
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
		$this->load->model('Model_slider', 'model_slider');
		# model slider cud
		$this->load->model('Model_slider_cud', 'model_slider_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	# daftar slider
	function daftar_slider()
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
			$total 	= $this->model_slider->get_total_daftar_slider($search);
			$list 	= $this->model_slider->get_index_daftar_slider($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar slider tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar slider berhasil ditemukan.',
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

	# check slide id
	function _ck_slide_id()
	{
		if ($this->input->post('id')) {
			if ($this->model_slider->check_slider_id($this->input->post('id'))) {
				return TRUE;
			} else {
				$this->form_validation->set_message('_ck_slide_id', 'Slider ID tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	# update slider
	function update_slider()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Slider ID<b>', 'trim|xss_clean|numeric|callback__ck_slide_id');
		$this->form_validation->set_rules('title', '<b>Judul Slider<b>', 'trim|required|xss_clean');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {

			$data = array();
			$data['title'] = $this->input->post('title');
			$data['company_id'] = $this->company_id;
			$data['last_update'] = date('Y-m-d');
			if (isset($_FILES['userFile']) and $_FILES['userFile']['size'] > 0) {
				# define photo name
				$photo_with_extention = '';
				if ($this->input->post('id')) {
					$photo_with_extention = $this->model_slider->get_slide_name($this->input->post('id')); # ger photo name from database
					$photo_name = explode('.', $photo_with_extention)[0];
				} else {
					$photo_name = md5(date('Y-m-d H:i:s')); #  generateed photo name
				}
				# define config photo
				$path = 'image/slider/';
				$config['upload_path'] = FCPATH . $path;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['file_name'] = $photo_name;
				$config['overwrite'] = TRUE;
				$config['max_size'] = 400;
				$this->load->library('upload', $config);
				$this->upload->overwrite = true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ($this->upload->do_upload('userFile')) {
					$fileData = $this->upload->data();
					$data['img'] = $fileData['file_name'];
					if ($photo_with_extention != $fileData['file_name'] and $photo_with_extention != '') {
						$src = FCPATH . 'image/slider/' . $photo_with_extention;
						if (file_exists($src)) {
							unlink($src);
						}
					}
				} else {
					$error 		= 1;
					$error_msg 	= $this->upload->display_errors();
				}
			}
			if ($error == 0) {
				# filter id
				if ($this->input->post('id')) {
					if (!$this->model_slider_cud->update_slider($this->input->post('id'), $data)) {
						$error = 1;
						$error_msg = 'Proses update gagal dilakukan.';
					} else {
						$error_msg = 'Proses update berhasil dilakukan.';
					}
				} else {
					$data['input_date'] = date('Y-m-d');
					# filter
					if (!$this->model_slider_cud->insert_slider($data)) {
						$error = 1;
						$error_msg = 'Proses insert gagal dilakukan.';
					} else {
						$error_msg = 'Proses insert berhasil dilakukan.';
					}
				}
			}
			# filter error
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => $error_msg,
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

	# delete slider
	function delete_slider()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Slider ID<b>', 'trim|required|xss_clean|numeric|callback__ck_slide_id');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get info slider
			$info_slide = $this->model_slider->get_info_slider($this->input->post('id'));
			# filter error
			if ($this->model_slider_cud->delete_slider($this->input->post('id'))) {
				$src = FCPATH . 'image/slider/' . $info_slide['img'];
				if (file_exists($src)) {
					unlink($src);
				}
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete slider berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete slider gagal dilakukan',
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

	function info_edit_slider()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Slider ID<b>', 'trim|required|xss_clean|numeric|callback__ck_slide_id');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get info slider
			$info_slide = $this->model_slider->get_info_slider_by_id($this->input->post('id'));
			# filter error
			if (count($info_slide) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data slider gagal ditemukan.',
					'value' => $info_slide,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data slider gagal ditemukan.',
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
