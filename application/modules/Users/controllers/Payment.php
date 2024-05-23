<?php

/**
 *  -----------------------
 *	Payment Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Payment extends CI_Controller
{
	private $company_code;
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_users', 'model_users');
		$this->load->model('Model_users_cud', 'model_users_cud');
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# midtrans
		$params = array('server_key' => 
								$this->config->item('midtrans_production') == true ? $this->config->item('midtrans_server_key') : $this->config->item('sb_midtrans_server_key') , 
							 'production' => $this->config->item('midtrans_production'));
		// load midtrans library 
		$this->load->library('midtrans');
		// insert midtrans param
		$this->midtrans->config( $params );
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

   function index(){

      if( $this->input->get('code') ) {
			# get info
			$data = $this->model_users->get_info_company( $this->input->get('code') );
			# filter
			if( count( $data ) > 0 ) {
				$data['midtrans_client_key'] = $this->config->item('midtrans_production') == true ? $this->config->item('midtrans_client_key') : $this->config->item('sb_midtrans_client_key');
				$this->templating->payment_templating($data);
			}else{
				redirect('Users/Sign_up', 'refresh');
			}
      }else{
         redirect('Users/Sign_up', 'refresh');
      }
   }

	function _ck_code_exist($code){
		if( ! $this->model_users->check_company_code_exist( $code ) ) {
			$this->form_validation->set_message('_ck_code_exist', 'Code perusahaan tidak ditemukan dipangkalan data.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	function get_token(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('code',	'<b>Code<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_exist');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {

			$info = $this->model_users->get_info_company( $this->input->post('code') );
			// Required
			$transaction_details = array(
			  'order_id' => $this->model_users->gen_order_id(),
			  'gross_amount' => $info['total'], // no decimal allowed for creditcard
			);
			// Costumer Detail
			$customer_details = array(
			  'first_name'    => $info['name'],
			  // 'last_name'     => $info['name'],
			  'email'         => $info['email']
			);
			// Data yang akan dikirim untuk request redirect_url.
		  	$credit_card['secure'] = true;
		  	// time
		  	$time = time();
		  	$custom_expiry = array(
				'start_time' => date("Y-m-d H:i:s O",$time),
				'unit' => 'hour',
				'duration'  => 2
		  	);
		  	# data transaction
		  	$transaction_data = array(
				'transaction_details'=> $transaction_details,
				'customer_details'   => $customer_details,
				'credit_card'        => $credit_card,
				'expiry'             => $custom_expiry
		  	);
			# snap
		  	$snapToken = $this->midtrans->getSnapToken($transaction_data);
			# filter error
			if ($snapToken == '' ) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Token gagal di generated.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Token berhasil digenerated.',
					'token' => $snapToken,
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

	function _ck_saldo($saldo){
		$saldo = explode('.', $saldo)[0];
		// _ck_saldo
		if($this->text_ops->hide_currency($saldo) > 0 ){
			return TRUE;
		}else{
			$this->form_validation->set_message('_ck_saldo', 'Untuk melanjutkan, Saldo tidak boleh nol.');
			return FALSE;
		}
	}

	# save log
	function save_process_log_saldo(){
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('code',	'<b>Code<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_code_exist');
		$this->form_validation->set_rules('status_code', '<b>Status Code<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('status_message', '<b>Status Message<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('transaction_id', '<b>Transaksi ID<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('order_id', '<b>Order ID<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('gross_amount', '<b>Gross Amount<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_saldo');
		$this->form_validation->set_rules('payment_type', '<b>Payment Type<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('transaction_time',	'<b>Transaction Time<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('transaction_status', '<b>Transaction Status<b>', 'trim|required|xss_clean|min_length[1]|in_list[pending,capture,settlement,deny,cancel,expire,failure,refund,chargeback,partial_refund,partial_changeback,authorize]');
		$this->form_validation->set_rules('fraud_status', '<b>Fraud Status<b>', 'trim|required|xss_clean|min_length[1]|in_list[accept,deny,challenge]');
		$this->form_validation->set_rules('bill_key', '<b>Bill Key<b>', 'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('biller_code', '<b>Biller Code<b>', 'trim|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pdf_url',	'<b>Pdf URL<b>', 'trim|required|xss_clean|min_length[1]');
		$this->form_validation->set_rules('finish_redirect_url',	'<b>Finish Redirect URL<b>', 'trim|required|xss_clean|min_length[1]');
		/*
		  Validation process
		*/
		if ($this->form_validation->run()) {
			# get info
			$info = $this->model_users->get_info_company( $this->input->post('code') );
			# gros amount
			$gross_amount = explode('.', $this->input->post('gross_amount'));
			# data
			$data = array();
			$data['status_code'] = $this->input->post('status_code');
			$data['transaction_type'] = 'subscribtion_payment';
			$data['status_message'] = $this->input->post('status_message');
			$data['transaction_id'] = $this->input->post('transaction_id');
			$data['order_id'] = $this->input->post('order_id');
			$data['gross_amount'] = $this->input->post('gross_amount');
			$data['payment_type'] = $this->input->post('payment_type');
			$data['transaction_time'] = $this->input->post('transaction_time');
			$data['transaction_status'] = $this->input->post('transaction_status');
			$data['fraud_status'] = $this->input->post('fraud_status');
			$data['bill_key'] = $this->input->post('bill_key');
			$data['biller_code'] = $this->input->post('biller_code');
			$data['pdf_url'] = $this->input->post('pdf_url');
			$data['finish_redirect_url'] = $this->input->post('finish_redirect_url');

			$data_subscription_history = array();
			$data_subscription_history['order_id']  = $this->input->post('order_id');
			# insert process
			if( !$this->model_users_cud->insert_payment_history( $data, $data_subscription_history, $info['company_id'] ) ) {
				$error = 1;
				$error_msg = 'Proses penyimpanan log pembayaran saldo gagal dilakukan.';
			}
			# filte process
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => $error_msg,
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses penyimpanan log pembayaran saldo berhasil dilakukan.',
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
