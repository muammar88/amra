<?php

/**
 *  -----------------------
 *	Model notif cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_notif_cud extends CI_Model
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

   function insert_notif( $data )
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert notif
      $this->db->insert('notif', $data);
      # get insert notif id
      $notif_id = $this->db->insert_id();
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
         $this->content = 'Melakukan insert data pesan baru dengan notif id ' . $notif_id . '.';
      }
      return $this->status;
   }

   function delete( $id ){
       # Starting Transaction
      $this->db->trans_start();
      # delete notif reader
      $this->db->where('notif_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('notif_reader');
      # delete notif
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('notif');   
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
         $this->content = 'Melakukan penghapusan data notif dengan notif id ' . $id . '.';
      }
      return $this->status;
   }

}