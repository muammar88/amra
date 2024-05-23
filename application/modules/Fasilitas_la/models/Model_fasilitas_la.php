<?php

/**
 *  -----------------------
 *	Model fasilitas la
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_fasilitas_la extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_fasilitas_la($search)
   {
      $this->db->select('id')
         ->from('mst_facilities_la')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('facilities_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_fasilitas_la($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('ma.id, h.header_name, ma.facilities_name, ma.price, ma.last_update')
         ->from('mst_facilities_la AS ma')
         ->join('mst_header_facilities_la AS h', 'ma.header_id=h.id', 'inner')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ma.facilities_name', $search)
            ->or_like('h.header_name', $search)
            ->group_end();
      }
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'header' => $rows->header_name,
               'nama_fasilitas' => $rows->facilities_name,
               'price' => $rows->price,
               'last_update' => $rows->last_update
            );
         }
      }
      return $list;
   }

   function get_list_header()
   {
      $this->db->select('id,header_name')
         ->from('mst_header_facilities_la');
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'header_name' => $rows->header_name
            );
         }
      }
      return $list;
   }

   function check_id_fasilitas_la_exist($id)
   {
      $this->db->select('id')
         ->from('mst_facilities_la')
         ->where('company_id', $this->company_id)
         ->where('id',  $id);
      $q = $this->db->get();
      if ($q->num_rows() >  0) {
         return true;
      } else {
         return false;
      }
   }

   function check_id_header_exist($id)
   {
      $this->db->select('id')
         ->from('mst_header_facilities_la')
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   // get info edit fasilitas la
   function get_info_edit_fasilitas_la($id)
   {
      $this->db->select('id, header_id, facilities_name, price')
         ->from('mst_facilities_la')
         ->where('id', $id);
      $list = array();
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['header_id'] = $rows->header_id;
            $list['nama_fasilitas_la'] = $rows->facilities_name;
            $list['price'] = $rows->price;
         }
      }
      return $list;
   }
}
