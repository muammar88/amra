<?php

/**
 *  -----------------------
 *	Model airlines cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_airlines_cud extends CI_Model
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

   function update_airlines($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # update data airlines
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('mst_airlines', $data['mst_airlines']);
      foreach ( $data['akun'] as $key => $value ) {
         # update data akun airlines
         $this->db->where('path', 'airlines:' . $key . ':' . $id)
            ->where('company_id', $this->company_id)
            ->update('akun_secondary', $value);
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
         $this->content = 'Melakukan perubahan data airlines dengan id airlines ' . $id . '.';
      }
      return $this->status;
   }

   function insert_airlines($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert airlines data
      $this->db->insert('mst_airlines', $data['mst_airlines']);
      # get airline id
      $airlines_id = $this->db->insert_id();
      # insert data akun airlines
      foreach ($data['akun'] as $key => $value) {
         $value['path'] = $value['path'] . $airlines_id;
         $this->db->insert('akun_secondary', $value);
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
         $this->content = 'Melakukan penambahan airlines dengan nama airlines ' . $data['mst_airlines']['airlines_name'] . ' dan dengan airline id ' . $airlines_id . '.';
      }
      return $this->status;
   }

   # delete airlines
   function delete_airlines($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      foreach (array('deposit', 'pendapatan', 'hpp') as $key => $value) {
         # delete airlines akun number
         $this->db->where('path', 'airlines:' . $value . ':' . $id)
            ->where('company_id', $this->company_id)
            ->delete('akun_secondary');
      }
      # delete mst airlines
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mst_airlines');
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
         $this->content = 'Melakukan penghapusan data airlines dengan airlines id ' . $id . '.';
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
