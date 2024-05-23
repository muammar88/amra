<?php

/**
 *  -----------------------
 *	Slider Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Artikel extends CI_Controller
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
		$this->load->model('Model_artikel', 'model_artikel');
		# model slider cud
		$this->load->model('Model_artikel_cud', 'model_artikel_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	# daftar artikel
	function daftar_artikel()
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
			$total 	= $this->model_artikel->get_total_daftar_artikel($search);
			$list 	= $this->model_artikel->get_index_daftar_artikel($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar artikel tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar artikel berhasil ditemukan.',
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

	# get info add artikel
	function get_info_add_artikel()
	{
		#  list topic
		$list_topic = $this->model_artikel->get_topik();
		# JSON Encode
		echo json_encode(array(
			'error' => false,
			'data' => array('topic' => $list_topic),
			'error_msg'    => validation_errors(),
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
		));
	}

	function _ck_artikel_id()
	{
		if ($this->input->post('id')) {
			if ($this->model_artikel->check_artikel_id_exist($this->input->post('id'))) {
				return  TRUE;
			} else {
				$this->form_validation->set_message('_ck_artikel_id', 'Artikel ID tidak ditemukan.');
				return FALSE;
			}
		} else {
			return TRUE;
		}
	}

	# topik id
	function _ck_topik_id_exist($topic)
	{
		if ($this->model_artikel->check_topik_id_exist($topic)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_topik_id_exist', 'Topik ID tidak ditemukan.');
			return FALSE;
		}
	}

	# add update artikel
	function addUpdateArtikel()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Artikel ID<b>', 'trim|xss_clean|numeric|callback__ck_artikel_id');
		$this->form_validation->set_rules('title', '<b>Judul Artikel<b>', 'trim|required|xss_clean');
		$this->form_validation->set_rules('photo_caption', '<b>Photo Caption Artikel<b>', 'trim|required|xss_clean');
		$this->form_validation->set_rules('topic', '<b>Topik Artikel<b>', 'trim|required|xss_clean|numeric|callback__ck_topik_id_exist');
		$this->form_validation->set_rules('place', '<b>Tempat Terbit Artikel<b>', 'trim|required|xss_clean');
		$this->form_validation->set_rules('headline', '<b>Headline Artikel<b>', 'trim|xss_clean|in_list[ya]');
		$this->form_validation->set_rules('artikel', '<b>Artikel<b>', 'trim|required|xss_clean');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# receive data
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['title'] = $this->input->post('title');
			if ($this->input->post('id')) {
				$data['slug'] = $this->model_artikel->check_artikel_slide($this->text_ops->createSlug($this->input->post('title')), $this->input->post('id'));
			} else {
				$data['slug'] = $this->model_artikel->check_artikel_slide($this->text_ops->createSlug($this->input->post('title')));
			}
			$data['photo_caption'] = $this->input->post('photo_caption');
			$data['description'] = $this->input->post('artikel');
			if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
				$data['author'] = "Administrator";
			} else {
				$data['author'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data['topic_id'] = $this->input->post('topic');
			$data['place'] = $this->input->post('place');
			$data['headline'] = $this->input->post('headline') == 'ya' ? 'ya' : 'tidak';
			$data['last_update'] = date('Y-m-d H:i:s');
			# upload photo
			if (isset($_FILES['photo']) and $_FILES['photo']['size'] > 0) {
				# define photo name
				$photo_with_extention = '';
				if ($this->input->post('id')) {
					$photo_with_extention = $this->model_artikel->get_photo_name($this->input->post('id')); # ger photo name from database
					$photo_name = explode('.', $photo_with_extention)[0];
				} else {
					$photo_name = md5(date('Y-m-d H:i:s')); #  generateed photo name
				}
				# define config photo
				$path = 'image/artikel/';
				$config['upload_path'] = FCPATH . $path;
				$config['allowed_types'] = 'jpg|jpeg|png';
				$config['file_name'] = $photo_name;
				$config['overwrite'] = TRUE;
				$config['max_size'] = 400;
				$this->load->library('upload', $config);
				$this->upload->overwrite = true;
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ($this->upload->do_upload('photo')) {
					$fileData = $this->upload->data();
					$data['photo'] = $fileData['file_name'];
					if ($photo_with_extention != $fileData['file_name'] and $photo_with_extention != '') {
						$src = FCPATH . 'image/artikel/' . $photo_with_extention;
						if (file_exists($src)) {
							unlink($src);
						}
					}
				} else {
					$error 		= 1;
					$error_msg 	= $this->upload->display_errors();
				}
			}
			# filter upload photo
			if ($error == 0) {
				# filter id
				if ($this->input->post('id')) {
					if (!$this->model_artikel_cud->update_artikel($this->input->post('id'), $data)) {
						$error = 1;
						$error_msg = 'Proses update artikel gagal dilakukan.';
					} else {
						$error_msg = 'Proses update artikel berhasil dilakukan.';
					}
				} else {
					$data['input_date'] = date('Y-m-d H:i:s');
					# filter
					if (!$this->model_artikel_cud->insert_artikel($data)) {
						$error = 1;
						$error_msg = 'Proses insert artikel gagal dilakukan.';
					} else {
						$error_msg = 'Proses insert artikel berhasil dilakukan.';
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

	# info photo artikel
	function info_photo_artikel()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Artikel ID<b>', 'trim|required|xss_clean|numeric|callback__ck_artikel_id');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get info
			$info = $this->model_artikel->get_info_photo_artikel($this->input->post('id'));
			# filter error
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Info photo berhasil ditemukan.',
					'data' => $info,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Info photo gagal ditemukan.',
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

	# delete topik
	function delete_topik()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Artikel ID<b>', 'trim|required|xss_clean|numeric|callback__ck_artikel_id');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get info
			$info = $this->model_artikel->get_info_photo_artikel($this->input->post('id'));
			# filter error
			if ($this->model_artikel_cud->delete_artikel($this->input->post('id'))) {
				# path
				$src = FCPATH . 'image/artikel/' . $info['photo'];
				# delete path
				if (file_exists($src)) : unlink($src);
				endif;
				# return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete berhasil dihapus.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete gagal dihapus.',
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

	# get info edit artikel
	function get_info_edit_artikel()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Artikel ID<b>', 'trim|required|xss_clean|numeric|callback__ck_artikel_id');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# data
			$data = $this->model_artikel->get_topik();
			# get value
			$value = $this->model_artikel->get_value_edit($this->input->post('id'));
			# filter error
			if (count($value) > 0) {
				# return
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete berhasil dihapus.',
					'data' =>  array('topic' => $data),
					'value' => $value,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete gagal dihapus.',
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
