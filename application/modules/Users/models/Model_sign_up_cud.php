<?php
/**
*  -----------------------
*	Model sign up cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_sign_up_cud extends CI_Model
{
   private $status;
   private $content;
   private $error;
   private $write_log;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
      $this->write_log = 1;
   }

   public function insert_new_company( $data, $data_subscription ){
      # define write log
      $this->write_log = 0;
      # Starting Transaction
      $this->db->trans_start();
      # insert to company table
      $this->db->insert('company', $data);
      # get company id
      $data_subscription['company_id'] = $this->db->insert_id();
      # insert data subsciption
      $this->db->insert('subscribtion_payment_history', $data_subscription);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
          # Something Went Wrong.
          $this->db->trans_rollback();
          $this->status = FALSE;
          $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      // $this->write_log = FALSE;
      return $this->status;
   }

   // update verified status
   function update_verified($id, $data){
      # disable write log
      $this->write_log = 0;
      # start update verfied_status
      $this->db->trans_start();
      # update verfied status process
      $this->db->where('id', $id);
      $this->db->update('company', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ( $this->db->trans_status() === FALSE )
      {
          # Something Went Wsrong.
          $this->db->trans_rollback();
          $this->status = FALSE;
          $this->error = 1;
      } else {

         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan verifikasi akun.';
      }
      return $this->status;
   }

   /* Write log master data*/
   public function __destruct()
   {
      if( $this->write_log == 1 ){
         if( $this->status == true){
            if ( $this->error == 0 ) {
               $this->syslog->write_log( $this->content );
            }
         }
      }
   }
}
