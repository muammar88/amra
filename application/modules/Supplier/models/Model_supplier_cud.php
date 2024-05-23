<?php

/**
 *  -----------------------
 *	Model fasilitas la cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_supplier_cud extends CI_Model
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

   // // update fasilitas la
   // function update_fasilitas_la( $id, $data){
   //    # Starting Transaction
   //    $this->db->trans_start();
   //    # update data mst facilities la
   //    $this->db->where('id', $id)
   //             ->where('company_id', $this->company_id )
   //             ->update('mst_facilities_la', $data);
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
   //       $this->content = 'Melakukan perubahan data fasilitas la dengan id fasilitas la '.$id.'.';
   //    }
   //    return $this->status;
   // }
   //
   // // inset fasilitas la
   // function insert_fasilitas_la( $data ){
   //    # Starting Transaction
   //    $this->db->trans_start();
   //    # insert mst_facilities_la data
   //    $this->db->insert('mst_facilities_la', $data);
   //    # fasilitas la id
   //    $fasilitas_la_id = $this->db->insert_id();
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
   //       $this->content = 'Melakukan penambahan fasilitas baru dengan nama fasilitas '.$data['facilities_name'].' dan dengan fasilitas la id '.$fasilitas_la_id.'.';
   //    }
   //    return $this->status;
   // }
   //
   function delete_supplier($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete supplier
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mst_supplier');
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
         $this->content = 'Melakukan penghapusan data supplier dengan supplier id ' . $id . '.';
      }
      return $this->status;
   }

   function update_supplier($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data mst supplier
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_supplier', $data);
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
         $this->content = 'Melakukan perubahan data supplier dengan id supplier ' . $id . '.';
      }
      return $this->status;
   }

   function insert_supplier($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert mst_supplier data
      $this->db->insert('mst_supplier', $data);
      # supplier id
      $supplier_id = $this->db->insert_id();
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
         $this->content = 'Melakukan penambahan supplier baru dengan nama supplier ' . $data['supplier_name'] . ' dan dengan supplier id ' . $supplier_id . '.';
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
