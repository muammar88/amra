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
		# model trans tiket cud
		$this->load->model('Model_ppob_cud', 'model_ppob_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	// server side
	function server_side(){
		$return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('search', '<b>Search<b>',     'trim|xss_clean|min_length[1]');
        $this->form_validation->set_rules('perpage',    '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
        $this->form_validation->set_rules('pageNumber', '<b>pageNumber<b>', 'trim|xss_clean|min_length[1]|numeric');
        /*
            Validation process
        */
        if ($this->form_validation->run()) {
            $search     = $this->input->post('search');
            $perpage = $this->input->post('perpage');
            $start_at = 0;
            if ($this->input->post('pageNumber')) {
                $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
            }
            $total  = $this->model_ppob->get_total_daftar_trans_ppob($search);
            $list   = $this->model_ppob->get_index_daftar_trans_ppob($perpage, $start_at, $search);
            if ($total == 0) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Daftar transaksi ppob tidak ditemukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Daftar transaksi ppob berhasil ditemukan.',
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

	function markup_server_side(){
		$return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('search', '<b>Search<b>',     'trim|xss_clean|min_length[1]');
        $this->form_validation->set_rules('perpage',    '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
        $this->form_validation->set_rules('pageNumber', '<b>pageNumber<b>', 'trim|xss_clean|min_length[1]|numeric');
        $this->form_validation->set_rules('tipe', '<b>Tipe<b>', 'trim|xss_clean|min_length[1]|in_list[prabayar,pascabayar]');
        /*
            Validation process
        */
        if ($this->form_validation->run()) {
        	$tipe = $this->input->post('tipe');
            $search     = $this->input->post('search');
            $perpage = $this->input->post('perpage');
            $start_at = 0;
            if ($this->input->post('pageNumber')) {
                $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
            }
            $total  = $this->model_ppob->get_total_daftar_markup_ppob($search, $tipe);
            $list   = $this->model_ppob->get_index_daftar_markup_ppob($perpage, $start_at, $search, $tipe);
            if ($total == 0) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Daftar markup ppob tidak ditemukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Daftar markup ppob berhasil ditemukan.',
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


	function _ck_product_code_exist( $product_code ) {
		$tipe = $this->input->post('tipe');
		if ( ! $this->model_ppob->check_product_code_exist( $product_code, $tipe ) ) {
			$this->form_validation->set_message('_ck_product_code_exist', 'ID Produk tidak ditemukan.');
 			return FALSE;
		} else {
			return TRUE;
		}
	}

	// get edit ppob
	function get_edit_ppob(){
		$return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('product_code', '<b>Kode Product<b>',     'trim|required|xss_clean|min_length[1]|callback__ck_product_code_exist');
        $this->form_validation->set_rules('tipe', '<b>Tipe Product<b>',     'trim|required|xss_clean|min_length[1]|in_list[prabayar,pascabayar]');
        /*
            Validation process
        */
        if ($this->form_validation->run() ) {
        	// markup perusahaan
        	$markup_perusahaan = $this->model_ppob->get_markup_perusahaan( $this->input->post('product_code'), $this->input->post('tipe') );
        	// error
            if ( $error == 1 ) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Daftar markup ppob tidak ditemukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Daftar markup ppob berhasil ditemukan.',
                    'data' => $markup_perusahaan,
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


	function proses_edit_markup_ppob(){
		$return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('product_code', '<b>Kode Product<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_product_code_exist');
        $this->form_validation->set_rules('tipe', '<b>Tipe Product<b>', 'trim|required|xss_clean|min_length[1]|in_list[prabayar,pascabayar]');
        $this->form_validation->set_rules('markup_perusahaan', '<b>Markup Product<b>', 'trim|required|xss_clean|min_length[1]');
        /*
            Validation process
        */
        if ($this->form_validation->run() ) {
        	$product_code = $this->input->post('product_code');
        	$tipe = $this->input->post('tipe');
        	$markup = $this->text_ops->hide_currency($this->input->post('markup_perusahaan'));
        	// error
            if ( ! $this->model_ppob_cud->update_markup_company( $product_code, $tipe, $markup, $this->company_id ) ) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Proses update data markup company gagal dilakukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Proses update data markup company berhasil dilakukan.',
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


	function delete_edit_ppob(){

		$return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('product_code', '<b>Kode Product<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_product_code_exist');
        $this->form_validation->set_rules('tipe', '<b>Tipe Product<b>', 'trim|required|xss_clean|min_length[1]|in_list[prabayar,pascabayar]');
        /*
            Validation process
        */
        if ($this->form_validation->run() ) {
        	$product_code = $this->input->post('product_code');
        	$tipe = $this->input->post('tipe');
        	// error
            if ( ! $this->model_ppob_cud->delete_markup_company( $product_code, $tipe, $this->company_id ) ) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Proses delete data markup company gagal dilakukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Proses delete data markup company berhasil dilakukan.',
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

	// get info markup company
	function get_edit_markup_company(){
		// error
		$error = 0;
		// markup
		$markup = $this->model_ppob->get_markup_company();
		// filter error
		if ( $error == 1 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Data markup perusahaan tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		} else {
			$return = array(
				'error'	=> false,
				'error_msg' => 'Data markup perusahaan berhasil ditemukan.',
				'data' => $markup,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}
		echo json_encode($return);
	}

	// proses edit markup
	function proses_edit_markup_perusahaan(){
		$return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('markup_perusahaan', '<b>Markup Product<b>', 'trim|required|xss_clean|min_length[1]');
        /*
            Validation process
        */
        if ($this->form_validation->run() ) {
        	// data
        	$data = array();
        	$data['company_markup'] = $this->text_ops->hide_currency($this->input->post('markup_perusahaan'));
        	$data['last_update'] = date('Y-m-d');
        	// error
            if ( ! $this->model_ppob_cud->update_markup_default_perusahaan($this->company_id, $data) ) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Proses update data default markup company gagal dilakukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Proses update data default markup company berhasil dilakukan.',
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