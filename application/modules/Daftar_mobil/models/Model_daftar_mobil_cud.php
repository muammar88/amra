<?php
/**
*  -----------------------
*	Model daftar mobil cud
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_mobil_cud extends CI_Model
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

   # insert mobil
   function insert_car($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert personal data mst car
      $this->db->insert('mst_car', $data);
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
         $this->content = 'Melakukan penambahan data mobil dengan nama mobil '.$data['car_name'].'.';
      }
      return $this->status;
   }

   # update data mobil
   function update_car($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update data mst car
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id )
               ->update('mst_car', $data);
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
         $this->content = 'Melakukan perubahan data mobil dengan nama mobil '.$data['car_name'].'.';
      }
      return $this->status;
   }

   // delete mst car
   function delete_car($id){
      # Starting Transaction
      $this->db->trans_start();
      # delete mst car
      $this->db->where('id', $id)
               ->where('company_id', $this->company_id )
               ->delete('mst_car');
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
         $this->content = 'Melakukan penghapusan data mobil dengan id mobil '.$id.'.';
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
