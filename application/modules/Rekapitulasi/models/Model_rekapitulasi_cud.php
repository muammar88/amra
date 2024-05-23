<?php

/**
 *  -----------------------
 *	Model airlines cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_rekapitulasi_cud extends CI_Model
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

   function insert_rekapitulasi($data, $tiket_transaction_id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert recapitulation data
      $this->db->insert('recapitulation', $data);
      # get transaction passport id
      $recapitulation_id = $this->db->insert_id();
      $stringTiketTransaksiId = '';
      # insert recapitulation detail
      foreach ($tiket_transaction_id as $key => $value) {
         $data_detail = array();
         $data_detail['company_id'] = $this->company_id;
         $data_detail['recapitulation_id'] = $recapitulation_id;
         $data_detail['tiket_transaction_id'] = $value;
         # insert recapitulation_detail table
         $this->db->insert('recapitulation_detail', $data_detail);
         if ($stringTiketTransaksiId != '') {
            $stringTiketTransaksiId .= ',' . $value;
         }
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
         $this->content = 'Melakukan rekapitulasi transaksi tiket dengan invoice rekapitulasi ' . $data['recapitulation_number'] . ' dan dengan transaksi tiket id (' . $stringTiketTransaksiId . ').';
      }
      return $this->status;
   }

   function delete_rekapitulasi($id)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete rekapitulasi detail
      $this->db->where('recapitulation_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('recapitulation_detail');
      # delete rekapitulasi
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->delete('recapitulation');
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
         $this->content = 'Melakukan penghapusan data rekapitulasi dengan rekapitulasi id ' . $id . '.';
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
