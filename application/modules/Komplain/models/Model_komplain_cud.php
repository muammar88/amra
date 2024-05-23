<?php
/**
*  -----------------------
*	Model komplain cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_komplain_cud extends CI_Model
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

   // komplain cud
   public function insert_komplain( $data ){
       # start transaction
      $this->db->trans_start();
      # insert komplain
      $this->db->insert('komplain', $data['komplain'] );
      # define komplain id
      $komplain_id = $this->db->insert_id();
      # get komplain id
      $data['bukti_error']['komplain_id'] = $komplain_id;
      # insert komplain
      $this->db->insert('bukti_error', $data['bukti_error'] );
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
         $this->content = ' Menambahkan data komplain dengan id : ' . $komplain_id;
      }
      return $this->status;
   }

   // delete komplain
   function delete_komplain($id, $bukti_error_id) {
      # start transaction
      $this->db->trans_start();
      # delete bukti error
      $this->db->where('id', $bukti_error_id)
         ->where('company_id', $this->company_id)
         ->delete('bukti_error');
      # delete komplain
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('komplain');
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
         $this->content = ' Menghapus data komplain dengan id : ' . $id;
      }
      return $this->status;
   }

}