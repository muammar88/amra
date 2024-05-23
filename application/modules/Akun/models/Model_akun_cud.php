<?php

/**
 *  -----------------------
 *	Model akun cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_akun_cud extends CI_Model
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

   # update akun
   function update_akun($id, $data, $data_saldo)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data akun
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('akun_secondary', $data);
      # delete data saldo
      $this->db->where('company_id', $this->company_id)
         ->where('akun_secondary_id', $id)
         ->delete('saldo');
      # insert data saldo
      $this->db->insert('saldo', $data_saldo);
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
         $this->content = 'Melakukan perubahan data akun';
      }
      return $this->status;
   }
   # insert data akun
   function insert_akun($data, $data_saldo)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data akun
      $this->db->insert('akun_secondary', $data);
      # insert data saldo
      $data_saldo['akun_secondary_id'] = $this->db->insert_id();
      # insert process
      $this->db->insert('saldo', $data_saldo);
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
         $this->content = 'Melakukan penambahan data akun';
      }
      return $this->status;
   }

   # delete akun
   function delete_akun($id, $nomor_akun)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete data akun_secondary
      $this->db->where('company_id', $this->company_id)
         ->where('id', $id)
         ->delete('akun_secondary');
      # delete data saldo
      $this->db->where('company_id', $this->company_id)
         ->where('akun_secondary_id', $id)
         ->delete('saldo');
      # delete data jurnal debet
      $this->db->where('company_id', $this->company_id)
         ->where('akun_debet', $nomor_akun)
         ->delete('jurnal');
      # delete data jurnal kredit
      $this->db->where('company_id', $this->company_id)
         ->where('akun_kredit', $nomor_akun)
         ->delete('jurnal');
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
         $this->content = 'Melakukan penghapusan data akun dengan nomor akun ' . $nomor_akun;
      }
      return $this->status;
   }

   # update saldo
   function update_saldo_akun($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete data saldo
      $this->db->where('company_id', $this->company_id)
         ->where('akun_secondary_id', $id)
         ->delete('saldo');
      # insert new saldo
      $this->db->insert('saldo', $data);
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
         $this->content = 'Melakukan penghapusan data akun dengan akun secondary id' . $id;
      }
      return $this->status;
   }

   # close book
   function close_book( $data, $periode_name ) {
      # Starting Transaction
      $this->db->trans_start();
      // inser new periode
      $this->db->insert( 'jurnal_periode', array('company_id' => $this->company_id, 
                                                 'nama_periode' => $periode_name, 
                                                 'input_date' => date('Y-m-d'),
                                                 'last_update' => date('Y-m-d')));
      # get periode id
      $periode_id = $this->db->insert_id();
      # update periode saldo
      $this->db->where('periode', 0)
               ->where('company_id', $this->company_id)
               ->update( 'saldo', array('periode' => $periode_id ) );
      // update periode jurnal
      $this->db->where('periode_id', 0)
               ->where('company_id', $this->company_id)
               ->update( 'jurnal', array('periode_id' => $periode_id ) );
      # insert new saldo
      foreach ( $data as $key => $value ) {
         $this->db->insert( 'saldo', $value );
      }
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
         $this->content = 'Melakukan Proses Tutup Buku ';
      }
      return $this->status;
   }

   /*
      Reopen Book
   */
   function reopen_book($id) {
      # Starting Transaction
         $this->db->trans_start();
      # delete jurnal
        $this->db->where('periode_id', 0)
            ->where('company_id', $this->company_id)
            ->delete('jurnal');
      # delete saldo
        $this->db->where('periode', 0)
            ->where('company_id', $this->company_id)
            ->delete('saldo');    
      # update last to 0
      # jurnal
      $this->db->where('periode_id', $id)
            ->where('company_id', $this->company_id)
            ->update('jurnal', array('periode_id' => 0));
        # saldo 
      $this->db->where('periode', $id)
            ->where('company_id', $this->company_id)
            ->update('saldo', array('periode' => 0));
      # delete periode
      $this->db->where('id', $id)
            ->where('company_id', $this->company_id)
            ->delete('jurnal_periode');   
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
         $this->content = 'Melakukan Proses Buka Buku ';
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
