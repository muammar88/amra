<?php

/**
 *  -----------------------
 *	Model modal
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_migrations extends CI_Model
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

   # get data from deposit transaction temporary
   function get_data_deposit_transaction_temporary(){
      $this->db->select('dtt.nomor_transaction, dtt.company_id, dtt.personal_id, dtt.debet, dtt.kredit, dtt.approver, dtt.transaction_requirement, dtt.info,
                         dtt.input_date, dtt.last_update')
         ->from('deposit_transaction_temporary AS dtt')
         ->join('personal AS p', 'dtt.personal_id=p.personal_id', 'inner')
         ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner')
         ->where('dtt.personal_id != ', '0')
         ->where_in('dtt.transaction_requirement', array('deposit', 'paket_deposit'));
      $q = $this->db->get();
      $list = array();
      $list_personal_uniq = array();

      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $list[] = array('nomor_transaction' => $rows->nomor_transaction,
                            'personal_id' => $rows->personal_id,
                            'company_id' => $rows->company_id,
                            'debet' => $rows->debet,
                            'kredit' => $rows->kredit,
                            'approver' => $rows->approver,
                            'transaction_requirement' => $rows->transaction_requirement,
                            'info' => $rows->info,
                            'input_date' => $rows->input_date,
                            'last_update' => $rows->last_update);
            if( ! array_key_exists($rows->personal_id, $list_personal_uniq) ){
               $list_personal_uniq[$rows->personal_id] = array('personal_id' => $rows->personal_id,
                                             'company_id' => $rows->company_id);
            }
         }
      }
      return array('list' => $list, 'list_personal_uniq' => $list_personal_uniq);
   }

   # get data personal jamaah
   function get_data_personal_jamaah(){
      $this->db->select('p.personal_id, j.id')
         ->from('personal AS p')
         ->join('jamaah AS j', 'p.personal_id=j.personal_id', 'inner');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $list[$rows->personal_id] = $rows->id;
         }
      }
      return $list;
   }

   function insert_pool($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert data pool
      $this->db->insert('pool', $data);
      # get pool id
      $pool_id = $this->db->insert_id();
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
      }
      return array('status' => $this->status, 'pool_id' => $pool_id);
   }


   function insert_deposit_transaction($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert data pool
      $this->db->insert('deposit_transaction', $data);
      # get deposit transaction id
      $deposit_transaction_id = $this->db->insert_id();
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
      }
      return array('status' => $this->status, 'deposit_transaction_id' => $deposit_transaction_id);
   }

   # insert pool deposit transaction
   function insert_pool_deposit_transaction($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert data pool
      $this->db->insert('pool_deposit_transaction', $data);
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
      }
      return $this->status;
   }

}
