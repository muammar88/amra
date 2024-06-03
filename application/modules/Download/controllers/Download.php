<?php

/**
 *  -----------------------
 *	Download Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Download extends CI_Controller
{

	private $company_code;
	private $company_id;
	private $sesi;

	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();
		# Load user model
		$this->load->model('Model_download', 'model_download');
		# checking is not Login
		$this->auth_library->Is_not_login();
		# get company id
		$this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
		# receive company code value
		$this->company_code = $this->input->get('company_code');
		# set date timezone
		ini_set('date.timezone', 'Asia/Jakarta');
	}

	function index()
	{
		$this->sesi = $this->session->userdata('download_to_excel');
		$type = 'model_' . $this->sesi['type'];
		# Fungsi header dengan mengirimkan raw data excel
		header("Content-type: application/vnd-ms-excel");
		# Mendefinisikan nama file ekspor "excel-buku-besar.xls"
		if( isset( $this->sesi['periode'] ) ){
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] . "_periode:" . $this->model_download->periode_name($this->sesi['periode']) . ".xls");
		}elseif( isset( $this->sesi['paket_name'] ) ){
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] . "_kode:".$this->sesi['kode_paket']."_paket:" . $this->sesi['paket_name'] . ".xls");
		}elseif( $this->sesi['type'] == 'download_manifest_tabungan_umrah'){
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] . "_:".
					( $this->sesi['filter']['filterTransaksi'] == 'sudah' ? 'Sudah_beli_paket' : 'Belum_beli_paket').".xls");
		}elseif( $this->sesi['type'] == 'download_absensi_kamar'){
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] .".xls");
		}elseif( $this->sesi['type'] == 'download_excel_daftar_agen'){
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] .".xls");
		}elseif( $this->sesi['type'] == 'download_all_jamaah_to_excel'){
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] .".xls");
		}elseif( $this->sesi['type'] == 'download_excel_info_saldo_member'){
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] .".xls");
		}else{
			header("Content-Disposition: attachment; filename=excel-" . $this->sesi['type'] . ".xls");
		}
		echo "<table>" . $this->model_download->$type($this->sesi) . '</table>';
	}
}
