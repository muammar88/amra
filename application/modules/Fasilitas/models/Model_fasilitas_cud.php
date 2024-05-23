<?php

/**
 *  -----------------------
 *	Model airlines cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_fasilitas_cud extends CI_Model
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

   function update_fasilitas($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data airlines
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_facilities', $data);
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
         $this->content = 'Melakukan perubahan data fasilitas dengan id fasilitas ' . $id . '.';
      }
      return $this->status;
   }

   function insert_fasilitas($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert airlines data
      $this->db->insert('mst_facilities', $data);
      # fasilitas id
      $fasilitas_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan fasilitas baru dengan nama fasilitas ' . $data['facilities_name'] . ' dan dengan fasilitas id ' . $fasilitas_id . '.';
      }
      return $this->status;
   }

   # delete fasilitas
   function delete_fasilitas($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete fasilitas
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mst_facilities');
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
         $this->content = 'Melakukan penghapusan data fasilitas dengan fasilitas id ' . $id . '.';
      }
      return $this->status;
   }

   /* Write log mst airlines */
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
