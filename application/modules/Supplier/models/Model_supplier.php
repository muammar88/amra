<?php

/**
 *  -----------------------
 *	Model tipe paket la
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_supplier extends CI_Model
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

   # get total daftar supplier
   function get_total_daftar_supplier($search)
   {
      $this->db->select('id')
         ->from('mst_supplier')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('supplier_name', $search)
            ->or_like('rekening_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get daftar supplier
   function get_index_daftar_supplier($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('ms.id, ms.supplier_name, ms.address, ms.rekening_number, mb.nama_bank')
         ->from('mst_supplier AS ms')
         ->join('mst_bank AS mb', 'ms.bank_id=mb.id', 'inner')
         ->where('ms.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('ms.supplier_name', $search)
            ->or_like('ms.rekening_number', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'supplier_name' => $row->supplier_name,
               'address' => $row->address,
               'rekening_number' => $row->rekening_number,
               'nama_bank' => $row->nama_bank
            );
         }
      }
      return $list;
   }

   # get list bank
   function get_list_bank()
   {
      $this->db->select('id, nama_bank')
         ->from('mst_bank')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 'nama_bank' => $rows->nama_bank);
         }
      }
      return $list;
   }

   function check_supplier_id_exist($id)
   {
      $this->db->select('id')
         ->from('mst_supplier')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function check_bank_id_exist($bank_id)
   {
      $this->db->select('id')
         ->from('mst_bank')
         ->where('id', $bank_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   # get data supplier
   function get_data_supplier($id)
   {
      $this->db->select('id, supplier_name, address, bank_id, rekening_number')
         ->from('mst_supplier')
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['supplier_name'] = $rows->supplier_name;
            $list['address'] = $rows->address;
            $list['bank_id'] = $rows->bank_id;
            $list['rekening_number'] = $rows->rekening_number;
         }
      }
      return $list;
   }
}
