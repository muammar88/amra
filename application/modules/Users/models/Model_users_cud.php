<?php
/**
*  -----------------------
*	Model sign up cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_users_cud extends CI_Model
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

   public function updateDataProfilAdministrator( $seed, $company_id ){
      # Starting Transaction
      $this->db->trans_start();
      # insert to company table
      $data = array();
      $data['name'] = $seed['name'];
      $data['email'] = $seed['email'];
      if( isset( $seed['photo'] ) && $seed['photo'] != '' ){
         $data['photo_profil'] = $seed['photo'];
      }
      if( isset( $seed['password'] ) && $seed['password'] != ''){
         $data['password'] = $seed['password'];
      }
      # Updating Data Company
      $this->db->where('id', $company_id);
      $this->db->update('company', $data);
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
      return $this->status;
   }

   # get personal id by user id
   function get_personal_id_by_user_id( $user_id ){
      $this->db->select('personal_id')
         ->from('base_users')
         ->where('user_id', $user_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return $q->row()->personal_id;
      }else{
         return 0;
      }
   }

   public function updateDataProfilUsers( $seed, $user_id ){
      # get personal id
      $personal_id = $this->get_personal_id_by_user_id( $user_id );
      # Starting Transaction
      $this->db->trans_start();
      # update to personal table
      $dataPersonal = array();
      $dataPersonal['fullname'] = $seed['name'];
      if( isset($seed['photo']) && $seed['photo'] != '' ){
         $dataPersonal['photo'] = $seed['photo'];
      }
      if( isset($seed['password']) AND $seed['password'] != '' ){
         $dataPersonal['password'] = password_hash($seed['password'] . '_' . $this->systems->getSalt(), PASSWORD_DEFAULT);
      }
      # update process
      $this->db->where('personal_id', $personal_id);
      $this->db->update('personal', $dataPersonal);
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
      return $this->status;
   }

   # insert saldo payment log
   function insert_payment_history( $data, $data_subscription_history, $company_id ){
     $this->write_log = 0;
      # Starting Transaction
     $this->db->trans_start();
     # insert saldo payment history
     $this->db->insert('payment_history', $data);
     // update
     $this->db->where('company_id', $company_id);
     $this->db->update('subscribtion_payment_history', $data_subscription_history);
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
        // $this->content = 'Melakukan penyimpanan log transaksi pembayaran biaya berlangganan aplikasi amra sebesar Rp '.number_format( explode('.', $data['gross_amount'])[0] );
     }
     return $this->status;
   }

   # update payment process
   function update_process_peyment($id){
      $this->write_log = 0;
      # Starting Transaction
      $this->db->trans_start();
      // update payment process
      $this->db->where('id', $id);
      $this->db->update('company', array('payment_process' => true));
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
        // $this->content = 'Melakukan penyimpanan log transaksi pembayaran biaya berlangganan aplikasi amra sebesar Rp '.number_format( explode('.', $data['gross_amount'])[0] );
      }
      return $this->status;
   }

   # insert renew subscribtion
   function insert_renew_subscribtion( $data ){
      # define write log
     $this->write_log = 0;
     # Starting Transaction
     $this->db->trans_start();
     # delete data subscribtion if exist
     $this->db->where('company_id', $data['company_id'])->where('payment_status', 'process')
              ->delete('subscribtion_payment_history');
     # insert data subsciption
     $this->db->insert('subscribtion_payment_history', $data);
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
