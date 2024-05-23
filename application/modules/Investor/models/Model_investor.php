<?php

/**
 *  -----------------------
 *	Model investor
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_investor extends CI_Model
{
    private $company_id;
    private $status;
    private $content;
    private $error;
    private $write_log;

    public function __construct()
    {
        parent::__construct();
        $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $this->error = 0;
        $this->write_log = 1;
    }

    # get total daftar investor
    function get_total_daftar_investor($search)
    {
        $this->db->select('id')
            ->from('investor')
            ->where('company_id', $this->company_id);
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
                ->like('nama', $search)
                ->or_like('nomor_identitas', $search)
                ->group_end();
        }
        $r     = $this->db->get();
        return $r->num_rows();
    }

    function get_index_daftar_investor($limit = 6, $start = 0, $search = '')
    {
        $this->db->select('id, nama, nomor_identitas, no_hp, alamat, investasi')
            ->from('investor')
            ->where('company_id', $this->company_id);
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
                ->like('nama', $search)
                ->or_like('nomor_identitas', $search)
                ->group_end();
        }
        $this->db->order_by('id', 'desc')->limit($limit, $start);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {

                $list[] = array(
                    'id' => $row->id,
                    'nama' => $row->nama,
                    'nomor_identitas' => $row->nomor_identitas,
                    'no_hp' => $row->no_hp,
                    'alamat' => $row->alamat,
                    'investasi' => $row->investasi
                );
            }
        }
        return $list;
    }

    # check investor exist
    function check_investor_exist($id)
    {
        $this->db->select('id')
            ->from('investor')
            ->where('company_id', $this->company_id)
            ->where('id', $id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    # get info edit investor
    function get_info_edit_investor($id)
    {
        $this->db->select('id, nama, nomor_identitas, no_hp, alamat, investasi, saham')
            ->from('investor')
            ->where('company_id', $this->company_id)
            ->where('id', $id);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list['id'] = $rows->id;
                $list['nama'] = $rows->nama;
                $list['nomor_identitas'] = $rows->nomor_identitas;
                $list['no_hp'] = $rows->no_hp;
                $list['alamat'] = $rows->alamat;
                $list['investasi'] = $rows->investasi;
                $list['saham'] = $rows->saham;
            }
        }
        return $list;
    }

    // check saham investor
    function check_saham_investor( $saham )
    {
        // filter
        if ( $this->input->post('id') ) {
            // total saham investor
            $saham_investor = $this->total_saham( $this->input->post('id') );
        } else {
            // total saham investor
            $saham_investor = $this->total_saham();
        }
        // total
        return ( $saham_investor + $saham ) > 100 ? false : true;
    }

    // total saham
    function total_saham($id = ''){
        $this->db->select('saham')
            ->from('investor')
            ->where('company_id', $this->company_id);
        if( $id != '') {
            $this->db->where('id !=', $id);
        }    
        $q = $this->db->get();
        $saham_investor = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $saham_investor = $saham_investor + $rows->saham;
            }
        }
        return $saham_investor;
    }

    // investasi
    function info_investasi_investor($id){
        $this->db->select('investasi, nama')
            ->from('investor')
            ->where('company_id', $this->company_id)
            ->where('id', $id);
        $list = array();
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
                $list['total_investasi'] = $rows->investasi;
                $list['nama_investor'] = $rows->nama;
            }
        }
        return $list;    
    }

    function get_investasi_investor( $id ) {
        $this->db->select('investasi')
            ->from('investor')
            ->where('company_id', $this->company_id)
            ->where('id', $id);
        $q = $this->db->get();
        $investasi = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $investasi = $rows->investasi;
            }
        }
        return $investasi;
    }
}
