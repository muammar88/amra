<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_master_data_cud extends CI_Model
{

	private $content = '';
	private $error = 1;
	private $CI;

	public function __construct()
	{
		parent::__construct();

		$this->CI = &get_instance();
		$this->CI->load->model('Model_Read/Model_master_data', 'model_masterdata');
	}

	/* Update Dusun */
	function updateDusun($id_dusun)
	{
		$data['nama_dusun'] = $this->input->post('nama_dusun');
		$this->db->where('id_dusun', $id_dusun);
		$update = $this->db->update('dusun', $data);
		if (!$update) {

			return false;
		} else {
			$this->error = 0;
			$this->content = 'Mengubah Data Dusun ' . $this->input->post('nama_dusun') . '';
			return true;
		}
	}

	/* Insert Dusun */
	function insertDusun()
	{
		$data['nama_dusun'] = $this->input->post('nama_dusun');
		/* insert process */
		$insert             = $this->db->insert('dusun', $data);
		if (!$insert) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menambahkan Data Dusun ' . $this->input->post('nama_dusun') . '';
			return true;
		}
	}

	/* Delete Dusun */
	function delete_dusun($id_dusun)
	{
		$nama_dusun = $this->CI->model_masterdata->get_nama_dusun($id_dusun);
		$this->db->where('id_dusun', $id_dusun);
		$delete = $this->db->delete('dusun');
		if (!$delete) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menghapus Data Dusun ' . $nama_dusun . '';
			return true;
		}
	}

	/* Update Agama */
	function updateAgama($id_agama)
	{
		$data['nama_agama'] = $this->input->post('nama_agama');
		$this->db->where('id_agama', $id_agama);
		$update = $this->db->update('agama', $data);
		if (!$update) {

			return false;
		} else {
			$this->error = 0;
			$this->content = 'Mengubah Data Agama ' . $this->input->post('nama_agama') . '';
			return true;
		}
	}

	/* Insert Agama */
	function insertAgama()
	{
		$data['nama_agama'] = $this->input->post('nama_agama');
		/* insert process */
		$insert  			= $this->db->insert('agama', $data);
		if (!$insert) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menambahkan Data Agama ' . $this->input->post('nama_agama') . '';
			return true;
		}
	}

	/* Delete Agama */
	function delete_agama($id_agama)
	{
		$nama_agama = $this->CI->model_masterdata->get_nama_agama($id_agama);
		$this->db->where('id_agama', $id_agama);
		$delete = $this->db->delete('agama');
		if (!$delete) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menhapus Data Agama ' . $nama_agama . '';
			return true;
		}
	}

	/* Update Pekerjaan */
	function updatePekerjaan($id_pekerjaan)
	{
		$data['nama_pekerjaan'] = $this->input->post('nama_pekerjaan');
		$this->db->where('id', $id_pekerjaan);
		$update = $this->db->update('pekerjaan', $data);
		if (!$update) {

			return false;
		} else {
			$this->error = 0;
			$this->content = 'Mengubah Data Pekerjaan ' . $this->input->post('nama_pekerjaan') . '';
			return true;
		}
	}

	/* Insert Pekerjaan */
	function insertPekerjaan()
	{
		$data['nama_pekerjaan'] = $this->input->post('nama_pekerjaan');
		/* insert process */
		$insert  			= $this->db->insert('pekerjaan', $data);
		if (!$insert) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menambahkan Data Pekerjaan ' . $this->input->post('nama_pekerjaan') . '';
			return true;
		}
	}

	/* Delete Pekerjaan */
	function delete_pekerjaan($id_pekerjaan)
	{
		$nama_pekerjaan = $this->CI->model_masterdata->get_nama_pekerjaan($id_pekerjaan);
		$this->db->where('id', $id_pekerjaan);
		$delete = $this->db->delete('pekerjaan');
		if (!$delete) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menghapus Data Pekerjaan ' . $nama_pekerjaan . '';
			return true;
		}
	}

	/* Update Pendidikan */
	function updatePendidikan($id_pendidikan)
	{
		$data['nama_pendidikan'] = $this->input->post('nama_pendidikan');
		$this->db->where('id_pendidikan', $id_pendidikan);
		$update = $this->db->update('pendidikan', $data);
		if (!$update) {

			return false;
		} else {
			$this->error = 0;
			$this->content = 'Mengubah Data Pendidikan ' . $this->input->post('nama_pendidikan') . '';
			return true;
		}
	}

	/* Insert Pendidikan */
	function insertPendidikan()
	{
		$data['nama_pendidikan'] = $this->input->post('nama_pendidikan');
		/* insert process */
		$insert  			= $this->db->insert('pendidikan', $data);
		if (!$insert) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menambahkan Data Pendidikan ' . $this->input->post('nama_pendidikan') . '';
			return true;
		}
	}

	/* Delete Pendidikan */
	function delete_pendidikan($id_pendidikan)
	{
		$nama_pendidikan = $this->CI->model_masterdata->get_nama_pendidikan($id_pendidikan);
		$this->db->where('id_pendidikan', $id_pendidikan);
		$delete = $this->db->delete('pendidikan');
		if (!$delete) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menghapus Data Pendidikan ' . $nama_pendidikan . '';
			return true;
		}
	}

	/* update unit */
	function updateUnit($id, $data){
		$this->db->where('id_unit', $id);
		$update = $this->db->update('unit', $data);
		if (!$update) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Mengubah Data Unit ' . $data['nama_unit'] . '';
			return true;
		}
	}

	/* insert unit */
	function insertUnit($data)
	{
		/* insert process */
		$insert  			= $this->db->insert('unit', $data);
		if (!$insert) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menambahkan Data Unit ' . $data['nama_unit'] . '';
			return true;
		}
	}

	/* Delete Unit */
	function deleteUnit($id)
	{
		$nama_unit = $this->CI->model_masterdata->get_nama_unit($id);
		$this->db->where('id_unit', $id);
		$delete = $this->db->delete('unit');
		if (!$delete) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Menghapus Data Unit Dengan Nama Unit ' . $nama_unit . '';
			return true;
		}
	}

	/* Write log master data*/
	function __destruct()
	{
		if ($this->error == 0) {
			$this->syslog->write_log($this->content);
		}
	}
}
