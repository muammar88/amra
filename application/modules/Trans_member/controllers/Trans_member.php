<?php
/**
*  -----------------------
*	Trans Member Controller
*	Created by Muammar Kadafi
*  -----------------------
*/

defined('BASEPATH') or exit('No direct script access allowed');

class Trans_member extends CI_Controller
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
		$this->load->model('Model_trans_member', 'model_trans_member');
		# model trans paket cud
		$this->load->model('Model_trans_member_cud', 'model_trans_member_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

   function daftar_transaksi_member(){
      $return 	= array();
      $error 		= 0;
      $error_msg = '';
      $this->form_validation->set_rules('status',	'<b>Status<b>', 	'trim|required|xss_clean|min_length[1]|in_list[diproses,disetujui,ditolak,semua]');
      $this->form_validation->set_rules('name_identity',	'<b>Nama / Nomor Identitas Member<b>', 	'trim|xss_clean|min_length[1]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 	'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $name_identity 	= $this->input->post('name_identity');
         $status = $this->input->post('status');
         $perpage = $this->input->post('perpage');
			$start_at = 0;
			if( $this->input->post('pageNumber') ){
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
         $total 	= $this->model_trans_member->get_total_trans_member($status, $name_identity);
         $list 	= $this->model_trans_member->get_index_trans_member($perpage, $start_at, $status, $name_identity);
         if ( $total == 0 ) {
            $return = array(
               'error'	=> true,
               'error_msg' => 'Data transaksi member tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Data transaksi member berhasil ditemukan.',
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

   function _ck_trans_member_id_exist($id){
      if( $this->model_trans_member->check_trans_member_id( $id ) ){
         return TRUE;
      }else{
         $this->form_validation->set_message('_ck_trans_member_id_exist', 'Trans member ID tidak ditemukan.');
         return FALSE;
      }
   }

	# function to claim paket process
	function _claimProcess( $id, $info_member_transaction ){
		$error = 0;
		$error_msg = '';

		$fee_id = array();
		$exp = explode(':', $info_member_transaction['ref']);
		if(strpos($exp[1], '#') !== false){
			$fee_id = explode('#', $exp);
		} else{
			$fee_id[] = $exp[1];
		}
		// insert ke deposit
		$data_deposit = array();
		$data_deposit['personal_id'] = $info_member_transaction['personal_id'];
		$data_deposit['company_id'] = $this->company_id;
		$data_deposit['debet'] = $info_member_transaction['amount'];
		$data_deposit['kredit'] = 0;
		$data_deposit['approver'] = $info_member_transaction['personal_id'];
		// approver
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
			$data_deposit['approver'] = "Administrator";
		}else{
			$data_deposit['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_deposit['info'] = 'Penambahan saldo dari proses klaim fee agen terhadap paket transaksi dengan nomor register berikut : ' . $info_member_transaction['nomor_register_paket_transaksi'];
		$data_deposit['input_date'] = date('Y-m-d H:i:s');
		$data_deposit['last_update'] = date('Y-m-d H:i:s');
		//update kolom reff dan status transaksi member dengan membuat status disetujui dan ref ditambahkan deposit id
		$data_member_transaction = array();
		$data_member_transaction['ref'] = $info_member_transaction['ref'];
		$data_member_transaction['status_request'] = 'disetujui';
		// approver
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
			$data_member_transaction['approver'] = "Administrator";
		}else{
			$data_member_transaction['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_member_transaction['last_update'] = date('Y-m-d H:i:s');
		# save to database
		if( !$this->model_trans_member_cud->approve_claim_trans_member($fee_id, $data_deposit, $data_member_transaction, $id) ){
			return false;

		}else{
			return true;
		}
	}

	function _depositProcess($id, $info_member_transaction){

		// insert ke deposit
		$data_deposit = array();
		$data_deposit['personal_id'] = $info_member_transaction['personal_id'];
		$data_deposit['company_id'] = $this->company_id;
		$data_deposit['debet'] = $info_member_transaction['amount'];
		$data_deposit['kredit'] = 0;
		// approver
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
			$data_deposit['approver'] = "Administrator";
		}else{
			$data_deposit['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_deposit['info'] = 'Penambahan saldo dari proses deposit member dengan member id ' . $info_member_transaction['personal_id'];
		$data_deposit['input_date'] = date('Y-m-d H:i:s');
		$data_deposit['last_update'] = date('Y-m-d H:i:s');
		//update kolom reff dan status transaksi member dengan membuat status disetujui dan ref ditambahkan deposit id
		$data_member_transaction = array();
		$data_member_transaction['ref'] = $info_member_transaction['ref'];
		$data_member_transaction['status_request'] = 'disetujui';
		// approver
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
			$data_member_transaction['approver'] = "Administrator";
		}else{
			$data_member_transaction['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_member_transaction['last_update'] = date('Y-m-d H:i:s');
		# save to database
		if( ! $this->model_trans_member_cud->approve_deposit_trans_member($data_deposit,$data_member_transaction,$id) ){
			return false;
		}else{
			return true;
		}
	}

	function _buyPaketProcess($id, $info_member_transaction) {

		// echo "Info Member Transaksi <br>";
		// print_r($info_member_transaction);
		// echo "<br>";


		$exp = explode(':', $info_member_transaction['ref']);
		$paket_transaksi_request_id = $exp[1];
		$get_data_paket_transaksi = $this->model_trans_member->get_info_trans_paket($paket_transaksi_request_id);
		// echo "Data Pket Transaksi <br>";
		// print_r($get_data_paket_transaksi);
		# data paket transaction
		$data_paket_transaction = array();
		$data_paket_transaction['company_id'] = $this->company_id;
		$data_paket_transaction['no_register'] = $get_data_paket_transaksi['no_register'];
		$data_paket_transaction['paket_id'] = $get_data_paket_transaksi['paket_id'];
		$data_paket_transaction['paket_type_id'] = $get_data_paket_transaksi['paket_type_id'];
		$data_paket_transaction['diskon'] = $get_data_paket_transaksi['diskon'];
		$data_paket_transaction['agen_id'] = $get_data_paket_transaksi['agen_id'];
		$data_paket_transaction['payment_methode'] = $get_data_paket_transaksi['payment_methode'];
		$data_paket_transaction['total_mahram_fee'] = $get_data_paket_transaksi['total_mahram_fee'];
		$data_paket_transaction['mahram'] = $get_data_paket_transaksi['mahram'];
		$data_paket_transaction['tenor'] = $get_data_paket_transaksi['tenor'];
		$data_paket_transaction['down_payment'] = $get_data_paket_transaksi['down_payment'];
		$data_paket_transaction['total_paket_price'] = $get_data_paket_transaksi['total_paket_price'];
		$data_paket_transaction['price_per_pax'] = $get_data_paket_transaksi['price_per_pax'];
		$data_paket_transaction['start_date'] = $get_data_paket_transaksi['start_date'];
		$data_paket_transaction['input_date'] = date('Y-m-d H:i:s');
		$data_paket_transaction['last_update'] = date('Y-m-d H:i:s');
		# fee agen
		$data_fee_agen = array();
		if( $get_data_paket_transaksi['agen_id'] != 0 ){
			$data_fee_agen = $this->model_trans_member->get_info_keagenan( $this->input->post('paket_id'), $this->input->post('agen') );
		}
		# data paket transaction history or installement
		$data_paket_transaction_history = array();
		if( $get_data_paket_transaksi['payment_methode'] == 0 ) {
			$data_paket_transaction_history['invoice'] = $this->text_ops->get_invoice_transaksi_paket();
			$data_paket_transaction_history['ket'] = 'cash';
			$data_paket_transaction_history['source_id'] = '';
		} else {
			$data_paket_transaction_history['invoice'] = $this->text_ops->get_invoice_transaksi_paket_cicilan();
			$data_paket_transaction_history['ket'] = 'dp';
		}
		$data_paket_transaction_history['company_id'] = $this->company_id;
		$data_paket_transaction_history['paid'] = $info_member_transaction['amount'];
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
			$data_paket_transaction_history['receiver'] = "Administrator";
		}else{
			$data_paket_transaction_history['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_paket_transaction_history['source'] = $info_member_transaction['payment_source'];
		$data_paket_transaction_history['deposit_name'] = $info_member_transaction['fullname'];
		$data_paket_transaction_history['deposit_phone'] = $info_member_transaction['no_hp'];
		$data_paket_transaction_history['deposit_address'] = $info_member_transaction['address'];
		$data_paket_transaction_history['input_date'] = date('Y-m-d H:i:s');
		$data_paket_transaction_history['last_update'] = date('Y-m-d H:i:s');
		# create installement scheme
		$data_installement_scheme = array();
		if( $get_data_paket_transaksi['payment_methode'] == 1 ){
			$pinjam = $get_data_paket_transaksi['total_paket_price'] - $get_data_paket_transaksi['down_payment'];
			$amount =  $pinjam / $get_data_paket_transaksi['tenor'];
         $amount = ceil( $amount );
         if (substr($amount,-3)>499){
            $amount=round($amount,-3);
         } else {
            $amount=round($amount,-3)+1000;
         }
         $dueDate = $get_data_paket_transaksi['start_date'];
         $totalPinjam = $get_data_paket_transaksi['total_paket_price'] - $get_data_paket_transaksi['down_payment'];
         $sisaTotalPinjam = $totalPinjam;
         for( $i = 1; $i <= $get_data_paket_transaksi['tenor']; $i++ ){
            if( $i != 1 ){
               $dueDate = date('Y-m-d', strtotime('+'.($i-1).' month', strtotime($get_data_paket_transaksi['start_date'])));
            }
				$data_installement_scheme[] = array('term' => $i,
																'amount' => ($sisaTotalPinjam < $amount ? $sisaTotalPinjam : $amount),
																'duedate' => $dueDate,
																'company_id' => $this->company_id);
            $sisaTotalPinjam = $sisaTotalPinjam - $amount;
         }
		}
		# paket transaksi jamaah
		$data_jamaah = array();
		foreach ($get_data_paket_transaksi['jamaah'] as $key => $value) {
			$data_jamaah[] = array('company_id' => $this->company_id,
									  	  'jamaah_id' => $value,
									  	  'leader' => $get_data_paket_transaksi['jamaah_leader'] == $value ? 1 : 0);
		}
		# data member transaction request
		$data_member_transaction = array();
		$data_member_transaction['ref'] = $info_member_transaction['ref'];
		$data_member_transaction['status_request'] = 'disetujui';
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
			$data_member_transaction['approver'] = "Administrator";
		}else{
			$data_member_transaction['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_member_transaction['last_update'] = date('Y-m-d H:i:s');
		# data deposit id use deposit
		$data_deposit = array();
		if( $info_member_transaction['payment_source'] == 'deposit' ){
			$data_deposit['nomor_transaction'] = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
			$data_deposit['personal_id'] = $info_member_transaction['personal_id'];
			$data_deposit['company_id'] = $this->company_id;
			$data_deposit['debet'] = 0;
			$data_deposit['kredit'] = $info_member_transaction['amount'];
			if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
				$data_deposit['approver']  = "Administrator";
			}else{
				$data_deposit['approver']  = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data_deposit['info'] = 'Pembelian paket dengan nomor register paket: '.$get_data_paket_transaksi['no_register'];
			$data_deposit['input_date'] = date('Y-m-d H:i:s');
			$data_deposit['last_update'] = date('Y-m-d H:i:s');
		}
		# save to database
		if( ! $this->model_trans_member_cud->approve_buy_paket_trans_member($id, $data_paket_transaction, $data_fee_agen,
																								 $data_paket_transaction_history, $data_installement_scheme,
																								 $data_jamaah, $data_member_transaction, $data_deposit) ){
			return false;
		}else{
			return true;
		}
	}


	function _paymentPaketProocess($id, $info_member_transaction){

		# data paket transaction history or installement
		$data_paket_transaction_history = array();
		if( $info_member_transaction['payment_methode'] == 0 ) {
			$data_paket_transaction_history['invoice'] = $this->text_ops->get_invoice_transaksi_paket();
			$data_paket_transaction_history['ket'] = 'cash';
			$data_paket_transaction_history['source_id'] = '';
		} else {
			$data_paket_transaction_history['invoice'] = $this->text_ops->get_invoice_transaksi_paket_cicilan();
			$data_paket_transaction_history['ket'] = 'cicilan';
		}
		$data_paket_transaction_history['company_id'] = $this->company_id;
		$data_paket_transaction_history['paid'] = $info_member_transaction['amount'];
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ) {
			$data_paket_transaction_history['receiver'] = "Administrator";
		}else{
			$data_paket_transaction_history['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_paket_transaction_history['paket_transaction_id'] = $info_member_transaction['paket_transaction_id'];
		$data_paket_transaction_history['source'] = $info_member_transaction['payment_source'] == 'transfer' ? 'tunai' : $info_member_transaction['payment_source'];
		$data_paket_transaction_history['deposit_name'] = $info_member_transaction['fullname'];
		$data_paket_transaction_history['deposit_phone'] = $info_member_transaction['no_hp'];
		$data_paket_transaction_history['deposit_address'] = $info_member_transaction['address'];
		$data_paket_transaction_history['input_date'] = date('Y-m-d H:i:s');
		$data_paket_transaction_history['last_update'] = date('Y-m-d H:i:s');
		# data member transaction request
		$data_member_transaction = array();
		$data_member_transaction['ref'] = $info_member_transaction['ref'];
		$data_member_transaction['status_request'] = 'disetujui';
		if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
			$data_member_transaction['approver'] = "Administrator";
		}else{
			$data_member_transaction['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
		}
		$data_member_transaction['last_update'] = date('Y-m-d H:i:s');
		# data deposit id use deposit
		$data_deposit = array();
		if( $info_member_transaction['payment_source'] == 'deposit' ){
			$data_deposit['nomor_transaction'] = $this->random_code_ops->generated_nomor_transaksi_deposit_saldo();
			$data_deposit['personal_id'] = $info_member_transaction['personal_id'];
			$data_deposit['company_id'] = $this->company_id;
			$data_deposit['debet'] = 0;
			$data_deposit['kredit'] = $info_member_transaction['amount'];
			if( $this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator' ){
				$data_deposit['approver']  = "Administrator";
			}else{
				$data_deposit['approver']  = $this->session->userdata($this->config->item('apps_name'))['fullname'];
			}
			$data_deposit['info'] = 'Pembayaran paket dengan nomor invoice paket: '.$data_paket_transaction_history['invoice'];
			$data_deposit['input_date'] = date('Y-m-d H:i:s');
			$data_deposit['last_update'] = date('Y-m-d H:i:s');
		}
		# save to database
		if( ! $this->model_trans_member_cud->approve_payment_paket_trans_member($id, $info_member_transaction, $data_paket_transaction_history, $data_member_transaction, $data_deposit) ){
			return false;
		}else{
			return true;
		}
	}

   function approve(){
      $return 	= array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id',	'<b>Member Transaksi ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_trans_member_id_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # get info member transaction
         $info_member_transaction = $this->model_trans_member->get_info_member_transaction( $this->input->post('id') );
         if( $info_member_transaction['activity_type'] == 'claim' ) {
				if( ! $this->_claimProcess($this->input->post('id'), $info_member_transaction) ){
					$error = 1;
					$error_msg = 'Proses approve data klaim transaksi paket gagal dilakukan.';
				}
      	} elseif ( $info_member_transaction['activity_type'] == 'deposit' ) {
				if( $info_member_transaction['payment_source'] == 'transfer' AND $info_member_transaction['transfer_evidence'] != '' ){
					if( ! $this->_depositProcess($this->input->post('id'), $info_member_transaction) ) {
						$error = 1;
						$error_msg = 'Proses approve request deposit member gagal dilakukan.';
					}
				}else{
					$error = 1;
					$error_msg = 'Proses tidak dapat dilanjutkan, karena bukti pembayaran belum diupload.';
				}
      	} elseif ( $info_member_transaction['activity_type'] == 'buying_paket' ) {
				if( $info_member_transaction['payment_source'] == 'transfer' AND $info_member_transaction['transfer_evidence'] == '' ){
					$error = 1;
					$error_msg = 'Proses tidak dapat dilanjutkan, karena bukti pembayaran paket belum diupload';
				}else{
					if( ! $this->_buyPaketProcess($this->input->post('id'), $info_member_transaction) ) {
						$error = 1;
						$error_msg = 'Proses approve pembelian paket oleh member gagal dilakukan.';
					}
				}
			} elseif ( $info_member_transaction['activity_type'] == 'payment_paket' ) {
				if( $info_member_transaction['payment_source'] == 'transfer' AND $info_member_transaction['transfer_evidence'] == '' ){
					$error = 1;
					$error_msg = 'Proses tidak dapat dilanjutkan, karena bukti pembayaran paket belum diupload';
				}else{
					if( ! $this->_paymentPaketProocess($this->input->post('id'), $info_member_transaction) ) {
						$error = 1;
						$error_msg = 'Proses approve pembayaran paket oleh member gagal dilakukan.';
					}
				}
			}
			# filter
         if ( $error == 0 ) {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Proses persetujuan permintaan transaksi berhasil dilakukan.',
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

	# decline
	function decline(){
		$return 	= array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id',	'<b>Member Transaksi ID<b>', 	'trim|required|xss_clean|min_length[1]|callback__ck_trans_member_id_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
			# get info member transaction
			$member_transaction = $this->model_trans_member->get_info_member( $this->input->post('id') );
			# decline process
			if( ! $this->model_trans_member_cud->decline_trans_member( $this->input->post('id') ) ) {
				$error = 1;
				$error_msg = 'Proses decline member gagal dilakukan.';
			} else {
				if( $member_transaction['payment_source'] == 'transfer' AND $member_transaction['bukti'] != '') {
					$src = FCPATH . 'image/bukti/' . $member_transaction['bukti'];
					if (file_exists($src)) {
						unlink($src);
					}
				}
			}
			# filter
         if ( $error == 0 ) {
            $return = array(
               'error'	=> false,
               'error_msg' => 'Proses decline member transaction request berhasil dilakukan.',
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
