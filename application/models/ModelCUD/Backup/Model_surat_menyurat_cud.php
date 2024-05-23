<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_surat_menyurat_cud extends CI_Model
{

    private $content = '';
    private $error = 1;
    private $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI = &get_instance();
        $this->CI->load->model('Model_Read/Model_surat_menyurat', 'model_suratmenyurat');
    }

    function insertRiwayatSuratKeteranganKepolisian($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat_keterangan_kepolisian', $data);
        if (!$insert) {
            return array('error' => true, 'id' => 0);
        } else {
            return array('error' => false, 'id' => $this->db->insert_id());
        }
    }

    function insertRiwayatSuratKeteranganAktaLahir($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat_keterangan_akta_lahir', $data);
        if (!$insert) {
            return array('error' => true, 'id' => 0);
        } else {
            return array('error' => false, 'id' => $this->db->insert_id());
        }
    }

    public function insertRiwayatSuratDomisiliPenduduk($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat_domisili_penduduk', $data);
        if (!$insert) {
            return array('error' => true, 'id' => 0);
        } else {
            return array('error' => false, 'id' => $this->db->insert_id());
        }
    }

    public function insertRiwayatSuratKeteranganNikah($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat_keterangan_nikah', $data);
        if (!$insert) {
            return array('error' => true, 'id' => 0);
        } else {
            return array('error' => false, 'id' => $this->db->insert_id());
        }
    }

    public function insertRiwayatSuratDomisiliPerusahaan($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat_domisili_perusahaan', $data);
        if (!$insert) {
            return array('error' => true, 'id' => 0);
        } else {
            return array('error' => false, 'id' => $this->db->insert_id());
        }
    }

    public function insertRiwayatSuratKeteranganUsaha($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat_keterangan_usaha', $data);
        if (!$insert) {
            return array('error' => true, 'id' => 0);
        } else {
            return array('error' => false, 'id' => $this->db->insert_id());
        }
    }

    // insert table surat keterangan tidak mampu
    function insertRiwayatSuratKeteranganTidakMampu($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat_keterangan_tidak_mampu', $data);
        if (!$insert) {
            return array('error' => true, 'id' => 0);
        } else {
            return array('error' => false, 'id' => $this->db->insert_id());
        }
    }

    public function insertRiwayatCetakSurat($data)
    {
        /* insert process */
        $insert  = $this->db->insert('surat', $data);
        if (!$insert) {
            return 1;
        } else {
            $this->session->set_userdata(array('cetak_surat' => $this->db->insert_id()));
            // insertRiwayatCetakSurat
            $this->content     = ' Mengeluarkan Surat Dengan Nomor Surat ' . $data['nomor_surat'] . '. ';
            $this->error     = 0;
            return 0;
        }
    }

    /* Delete Surat */
    public function delete_surat($id_surat)
    {
        $error = 0;
        $simple_info_surat = $this->CI->model_suratmenyurat->getInfoSurat_simple($id_surat);
        if ($simple_info_surat['jenis_surat'] == 1) { // delete surat domisili
            $this->db->where('id', $simple_info_surat['id_jenis_surat']);
            $delete_surat = $this->db->delete('surat_domisili_penduduk');
            if (!$delete_surat) {
                $error = 1;
            }
        } elseif ($simple_info_surat['jenis_surat'] == 2) {
            $this->db->where('id', $simple_info_surat['id_jenis_surat']);
            $delete_surat = $this->db->delete('surat_domisili_perusahaan');
            if (!$delete_surat) {
                $error = 1;
            }
        } elseif ($simple_info_surat['jenis_surat'] == 3) {
            $this->db->where('id', $simple_info_surat['id_jenis_surat']);
            $delete_surat = $this->db->delete('surat_keterangan_usaha');
            if (!$delete_surat) {
                $error = 1;
            }
        } elseif ($simple_info_surat['jenis_surat'] == 4) {
            $this->db->where('id', $simple_info_surat['id_jenis_surat']);
            $delete_surat = $this->db->delete('surat_keterangan_nikah');
            if (!$delete_surat) {
                $error = 1;
            }
        } elseif ($simple_info_surat['jenis_surat'] == 5) {
            $this->db->where('id', $simple_info_surat['id_jenis_surat']);
            $delete_surat = $this->db->delete('surat_keterangan_tidak_mampu');
            if (!$delete_surat) {
                $error = 1;
            }
        }
        // delete surat
        $this->db->where('id_surat', $id_surat);
        $delete = $this->db->delete('surat');
        if (!$delete_surat) {
            $error = 1;
        }

        if ($error == 1) {
            return false;
        } else {
            $this->error = 0;
            $this->content = 'Menghapus Data Surat Dengan Nomor Surat ' . $simple_info_surat['nomor_surat'] . '';
            return true;
        }
    }

    /* Write log master data*/
    public function __destruct()
    {
        if ($this->error == 0) {
            $this->syslog->write_log($this->content);
        }
    }
}
