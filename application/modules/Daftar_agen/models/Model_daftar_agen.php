<?php

/**
 *  -----------------------
 *	Model daftar agen
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_agen extends CI_Model
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

   function get_total_level_agen($search){
      $this->db->select('id')
         ->from('level_keagenan')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get total daftar agen
   function get_total_daftar_agen($search)
   {
      $this->db->select('a.id')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar agen
   function get_index_daftar_agen($limit = 6, $start = 0, $search = '')
   {
      // (SELECT COUNT(id) FROM detail_fee_keagenan
      // WHERE company_id="' . $this->company_id . '"
      //   AND agen_id=a.id) AS jumlahTransaksi,
      $this->db->select('a.id, p.fullname, p.identity_number, la.nama,
                           (SELECT per.fullname FROM agen AS ag
                           INNER JOIN personal AS per ON ag.personal_id=per.personal_id
                           WHERE ag.company_id="' . $this->company_id . '"
                              AND ag.id=a.upline) AS uplines,
                          (SELECT COUNT( DISTINCT(j.id) ) FROM jamaah AS j
                           WHERE j.company_id="' . $this->company_id . '"
                              AND j.agen_id=a.id) AS jumlahJamaah')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->join('level_keagenan AS la', 'a.level_agen_id=la.id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('a.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'level_agen' => $row->nama,
               'upline' => $row->uplines,
               // 'jumlah_transaksi' => $row->jumlahTransaksi,
               'jumlah_jamaah' => $row->jumlahJamaah
            );
         }
      }
      return $list;
   }

   function get_index_level_agen($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, nama, level, default_fee')
         ->from('level_keagenan')
         ->where('company_id', $this->company_id);
     if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
           ->like('nama', $search)
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
               'level' => $row->level,
               'default_fee' => $row->default_fee,
           );
         }
     }
     return $list;
   }

   function get_personal_id_agen($agen_id)
   {
      $this->db->select('personal_id')
         ->from('agen')
         ->where('company_id', $this->company_id)
         ->where('id', $agen_id);
      $q = $this->db->get();
      $personal_id = '';
      if ($q->num_rows() > 0) {
         $personal_id = $q->row()->personal_id;
      }
      return $personal_id;
   }

   # check id agen exist
   function check_id_agen_exist($id)
   {
      $this->db->select('id')
         ->from('agen')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_level_agen($id)
   {
      $this->db->select('level_agen')
         ->from('agen')
         ->where('company_id', $this->company_id)
         ->where('id', $id)
         ->where('level_agen', 'agen');
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_id_level_keagenan($id){
      $this->db->select('id')
         ->from('level_keagenan')
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function check_level_keagenan_is_exist($level, $id = 0){
      $this->db->select('id')
         ->from('level_keagenan')
         ->where('level', $level)
         ->where('company_id', $this->company_id);
      if( $id != 0 ){
         $this->db->where('id !=', $id);
      }
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_level_keagenan(){
      $feedBack = false;
		$i = 1;
		do {
			$this->db->select('id')
				->from('level_keagenan')
				->where('level', $i)
            ->where('company_id', $this->company_id);
			$q = $this->db->get();
         if ($q->num_rows() == 0) {
            $feedBack = true;
         }else{
            $i++;
         }
		} while ($feedBack == false);
		return $i;
   }

   function get_value_level_keagenan($id){
      $this->db->select('id,nama, level, default_fee')
         ->from('level_keagenan')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['nama'] = $rows->nama;
            $list['level'] = $rows->level;
            $list['default_fee'] = $rows->default_fee;
         }
      }
      return $list;
   }

   function check_id_level_keagenan_is_use($id){
      $this->db->select('id')
         ->from('agen')
         ->where('company_id', $this->company_id)
         ->where('level_agen_id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }
}
