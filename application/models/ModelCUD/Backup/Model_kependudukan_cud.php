<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_kependudukan_cud extends CI_Model
{

	private $content = '';
	private $error = 1;
	private $CI;

	public function __construct()
	{
		parent::__construct();

		$this->CI = &get_instance();
		$this->CI->load->model('Model_Read/Model_kependudukan', 'model_kependudukan');
	}

	/* update kependudukan */
	function updateKependudukan($id_penduduk, $data)
	{
		$this->db->where('id_penduduk', $id_penduduk);
		$update = $this->db->update('penduduk', $data);
		if (!$update) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Mengubah Data Penduduk ' . $data['nama'] . '';
			return true;
		}
	}

	/* insert kependudukan */
	function insertKependudukan($data)
	{
		/* insert process */
		$insert  			= $this->db->insert('penduduk', $data);
		if (!$insert) {
			return array('error' => false, 'id' => $this->db->insert_id());
		} else {
			$this->error = 0;
			$this->content = 'Menambahkan Data Penduduk ' . $data['nama'];
			return array('error' => true, 'id' => $this->db->insert_id());
		}
	}

	/* add update KK */
	function addupdateKK($no_kk, $data, $alamat)
	{
		$loc_err 		= 0;
		$nama_penduduk 	= $this->CI->model_kependudukan->get_nama_penduduk($data['id_penduduk']);

		// check kk
		$this->db->select('id_kk')
			->from('kk')
			->where('nomor_kk', $no_kk);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			// define id kk
			$data['id_kk']	= $q->row()->id_kk;
			// update process 
			$this->db->where('id_penduduk', $data['id_penduduk']);
			$delete = $this->db->delete('kk_anggota');
			if (!$delete) {
				$loc_err = 1;
			}
		} else {
			$dataKK = array();
			$dataKK['nomor_kk'] = $no_kk;
			$dataKK['alamat'] = $alamat;
			// insert process
			$insertkk  			= $this->db->insert('kk', $dataKK);
			$data['id_kk'] 		= $this->db->insert_id();
			if (!$insertkk) {
				$loc_err = 1;
			}
		}

		$insert  = $this->db->insert('kk_anggota', $data);
		if (!$insert) {
			$loc_err = 1;
		}

		if ($loc_err == 0) {
			$this->content = 'Memperbaharui Data Anggota Keluarga ' . $nama_penduduk;
			$this->error = 0;
		}
	}

	/* Delete Penduduk */
	function delete_penduduk($id_penduduk)
	{
		// get info nama dan foto
		$nama_foto_penduduk = $this->CI->model_kependudukan->get_nama_foto_penduduk($id_penduduk);

		// proses delete foto
		if ($nama_foto_penduduk['foto'] != 'default.png') {
			$src = FCPATH . 'image/foto_penduduk/' . $nama_foto_penduduk['foto'];
			if (file_exists($src)) {
				unlink($src);
			}
		}

		// delete data penduduk
		$this->db->where('id_penduduk', $id_penduduk);
		$delete = $this->db->delete('penduduk');
		if (!$delete) {
			return false;
		} else {
			$this->db->where('id_penduduk', $id_penduduk);
			$this->db->delete('kk_anggota');

			$this->error 	= 0;
			$this->content 	= 'Menghapus Data Penduduk ' . $nama_foto_penduduk['nama'] . '';
			return true;
		}
	}

	/* Update */
	public function updateKKKependudukan($id_kk, $data)
	{
		$this->db->where('id_kk', $id_kk);
		$update = $this->db->update('kk', $data);
		if (!$update) {
			return 1;
		} else {
			$this->error 	= 0;
			$this->content 	= ' Mengubah Data Kartu Keluarga Dengan Nomor KK ' . $data['nomor_kk'] . '';
			return 0;
		}
	}

	/* Insert Proocess */
	public function insertKKKependudukan($data)
	{
		$insert  = $this->db->insert('kk', $data);
		$id_kk   = $this->db->insert_id();
		if (!$insert) {
			return array('return' => $this->error);
		} else {
			$this->error 	= 0;
			$this->content 	= 'Menambahkan Data Kartu Keluarga Dengan Nomor KK ' . $data['nomor_kk'] . '';
			return array('return' => $this->error,  'id_kk' => $id_kk);
		}
	}

	/* delete process */
	function deleteKKAnggota($id_kk)
	{
		$this->db->where('id_kk', $id_kk);
		$delete = $this->db->delete('kk_anggota');
		if (!$delete) {
			return false;
		} else {
			return true;
		}
	}

	// insert Anggota Keluarga
	public function insertKKAnggota($data, $nomor_kk)
	{
		$infoPenduduk = $this->CI->model_kependudukan->get_nik_nama($data['id_penduduk']);
		$insert  = $this->db->insert('kk_anggota', $data);
		if (!$insert) {
			return false;
		} else {
			// update data penduduk
			$update = $this->db->where('id_penduduk', $data['id_penduduk'])
				->update('penduduk', array('no_kk' => $nomor_kk, 'uid_statuskeluarga' => $data['status']));
			$this->error 	= 0;
			$this->content 	= 'Menambahkan Anggota Keluarga Dengan NIK ' . $infoPenduduk['nik'] . ' dan Nama ' . $infoPenduduk['nama'] . '';
			return true;
		}
	}

	function deleteKartuKeluarga($id_kk)
	{
		$no_kk = $this->CI->model_kependudukan->get_no_kk($id_kk);
		// update kk peduduk
		$update = $this->db->where('no_kk', $no_kk)
			->update('penduduk', array('no_kk' => '', 'uid_statuskeluarga' => 0));
		$this->db->where('id_kk', $id_kk);
		$delete = $this->db->delete('kk');
		if (!$delete) {
			return false;
		} else {
			$delete_kk_anggota = $this->db->where('id_kk', $id_kk)
				->delete('kk_anggota');
			if (!$delete_kk_anggota) {
				return false;
			} else {
				$this->error = 0;
				$this->content = ' Menghapus Data Kartu Keluarga dengan nomor KK ' . $no_kk . ' ';
				return true;
			}
		}
	}

	function updatePhotoKartuKeluarga($file_name, $id_penduduk)
	{
		$feedBack = $this->CI->model_kependudukan->get_penduduk_by_name($id_penduduk, 0);
		$this->db->where('id_penduduk', $id_penduduk);
		$update = $this->db->update('penduduk', array('foto' => $file_name));
		if (!$update) {
			return false;
		} else {
			$this->error = 0;
			$this->content = 'Mengubah Data Penduduk ' . $feedBack['nama'] . '';
			return true;
		}
	}

	function insertPendudukLuar($data)
	{
		$insert  = $this->db->insert('penduduk_luar', $data);
		if (!$insert) {
			return array('error' => true);
		} else {
			$this->error 	= 0;
			$this->content 	= 'Melakukan Proses Pindah Penduduk Dengan ' . $data['nik'] . ' dan Nama ' . $data['nama'] . '';
			return array('error' => false, 'id' => $this->db->insert_id());
		}
	}

	function insertPindah($data)
	{
		$insert  = $this->db->insert('pindah', $data);
		if (!$insert) {
			return array('success' => false);
		} else {
			return array('success' => true, 'id' => $this->db->insert_id());
		}
	}

	function deletePenduduk($nik)
	{
		$this->db->where('nik', $nik);
		$delete = $this->db->delete('penduduk');
		if (!$delete) {
			return false;
		}
	}

	function deleteanggotaKK($penduduk_id, $no_kk)
	{
		$error = 0;
		// delete anggota kk
		// print_r($penduduk_id);
		$this->db->where('id_penduduk', $penduduk_id);
		$delete = $this->db->delete('kk_anggota');
		if (!$delete) {
			$error = 1;
		}

		// check kk
		$this->db->select('id_kk')
			->from('kk')
			->where('nomor_kk', $no_kk);
		$r = $this->db->get();
		if ($r->num_rows() > 0) {
			// define id kk
			$id_kk = $r->row()->id_kk;
			$this->db->select('*')
				->from('kk_anggota')
				->where('id_kk', $id_kk);
			$q = $this->db->get();
			if ($q->num_rows() == 0) {
				// delete kk
				$this->db->where('id_kk', $id_kk);
				$delete = $this->db->delete('kk');
				if (!$delete) {
					$error = 1;
				}
			}
		}

		if ($error == 1) {
			false;
		} else {
			true;
		}
	}

	function insertPindahDatang($data)
	{
		$insert  = $this->db->insert('penduduk', $data);
		if (!$insert) {
			return array('error' => true);
		} else {
			$this->error 	= 0;
			$this->content 	= 'Melakukan Proses Pindah Datang Penduduk Dengan ' . $data['nik'] . ' dan Nama ' . $data['nama'] . '';
			return array('error' => false, 'id' => $this->db->insert_id());
		}
	}

	function deleteIfPendudukLuar($nik)
	{
		$this->db->select('id_penduduk_luar')
			->from('penduduk_luar')
			->where('nik', $nik);
		$q = $this->db->get();
		if ($q->num_rows() > 0) {
			$this->db->where('nik', $nik);
			$delete = $this->db->delete('penduduk_luar');
			if (!$delete) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	function delete_penduduk_luar($id)
	{
		// get info penduduk luar nama dan nik
		$info_penduduk_luar = $this->CI->model_kependudukan->get_nama_penduduk_luar($id);
		// delete data penduduk
		$this->db->where('id_penduduk_luar', $id);
		$delete = $this->db->delete('penduduk_luar');
		if (!$delete) {
			return false;
		} else {
			$this->error 	= 0;
			$this->content = 'Menghapus Data Penduduk Luar Dengan NiK : ' . $info_penduduk_luar['nik'] . ' dan Nama :' . $info_penduduk_luar['nama'];
			return true;
		}
	}

	// menambahkan penduduk ke table pindah penduduk
	function insertPindahPenduduk( $data ){
		$insert  = $this->db->insert('pindah_penduduk', $data);
		if (!$insert) {
			return false;
		} else {
			$this->error 	= 0;
			$this->content = 'Menambahkan Data Penduduk Kedalam Table Pindah Penduduk Dengan NiK : '.$data['nik'].'  dan Nama :' . $data['nama'];
			return true;
		}
	}

	// insert data kematian
	function insertKematianPenduduk($id, $tanggal_kematian){
		$error = 0;
		$this->db->select('p.*, ka.id_kk')
			->from('penduduk AS p')
			->join('kk_anggota AS ka', 'p.id_penduduk=ka.id_penduduk', 'inner')
			->where('p.id_penduduk', $id);
		$q = $this->db->get();
		$data = array();
		if( $q->num_rows() > 0 )
		{
			foreach ($q->result() as $rows) 
			{

				$data['tanggal_kematian'] = $tanggal_kematian;
				$data['nik'] = $rows->nik;
				$data['nama'] = $rows->nama;
				$data['no_kk'] = $rows->no_kk;
				$data['uid_jenkel'] = $rows->uid_jenkel;
				$data['tempat_lahir'] = $rows->tempat_lahir;
				$data['tanggal_lahir'] = $rows->tanggal_lahir;
				$data['uid_goldar'] = $rows->uid_goldar;
				$data['uid_agama'] = $rows->uid_agama;
				$data['uid_statusnikah'] = $rows->uid_statusnikah;
				$data['uid_statuskeluarga'] = $rows->uid_statuskeluarga;
				$data['uid_pekerjaan'] = $rows->uid_pekerjaan;
				$data['ayah'] = $rows->ayah;
				$data['ibu'] = $rows->ibu;
				$data['status_hidup'] = $rows->status_hidup;
				$data['uid_pendidikan'] = $rows->uid_pendidikan;
				$data['uid_dusun'] = $rows->uid_dusun;
				$data['foto'] = $rows->foto;
				$data['input_date'] = date('Y-m-d');
				$data['last_update'] = date('Y-m-d');

				// insert data
				$insert  = $this->db->insert('kematian', $data);
				if (!$insert) {
					$error = 1;
				}

				// delete data penduduk
				$this->db->where('id_penduduk', $rows->id_penduduk);
				$delete = $this->db->delete('penduduk');
				if(!$delete){
					$error = 1;
				}

				// delete data kk anggota
				$this->db->where('id_penduduk', $rows->id_penduduk);
				$delete_kk_anggota = $this->db->delete('kk_anggota');
				if(!$delete_kk_anggota){
					$error = 1;
				}

				// check if penduduk exist 
				$this->db->select('id_penduduk')
					->from('kk_anggota')
					->where('id_kk', $rows->id_kk);
				$s = $this->db->get();
				if( $s->num_rows() ==  0 ){
					$this->db->where('id_kk', $rows->id_kk);
					$delete_kk = $this->db->delete('kk');
					if(!$delete_kk){
						$error = 1;
					}
				}

			}
		}	

		if( $error == 1 ){
			return false;
		}else{
			$this->error 	= 0;
			$this->content = 'Menambahkan Data Penduduk Kedalam Table Kematian Dengan NiK : '.$data['nik'].'  dan Nama :' . $data['nama'];
			return true;
		}
	}

	function delete_kematian($id){
		// delete kematian
		$this->db->where('id_kematian', $id);
		$delete = $this->db->delete('kematian');
		if( ! $delete){
			return false;
			// $error = 1;
		}else{
			$this->error 	= 0;
			$this->content = ' Menghapus ID Penduduk Meninggal ';
			return true;
		}
	}

	function insertBantuan( $data ){
		$insert  = $this->db->insert('bantuan', $data);
		if (!$insert) {
			return false;
		} else {
			$this->error 	= 0;
			$this->content = 'Menambahkan Data Bantuan Penduduk Dengan NiK : '.$data['id_penduduk_kk'];
			return true;
		}
	}

	/* Write log Kependudukan */
	function __destruct()
	{
		if ($this->error == 0) {
			$this->syslog->write_log($this->content);
		}
	}
}
