<?php

/**
 *  -----------------------
 *	Random code library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');


class Random_code_ops
{

	private $data = array();
	private $CI;
	private $company_id;

	function __construct()
	{
		$this->CI = &get_instance();
		// $this->company_id = $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'];
	}

	function generated_kode_biaya(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_numeric(3);
			$q = $this->CI->db->select('id')
							  ->from('request_tambah_saldo_company')
							  ->where('kode_biaya', $rand)
							  ->where('status', 'proses')
							  ->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
							  ->get();
			if ( $q->num_rows() == 0 ) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_kode_tambah_saldo(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(8);
			$q = $this->CI->db->select('id')
				->from('request_tambah_saldo_company')
				->where('kode', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_biaya_kode(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(10);
			$q = $this->CI->db->select('id')
				->from('request_tambah_saldo_company')
				->where('kode', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}


	function generated_no_invoice_pembayaran_peminjaman(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(10);
			$q = $this->CI->db->select('id')
				->from('pembayaran_peminjaman')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_no_register_info_peminjaman(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(10);
			$q = $this->CI->db->select('id')
				->from('peminjaman')
				->where('register_number', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function random_invoice_deposit_transaction(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('deposit_transaction')
				->where('nomor_transaction', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function random_numeric($size) {
		$key = '';
		$keys = range(1, 9);
		for ($i = 0; $i < ($size - 2); $i++) {
			$key .= $keys[array_rand($keys)];
		}
		return $key;
	}

	function random_alpha_numeric($size)
	{
		$alpha_key = '';
		$keys = range('A', 'Z');
		for ($i = 0; $i < 2; $i++) {
			$alpha_key .= $keys[array_rand($keys)];
		}
		$key = '';
		$keys = range(0, 9);
		for ($i = 0; $i < ($size - 2); $i++) {
			$key .= $keys[array_rand($keys)];
		}
		return $alpha_key . $key;
	}

	function rand_bank_code()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('mst_bank')
				->where('kode_bank', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}


	function rand_invoice_visa()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('visa_transaction')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function rand_invoice_hotel()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('hotel_transaction')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function rand_invoice_passport()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('passport_transaction')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function rand_invoice_transport()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('transport_transaction')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function rand_recapitulation()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('recapitulation')
				->where('recapitulation_number', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function rand_tiket_history()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(10);
			$q = $this->CI->db->select('id')
				->from('tiket_transaction_history')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function rand_payment_invoice()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(10);
			$q = $this->CI->db->select('id')
				->from('fee_keagenan_payment')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_nomor_transaksi_deposit_saldo_api($company_id){
		$feedBack = false;
		$rand = '';
		do {
			$rand = $this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('deposit_transaction')
				->where('nomor_transaction', $rand)
				->where('company_id', $company_id)
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_nomor_transaksi_deposit_saldo()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('deposit_transaction')
				->where('nomor_transaction', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_nomor_register_trans_paket_la()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('paket_la_transaction')
				->where('register_number', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_invoice_history_paket_la()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('paket_la_transaction_history')
				->where('invoice', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function gen_kode_paket()
	{
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(5);
			$q = $this->CI->db->select('id')
				->from('paket')
				->where('kode', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function gen_nomor_transaction(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('deposit_transaction')
				->where('nomor_transaction', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}

	function generated_nomor_transaksi_deposit_paket(){
		$feedBack = false;
		$rand = '';
		do {
			$rand = 	$this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('deposit_transaction')
				->where('nomor_transaction', $rand)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $rand;
	}


	function number_transaction_detail_fee_keagenan()
	{
		$feedBack = false;
		$number_transaction = '';
		do {
			$number_transaction = $this->random_alpha_numeric(6);
			$q = $this->CI->db->select('id')
				->from('detail_fee_keagenan')
				->where('transaction_number', $number_transaction)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $number_transaction;
	}


	function invoice_pembayaran_fee_agen(){
		$feedBack = false;
		$number_transaction = '';
		do {
			$number_transaction = $this->random_alpha_numeric(8);
			$q = $this->CI->db->select('id')
				->from('fee_keagenan_payment')
				->where('invoice', $number_transaction)
				->where('company_id', $this->CI->session->userdata($this->CI->config->item('apps_name'))['company_id'])
				->get();
			if ($q->num_rows() == 0) {
				$feedBack = true;
			}
		} while ($feedBack == false);
		return $number_transaction;
	}
}
