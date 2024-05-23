<?php

/**
 *  -----------------------
 *	Model daftar agen cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_agen_cud extends CI_Model
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

   function delete_agen($id, $personal_id)
   {
      # Starting Transaction
      $this->db->trans_start();

      # delete request agen
      $this->db->where('member_id', $personal_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('agen_request');

      // cari fee keagenan id di detail fee keagenan
      $this->db->select('id, fee_keagenan_id')
         ->from('detail_fee_keagenan')
         ->where('agen_id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $this->db->select('id')
               ->from('detail_fee_keagenan')
               ->where('company_id', $this->company_id)
               ->where('fee_keagenan_id', $rows->fee_keagenan_id)
               ->where('agen_id != ', $id);
            $r = $this->db->get();
            if( $r->num_rows() > 0 ){ # delete fee keagenan by detail fee keagenan id;
               $this->db->where('id', $rows->id)
                        ->where('company_id', $this->company_id)
                        ->delete('detail_fee_keagenan');
            }else{ # delete semua
               // delete detail fee keagenan
               $this->db->where('id', $rows->id)
                        ->where('company_id', $this->company_id)
                        ->delete('detail_fee_keagenan');
               // delete fee keageanan
               $this->db->where('id', $rows->fee_keagenan_id)
                        ->where('company_id', $this->company_id)
                        ->delete('fee_keagenan');
               // update ke nol fee_keagenan id di pool table
               $this->db->where('fee_keagenan_id', $rows->fee_keagenan_id)
                        ->where('company_id', $this->company_id)
                        ->update('paket_transaction', array('fee_keagenan_id' => 0,
                                                            'last_update' => date('Y-m-d H:i:s') ) );
               // update ke nol fee_keagenan id di pool table
               $this->db->where('fee_keagenan_id', $rows->fee_keagenan_id)
                        ->where('company_id', $this->company_id)
                        ->update('pool', array('fee_keagenan_id' => 0,
                                               'last_update' => date('Y-m-d H:i:s') ) );
            }
         }
      }

      # delete agen
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('agen');

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penghapusan status agen untuk member id ' . $personal_id . ' dengan agen id ' . $id . '.';
      }
      return $this->status;
   }

   # update data agen
   function upgrade_level_agen($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data agen
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('agen', array(
            'level_agen' => 'cabang',
            'last_update' => date('Y-m-d H:i:s')
         ));
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan perubahan level agen ke level cabang';
      }
      return $this->status;
   }

   function update_level_keagenan($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update data agen
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('level_keagenan', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan perubahan level keagenan';
      }
      return $this->status;
   }

   # insert level keagenan
   function insert_level_keagenan($data){
      # start transaction
     $this->db->trans_start();
     # insert level keagenan
     $this->db->insert('level_keagenan', $data);
     # get level keagenan id
     $level_keagenan_id = $this->db->insert_id();
     # Transaction Complete
     $this->db->trans_complete();
     # Filter Status
     if ($this->db->trans_status() === FALSE) {
        # Something Went Wrong.
        $this->db->trans_rollback();
        $this->status = FALSE;
        $this->error = 1;
     } else {
        # Transaction Commit
        $this->db->trans_commit();
        $this->status = TRUE;
        $this->content = ' Menambahkan data level keagenan dengan id : ' . $level_keagenan_id;
     }
     return $this->status;
   }

   # delete level keagenan
   function delete_level_keagenan($id){
      # Starting Transaction
      $this->db->trans_start();
      # delete request agen
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('level_keagenan');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penghapusan level keagenan dengan level keagenan id ' . $id . '.';
      }
      return $this->status;
   }

   /* Write log master data*/
   public function __destruct()
   {
      if ($this->write_log == 1) {
         if ($this->status == true) {
            if ($this->error == 0) {
               $this->syslog->write_log($this->content);
            }
         }
      }
   }
}
