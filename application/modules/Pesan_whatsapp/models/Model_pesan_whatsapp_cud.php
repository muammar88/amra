<?php

/**
 *  -----------------------
 *	Model pesan whatsapp cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pesan_whatsapp_cud extends CI_Model
{
   private $company_id;
   private $status;
   private $content;
   private $error;
   private $write_log;


   private $pesan_whatsapp_id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      $this->error = 0;
      $this->write_log = 1;
   }

   # save pesan whatsapp
   function save_pesan_whatsapp($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert pesan whatsapp
      $this->db->insert('pesan_whatsapp', $data);
      # get pesan whatsapp id
      $this->pesan_whatsapp_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan pesan whatsapp dengan pesan_whatsapp_id ' .$this->pesan_whatsapp_id ;
      }
      return $this->status;
   }

   # save detail pesan whatsapp
   function save_detail_pesan_whatsapp($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert pesan whatsapp
      $this->db->insert('detail_pesan_whatsapp', $data);
      # get detail pesan whatsapp id
      $detail_pesan_whatsapp_id = $this->db->insert_id();
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
         // $this->content = 'Melakukan penambahan pesan whatsapp dengan detail pesan_whatsapp_id ' .$detail_pesan_whatsapp_id ;
      }
      return $this->status;
   }

   # update proses selesai proses pengiriman pesan whatsapp
   function update_selesai_proses_pengiriman($pesan_whatsapp_id){
      # Starting Transaction
      $this->db->trans_start();
      # update data pesan whatsapp
      $this->db->where('id', $pesan_whatsapp_id)
         ->where('company_id', $this->company_id)
         ->update('pesan_whatsapp', array('status_pesan' => 'selesai'));
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
      }
      return $this->status;
   }

   # pesan whatsapp id
   function pesan_whatsapp_id(){
      return $this->pesan_whatsapp_id;
   }

   # update status pesan whatsapp
   function update_status_pesan_whatsapp( $message_id, $data ){
      # Starting Transaction
      $this->db->trans_start();
      # update data detail pesan whatsapp
      $this->db->where('message_id', $message_id)
         ->where('company_id', $this->company_id)
         ->update('detail_pesan_whatsapp', $data);
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
      }
      return $this->status;
   }

   // /* Write log mst airlines */
   // public function __destruct()
   // {
   //    if ($this->write_log == 1) {
   //       if ($this->status == true) {
   //          if ($this->error == 0) {
   //             $this->syslog->write_log($this->content);
   //          }
   //       }
   //    }
   // }

}