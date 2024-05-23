<?php

/**
 *  -----------------------
 *	Model daftar kota
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_kota extends CI_Model
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

   function get_total_daftar_kota($search)
   {
      $this->db->select('id')
         ->from('mst_city')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('city_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_kota($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id,city_name,city_code,last_update')
         ->from('mst_city')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('city_name', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'city_name' => $row->city_name,
               'city_code' => $row->city_code,
               'last_update' => $row->last_update
            );
         }
      }
      return $list;
   }

   function check_id_city_exist($id)
   {
      $this->db->select('id')
         ->from('mst_city')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }


   function get_info_city($id)
   {
      $this->db->select('id, city_name, city_code')
         ->from('mst_city')
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['id'] = $rows->id;
            $list['city_name'] = $rows->city_name;
            $list['city_code'] = $rows->city_code;
         }
      }
      return $list;
   }

   function check_city_was_use($id)
   {
      $exist = 0;
      // check in mst hotel table
      $this->db->select('id')
         ->from('mst_hotel')
         ->where('city_id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $exist = 1;
      }
      // check in bus table
      $this->db->select('id')
         ->from('bus')
         ->where('city_id', $id)
         ->where('company_id', $this->company_id);
      $r = $this->db->get();
      if ($r->num_rows() > 0) {
         $exist = 1;
      }
      if ($exist == 1) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   //
   // # update data bank
   // function update_bank($id, $data, $data_akun, $kode_bank){
   //    # Starting Transaction
   //    $this->db->trans_start();
   //    # update data bank
   //    $this->db->where('id', $id)
   //             ->where('company_id', $this->company_id )
   //             ->update('mst_bank', $data);
   //    # update data akun
   //    $this->db->where('path', 'bank:kodebank:'.$kode_bank)
   //             ->where('company_id', $this->company_id )
   //             ->update('akun_secondary', $data_akun);
   //    # Transaction Complete
   //    $this->db->trans_complete();
   //    # Filter Status
   //    if ( $this->db->trans_status() === FALSE )
   //    {
   //        # Something Went Wsrong.
   //        $this->db->trans_rollback();
   //        $this->status = FALSE;
   //        $this->error = 1;
   //    } else {
   //       # Transaction Commit
   //       $this->db->trans_commit();
   //       $this->status = TRUE;
   //       $this->content = 'Melakukan perubahan data bank dengan kode bank '.$kode_bank.'.';
   //    }
   //    return $this->status;
   // }
   //
   // function insert_bank($data, $data_akun){
   //    # Starting Transaction
   //    $this->db->trans_start();
   //    # insert personal data
   //    $this->db->insert('mst_bank', $data);
   //    # insert data akun
   //    $this->db->insert('akun_secondary', $data_akun);
   //    # Transaction Complete
   //    $this->db->trans_complete();
   //    # Filter Status
   //    if ( $this->db->trans_status() === FALSE )
   //    {
   //        # Something Went Wsrong.
   //        $this->db->trans_rollback();
   //        $this->status = FALSE;
   //        $this->error = 1;
   //    } else {
   //       # Transaction Commit
   //       $this->db->trans_commit();
   //       $this->status = TRUE;
   //       $this->content = 'Melakukan penambahan bank dengan kode bank '.$data['kode_bank'].'.';
   //    }
   //    return $this->status;
   // }
   //
   // // delete bank
   // function delete_bank($id, $kode_bank){
   //    # Starting Transaction
   //    $this->db->trans_start();
   //    # delete bank akun number
   //    $this->db->where('path', 'bank:kodebank:'.$kode_bank);
   //    $this->db->where('company_id', $this->company_id );
   //    $this->db->delete('akun_secondary');
   //    # delete mst bank
   //    $this->db->where('id', $id);
   //    $this->db->where('company_id', $this->company_id );
   //    $this->db->delete('mst_bank');
   //    # Transaction Complete
   //    $this->db->trans_complete();
   //    # Filter Status
   //    if ( $this->db->trans_status() === FALSE )
   //    {
   //        # Something Went Wsrong.
   //        $this->db->trans_rollback();
   //        $this->status = FALSE;
   //        $this->error = 1;
   //    } else {
   //       # Transaction Commit
   //       $this->db->trans_commit();
   //       $this->status = TRUE;
   //       $this->content = 'Melakukan penghapusan bank dengan kode bank '.$kode_bank.'.';
   //    }
   //    return $this->status;
   // }

   // /* Write log master data*/
   // public function __destruct()
   // {
   //    if( $this->write_log == 1 ){
   //       if( $this->status == true){
   //          if ( $this->error == 0 ) {
   //             $this->syslog->write_log( $this->content );
   //          }
   //       }
   //    }
   // }
}
