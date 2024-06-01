<?php

/**
 *  -----------------------
 *	Model kostumer paket la
 *	Created by Muammar Kadafi
 *  -----------------------
 */

   // function get_total_server_side($limit = 6, $start = 0, $search = '', $status_komplain){
   //    $this->db->select('k.id, k.status, k.komplain, k.info_penolakan, k.tanggal_komplain, bt.name')
   //       ->from('komplain AS k')
   //       ->join('base_tab AS bt', 'k.tab_id=bt.id', 'inner')
   //       ->where('k.company_id', $this->company_id);
   //    if( $status_komplain != 'all' ) {
   //       $this->db->where('status', $status_komplain);
   //    }
   //    if ($search != '' or $search != null or !empty($search)) {
   //       $this->db->group_start()
   //          ->like('k.info_penolakan', $search)
   //          ->group_end();
   //    }
   //    $this->db->order_by('k.id', 'desc')->limit($limit, $start);
   //    $q = $this->db->get();
   //    $list = array();
   //    if ($q->num_rows() > 0) {
   //       foreach ($q->result() as $row) {
   //          $list[] = array(
   //             'id' => $row->id,
   //             'status' => $row->status,
   //             'komplain' => $row->komplain,
   //             'tanggal_komplain' => $this->date_ops->change_date($row->tanggal_komplain),
   //             'tab' => $row->name
   //          );
   //       }
   //    }
   //    return $list;
   // }


if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_kostumer_paket_la extends CI_Model
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


   function check_kostumer_id($id){
      $this->db->select('name, mobile_number')
         ->from('paket_la_costumer')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      // check number rows
      if ($q->num_rows() > 0) {
         return true;
      }else{
         return false;
      }
   }

   // get total server side
   function get_total_server_side($search)
   {
      $this->db->select('id, name, mobile_number, address')
         ->from('paket_la_costumer')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('name', $search)
            ->or_like('mobile_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index komplain
   function get_index_server_side($limit = 6, $start = 0, $search = '', $status_komplain){
       $this->db->select('id, name, mobile_number, address')
         ->from('paket_la_costumer')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('name', $search)
            ->or_like('mobile_number', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'name' => $row->name,
               'mobile_number' => $row->mobile_number,
               'address' => $row->address,
            );
         }
      }
      return $list;
   }


   function get_info_edit( $id ){
      $this->db->select('id, name, mobile_number, address')
         ->from('paket_la_costumer')
         ->where('company_id',$this->company_id)
         ->where('id',$id);
      $q = $this->db->get();
      $feedBack = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $feedBack['id'] = $rows->id;
            $feedBack['name'] = $rows->name;
            $feedBack['mobile_number'] = $rows->mobile_number;
            $feedBack['address'] = $rows->address;

         }
      }
      return  $feedBack;
   }

}
