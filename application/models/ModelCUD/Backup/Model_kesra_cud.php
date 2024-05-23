<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_kesra_cud extends CI_Model
{

    private $content = '';
    private $error = 1;
    private $CI;

    public function __construct()
    {
        parent::__construct();

        $this->CI = &get_instance();
        $this->CI->load->model('Model_Read/Model_kesra', 'model_kesra');
    }

    // update bantuan 
    function updateBantuan($data, $id_bantuan)
    {
        $this->db->where('id_bantuan', $id_bantuan);
        $update = $this->db->update('bantuan', $data);
        if (!$update) {
            return false;
        } else {
            $this->error = 0;
            if ($data['jenis_identitas'] == 1) {
                $this->content = 'Mengubah Data Bantuan untuk NIK ' . $data['id_penduduk_kk'] . ' Dengan Nama Bantuan ' . $this->model_kesra->get_jenis_bantuan($data['id_jenis_bantuan'])['jenis_bantuan'] . ' ';
            } else {
                $this->content = 'Mengubah Data Bantuan untuk Nomor KK ' . $data['id_penduduk_kk'] . ' Dengan Nama Bantuan ' . $this->model_kesra->get_jenis_bantuan($data['id_jenis_bantuan'])['jenis_bantuan'] . ' ';
            }
            return true;
        }
    }


    function insertBantuan($data)
    {
        /* insert process */
        $insert              = $this->db->insert('bantuan', $data);
        if (!$insert) {
            return false;
        } else {

            $this->error = 0;
            if ($data['jenis_identitas'] == 1) {
                $this->content = 'Menanbahkan Data Bantuan untuk NIK ' . $data['id_penduduk_kk'] . ' Dengan Nama Bantuan ' . $this->model_kesra->get_jenis_bantuan($data['id_jenis_bantuan'])['jenis_bantuan'] . ' ';
            } else {
                $this->content = 'Menanbahkan Data Bantuan untuk Nomor KK ' . $data['id_penduduk_kk'] . ' Dengan Nama Bantuan ' . $this->model_kesra->get_jenis_bantuan($data['id_jenis_bantuan'])['jenis_bantuan'] . ' ';
            }
            return true;
        }
    }


    /* Update Jenis Bantuan */
    function updateJenisBantuan($id_jenis_bantuan)
    {
        $data['jenis_bantuan'] = $this->input->post('jenis_bantuan');
        $data['asal_bantuan']   = $this->input->post('asal_bantuan');
        $this->db->where('id_jenis_bantuan', $id_jenis_bantuan);
        $update = $this->db->update('jenis_bantuan', $data);
        if (!$update) {
            return false;
        } else {
            $this->error = 0;
            $this->content = 'Mengubah Data Jenis Bantuan ' . $this->input->post('jenis_bantuan') . '';
            return true;
        }
    }

    function insertJenisBantuan()
    {
        $data['jenis_bantuan']  = $this->input->post('jenis_bantuan');
        $data['asal_bantuan']   = $this->input->post('asal_bantuan');        
        /* insert process */
        $insert              = $this->db->insert('jenis_bantuan', $data);
        if (!$insert) {
            return false;
        } else {
            $this->error = 0;
            $this->content = 'Menambahkan Data Jenis Bantuan ' . $this->input->post('jenis_bantuan') . '';
            return true;
        }
    }

    function delete_jenis_bantuan($id_jenis_bantuan)
    {
        $jenis_bantuan = $this->CI->model_kesra->get_jenis_bantuan($id_jenis_bantuan);
        $this->db->where('id_jenis_bantuan', $id_jenis_bantuan);
        $delete = $this->db->delete('jenis_bantuan');
        if (!$delete) {
            return false;
        } else {
            $this->error = 0;
            $this->content = 'Menghapus Data Jenis Bantuan ' . $jenis_bantuan . '';
            return true;
        }
    }

    // delete bantuan
    function delete_bantuan($id_bantuan)
    {
        $jenis_bantuan = $this->CI->model_kesra->get_info_bantuan($id_bantuan);
        $this->db->where('id_bantuan', $id_bantuan);
        $delete = $this->db->delete('bantuan');
        if (!$delete) {
            return true;
        } else {
            $this->error     = 0;
            $this->content     = 'Menghapus Data Bantuan ' . $jenis_bantuan['jenis_bantuan'] . ' Dengan Identitas ' . $jenis_bantuan['id_penduduk_kk'] . ' ';
            return false;
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
