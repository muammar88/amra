<?php

/**
 *  -----------------------
 *	Model trans tiket CUD (Create Update Delete)
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_tipe_paket extends CI_Model
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

   function get_total_daftar_tipe_paket($search)
   {
      $this->db->select('id')
         ->from('mst_paket_type')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('paket_type_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_tipe_paket($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, paket_type_name, last_update')
         ->from('mst_paket_type')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('paket_type_name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'nama_tipe_paket' => $row->paket_type_name,
               'last_update' => $row->last_update
            );
         }
      }
      return $list;
   }

   function check_id_tipe_paket_exist($id)
   {
      $this->db->select('id')
         ->from('mst_paket_type')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_info_edit_tipe_paket($id)
   {
      $this->db->select('id, paket_type_name')
         ->from('mst_paket_type')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['nama_tipe_paket'] = $rows->paket_type_name;
         }
      }
      return $list;
   }
}
