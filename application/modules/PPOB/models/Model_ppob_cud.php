<?php

/**
 *  -----------------------
 *	Model ppob cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_ppob_cud extends CI_Model
{
   private $status;
   private $category_id;
   private $category_id_pascabayar;
   private $operator_id;

    # insert category
   function insert_category( $data ){
      # Starting Transaction
      $this->db->trans_start();
      # insert category
      $this->db->insert('ppob_prabayar_category', $data);
      # get category id
      $this->category_id = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   function get_category_id(){
      return $this->category_id;
   }

   function insert_operator( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # insert operator
      $this->db->insert('ppob_prabayar_operator', $data);
      # get operator id
      $this->operator_id = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   function get_operator_id(){
      return $this->operator_id;
   }

   function insert_prefix( $data ){

      # Starting Transaction
      $this->db->trans_start();
      # insert prefix
      foreach ($data as $key => $value) {
         $this->db->insert('prefix_operator', $value);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }


   function insert_product($data){
      # Starting Transaction
      $this->db->trans_start();
      # insert product
      $this->db->insert('ppob_prabayar_product', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   function update_price( $data ) {
      # Starting Transaction
      $this->db->trans_start();
      # update data pesan whatsapp
      $this->db->where('product_code', $data['product_code'])
               ->update('ppob_prabayar_product', array('price' => $data['price'], 
                                                       'application_markup' => $data['markup'], 
                                                       'status' => $data['status'], 
                                                       'updated_at' => date('Y-m-d H:i:s')));
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

   // insert pascabayar category
   function insert_category_pascabayar( $data ) {
       # Starting Transaction
      $this->db->trans_start();
      # insert category
      $this->db->insert('ppob_pascabayar_category', $data);
      # get category id
      $this->category_id_pascabayar = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }


   function get_category_id_pascabayar(){
      return $this->category_id_pascabayar;
   }

   // insert product pascabayar
   function insert_product_pascabayar( $data ){
      # Starting Transaction
      $this->db->trans_start();
      # insert product
      $this->db->insert('ppob_pascabayar_product', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
      }
      return $this->status;
   }

   function update_product_pascabayar($product_code, $data ){
      # Starting Transaction
      $this->db->trans_start();
      # update data product pascabayar
      $this->db->where('product_code', $product_code)
               ->update('ppob_pascabayar_product', $data);
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

   // update markup company
   function update_markup_company( $product_code, $tipe, $markup, $company_id ){
      # Starting Transaction
      $this->db->trans_start();
      // filter process
      $this->db->select('id')
               ->from('ppob_company_markup')
               ->where('company_id', $company_id)
               ->where('product_code', $product_code)
               ->where('type', $tipe);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         // update process
         $this->db->where('product_code', $product_code)
                  ->where('company_id', $company_id)
                  ->where('type', $tipe)
                  ->update('ppob_company_markup', array('markup_company' => $markup));
      }else{
         // insert process
         $data = array();
         $data['company_id'] = $company_id;
         $data['product_code'] = $product_code;
         $data['type'] = $tipe;
         $data['markup_company'] = $markup;
         $data['created_at'] = date('Y-m-d H:i:s');
         # insert product
         $this->db->insert('ppob_company_markup', $data);
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
      }
      return $this->status;
   }

   // delete markup company
   function delete_markup_company( $product_code, $tipe, $company_id ){
      # Starting Transaction
      $this->db->trans_start();
      # delete markup company
      $this->db->where('product_code', $product_code)
               ->where('type', $tipe)
               ->where('company_id', $company_id)
               ->delete('ppob_company_markup');
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
         $this->content = 'Melakukan penghapusan data markup dengan kode produk ' . $product_code . '.';
      }
      return $this->status;
   }

   // update markup default perusahaan
   function update_markup_default_perusahaan($id, $data){
      # Starting Transaction
      $this->db->trans_start();
      # update data company
      $this->db->where('id', $id)
               ->update('company', $data);
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