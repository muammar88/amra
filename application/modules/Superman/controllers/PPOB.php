<?php

/**
 *  -----------------------
 *	PPOB Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class PPOB extends CI_Controller
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
		$this->load->model('Model_ppob', 'model_ppob');
		# model ppob cud
		$this->load->model('Model_ppob_cud', 'model_ppob_cud');
		# checking is not Login
		$this->auth_library->Is_superman_not_login();
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function _ck_categori_operator(){
		if( $this->input->post('category_operator') ) {
			if( ! $this->model_ppob->check_categori_operator( $this->input->post('category_operator') ) ) {
				$this->form_validation->set_message('_ck_categori_operator', 'Operator ID Tidak Ditemukan.');
				return FALSE;
			}else{
				return true;
			}
		}
	}

	// daftar produk amra
	public function daftar_produk_amra(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('category_operator',	'<b>Kategori Operator<b>', 	'trim|xss_clean|min_length[1]|callback__ck_categori_operator');
		$this->form_validation->set_rules('status_product',	'<b>Status Produk<b>', 	'trim|xss_clean|min_length[1]|in_list[pilih_semua,active,inactive]');
		$this->form_validation->set_rules('server_product',	'<b>Server Produk<b>', 	'trim|xss_clean|min_length[1]|in_list[pilih_semua,tripay,iak,none]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$search 	= $this->input->post('search');
			$perpage = $this->input->post('perpage');
			
			$operator = $this->input->post('category_operator');
			$status_product = $this->input->post('status_product');
			$server_product = $this->input->post('server_product');

			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_ppob->get_total_daftar_produk_amra($search, $operator, $status_product, $server_product);
			$list 	= $this->model_ppob->get_index_daftar_produk_amra($perpage, $start_at, $search, $operator, $status_product, $server_product);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar produk amra tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar produk amra berhasil ditemukan.',
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

	public function _ck_produk_id( $id ){
		if(! $this->model_ppob->check_product_id($id) ){
			$this->form_validation->set_message('_ck_produk_id', 'ID Produk tidak ditemukan.');
         	return FALSE;
		}else{
			return TRUE;
		}
	}

	// get info markup produk
	public function get_info_markup_produk(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_produk_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// id
			$id = $this->input->post('id');
			// get data
			$data = $this->model_ppob->get_data_product($id);
			// filter
			if ( count( $data ) == 0 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data produk amra tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data produk amra berhasil ditemukan.',
					'data' => $data,
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

	// proses update markup 
	public function proses_update_markup(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_produk_id');
		$this->form_validation->set_rules('markup',	'<b>Markup<b>', 'required|trim|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get data
			$data = array();
			$data['application_markup'] = $this->text_ops->hide_currency( $this->input->post('markup') );
			$data['updated_at'] = date('Y-m-d H:i:s');
			// filter
			if ( ! $this->model_ppob_cud->update_markup( $this->input->post('id'), $data ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses update markup gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses update markup berhasil dilakukan.',
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

	// get info add update produk
	public function get_info_add_update_produk(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		// get list operator
		$list_operator = $this->model_ppob->get_list_operator();
		// count list operator
		if ( count( $list_operator ) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data tidak ditemukan dipangkalan data.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data berhasil ditemukan dipangkalan data.',
				'data' => $list_operator,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// check produk id in add update product process
	public function _ck_produk_id_cud(){
		if( $this->input->post('id') ) {
			if( ! $this->model_ppob->check_product_id( $this->input->post('id') ) ){
				$this->form_validation->set_message('_ck_produk_id_cud', 'ID Produk tidak ditemukan.');
         		return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	// check operator id
	public function _ck_operator_id( $id ){
		if( ! $this->model_ppob->check_operator( $id ) ) {
			$this->form_validation->set_message('_ck_operator_id', 'ID Operator tidak ditemukan.');
         	return FALSE;
		}else{
			return TRUE;
		}
	}

	// check kode produk
	public function _ck_kode_produk( $kode ) {
		if( $this->input->post('id') ) {
			// check kode produk if id exist
			if( $this->model_ppob->check_kode_product( $kode, $this->input->post('id') ) ) {
				$this->form_validation->set_message('_ck_kode_produk', 'Kode produk sudah terdaftar dipangkalan data.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			// check kode produk
			if( $this->model_ppob->check_kode_product( $kode ) ) {
				$this->form_validation->set_message('_ck_kode_produk', 'Kode produk sudah terdaftar dipangkalan data.');
				return FALSE;
			}else{
				return TRUE;
			}
		}
	}

	// proses add update produk
	public function proses_add_update_produk(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_produk_id_cud');
		$this->form_validation->set_rules('operator', '<b>Operator<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_operator_id');
		$this->form_validation->set_rules('kode', '<b>Kode Produk<b>', 'required|trim|xss_clean|min_length[1]|callback__ck_kode_produk');
		$this->form_validation->set_rules('name', '<b>Nama Produk<b>', 'required|trim|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// 
			$data = array();
			$data['operator_id'] = $this->input->post('operator');
			$data['product_code'] = $this->input->post('kode');
			$data['product_name'] = $this->input->post('name');
			$data['updated_at'] = date('Y-m-d H:i:s');
			// filter
			if ( $this->input->post('id') ) {
				if( ! $this->model_ppob_cud->update_produk( $this->input->post('id'), $data ) ) {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses update data produk gagal dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}else{
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses update data produk berhasil dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
			} else {
				$data['created_at'] = date('Y-m-d H:i:s');
				if( ! $this->model_ppob_cud->insert_produk( $data ) ) {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses insert data produk gagal dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}else{
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses insert data produk berhasil dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
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

	public function delete_produk(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_produk_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// delete process
			if( ! $this->model_ppob_cud->delete_produk( $this->input->post('id') ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete data produk gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete data produk berhasil dilakukan.',
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

	// get data edit product from database
	public function get_info_edit_update_produk(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_produk_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get data
			$data = $this->model_ppob->get_list_operator();
			// get value
			$value = $this->model_ppob->get_data_edit_product( $this->input->post('id') );
			// filter
			if( count( $data ) == 0 ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'data produk gagal ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'data produk berhasil ditemukan.',
					'data' => $data,
					'value' => $value,
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

	function _ck_operator_markup_id($id){
		if( $id != 0 ) {
			if( ! $this->model_ppob->check_operator( $id ) ) {
				$this->form_validation->set_message('_ck_operator_id', 'ID Operator tidak ditemukan.');
	         	return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	//
	public function proses_markup_massal_produk() {
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('operator', '<b>Operator ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_operator_markup_id');
		$this->form_validation->set_rules('nominal_1', '<b>Nominal Markup 1 - 10.000<b>', 'required|trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nominal_2', '<b>Nominal Markup 2<b>', 'required|trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nominal_3', '<b>Nominal Markup 3<b>', 'required|trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nominal_4', '<b>Nominal Markup 4<b>', 'required|trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nominal_5', '<b>Nominal Markup 5<b>', 'required|trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('nominal_6', '<b>Nominal Markup 6<b>', 'required|trim|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// operator
			$this->db->select('id, price')->from('ppob_prabayar_product');
			if( $this->input->post('operator') != 0 ) {
				$this->db->where('operator_id', $this->input->post('operator') );
			}
			$error = false;
			$q = $this->db->get();
			if( $q->num_rows() > 0 ) {
				foreach ( $q->result() as $rows ) {
					$markup = 0;
					if( $rows->price >= 1 && $rows->price <= 10000 ) {
						$markup = $this->text_ops->hide_currency( $this->input->post('nominal_1') );
					} else if( $rows->price >= 10001 && $rows->price <= 50000 ) {
						$markup = $this->text_ops->hide_currency( $this->input->post('nominal_2') );
					} else if( $rows->price >= 50001 && $rows->price <= 100000 ) {
						$markup = $this->text_ops->hide_currency( $this->input->post('nominal_3') );
					} else if( $rows->price >= 100001 && $rows->price <= 300000 ) {
						$markup = $this->text_ops->hide_currency( $this->input->post('nominal_4') );
					} else if( $rows->price >= 300001 && $rows->price <= 500000 ) {
						$markup = $this->text_ops->hide_currency( $this->input->post('nominal_5') );
					} else if( $rows->price >= 500001 ) {
						$markup = $this->text_ops->hide_currency( $this->input->post('nominal_6') );
					}
					// data
					$data = array();
					$data['application_markup'] = $markup;
					$data['updated_at'] = date('Y-m-d H:i:s');
					// filter
					if( ! $this->model_ppob_cud->update_produk($rows->id, $data ) ) {
						$error = true;
					}
				}
			}
			// filter
			if( $error == true ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses markup data gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses markup data berhasil dilakukan.',
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

	function daftar_operator_amra(){
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
			$total 	= $this->model_ppob->get_total_daftar_operator_amra($search);
			$list 	= $this->model_ppob->get_index_daftar_operator_amra($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar operator amra tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar operator amra berhasil ditemukan.',
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

	// get info category
	public function get_info_operator(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		// get list operator
		$list_category = $this->model_ppob->get_list_category();
		// count list operator
		if ( count( $list_category ) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data tidak ditemukan dipangkalan data.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data berhasil ditemukan dipangkalan data.',
				'data' => $list_category,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	public function _ck_operator_id_exist(){
		if( $this->input->post('id') ) {
			if( ! $this->model_ppob->check_operator_id( $this->input->post('id') ) ) {
				$this->form_validation->set_message('_ck_operator_id_exist', 'ID Operator tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}else{
			return TRUE;
		}
	}

	// categori
	public function _ck_category_id_exist($id){
		if( ! $this->model_ppob->check_category_ppob_exist( $id ) ) {
			$this->form_validation->set_message('_ck_category_id_exist', 'ID Kategori tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// proses update operator
	function proses_update_operator(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Operator ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_operator_id_exist');
		$this->form_validation->set_rules('category', '<b>Category<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_category_id_exist');
		$this->form_validation->set_rules('kode', '<b>Kode Operator<b>', 'required|trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('name', '<b>Nama Operator<b>', 'required|trim|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get data
			$data = array();
			$data['category_id'] = $this->input->post('category');
			$data['operator_code'] = $this->input->post('kode');
			$data['operator_name'] = $this->input->post('name');
			// filter id
			if( $this->input->post('id') ) {
				if( ! $this->model_ppob_cud->update_operator( $this->input->post('id'), $data ) ){
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses update data operator gagal dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}else{
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses update data operator berhasil dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
			}else{
				$data['created_at'] = date('Y-m-d H:i:s');
				if( ! $this->model_ppob_cud->insert_operator( $data ) ) {
					$return = array(
						'error'	=> true,
						'error_msg' => 'Proses insert data operator gagal dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}else{
					$return = array(
						'error'	=> false,
						'error_msg' => 'Proses insert data operator berhasil dilakukan.',
						$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
					);
				}
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

	// get info edit operator
	function get_info_edit_operator(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Operator ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_operator_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// get list operator
			$data = $this->model_ppob->get_list_category();
			// get value 
			$value  = $this->model_ppob->get_value_operator( $this->input->post('id') );
			// filter
			if( count($value) == 0 ){
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data operator tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data operator berhasil ditemukan.',
					'data' => $data, 
					'value' => $value,
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

	// delete operator
	function delete_operator(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Operator ID<b>', 'trim|xss_clean|min_length[1]|numeric|callback__ck_operator_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			// filter
			if( ! $this->model_ppob_cud->delete_operator( $this->input->post('id') ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete operator gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete berhasil ditemukan.',
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

	// daftar operator iak
	function daftar_operator_iak(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 'trim|xss_clean|min_length[1]|numeric');
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
			$total 	= $this->model_ppob->get_total_daftar_operator_iak($search);
			$list 	= $this->model_ppob->get_index_daftar_operator_iak($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar operator iak tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar operator iak berhasil ditemukan.',
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

	// daftar product iak
	function daftar_produk_iak(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 'trim|xss_clean|min_length[1]|numeric');
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
			$total 	= $this->model_ppob->get_total_daftar_product_iak($search);
			$list 	= $this->model_ppob->get_index_daftar_product_iak($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar product iak tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar product iak berhasil ditemukan.',
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

	function _ck_category( $id ){
		if( $id == 0 ) {
			return TRUE;
		}else{
			if( ! $this->model_ppob->check_category_ppob_exist( $id )  ){
				$this->form_validation->set_message('_ck_category', 'ID Kategori tidak ditemukan.');
				return FALSE;
			}else{
				return TRUE;
			}
		}

	}

	function daftar_produk_sinkronisasi(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 'trim|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('category',	'<b>Kategori<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_category');
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
			$total 	= $this->model_ppob->get_total_daftar_product_sinkronisasi($search, $this->input->post('category'));
			$list 	= $this->model_ppob->get_index_daftar_product_sinkronisasi($perpage, $start_at, $search, $this->input->post('category'));
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar product sinkronisasi tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar product sinkronisasi berhasil ditemukan.',
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

	function get_list_product_iak(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		// get list operator
		$list_product_iak = $this->model_ppob->get_list_product_iak();
		// count list operator
		if ( count( $list_product_iak ) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data tidak ditemukan dipangkalan data.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data berhasil ditemukan dipangkalan data.',
				'data' => $list_product_iak,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function get_list_product_tripay(){

		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		// get list operator
		$list_product_tripay = $this->model_ppob->get_list_product_tripay();
		// count list operator
		if ( count( $list_product_tripay ) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data tidak ditemukan dipangkalan data.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data berhasil ditemukan dipangkalan data.',
				'data' => $list_product_tripay,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);

	}

	function _ck_server($id){
		$server = array("iak", "tripay");
		if( in_array($id, $server ) ) {
			return TRUE;
		}else{
			$this->form_validation->set_message('_ck_server', 'Server tidak ditemukan.');
			return FALSE;
		}
	}

	// check iak product id
	function _ck_server_id($id){
		if( $this->input->post('server') ) {
			$server = $this->input->post('server');
			if( $server == 'iak' ) {
				if( ! $this->model_ppob->check_iak_product_id_exist( $id ) ){
					$this->form_validation->set_message('_ck_server_id', 'Product Server ID tidak ditemukan.');
					return FALSE;
				}else{
					return TRUE;
				}
			}else{
				if( ! $this->model_ppob->check_tripay_product_id_exist( $id ) ){
					$this->form_validation->set_message('_ck_server_id', 'Product Server ID tidak ditemukan.');
					return FALSE;
				}else{
					return TRUE;
				}
			}
		}else{
			return TRUE;
		}

	}

	function simpanPerubahanKoneksi(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('server', '<b>Server<b>', 'required|trim|xss_clean|min_length[1]|callback__ck_server');
		$this->form_validation->set_rules('id', '<b>Produk ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_produk_id');
		$this->form_validation->set_rules('server_id', '<b>Server Produk ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_server_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$data = array();
			if( $this->input->post('server') == 'iak' ) {
				$data['product_id_iak'] = $this->input->post('server_id');
			}else{
				$data['product_id_tripay'] = $this->input->post('server_id');
			}
			// process
			if( $this->model_ppob->check_tabel_connection_exist( $this->input->post('id') ) ) {
				# update
				if( ! $this->model_ppob_cud->updateKoneksiServer($this->input->post('id'), $data) ){
					$error = 1;
					$error_msg = 'Proses update gagal dilakukan.';
				}
			}else{
				// insert
				$data['product_id'] = $this->input->post('id');
				# insert
				if( ! $this->model_ppob_cud->insertKoneksiServer( $data ) ){
					$error = 1;
					$error_msg = 'Proses insert gagal dilakukan.';
				}
			}
			// filter
			if( $error == 1 ){
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses koneksi ke server berhasil dilakukan.',
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

	// get category
	function get_category(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		// get list operator
		$list_category = $this->model_ppob->get_list_category();
		// count list operator
		if ( count( $list_category ) == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data kategori tidak ditemukan dipangkalan data.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data kategori berhasil ditemukan dipangkalan data.',
				'data' => $list_category,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// delete koneksi
	function deleteKoneksi(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('server', '<b>Server<b>', 'required|trim|xss_clean|min_length[1]|callback__ck_server');
		$this->form_validation->set_rules('id', '<b>Produk ID<b>', 'required|trim|xss_clean|min_length[1]|numeric|callback__ck_produk_id');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			# data
			$data = array();
			if( $this->input->post('server') == 'iak' ) {
				$data['product_id_iak'] = 0;
			}else{
				$data['product_id_tripay'] = 0;
			}
			if( ! $this->model_ppob_cud->updateKoneksiServer($this->input->post('id'), $data) ){
				$error = 1;
				$error_msg = 'Proses close koneksi gagal dilakukan.';
			}
			// filter
			if( $error == 1 ){
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}else{
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses koneksi ke server berhasil dilakukan.',
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

	// update data product
	function updateDataProduct(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		// update harga produk
		$this->iak->get_price_product_list();
		// error
		if ( $error == 1 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data produk gagal diupdate.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data produk berhasil diupdate.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// update harga produk
	function update_harga_produk(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		if( ! $this->model_ppob->update_harga_produk() ){
			$return = array(
				'error'	=> true,
				'error_msg' => 'Proses update harga produk gagal dilakukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}else{
			$return = array(
				'error'	=> false,
				'error_msg' => 'Proses update harga produk berhasil dilakukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function updateDataProductTripay(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		// update harga produk
		$this->tripay->get_product();
		// error
		if ( $error == 1 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data produk gagal diupdate.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data produk berhasil diupdate.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// daftar product tripay
	function daftar_produk_tripay(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('search',	'<b>Search<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 'trim|xss_clean|min_length[1]|numeric');
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
			$total 	= $this->model_ppob->get_total_daftar_product_tripay($search);
			$list 	= $this->model_ppob->get_index_daftar_product_tripay($perpage, $start_at, $search);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar product tripay tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar product tripay berhasil ditemukan.',
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


	function get_parameter_product(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$kategori_operator = $this->model_ppob->get_kategori_product();
		// error
		if ( $error == 1 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data produk gagal diupdate.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data produk berhasil diupdate.',
				'data' => $kategori_operator, 
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	function updateStatusTransaksi() {
		// get list product
		$list_product_process = $this->model_ppob->get_list_product_process();
		// looping
		foreach ($list_product_process as $key => $value) {
			$feedBack = array();
			$feedBack['transaction_code'] = $value['transaction_code'];
			$feedBack['product_code'] =  $value['product_code'];
			$feedBack['nomor_tujuan'] =  $value['nomor_tujuan'];
			$feedBack['price'] = 'Rp '.number_format($value['company_price']);
			# server
			if( $value['server'] == 'iak' ) {
				# check status IAK
				$check_status = $this->iak->check_status_transaksi($value['transaction_code']);
				# filter Token Listrik
				if( $value['category_code'] == 'TL' ) {
					// check
					if ( isset( $check_status->data->status ) AND $check_status->data->status == 1  ) {
						$feedBack['pesan'] = $check_status->data->sn;
						$feedBack['status'] = 'Sukses';
					} else if( isset( $check_status->data->status ) AND $check_status->data->status == 2 ) {
						$feedBack['pesan'] = 'Pembelian '.$value['product_code'].' ke '.$value['nomor_tujuan'].' Gagal dilakukan';
						$feedBack['status'] = 'Gagal';
						# saldo company now
                        $saldo_company_now = $this->model_ppob->get_saldo_company_now($value['company_id']);
                        # get saldo
                        $get_back_saldo = $saldo_company_now + $value['application_price'];
                        # get back saldo
                        $feedBack['get_back_saldo'] = $get_back_saldo;
					}
				}else{
					if ( isset( $check_status->data->status ) AND $check_status->data->status == 1  ) {
						$feedBack['pesan'] = 'Pembelian '.$value['product_code'].' ke '.$value['nomor_tujuan'].' Berhasil dilakukan';
						$feedBack['status'] = 'Sukses';
					} else if( isset( $check_status->data->status ) AND $check_status->data->status == 2 ) {
						$feedBack['pesan'] = 'Pembelian '.$value['product_code'].' ke '.$value['nomor_tujuan'].' Gagal dilakukan';
						$feedBack['status'] = 'Gagal';
						# saldo company now
                        $saldo_company_now = $this->model_ppob->get_saldo_company_now($value['company_id']);
                        # get saldo
                        $get_back_saldo = $saldo_company_now + $value['application_price'];
                        # get back saldo
                        $feedBack['get_back_saldo'] = $get_back_saldo;
					}
				}
				// update Model_ppob_cud
				$this->model_ppob_cud->update_status_ppob( $feedBack, $value['company_id']  );
			} else if( $value['server'] == 'tripay' ) { 
				# check status TRIPAY
				$check_status = $this->tripay->check_status_transaksi($value['transaction_code']);
				# filter Token Listrik
				if( $value['category_code'] == 'TL' ) {
					if ( isset( $check_status->success ) AND $check_status->success == 1  ) {
						if ( isset( $check_status->data->status ) AND $check_status->data->status == 1 ) { // sukses
							$feedBack['pesan'] = $check_status->data->note;
							$feedBack['status'] = 'Sukses';
						} else if ( isset( $check_status->data->status ) AND ( $check_status->data->status == 2 || $check_status->data->status == 3 ) ) {
							$feedBack['pesan'] = 'Pembelian '.$value['product_code'].' ke '.$value['nomor_tujuan'].' Gagal dilakukan';
							$feedBack['status'] = 'Gagal';
							# saldo company now
                        	$saldo_company_now = $this->model_ppob->get_saldo_company_now($value['company_id']);
                            # get saldo
                            $get_back_saldo = $saldo_company_now + $value['application_price'];
                            # get back saldo
                            $feedBack['get_back_saldo'] = $get_back_saldo;
						}
					}
				}else{
					if ( isset( $check_status->data->status ) AND $check_status->data->status == 1 ) { // sukses
						$feedBack['pesan'] = 'Pembelian '.$value['product_code'].' ke '.$value['nomor_tujuan'].' Berhasil dilakukan';
						$feedBack['status'] = 'Sukses';
					} else if ( isset( $check_status->data->status ) AND ( $check_status->data->status == 2 || $check_status->data->status == 3 ) ) {
						$feedBack['pesan'] = 'Pembelian '.$value['product_code'].' ke '.$value['nomor_tujuan'].' Gagal dilakukan';
						$feedBack['status'] = 'Gagal';
						# saldo company now
                        $saldo_company_now = $this->model_api->get_saldo_company_now($value['company_id']);
                        # get saldo
                        $get_back_saldo = $saldo_company_now + $value['application_price'];
                        # get back saldo
                        $feedBack['get_back_saldo'] = $get_back_saldo;
					}
				}
				// update
				$this->model_ppob_cud->update_status_ppob( $feedBack, $value['company_id']  );
			}
		}
		$return = array(
			'error'	=> false,
			'error_msg' => 'Proses update status berhasil dilakukan.',
			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
		);

		echo json_encode($return);
	}

}

//082169797745
//082169797745
			//https://wapisender.id/api/v5/device/qr?api_key=4BQEL001HE7FCFFCSTYHFNNAS9D2FENS&device_key=X1KQU7

<iframe src="https://wapisender.id/api/v5/device/qr?api_key=YOURAPIKEY&device_key=YOURDEVICEKEY"></iframe>