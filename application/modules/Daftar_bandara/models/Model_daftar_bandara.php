<?php

/**
 *  -----------------------
 *	Model Bandara
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_bandara extends CI_Model
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

   function get_total_daftar_bandara( $search ){
      $this->db->select('a.id')
         ->from('mst_airport AS a')
         ->join('mst_city AS c', 'a.city_id=c.id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('a.airport_name', $search)
            ->orlike('c.city_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_bandara( $limit = 6, $start = 0, $search = '' ){
      $this->db->select('a.id, a.airport_name, c.city_name')
         ->from('mst_airport AS a')
         ->join('mst_city AS c', 'a.city_id=c.id', 'inner')
         ->where('a.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
           ->like('a.airport_name', $search)
           ->orlike('c.city_name', $search)
           ->group_end();
      }
      $this->db->order_by('a.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'airport_name' => $row->airport_name,
                            'city_name' => $row->city_name);
         }
      }
      return $list;
   }

   function get_list_city(){
      $this->db->select('id, city_name, city_code')
         ->from('mst_city')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'city_name' => $row->city_name,
               'city_code' => $row->city_code
            );
         }
      }
      return $list;
   }

   function check_city_id_exist( $city_id )  {
      $this->db->select('id')
         ->from('mst_city')
         ->where('id', $city_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # check bandara is exist
   function check_bandara_id_exist( $bandara_id ){
      $this->db->select('id')
         ->from('mst_airport')
         ->where('id', $bandara_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # get value
   function get_value( $id ) {
      $this->db->select('id, airport_name, city_id')
         ->from('mst_airport')
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['airport_name'] = $rows->airport_name;
            $list['city_id'] = $rows->city_id;
         }
      }
      return $list;
   }

}
