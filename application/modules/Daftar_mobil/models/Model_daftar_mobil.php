<?php
/**
*  -----------------------
*	Model daftar mobil
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_mobil extends CI_Model
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

   function get_total_daftar_mobil($search){
      $this->db->select('id')
               ->from('mst_car')
               ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('car_name', $search)
            ->group_end();
      }
      $r 	= $this->db->get();
      return $r->num_rows();
   }

   // get index daftar mobil
   function get_index_daftar_mobil($limit = 6, $start = 0, $search = ''){
      $this->db->select('id,car_name,last_update')
               ->from('mst_car')
               ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('car_name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'car_name' => $row->car_name,
                            'last_update' => $row->last_update);
         }
      }
      return $list;
   }


   function get_info_edit_car($id){
      $this->db->select('id,car_name')
         ->from('mst_car')
         ->where('company_id', $this->company_id)
         ->where('id',$id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['car_name'] = $rows->car_name;
         }
      }
      return $list;
   }

   function check_car_id_exist($id){
      $this->db->select('id')
         ->from('mst_car')
         ->where('id',$id);
      $q =$this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }


}
