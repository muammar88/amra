<?php

/**
 *  -----------------------
 *	Model request keagenan cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_request_keagenan_cud extends CI_Model
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


   function approve($id, $data, $data_agen)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data agen request
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('agen_request', $data);
      # insert agen
      $this->db->insert('agen', $data_agen);
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
         $this->content = 'Melakukan persetujuan terhadap request keagenan member id ' . $data_agen['personal_id'] . '.';
      }
      return $this->status;
   }

   function decline($id, $agen_member_id, $data, $member_id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data agen request
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('agen_request', $data);
      # delete if agen was exist
      if (count($agen_member_id) > 0) {
         # delete fee keagenan
         $this->db->where('agen_id', $agen_member_id['agen_id']);
         $this->db->where('company_id', $this->company_id);
         $this->db->delete('fee_keagenan');
         # update paket with agen
         $this->db->where('agen_id', $agen_member_id['agen_id'])
            ->where('company_id', $this->company_id)
            ->update('paket_transaction', array(
               'agen_id' => 0,
               'last_update' => date('Y-m-d H:i:s')
            ));
         # delete agen
         $this->db->where('id', $agen_member_id['agen_id']);
         $this->db->where('company_id', $this->company_id);
         $this->db->delete('agen');
      }
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
         $this->content = 'Melakukan penolakan terhadap request keagenan member id ' . $member_id . '.';
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
