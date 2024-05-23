<?php
/**
*  -----------------------
*	Model sign up cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_sign_in_cud extends CI_Model
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

   function insert_log_login($level, $userData, $code) {
      //echo "masuk";
      if( $level == 'administrator') {
         // echo "yy";
         $kode = '';
         $this->db->select('name, code')
                  ->from('company')
                  ->where('email', $userData['email']);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ){
            $nama_perusahaan = '';
            foreach ( $q->result() as $rows ) {
               $nama_perusahaan = $rows->name;
               $kode = $rows->code;
            }
            $this->syslog->write_log( 'Adminstrator Perusahaan ' . $nama_perusahaan . ' dengan code ' . $kode . ' melakukan login ke dalam system' );
         }      
      } else {
         $this->db->select('p.fullname, c.name, c.code')
                     ->from('personal AS p')
                     ->join('company AS c', 'p.company_id=c.id', 'inner')
                     ->where('p.personal_id', $userData['personal_id']);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            $fullname = '';
            $kode = '';
            $nama_perusahaan = '';
            foreach ( $q->result() as $rows ) {
               $fullname = $rows->fullname;
               $nama_perusahaan = $rows->name;
                $kode = $rows->code;
            }
            $this->syslog->write_log( 'Staff Perusahaan ' . $nama_perusahaan . ' dengan nama ' . $fullname . ' melakukan login ke dalam system' );
         }
      }
   }

   # verify company
   function verify_company( $id, $data_subscribing_payment_history, $data_company ) {
      # disable write log
      $this->write_log = 0;
      # start update verfied_status
      $this->db->trans_start();
      # subscribing payment history
      $this->db->insert('subscribing_payment_history', $data_subscribing_payment_history);
      # update company
      $this->db->where('id', $id)->update('company', $data_company);
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
         $this->content = '.';
      }
      return $this->status;
   }

   function update_verified($data, $email){
      # disable write log
      $this->write_log = 0;
      # start update verfied_status
      $this->db->trans_start();
      # update company
      $this->db->where('email', $email)->update('company', $data);
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
         $this->content = '.';
      }
      return $this->status;
   }

   # remove otp
   function remove_otp( $personal_id ) {
      # disable write log
      $this->write_log = 0;
      # start update
      $this->db->trans_start();
      # update otp personal
      $this->db->where('personal_id', $personal_id)
               ->update('personal', array('otp' => '0',
                                          'otp_expire' => '0000-00-00 00:00:00'));
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
         $this->content = '.';
      }
      return $this->status;
   }

   # update payment process
   function update_payment_process( $code ) {
      # disable write log
      $this->write_log = 0;
      # start update
      $this->db->trans_start();
      # update otp personal
      $this->db->where('code', $code)
               ->update('company', array('payment_process' => 'true'));
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
         $this->content = '.';
      }
      return $this->status;
   }

}
