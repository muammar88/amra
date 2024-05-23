<?php

/**
 *  -----------------------
 *	Model komplain
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_komplain extends CI_Model
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

   // get total komplain
   function get_total_komplain($search, $status_komplain)
   {
      $this->db->select('k.id, k.status, k.info_penolakan, k.tanggal_komplain')
         ->from('komplain AS k')
         ->join('base_tab AS bt', 'k.tab_id=bt.id', 'inner')
         ->where('k.company_id', $this->company_id);
      if( $status_komplain != 'all' ) {
         $this->db->where('status', $status_komplain);
      }   
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('k.info_penolakan', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index komplain
   function get_index_komplain($limit = 6, $start = 0, $search = '', $status_komplain){
      $this->db->select('k.id, k.status, k.komplain, k.info_penolakan, k.tanggal_komplain, bt.name')
         ->from('komplain AS k')
         ->join('base_tab AS bt', 'k.tab_id=bt.id', 'inner')
         ->where('k.company_id', $this->company_id);
      if( $status_komplain != 'all' ) {
         $this->db->where('status', $status_komplain);
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('k.info_penolakan', $search)
            ->group_end();
      }
      $this->db->order_by('k.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'status' => $row->status,
               'komplain' => $row->komplain,
               'tanggal_komplain' => $this->date_ops->change_date($row->tanggal_komplain),
               'tab' => $row->name
            );
         }
      }
      return $list;
   }

   // get tab
   function get_tab(){
      $this->db->select('id, name')
         ->from('base_tab');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $row) {
            $list[$row->id] = $row->name; 
         }
      }   
      return $list;
   }

   // check tab id
   function check_tab_id($id){
      $this->db->select('id')
         ->from('base_tab')
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   // check komplain id
   function check_komplain_id($id){
      $this->db->select('id')
         ->from('komplain')
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }


}