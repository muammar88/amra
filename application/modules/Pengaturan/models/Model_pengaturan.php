<?php

/**
 *  -----------------------
 *	Model pengaturan
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_pengaturan extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_info_pengaturan($company_id)
   {
      $this->db->select('logo, description, city, address, pos_code, telp, whatsapp_number, invoice_email, invoice_title, invoice_note, invoice_address')
         ->from('company')
         ->where('id', $company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if ($rows->logo != '') :
               $logo = file_exists(FCPATH . 'image/company/invoice_logo/' . $rows->logo) ? $rows->logo : 'default.png';
            else :
               $logo = 'default.png';
            endif;
            $list = array(
               'logo' => $logo,
               'description' => $rows->description,
               'city' => $rows->city,
               'address' => $rows->address,
               'pos_code' => $rows->pos_code,
               'telp' => $rows->telp,
               'whatsapp_number' => $rows->whatsapp_number,
               'invoice_email' => $rows->invoice_email,
               'invoice_title' => $rows->invoice_title,
               'invoice_note' => $rows->invoice_note,
               'invoice_address' => $rows->invoice_address
            );
         }
      }
      return $list;
   }

   # get logo
   function get_logo($company_id)
   {
      $this->db->select('logo')
         ->from('company')
         ->where('id', $company_id);
      $q = $this->db->get();
      $logo = '';
      if ($q->num_rows() > 0) {
         $logo = $q->row()->logo;
      }
      return $logo;
   }

   function get_total_daftar_bank_transfer($search = '' ){
      $this->db->select('ct.id')
               ->from('company_bank_transfer AS ct')
               ->join('mst_bank_transfer AS mt', 'ct.bank_id=mt.id', 'inner')
               ->where('ct.company_id', $this->company_id);
     if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
           ->like('mt.nama_bank', $search)
           ->or_like('ct.account_number', $search)
           ->group_end();
     }
     $q 	= $this->db->get();
     return $q->num_rows();
   }

   function get_index_daftar_bank_transfer($limit = 6, $start = 0, $search = ''){
      $this->db->select('ct.id, mt.nama_bank, mt.logo_bank, ct.account_name, ct.account_number')
               ->from('company_bank_transfer AS ct')
               ->join('mst_bank_transfer AS mt', 'ct.bank_id=mt.id', 'inner')
               ->where('ct.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
                  ->like('mt.nama_bank', $search)
                  ->or_like('ct.account_number', $search)
                  ->group_end();
     }
     $this->db->order_by('ct.last_update', 'desc')->limit($limit, $start);
     $q 	= $this->db->get();
     $list = array();
     if( $q->num_rows() > 0 ) {
        foreach ($q->result() as $rows) {
           $list[] = array('id' => $rows->id,
                           'nama_bank' => $rows->nama_bank,
                           'logo_bank' =>  base_url().'image/bank_logo/'.$rows->logo_bank,
                           'nama_rekening' => $rows->account_name,
                           'nomor_rekening' => $rows->account_number);
        }
     }
     return $list;
   }

   function get_info_bank_transfer(){
      $this->db->select('id, nama_bank')
         ->from('mst_bank_transfer');
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list[] = array('id' => $rows->id, 'nama_bank' => $rows->nama_bank);
         }
      }
      return $list;
   }

   function check_bank($bank_id){
      $this->db->select('id, nama_bank')
         ->from('mst_bank_transfer')
         ->where('id', $bank_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function check_company_bank_transfer_id($id){
      $this->db->select('id')
         ->from('company_bank_transfer')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_value_bank_transfer($id){
      $this->db->select('id, bank_id, account_name, account_number')
         ->from('company_bank_transfer')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            $list['bank_id'] = $rows->bank_id;
            $list['account_number'] = $rows->account_number;
            $list['account_name'] = $rows->account_name;
            $list['id'] = $rows->id;
         }
      }
      return  $list;
   }
}
