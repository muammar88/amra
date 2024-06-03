<?php

/**
 *  -----------------------
 *	Akun Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Akun extends CI_Controller
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
		# model daftar akun
		$this->load->model('Model_akun', 'model_daftar_akun');
		# model daftar akun cud
		$this->load->model('Model_akun_cud', 'model_daftar_akun_cud');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	# daftar akun
	function daftar_akun()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('filter', '<b>Filter<b>', 'trim|required|xss_clean|min_length[1]|in_list[0,10000,20000,30000,40000,50000,60000,80000,90000]');
		$this->form_validation->set_rules('perpage',	'<b>Perpage<b>', 'trim|required|xss_clean|min_length[1]|numeric');
		$this->form_validation->set_rules('pageNumber',	'<b>pageNumber<b>', 	'trim|xss_clean|min_length[1]|numeric');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			$filter 	= $this->input->post('filter');
			$perpage = $this->input->post('perpage');
			$start_at = 0;
			if ($this->input->post('pageNumber')) {
				$start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
			}
			$total 	= $this->model_daftar_akun->get_total_daftar_akun($filter);
			$list 	= $this->model_daftar_akun->get_index_daftar_akun($perpage, $start_at, $filter);
			if ($total == 0) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Daftar akun tidak ditemukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Daftar akun berhasil ditemukan.',
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

	function _ck_header_akun($new_akun)
	{
		$list = array('10000', '20000', '30000', '40000', '50000', '60000', '80000', '90000');
		if (!in_array($new_akun, $list)) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_header_akun', 'Nomor akun sudah digunakan.');
			return FALSE;
		}
	}

	function check_akun_exist()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('akun_id', '<b>Akun ID<b>', 'trim|xss_clean|numeric|max_length[5]|callback__ck_akun_id');
		$this->form_validation->set_rules('new_akun', '<b>Akun<b>', 'trim|required|xss_clean|numeric|max_length[5]|callback__ck_header_akun');
		/*
         Validation process
      */
		if ($this->form_validation->run()) {
			# akun id
			if ($this->input->post('akun_id')) {
				# filter
				if ($this->model_daftar_akun->check_akun_exist($this->input->post('new_akun'), $this->input->post('akun_id'))) {
					$error = 1;
				}
			} else {
				# filter
				if ($this->model_daftar_akun->check_akun_exist($this->input->post('new_akun'))) {
					$error = 1;
				}
			}
			# filter
			if ($error == 1) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Nomor Akun tidak tersedia',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Akun masih tersedia.',
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

	function _ck_akun_id()
	{
		if ($this->input->post('akun_id')) {
			if (!$this->model_daftar_akun->check_akun_id_exist($this->input->post('akun_id'))) {
				$this->form_validation->set_message('_ck_akun_id', 'ID Akun tidak ditemukan.');
				return FALSE;
			} else {
				return TRUE;
			}
		} else {
			return TRUE;
		}
	}

	# check new akun
	function _ck_new_akun($new_akun)
	{
		$head_akun = $this->input->post('head_akun');
		$error = 0;
		if ($this->input->post('akun_id')) {
			if ($this->model_daftar_akun->check_new_akun($head_akun . $new_akun, $this->input->post('akun_id'))) {
				$error = 1;
			}
		} else {
			if ($this->model_daftar_akun->check_new_akun($head_akun . $new_akun)) {
				$error = 1;
			}
		}
		# filter error
		if ($error == 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_new_akun', 'Nomor akun sudah terdaftar di pangkalan data.');
			return FALSE;
		}
	}

	# add update akun
	function add_update_akun()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('akun_id', '<b>Akun ID<b>', 'trim|xss_clean|numeric|callback__ck_akun_id');
		$this->form_validation->set_rules('head_akun', '<b>Head Akun<b>', 'trim|required|xss_clean|numeric|max_length[1]|in_list[1,2,3,4,5,6]');
		$this->form_validation->set_rules('new_akun', '<b>Akun Baru<b>', 'trim|required|xss_clean|numeric|max_length[5]|callback__ck_new_akun');
		$this->form_validation->set_rules('nama_akun', '<b>Nama Akun<b>', 'trim|required|xss_clean');
		$this->form_validation->set_rules('saldo', '<b>Saldo<b>', 'trim|required|xss_clean');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get post akun
			$periode_id = $this->model_daftar_akun->last_periode();
			# data secondary
			$data_akun_secondary = array();
			$data_akun_secondary['company_id'] = $this->company_id;
			$data_akun_secondary['akun_primary_id'] = $this->input->post('head_akun');
			$data_akun_secondary['nomor_akun_secondary'] = $this->input->post('head_akun') . $this->input->post('new_akun');
			$data_akun_secondary['nama_akun_secondary'] = strtoupper($this->input->post('nama_akun'));
			$data_akun_secondary['tipe_akun'] = 'tambahan';
			$data_akun_secondary['path'] = '';
			# data saldo
			$data_saldo = array();
			$data_saldo['company_id'] = $this->company_id;
			$data_saldo['saldo'] = $this->text_ops->hide_currency($this->input->post('saldo'));
			$data_saldo['periode'] = $periode_id;
			$data_saldo['input_date'] = date('Y-m-d');
			$data_saldo['last_update'] = date('Y-m-d');

			echo "==============";
			print_r($data_saldo);
			echo "==============";
			# filter
			if ($this->input->post('akun_id')) {
				# get akun id
				$data_saldo['akun_secondary_id'] = $this->input->post('akun_id');
				# update process
				if (!$this->model_daftar_akun_cud->update_akun($this->input->post('akun_id'), $data_akun_secondary, $data_saldo)) {
					$error = 1;
					$error_msg = 'Proses update data akun gagal dilakukan.';
				}
			} else {
				# insert process
				if (!$this->model_daftar_akun_cud->insert_akun($data_akun_secondary, $data_saldo)) {
					$error = 1;
					$error_msg = 'Proses insert data akun gagal dilakukan.';
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

	# delete akun accepted
	function _ck_delete_akun_accepted($id)
	{
		if (!$this->model_daftar_akun->check_accepted_akun($id)) {
			$this->form_validation->set_message('_ck_delete_akun_accepted', 'Nomor akun sudah tidak dapat dihapus.');
			return FALSE;
		} else {
			return TRUE;
		}
	}

	# delete akun
	function delete_akun()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Akun ID<b>', 'trim|required|xss_clean|numeric|callback__ck_delete_akun_accepted');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get nomor akun
			$nomor_akun = $this->model_daftar_akun->get_nomor_akun($this->input->post('id'));
			# delete process
			if (!$this->model_daftar_akun_cud->delete_akun($this->input->post('id'), $nomor_akun)) {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses delete akun gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses delete akun berhasil dilakukan.',
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

	# get info edit akun
	function info_edit_akun()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Akun ID<b>', 'trim|required|xss_clean|numeric|callback__ck_akun_id');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get info edit akun
			$info = $this->model_daftar_akun->get_info_edit_akun($this->input->post('id'));
			# delete process
			if (count($info) > 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info akun ditemukan.',
					'data' => $info['nomor_akun_primary'],
					'value' => $info['value'],
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data info akun tidak ditemukan.',
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

	# get info saldo akun
	function get_info_saldo_akun()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Akun ID<b>', 'trim|required|xss_clean|numeric|callback__ck_akun_id');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get info edit akun
			$info = $this->model_daftar_akun->get_info_saldo_akun($this->input->post('id'));
			# delete process
			if ($error == 0) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Data info saldo akun ditemukan.',
					'saldo' => $info['saldo'],
					'nama_akun' => $info['nama_akun_secondary'],
					'nomor_akun' => $info['nomor_akun_secondary'],
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Data info saldo akun tidak ditemukan.',
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

	function _ck_currency_not_null($biaya)
	{
		if ($this->text_ops->hide_currency($biaya) > 0) {
			return TRUE;
		} else {
			$this->form_validation->set_message('_ck_currency_not_null', 'Saldo tidak boleh kosong.');
			return FALSE;
		}
	}

	# update saldo
	function update_saldo()
	{
		$return 	= array();
		$error 		= 0;
		$error_msg = '';
		$this->form_validation->set_rules('id', '<b>Akun ID<b>', 'trim|required|xss_clean|numeric|callback__ck_akun_id');
		$this->form_validation->set_rules('saldo', '<b>Saldo Awal Akun<b>', 'trim|required|xss_clean');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# get info edit akun
			$data = array();
			$data['company_id'] = $this->company_id;
			$data['akun_secondary_id'] = $this->input->post('id');
			$data['saldo'] = $this->text_ops->hide_currency($this->input->post('saldo'));
			$data['periode'] = $this->model_daftar_akun->last_periode();
			$data['input_date'] = date('Y-m-d');
			$data['last_update'] = date('Y-m-d');
			# delete process
			if ($this->model_daftar_akun_cud->update_saldo_akun($this->input->post('id'), $data)) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses penambahan saldo berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses penambahan saldoo gagal dilakukan.',
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


	function _checking_saldo($id, $nomor_akun, $sn){
		// get saldo awal
		$this->db->select('saldo')
				 ->from('saldo')
				 ->where('akun_secondary_id', $id)
				 ->where('company_id', $this->company_id)
				 ->where('periode', 0);
		$q = $this->db->get();
		$saldo_awal = 0;
		if($q->num_rows() > 0){
			foreach ($q->result() as $row) {
				$saldo_awal = $saldo_awal + $row->saldo;
			}
		}
		// get debet kredit
		$debet = 0;
		$kredit = 0;
		$this->db->select('saldo, akun_debet, akun_kredit')
				 ->from('jurnal')
				 ->where('company_id', $this->company_id)
				 ->where('periode_id', 0)
				 ->where('akun_debet ='.$nomor_akun.' OR akun_kredit='.$nomor_akun);
		$r = $this->db->get();
		if($r->num_rows() > 0){
			foreach ($r->result() as $rows) {
				if($rows->akun_debet == $nomor_akun){
					$debet = $debet + $rows->saldo;
				}
				if($rows->akun_kredit == $nomor_akun){
					$kredit = $kredit+ $rows->saldo;
				}
			}
		}

		// if( substr($nomor_akun,0,1) == '4') {
		// 	echo "__________________<br>";
		// 	echo "Nomor Akun  : " . $nomor_akun . "<br>";
		// 	echo "Debet : " . $debet . "<br>";
		// 	echo " Kredit : " . $kredit . "<br>";
		// }
		

		if($sn == 'D')
		{
			$saldo_awal = $saldo_awal + $debet; 
			$saldo_sekarang	= $saldo_awal - $kredit; 
		}elseif ($sn == 'K') 
		{
			$saldo_awal = $saldo_awal + $kredit; 
			$saldo_sekarang	= $saldo_awal - $debet; 
		}else{
			$saldo_sekarang	= 0; 
		}
		
		return $saldo_sekarang;
	}


	function _hit_labarugi(){

		$this->db->select('pr.nomor_akun, pr.sn, pr.pos, sc.id, sc.nomor_akun_secondary')
				 ->from('akun_secondary AS sc')
				 ->join('akun_primary AS pr', 'sc.akun_primary_id=pr.id', 'inner')
				 ->where('sc.company_id', $this->company_id)
				 ->where('pr.nomor_akun IN (40000, 50000, 60000)')
				 ->order_by('sc.nomor_akun_secondary', 'desc');
		$q = $this->db->get();
		$total_4 = 0;
		$total_5 = 0;
		$total_6 = 0;
		$i = 0;
		if( $q->num_rows() > 0 ) {
			foreach ( $q->result() as $rows ) {
				# get saldo
				$saldo 	= $this->_checking_saldo( $rows->id, $rows->nomor_akun_secondary, $rows->sn );
				# filter
				if( $rows->nomor_akun == '40000' )
				{
					// echo "Saldo 40000 : " . $saldo . "<br>";
					// echo "__________________<br>";
					$total_4 = $total_4 + $saldo;
				}
				if( $rows->nomor_akun == '50000' ) 
				{
					$total_5 = $total_5 + $saldo;
				}
				if( $rows->nomor_akun == '60000' )
				{
					$total_6 = $total_6 + $saldo;
				}
				$i++;
			}
		}	

		// echo "Total 40000 : " . $total_4 . "<br>";
		// echo "Total 50000 : " . $total_5 . "<br>";
		// echo "Total 60000 : " . $total_6 . "<br>";

		$laba_kotor = $total_4 - $total_5;
		$laba_bersih = $laba_kotor - $total_6;

		return $laba_bersih;
	}

	function _hit_labaditahan(){

		$this->db->select('s.saldo, a.nomor_akun_secondary')
				 ->from('saldo AS s')
				 ->join('akun_secondary AS a', 's.akun_secondary_id=a.id', 'inner')
				 ->where('s.company_id', $this->company_id)
				 ->where('s.periode', '0')
				 ->where('a.nomor_akun_secondary ="32000" OR a.nomor_akun_secondary="33000"');
		$q = $this->db->get();
		$saldo_awal_laba_ditahan = 0; 	// saldo awal
		$saldo_awal_laba_rugi = 0; 		// saldo laba rugi
		if( $q->num_rows() > 0 ){
			foreach ($q->result() as $row) {
				if($row->nomor_akun_secondary == '32000'){
					$saldo_awal_laba_ditahan = $saldo_awal_laba_ditahan + $row->saldo;		
				}
				if($row->nomor_akun_secondary == '33000'){
					$saldo_awal_laba_rugi = $saldo_awal_laba_rugi + $row->saldo;
				}
			}
		}

		// penarikan laba 
		$this->db->select('saldo, akun_debet, akun_kredit')
				 ->from('jurnal')
				 ->where('company_id', $this->company_id)
				 ->where('periode_id', '0')
				 ->where('akun_debet ="32000" OR akun_kredit="32000"');
		$q = $this->db->get();
		$saldo_akun_debet = 0;
		$saldo_akun_kredit = 0;
		if( $q->num_rows() > 0 ){
			foreach ( $q->result() as $row ) {
				if($row->akun_debet == '32000'){
					$saldo_akun_debet = $row->saldo;
				}
				if($row->akun_kredit == '32000'){
					$saldo_akun_kredit = $row->saldo;
				}
			}
		}

		$saldo_perubahan = $saldo_akun_kredit - $saldo_akun_debet;
		return  $saldo_awal_laba_ditahan + $saldo_awal_laba_rugi - $saldo_perubahan;
	}

	function _count_akun($akun_secondary_id, $akun_secondary, $sn, $pos){
		// saldo awal
		$saldo_awal = 0;
		$this->db->select('saldo')
				 ->from('saldo')
				 ->where('company_id', $this->company_id)
				 ->where('akun_secondary_id', $akun_secondary_id)
				 ->where('periode', 0);
		$q = $this->db->get();
		if($q->num_rows() > 0 ){
			foreach ($q->result() as $rows) {
				$saldo_awal = $saldo_awal + $rows->saldo;
			}
		}
		// if( $akun_secondary == '11010'){
		// 	echo "saldo awal KAS<br>";
		// 	echo $saldo_awal;
		// 	echo "<br>";
		// 	echo "saldo awal KAS<br>";
		// }
		$total = $saldo_awal;
		// debet kredit
		$this->db->select('akun_debet, akun_kredit, saldo, periode_id')
				 ->from('jurnal')
				 ->where('company_id', $this->company_id)
				 ->where('periode_id', 0);
		// $debet = 0;
		// $kredit = 0;
		$q = $this->db->get();
		if( $q->num_rows() > 0 ) {
			foreach ( $q->result() as $row ) {
				if( $sn == 'D') {

					if( $row->akun_debet == $akun_secondary ) {
						$total = $total + $row->saldo;
					}

	 				if( $row->akun_kredit == $akun_secondary ) {
						$total = $total - $row->saldo;
					}

				}elseif( $sn == 'K') {


					if( $row->akun_debet == $akun_secondary ) {
						$total = $total - $row->saldo;
					}

	 				if( $row->akun_kredit == $akun_secondary ) {
						$total = $total + $row->saldo;
					}
				}
			}
		}


		// $total = $kredit + $debet; 
		// if( $row->akun_debet == $akun_secondary ) {
		// 	// if( $row->akun_debet == '11010') {
		// 	// 	// echo "saldo awal KAS<br>";
		// 	// 	// echo $saldo_awal;
		// 	// 	// echo "<br>";
		// 	// 	// echo $debet;
		// 	// 	// echo "<br>";
		// 	// 	// echo "saldo awal KAS<br>";
		// 	// }
		// 	$debet 	= $debet + $row->saldo;
		// }
		// if( $row->akun_kredit == $akun_secondary ) {
		// 	$kredit = $kredit + $row->saldo;
		// }

		 // if ($rows->sn == 'D') {
	     //      # akun debet
	     //      if (isset($akun_debet[$rows->nomor_akun_secondary])) {
	     //         $saldo = $saldo + $akun_debet[$rows->nomor_akun_secondary];
	     //      }
	     //      # akun kredit
	     //      if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
	     //         $saldo = $saldo - $akun_kredit[$rows->nomor_akun_secondary];
	     //      }
	     //   } elseif ($rows->sn == 'K') {
	     //      # akun debet
	     //      if (isset($akun_debet[$rows->nomor_akun_secondary])) {
	     //         $saldo = $saldo - $akun_debet[$rows->nomor_akun_secondary];
	     //      }
	     //      # akun kredit
	     //      if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
	     //         $saldo = $saldo + $akun_kredit[$rows->nomor_akun_secondary];
	     //      }
	     //   }


		// if( $akun_secondary == '11010'){
		// 	echo "Debet <br>";
		// 	print_r($debet);
		// 	echo "Debet <br>";
		// 	echo "Kredit <br>";
		// 	print_r($kredit);
		// 	echo "Kredit <br>";
		// 	// echo $saldo_awal;
		// 	// echo "<br>";
		// 	// echo "saldo awal KAS<br>";
		// }

		// $total = 0;
		// if( $sn == 'D') {
		// 	$total = $debet + $kredit; 
		// }else if( $sn == 'K') {
		// 	$total = $kredit + $debet; 
		// }
		// $total = $saldo_awal + $total;

		return $total;
	}

	function get_saldo_awal_31000() {
		# get saldo awal
		$saldo_awal = $this->akuntansi->saldo_awal(0, $this->company_id);
		# jurnal 
		$data_jurnal = $this->akuntansi->get_jurnal_by_periode(0, $this->company_id);
		$akun_debet = $data_jurnal['akun_debet'];
		$akun_kredit = $data_jurnal['akun_kredit'];
		// list
		$list = $this->akuntansi->total_saldo(0, $akun_debet, $akun_kredit, $this->company_id, $saldo_awal );
		// LABA/RUGI TAHUN BERJALAN
		$modal = $list[3]['EKUITAS/MODAL']['saldo'];
		$laba_rugi = $list[3]['LABA/RUGI TAHUN BERJALAN']['saldo'];

		$total = $modal + $laba_rugi;

		return $total;
	}

	# close book
	function close_book()
	{
		$return = array();
		$error = 0;
		$error_msg = '';
		$this->form_validation->set_rules('periode_name', '<b>Nama Periode Selanjutnya<b>', 'trim|required|xss_clean');
		/*
		 Validation process
		*/
		if ($this->form_validation->run()) {
			# select
			$this->db->select('pr.nomor_akun, pr.sn, pr.pos, sc.id, sc.nomor_akun_secondary')
					 ->from('akun_secondary AS sc')
					 ->join('akun_primary AS pr', 'sc.akun_primary_id=pr.id', 'inner')
					 ->where('sc.company_id', $this->company_id)
					 ->order_by('sc.nomor_akun_secondary', 'desc');
			$q = $this->db->get();
			$data = array();
			$i = 0;
			if( $q->num_rows() > 0 ) {
				foreach ( $q->result() as $rows ) {
					// total
					$total = $this->_count_akun($rows->id, $rows->nomor_akun_secondary, $rows->sn, $rows->pos);

					// echo "<br>";
					// echo $rows->nomor_akun;
					// echo "<br>";
					// echo $rows->nomor_akun_secondary;
					// echo "<br>";
					// echo $total;
					// echo "<br>";

					// filter
					if ($rows->nomor_akun == 10000 OR $rows->nomor_akun == 20000) {
						$data[$i]['company_id'] = $this->company_id;
						$data[$i]['akun_secondary_id'] = $rows->id;
						$data[$i]['saldo'] = $total;
					} elseif ($rows->nomor_akun == 30000) {
						if( $rows->nomor_akun_secondary == 31000 ) 
						{
							$data[$i]['company_id'] = $this->company_id;
							$data[$i]['akun_secondary_id'] = $rows->id;
							$data[$i]['saldo'] = $this->get_saldo_awal_31000();
							// $data[$i]['saldo'] = $total;
						
						//  $total;
						}elseif ( $rows->nomor_akun_secondary == 32000 ) 
						{
							$data[$i]['company_id'] = $this->company_id;
							$data[$i]['akun_secondary_id'] = $rows->id;
							$data[$i]['saldo'] = $this->_hit_labaditahan();
						}elseif ( $rows->nomor_akun_secondary == 33000 ) 
						{
							$data[$i]['company_id'] = $this->company_id;
							$data[$i]['akun_secondary_id'] = $rows->id;
							$data[$i]['saldo'] = 0;
							//$this->_hit_labarugi();
						}
					} elseif ( $rows->nomor_akun >= 40000 ) {
						$data[$i]['company_id'] = $this->company_id;
						$data[$i]['akun_secondary_id'] = $rows->id;
						$data[$i]['saldo'] = 0;
					}
					$data[$i]['periode'] = 0;
					$data[$i]['input_date'] = date('Y-m-d');
					$data[$i]['last_update'] = date('Y-m-d');
					$i++;
				}
			}

			// echo("----------------------------");
			// echo '<pre>'; print_r($data); echo '</pre>';
			// echo("----------------------------");
			// close book process
			if ( $this->model_daftar_akun_cud->close_book( $data, $this->input->post('periode_name') ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses tutup buku berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses tutup buku gagal dilakukan.',
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

	// reopen book
	function reopen_book(){
		// get last jurnal
		$this->db->select('id, nama_periode')
				 ->from('jurnal_periode')
				 ->where('company_id', $this->company_id)
				 ->order_by('id', 'desc')
				 ->limit(1);
		$q = $this->db->get();
		$id = 0;
		if( $q->num_rows() > 0 ) {
			foreach ( $q->result() as $rows ) {
				$id = $rows->id;
			}
		}
		if( $id == 0 ) {
			$return = array(
				'error'	=> true,
				'error_msg' => 'Periode Sebelumnya tidak ditemukan.',
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
			);
		}else{
			// get model daftar akun cud
			if ( $this->model_daftar_akun_cud->reopen_book( $id ) ) {
				$return = array(
					'error'	=> false,
					'error_msg' => 'Proses buka buku berhasil dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			} else {
				$return = array(
					'error'	=> true,
					'error_msg' => 'Proses buka buku gagal dilakukan.',
					$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
				);
			}
		}
		
		echo json_encode($return);
	}
}


// get id periode terakhir 
// $this->db->select('pr.nomor_akun, pr.sn, pr.pos, sc.id, sc.nomor_akun_secondary')
// 		 ->from('akun_secondary AS sc')
// 		 ->join('akun_primary AS pr', 'sc.akun_primary_id=pr.id', 'inner')
// 		 ->where('sc.company_id', $this->company_id)
// 		 ->order_by('sc.nomor_akun_secondary', 'desc');
// hapus periode id yang 0
// ubah periode terakhir yang selain nol menjadi nol.
// $return = array();
// $error = 0;
// $error_msg = '';
// $this->form_validation->set_rules('periode_name', '<b>Nama Periode Selanjutnya<b>', 'trim|required|xss_clean');
// /*
//  Validation process
// */
// if ($this->form_validation->run()) {
// 	// # select
// 	// $this->db->select('pr.nomor_akun, pr.sn, pr.pos, sc.id, sc.nomor_akun_secondary')
// 	// 		 ->from('akun_secondary AS sc')
// 	// 		 ->join('akun_primary AS pr', 'sc.akun_primary_id=pr.id', 'inner')
// 	// 		 ->where('sc.company_id', $this->company_id)
// 	// 		 ->order_by('sc.nomor_akun_secondary', 'desc');
// 	// $q = $this->db->get();
// 	// $data = array();
// 	// $i = 0;
// 	// if( $q->num_rows() > 0 ) {
// 	// 	foreach ( $q->result() as $rows ) {
// 	// 		// total
// 	// 		$total = $this->_count_akun($rows->id, $rows->nomor_akun_secondary);
// 	// 		// filter
// 	// 		if ($rows->nomor_akun == 10000 OR $rows->nomor_akun == 20000) {
// 	// 			$data[$i]['company_id'] = $this->company_id;
// 	// 			$data[$i]['akun_secondary_id'] = $rows->id;
// 	// 			$data[$i]['saldo'] = $total;
// 	// 		} elseif ($rows->nomor_akun == 30000) {
// 	// 			if( $rows->nomor_akun_secondary == 31000 ) 
// 	// 			{
// 	// 				$data[$i]['company_id'] = $this->company_id;
// 	// 				$data[$i]['akun_secondary_id'] = $rows->id;
// 	// 				$data[$i]['saldo'] = $this->get_saldo_awal_31000();
				
// 	// 			//  $total;
// 	// 			}elseif ( $rows->nomor_akun_secondary == 32000 ) 
// 	// 			{
// 	// 				$data[$i]['company_id'] = $this->company_id;
// 	// 				$data[$i]['akun_secondary_id'] = $rows->id;
// 	// 				$data[$i]['saldo'] = $this->_hit_labaditahan();
// 	// 			}elseif ( $rows->nomor_akun_secondary == 33000 ) 
// 	// 			{
// 	// 				$data[$i]['company_id'] = $this->company_id;
// 	// 				$data[$i]['akun_secondary_id'] = $rows->id;
// 	// 				$data[$i]['saldo'] = 0;
// 	// 				// $this->_hit_labarugi();
// 	// 			}
// 	// 		} elseif ( $rows->nomor_akun >= 40000 ) {
// 	// 			$data[$i]['company_id'] = $this->company_id;
// 	// 			$data[$i]['akun_secondary_id'] = $rows->id;
// 	// 			$data[$i]['saldo'] = 0;
// 	// 		}
// 	// 		$data[$i]['periode'] = 0;
// 	// 		$data[$i]['input_date'] = date('Y-m-d');
// 	// 		$data[$i]['last_update'] = date('Y-m-d');
// 	// 		$i++;
// 	// 	}
// 	// }

// 	// echo("----------------------------");
// 	// // print_r($data);
// 	//  echo '<pre>'; print_r($data); echo '</pre>';
// 	// echo("----------------------------");
// 	// close book process
// 	//if ( $this->model_daftar_akun_cud->close_book( $data, $this->input->post('periode_name') ) ) {
// 		// $return = array(
// 		// 	'error'	=> false,
// 		// 	'error_msg' => 'Proses tutup buku berhasil dilakukan.',
// 		// 	$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
// 		// );
// 	// } else {
// 	// 	$return = array(
// 	// 		'error'	=> true,
// 	// 		'error_msg' => 'Proses tutup buku gagal dilakukan.',
// 	// 		$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
// 	// 	);
// 	// }
// } else {
// 	if (validation_errors()) {
// 		// define return error
// 		$return = array(
// 			'error'         => true,
// 			'error_msg'    => validation_errors(),
// 			$this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
// 		);
// 	}
// }

// echo json_encode($return);