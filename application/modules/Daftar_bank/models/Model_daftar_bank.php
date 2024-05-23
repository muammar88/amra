<?php

/**
 *  -----------------------
 *	Model trans tiket CUD (Create Update Delete)
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_bank extends CI_Model
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

   // get total daftar bank
   function get_total_daftar_bank($search)
   {
      $this->db->select('id')
         ->from('mst_bank')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('kode_bank', $search)
            ->or_like('nama_bank', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // (SELECT CONCAT_WS(\'$\', nomor_akun_secondary, nama_akun_secondary, path )
   //    FROM akun_secondary
   //    WHERE path=CONCAT(\'bank:kodebank:\', m.kode_bank)
   //          AND company_id=' . $this->company_id . ') AS akun


   // get index daftar bank
   function get_index_daftar_bank($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, kode_bank, nama_bank')
         ->from('mst_bank AS m')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('kode_bank', $search)
            ->or_like('nama_bank', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            // $exp = explode('$', $row->akun);
            $list[] = array(
               'id' => $row->id,
               'kode_bank' => $row->kode_bank,
               'nama_bank' => $row->nama_bank,
               // 'nomor_akun' => $exp[0],
               // 'nama_akun' => $exp[1],
               // 'path_akun' => $exp[2]
            );
         }
      }
      return $list;
   }

   // check exist kode bank
   function check_exist_kode_bank($kode_bank, $id = 0)
   {
      $this->db->select('id')
         ->from('mst_bank')
         ->where('kode_bank', $kode_bank)
         ->where('company_id', $this->company_id);
      if ($id != 0) {
         $this->db->where('id !=', $id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function generated_kode_akun_bank()
   {
      $this->db->select('nomor_akun_secondary, path')
         ->from('akun_secondary')
         ->like('path', 'bank')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $main_bank_akun = 0;
         $secondary_bank_akun = array();
         foreach ($q->result() as $row) {
            if ($row->path == 'bank') {
               $main_bank_akun = $row->nomor_akun_secondary;
            } else {
               $secondary_bank_akun[] = $row->nomor_akun_secondary;
            }
         }
         $looping = true;
         $new_akun_bank = $main_bank_akun;
         while ($looping) {
            $new_akun_bank++;
            if (!in_array($new_akun_bank, $secondary_bank_akun)) {
               $looping = false;
            }
         }
         return $new_akun_bank;
      } else {
         return '11021';
      }
   }

   function get_kode_bank($id)
   {
      $this->db->select('kode_bank')
         ->from('mst_bank')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return $q->row()->kode_bank;
      } else {
         return  '';
      }
   }

   function check_id_bank_exist($id)
   {
      $this->db->select('id')
         ->from('mst_bank')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return  TRUE;
      } else {
         return FALSE;
      }
   }

   function get_info_edit_bank($id)
   {
      $this->db->select('id,kode_bank,nama_bank')
         ->from('mst_bank')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $data = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $data['id'] = $rows->id;
            $data['kode_bank'] = $rows->kode_bank;
            $data['nama_bank'] = $rows->nama_bank;
         }
      }
      return $data;
   }

   function get_akun_bank_by_id($id)
   {
      $this->db->select('(SELECT nomor_akun_secondary
                              FROM akun_secondary
                              WHERE path=CONCAT(\'bank:kodebank:\', m.kode_bank)
                                    AND company_id=' . $this->company_id . ') AS nomor_akun ')
         ->from('mst_bank AS m')
         ->where('m.id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return $q->row()->nomor_akun;
      } else {
         return '';
      }
   }

   function checking_akun_in_jurnal($id)
   {
      $nomor_akun = $this->get_akun_bank_by_id($id);
      if ($nomor_akun != '') {
         $this->db->select('id, akun_debet, akun_kredit')
            ->from('jurnal')
            ->where('akun_debet', $nomor_akun)
            ->or_where('akun_kredit', $nomor_akun);
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            return true;
         } else {
            return false;
         }
      } else {
         return false;
      }
   }
}
