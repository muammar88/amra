<?php

/**
 *  -----------------------
 *	Model template pesan whatsapp cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_template_pesan_whatsapp_cud extends CI_Model
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

   # update template pesan
   function update_template_pesan( $id, $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update data template pesan whatsapp
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id)
               ->update('template_pesan_whatsapp', $data);
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
         $this->content = 'Melakukan perubahan data template pesan whatsapp dengan id template ' . $id . '.';
      }
      return $this->status;
   }

   # insert template pesan whatsapp
   function insert_template_pesan($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert template pesan whatsapp
      $this->db->insert('template_pesan_whatsapp', $data);
      # template id
      $template_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan data template pesan whatsapp dengan id template ' . $template_id . '.';
      }
      return $this->status;
   }

   # delete template pesan whatsapp
   function delete_template_pesan($id){
      # Starting Transaction
      $this->db->trans_start();
      # delete template pesan whatsapp
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('template_pesan_whatsapp');
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
         $this->content = 'Melakukan penghapusan data template pesan whatsapp dengan template id ' . $id . '.';
      }
      return $this->status;
   }

}