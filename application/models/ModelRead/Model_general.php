<?php

/**
*  -----------------------
*	Model General
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_general extends CI_Model
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

    function fee_keagenan_deposit_paket($jamaah_id){
        // get_personal_id
        $this->db->select('a.level_agen_id, a.upline')
        ->from('agen AS a')
        ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
        ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner')
        ->where('a.company_id', $this->company_id)
        ->where('j.id', $jamaah_id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            foreach ($q->result() as $rows) {
                if( $rows->upline != 0 ){
                    $tree = $this->agen_upline_tree($rows->upline);
                    if( $tree[$rows->upline]['level_agen_id'] > $rows->level_agen_id ) { // apabila level upline lebih besar dari level jamaah
                        $list = $tree;
                    }
                }
            }
        }else{
            $this->db->select('agen_id')
            ->from('jamaah')
            ->where('company_id', $this->company_id)
            ->where('id', $jamaah_id);
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
                foreach ($q->result() as $rows) {
                    if( $rows->agen_id != 0 ) {
                        $list = $this->agen_upline_tree($rows->agen_id);
                    }
                }
            }
        }
        return $list;
    }

    function agen_upline_tree($agen_id){
        $list = array();

        $feedBack = false;
        # level keagenan
        $level_keagenan = $this->fee_default_level_agen();
        $last_level = 0;
        do {
            $this->db->select('a.id, p.fullname, a.level_agen_id, a.upline, la.nama')
            ->from('agen AS a')
            ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
            ->join('level_keagenan AS la', 'a.level_agen_id=la.id', 'inner')
            ->where('a.company_id', $this->company_id)
            ->where('a.id', $agen_id);
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
                foreach ($q->result() as $rows) {
                    if($last_level < $rows->level_agen_id) {
                        $list[$rows->id] = array('id' => $rows->id,
                        'level_agen_id' => $rows->level_agen_id,
                        'level' => $rows->nama,
                        'nama_agen' => $rows->fullname,
                        'fee' => $level_keagenan[$rows->level_agen_id]);
                        $last_level = $rows->level_agen_id;

                        if( $rows->upline != 0 ) {
                            $agen_id = $rows->upline;
                        }else{
                            $feedBack = true;
                        }
                    }else{
                        $feedBack = true;
                    }
                }
            }else{
                $feedBack = true;
            }
        } while ($feedBack == false);

        return $list;
    }

    function fee_default_level_agen(){
        $this->db->select('id, default_fee')
        ->from('level_keagenan')
        ->where('company_id', $this->company_id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            foreach ($q->result() as $rows) {
                $list[$rows->id] = $rows->default_fee;
            }
        }
        return $list;
    }

    function get_jamaah_id_by_personal_id($personal_id){
        $this->db->select('j.id')
        ->from('jamaah AS j')
        ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
        ->where('j.personal_id', $personal_id);
        $q = $this->db->get();
        $jamaah_id = 0;
        if( $q->num_rows() > 0 ){
            foreach ($q->result() as $rows) {
                $jamaah_id = $rows->id;
            }
        }
        return $jamaah_id;
    }

    function get_pool_id( $personal_id ){
        $this->db->select('p.id')
        ->from('pool AS p')
        ->join('jamaah AS j', 'p.jamaah_id=j.id', 'inner')
        ->where('j.personal_id', $personal_id)
        ->where('p.active', 'active');
        $pool_id = 0;
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
                $pool_id = $rows->id;
            }
        }
        return $pool_id;
    }
}
