<?php

/**
 *  -----------------------
 *	Model daftar asuransi
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_asuransi extends CI_Model
{
   private $company_id;
   private $status;
   private $content;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_asuransi($search){
      $this->db->select('id')
         ->from('mst_asuransi')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_asuransi', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   
   function get_index_daftar_asuransi($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, nama_asuransi, last_update')
         ->from('mst_asuransi')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_asuransi', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'nama_asuransi' => $rows->nama_asuransi,
               'last_update' => $rows->last_update
            );
         }
      }
      return $list;
   }

   function check_asuransi_id($id){
      $this->db->select('id')
         ->from('mst_asuransi')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      } 
   }

   function get_data_asuransi_by_id( $id ) {
      $this->db->select('id, nama_asuransi')
         ->from('mst_asuransi')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $feedBack['id'] = $rows->id;
            $feedBack['nama'] = $rows->nama_asuransi;
         }
      }
      return $feedBack;
   }
}