<?php

/**
 *  -----------------------
 *	Model airlines
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_fasilitas extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_daftar_fasilitas($search)
   {
      $this->db->select('id')
         ->from('mst_facilities')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('facilities_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_fasilitas($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, facilities_name, input_date')
         ->from('mst_facilities')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('facilities_name', $search)
            ->group_end();
      }
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array(
               'id' => $rows->id,
               'nama_fasilitas' => $rows->facilities_name,
               'input_date' => $rows->input_date
            );
         }
      }
      return $list;
   }

   function check_id_fasilitas_exist($id)
   {
      $this->db->select('id')
         ->from('mst_facilities')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return  TRUE;
      } else {
         return FALSE;
      }
   }

   function get_info_edit_fasilitas($id)
   {
      $this->db->select('id,facilities_name')
         ->from('mst_facilities')
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['nama_fasilitas'] = $rows->facilities_name;
         }
      }
      return $list;
   }
}
