<?php

/**
 *  -----------------------
 *	Notification Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends CI_Controller
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Model_notification', 'model_notification');
		$this->load->model('Model_notification_cud', 'model_notification_cud');

		$params = array('server_key' => $this->config->item('midtrans_server_key'), 'production' => true);
		$this->load->library('midtrans');
		$this->midtrans->config($params);
		$this->load->helper('url');
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function _ck_order_id_exist($order_id){
		if( ! $this->model_notification->check_order_exist( $order_id ) ) {
			$this->form_validation->set_message('_ck_order_id_exist', 'Order ID tidak ditemukan.');
			return FALSE;
		}else{
			return TRUE;
		}
	}

	// function ppob_callback(){
	// 	// topUp($ref_id, $amra_product_code, $costumer_id, $pln_status )
	// 	// $feedBack= $this->tripay->topUp('12312', 'PIUTSL5', '085262802141', false);
	// 	$feedBack= $this->tripay->get_harga_from_server('S5');
		
	// 	print_r($feedBack);
	// }

	# notification
	function index(){
		# accepted transaction
		$accepted = array('settlement', 'capture');
		# reject transaction
		$reject = array('deny', 'cancel', 'expire', 'failure');
		# get json result
		$json_result = file_get_contents('php://input');
		# get result
		$result = json_decode( $json_result, "true" );
		# info
		$info = $this->model_notification->check_order_exist( $result['order_id'] );
		# check order id exist
		if( $info['num'] ) {
			$key = $this->config->item('midtrans_production') == true ? $this->config->item('midtrans_server_key') : $this->config->item('sb_midtrans_server_key');
			# signature_key
			$signature_key = $result['signature_key'];
			# hash
			$hash = hash("sha512", $info['order_id'].$result['status_code'].$info['gross_amount'].$key );
			# filter
			if( $signature_key == $hash ) {
				if( $info['transaction_type'] == 'subscribtion_payment' ) {
					// subscribtion_payment_history
					$info_subscribtion_payment_history = $this->model_notification->get_info_subscribtion( $info['order_id'] );
					print("<br>Info<br>");
					print_r($info);
					print("<br>");

					if( count( $info_subscribtion_payment_history ) > 0 ) {
						$company_id = $info_subscribtion_payment_history['company_id'];
						// update payment history
						$data_payment_history = array();
						$data_payment_history['status_code'] = $result['status_code'];
						$data_payment_history['transaction_status'] = $result['transaction_status'];
						$data_payment_history['fraud_status'] = $result['fraud_status'];
						if( $result['transaction_status'] == 'settlement') {
							$data_payment_history['settlement_time'] = $result['settlement_time'];
						}
						// update subscribtion payment history
						$data_subscribtion_payment_history = array();
						$data_subscribtion_payment_history['last_update'] = date('Y-m-d H:i:s');
						// update company
						$data_company = array();
						if( in_array( $result['transaction_status'], $accepted ) ){
							$info_company = $this->model_notification->get_info_company( $company_id );

							print("<br>Info_company<br>");
							print_r($info_company);
							print("<br>");

							if( $info_company['end_date_subscribtion'] > $info_subscribtion_payment_history['start_date_subscribtion'] ) {
								$data_company['end_date_subscribtion'] = $info_subscribtion_payment_history['end_date_subscribtion'];
							}else{
								$data_company['start_date_subscribtion'] = $info_subscribtion_payment_history['start_date_subscribtion'];
								$data_company['end_date_subscribtion'] = $info_subscribtion_payment_history['end_date_subscribtion'];
							}
							$data_company['payment_process'] = false;
							$data_company['last_update'] = date('Y-m-d');
							$data_subscribtion_payment_history['payment_status'] = 'accept';
						}elseif (  in_array( $result['transaction_status'], $reject ) ) {
							$data_subscribtion_payment_history['payment_status'] = 'reject';
						}
						# update process
						if( ! $this->model_notification_cud->update_subscribtion_transaction( $data_company, $data_payment_history, $data_subscribtion_payment_history, $company_id, $info['order_id']) ){
							$error = 1;
							$error_msg = 'Proses update data berlangganan gagal dilakukan.';
						}
					}
				
				}elseif ( $info['transaction_type'] == 'deposit_saldo' ) {

					$info_saldo_transaction = $this->model_notification->info_saldo_transaction( $info['order_id'] );

					$company_saldo_transaction_id = $info_saldo_transaction['id'];
					$company_id = $info_saldo_transaction['company_id'];
					$saldo_company = $info_saldo_transaction['saldo'];
					# receive data history
					$data_payment_history = array();
					$data_payment_history['status_code'] = $result['status_code'];
					$data_payment_history['transaction_status'] = $result['transaction_status'];
					$data_payment_history['fraud_status'] = $result['fraud_status'];
					if( $result['transaction_status'] == 'settlement') {
						$data_payment_history['settlement_time'] = $result['settlement_time'];
					}
					# receive data transaction
					$data_transaction = array();
					# data company
					$data_company = array();
					# data jurnal
					$data_jurnal = array();
					# filter status
					if( in_array( $result['transaction_status'],  $accepted ) ){
						$data_transaction['status'] = 'accepted';
						$data_transaction['last_update'] = date('Y-m-d H:i:s');
						$data_company['saldo'] = $saldo_company + explode('.', $info['gross_amount'])[0];
						$data_company['last_update'] = date('Y-m-d');
						// print("<br>");
						// print($result['transaction_status']);
						// print("<br>");
						if( $result['transaction_status'] == 'settlement' ) {
							// ser
							$saldo_kas = $this->model_notification->get_kas_company( $company_id, explode('.', $info['gross_amount'])[0] );
							// 
							$data_jurnal['company_id'] = $company_id;
							$data_jurnal['source'] = 'depositsaldocompany:'.$company_saldo_transaction_id;
							$data_jurnal['ref'] = 'depositsaldocompany:'.$company_saldo_transaction_id;
							$data_jurnal['ket'] = 'depositsaldocompany:'.$company_saldo_transaction_id;
							$data_jurnal['akun_debet'] = '11030';
							$data_jurnal['akun_kredit'] = '11010';
							$data_jurnal['saldo'] = explode('.', $info['gross_amount'])[0];
							$data_jurnal['periode_id'] = 0;
							$data_jurnal['input_date'] = date('Y-m-d H:i:s');
							$data_jurnal['last_update'] = date('Y-m-d H:i:s');
						}
					}elseif (  in_array( $result['transaction_status'], $reject ) ) {
						$data_transaction['status'] = 'rejected';
						$data_transaction['last_update'] = date('Y-m-d H:i:s');
					}
					# update process
					if( ! $this->model_notification_cud->update_transaction( $data_transaction, $data_payment_history, $data_company, $company_id, $result['order_id'], $company_saldo_transaction_id, $data_jurnal ) ){
						$error = 1;
						$error_msg = 'Proses update data tambah saldo gagal dilakukan.';
					}
				}
			}
		}
	}
}
