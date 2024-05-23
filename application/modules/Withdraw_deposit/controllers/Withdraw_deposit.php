<?php

/**
 *  -----------------------
 *	Daftar transaksi visa Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Withdraw_deposit extends CI_Controller
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
		$this->load->model('Model_withdraw', 'model_withdraw');
		# model withdraw cud
		$this->load->model('Model_withdraw_cud', 'model_withdraw_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function server_side_withdraw()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('status',	'<b>Status<b>', 'trim|required|xss_clean|min_length[1]|in_list[diproses,disetujui,ditolak]');
		$this->form_validation->set_rules('search',	'<b>Search<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage', '<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>',	'trim|xss_clean|min_length[1]|numeric');
		/*
			Validation process
		*/
		if ($this->form_validation->run()) {
			$status = $this->input->post('status');
			$search = $this->input->post('search');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total = $this->model_withdraw->get_total_withdraw($search, $status);
			$list = $this->model_withdraw->get_index_withdraw($perpage, $start_at, $search, $status);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar transaksi visa tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar transaksi visa berhasil ditemukan.',
					'total' => $total,
					'data' => $list,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	// check id withdraw
	function _ck_id_withdraw($id){
		// filter
		if( ! $this->model_withdraw->check_id_withdraw( $id ) ){
			$this->form_validation->set_message('_ck_id_withdraw', 'ID Withdraw tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// delete request withdraw
	function tolak_request_withdraw(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Withdraw<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_withdraw');
		$this->form_validation->set_rules('alasan',	'<b>Alasan Penolakan Withdraw<b>', 'trim|required|xss_clean|min_length[1]');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			$data = array();
			# petugas
	        if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
	           $data['approver'] = "Administrator";
	        } else {
	           $data['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
	        }
			$data['status_request'] = 'ditolak';
			$data['status_note'] = $this->input->post('alasan');
			$data['last_update'] = date('Y-m-d H:i:s');
			// delete request withdraw
			if ( ! $this->model_withdraw_cud->tolak_request_withdraw( $this->input->post('id'), $data ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete withdraw gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete withdraw berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}

	// approve request withdraw
	function approve_request_withdraw(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id',	'<b>ID Withdraw<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_id_withdraw');
		/*
			Validation process
		*/
		if ( $this->form_validation->run() ) {
			$markup_withdraw = $this->model_withdraw->get_markup_withdraw_perusahaan();
			# get info withdraw
			$info_withdraw = $this->model_withdraw->get_info_withdraw_by_id( $this->input->post('id') );
			# generate nomor transaksis
      		$nomor_transaksi = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
			# define array
			$data = array();
			// table withdraw
			$data['withdraw_member']['status_request'] = 'disetujui';
			$data['withdraw_member']['last_update'] = date('Y-m-d H:i:s');
			// table deposit tansaction
			$data['deposit_transaction']['nomor_transaction'] = $nomor_transaksi;
	        $data['deposit_transaction']['company_id'] = $this->company_id;
	        $data['deposit_transaction']['personal_id'] = $info_withdraw['personal_id'];
	        $data['deposit_transaction']['debet'] = 0;
	        $data['deposit_transaction']['kredit'] = $markup_withdraw + $info_withdraw['amount'];
	        $data['deposit_transaction']['transaction_requirement'] = 'deposit';
	        # penerima
	        if ( $this->session->userdata( $this->config->item('apps_name') )['level_akun'] == 'administrator' ) {
	            $data['deposit_transaction']['approver'] = "Administrator";
	        } else {
	            $data['deposit_transaction']['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
	        }
	        $data['deposit_transaction']['info'] = 'Request Withdraw Deposit Member Dengan Nomor Transaksi Withdraw '.$info_withdraw['transaction_number'];
	        $data['deposit_transaction']['input_date'] = date('Y-m-d H:i:s');
	        $data['deposit_transaction']['last_update'] = date('Y-m-d H:i:s');
	        // table jurnal
	        $data['jurnal'][0]['company_id'] = $this->company_id;
	        $data['jurnal'][0]['source'] = 'withdraw:id:'.$info_withdraw['id']; 
	        $data['jurnal'][0]['ref'] = 'Withdraw Deposit Member No Transaction :'.$info_withdraw['transaction_number'];
	        $data['jurnal'][0]['ket'] = 'Withdraw Deposit Member No Transaction :'.$info_withdraw['transaction_number'];
	        $data['jurnal'][0]['akun_debet'] = '23000';
	        $data['jurnal'][0]['akun_kredit'] = '11010';
	        $data['jurnal'][0]['saldo'] = $info_withdraw['amount'] + $markup_withdraw;
	        $data['jurnal'][0]['periode_id'] = 0;
	        $data['jurnal'][0]['input_date'] = date('Y-m-d H:i:s');
	        $data['jurnal'][0]['last_update'] = date('Y-m-d H:i:s');

	        $data['jurnal'][1]['company_id'] = $this->company_id;
	        $data['jurnal'][1]['source'] = 'pendapatan_markup_withdraw:id:'.$info_withdraw['id']; 
	        $data['jurnal'][1]['ref'] = 'Pendapatan Markup Withdraw Deposit Member No Transaction :'.$info_withdraw['transaction_number'];
	        $data['jurnal'][1]['ket'] = 'Pendapatan Markup Withdraw Deposit Member No Transaction :'.$info_withdraw['transaction_number'];
	        $data['jurnal'][1]['akun_debet'] = '11010';
	        $data['jurnal'][1]['akun_kredit'] = '48000';
	        $data['jurnal'][1]['saldo'] = $markup_withdraw;
	        $data['jurnal'][1]['periode_id'] = 0;
	        $data['jurnal'][1]['input_date'] = date('Y-m-d H:i:s');
	        $data['jurnal'][1]['last_update'] = date('Y-m-d H:i:s');

			// delete request withdraw
			if ( ! $this->model_withdraw_cud->approve_request_withdraw( $this->input->post('id'), $data ) ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete withdraw gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete withdraw berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		} else {
			if (validation_errors()) {
				# define return error
				$return = array(
					'error'         => true,
					'error_msg'    => validation_errors(),
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		echo json_encode($return);
	}


	// get info markup withdraw
	function get_info_markup_withdraw(){
		// error
		$error = 0;
		// markup
		$markup = $this->model_withdraw->get_markup_withdraw_perusahaan();
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

	// proses update withdraw
	function proses_update_markup_withdraw(){
		$return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('markup_withdraw', '<b>Markup Withdraw<b>', 'trim|required|xss_clean|min_length[1]');
        /*
            Validation process
        */
        if ($this->form_validation->run() ) {
        	// data
        	$data = array();
        	$data['markup_withdraw'] = $this->text_ops->hide_currency($this->input->post('markup_withdraw'));
        	$data['last_update'] = date('Y-m-d');
        	// error
            if ( ! $this->model_withdraw_cud->update_markup_withdraw($this->company_id, $data) ) {
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