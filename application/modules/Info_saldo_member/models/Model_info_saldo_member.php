<?php

/**
 *  -----------------------
 *	Model info saldo member
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_info_saldo_member extends CI_Model
{
   private $company_id;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   // get total daftar member
   function get_total_daftar_member($search){
      $this->db->select('p.personal_id')
         ->from('personal AS p')
         ->join('mst_bank_transfer AS m', 'p.bank_id=m.id', 'left')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('p.fullname', $search)
                  ->or_like('p.identity_number', $search)
                  ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // get index daftar member
   function get_index_daftar_member($limit = 6, $start = 0, $search = ''){
      $this->db->select('p.personal_id, p.fullname, p.identity_number, p.gender, p.birth_place, p.birth_date, p.address, p.email, 
                         p.nomor_whatsapp, p.account_name, p.number_account, 
                         (SELECT GROUP_CONCAT( user_id SEPARATOR \';\')
                            FROM base_users
                            WHERE personal_id=p.personal_id) AS userExist,
                         (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                            FROM agen
                            WHERE company_id="' . $this->company_id . '" AND
                            personal_id=p.personal_id) AS agenExist,
                         (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                            FROM jamaah
                            WHERE company_id="' . $this->company_id . '" AND
                            personal_id=p.personal_id) AS jamaahExist,
                         (SELECT GROUP_CONCAT( id SEPARATOR \';\')
                            FROM muthawif
                            WHERE company_id="' . $this->company_id . '" AND
                            personal_id=p.personal_id) AS muthawifExist

                            ')
         ->from('personal AS p')
         ->join('mst_bank_transfer AS m', 'p.bank_id=m.id', 'left')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('p.fullname', $search)
                  ->or_like('p.identity_number', $search)
                  ->group_end();
      }
      $this->db->order_by('p.personal_id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            // saldo
            $saldo = $this->info_deposit_tabungan($this->company_id, $row->personal_id);
            // register as
            $register_as = array();
            if ($row->userExist != '') {
               $register_as[] = 'User';
            }
            if ($row->agenExist != '') {
               $register_as[] = 'Agen';
            }
            if ($row->jamaahExist != '') {
               $register_as[] = 'Jamaah';
            }
            if ($row->muthawifExist != '') {
               $register_as[] = 'Muthawif';
            }

            // list
            $list[] = array(
               'id' => $row->personal_id, 
               'fullname' => $row->fullname, 
               'identity_number' => $row->identity_number, 
               'gender' => $row->gender == 0 ? 'Laki-laki' : 'Perempuan', 
               'birth_place' => $row->birth_place, 
               'birth_date' => $row->birth_date, 
               'address' => $row->address, 
               'email' => $row->email, 
               'nomor_whatsapp' => $row->nomor_whatsapp,
               'account_name' => $row->account_name,
               'number_account' => $row->number_account,
               'deposit' => $saldo['deposit'],
               'tabungan' => $saldo['tabungan'],
               'register_as' => $register_as,
            );
         }
      }
      return $list;
   }


   # saldo
   function info_deposit_tabungan($company_id, $personal_id)
   {
        $this->db->select('dt.debet, dt.kredit, dt.transaction_requirement')
                 ->from('deposit_transaction AS dt')
                 ->where('dt.personal_id', $personal_id)
                 ->where('dt.company_id', $company_id)
                 ->order_by('dt.id', 'desc');
        $q = $this->db->get();

        $debet_deposit = 0;
        $kredit_deposit = 0;

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                if( $rows->transaction_requirement == 'deposit' ){
                    if( $rows->debet != 0 ) {
                        $debet_deposit = $debet_deposit + $rows->debet;    
                    }
                    if( $rows->kredit != 0 ){
                        $kredit_deposit = $kredit_deposit + $rows->kredit;    
                    }
                }
            }
        }

        $total_deposit = $debet_deposit - $kredit_deposit;

        $this->db->select('dt.debet, dt.kredit, dt.transaction_requirement')
                 ->from('deposit_transaction AS dt')
                 ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id', 'inner')
                 ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
                 ->where('dt.personal_id', $personal_id)
                 ->where('dt.company_id', $company_id)
                 ->where('p.active', 'active')
                 ->order_by('dt.id', 'desc');
        $q = $this->db->get();

        $debet_tabungan = 0;
        $kredit_tabungan = 0;

        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                if ( $rows->transaction_requirement == 'paket_deposit' ) {
                    if( $rows->debet != 0 ) {
                        $debet_tabungan = $debet_tabungan + $rows->debet;    
                    }
                    if( $rows->kredit != 0 ){
                        $kredit_tabungan = $kredit_tabungan + $rows->kredit;    
                    }
                }
            }
        }

        $total_tabungan = $debet_tabungan - $kredit_tabungan;

        // get markup withdraw
        $this->db->select('markup_withdraw')
                 ->from('company')
                 ->where('id', $company_id);
        $q = $this->db->get();
        $markup_withdraw = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $markup_withdraw = $rows->markup_withdraw;
            }
        }

        # return
        return array('deposit' => $total_deposit, 'tabungan' => $total_tabungan, 'markup_withdraw' => $markup_withdraw);
   }

}