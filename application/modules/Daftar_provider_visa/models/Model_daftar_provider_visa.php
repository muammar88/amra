<?php

/**
 *  -----------------------
 *	Model daftar provider visa
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_provider_visa extends CI_Model
{
   private $company_id;
   private $status;
   private $content;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_provider_visa($search){
      $this->db->select('id')
         ->from('mst_provider')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_provider', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   
   function get_index_daftar_provider_visa($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, nama_provider, last_update')
         ->from('mst_provider')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('nama_provider', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'nama_provider' => $rows->nama_provider,
               'last_update' => $rows->last_update
            );
         }
      }
      return $list;
   }

   function check_provider_visa_id($id){
      $this->db->select('id')
         ->from('mst_provider')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }   
   }

   function get_data_provider_visa_by_id( $id ){
      $this->db->select('id, nama_provider')
         ->from('mst_provider')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $feedBack['id'] = $rows->id;
            $feedBack['nama'] = $rows->nama_provider;
         }
      }
      return $feedBack;
   }

   
}