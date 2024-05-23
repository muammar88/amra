<?php

/**
 *  -----------------------
 *	Model artikel cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_artikel_cud extends CI_Model
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

   # update artikel
   function update_artikel($id, $data)
   {
      # start transaction
      $this->db->trans_start();
      # update process
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('artikel', $data);
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
         $this->content = ' Memperbaharui data artikel dengan artikel id : ' . $id;
      }
      return $this->status;
   }

   # insert artikel
   function insert_artikel($data)
   {
      # start transaction
      $this->db->trans_start();
      # insert artikel
      $this->db->insert('artikel', $data);
      # get artikel id
      $artikel_id = $this->db->insert_id();
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
         $this->content = ' Menambahkan data artikel dengan id : ' . $artikel_id;
      }
      return $this->status;
   }

   # delete artikel
   function delete_artikel($id)
   {
      # start transaction
      $this->db->trans_start();
      # delete slider data
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('artikel');
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
         $this->content = ' Menghapus data artikel dengan id : ' . $id;
      }
      return $this->status;
   }

   /* Write log mst topik */
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
