<?php

/**
 *  -----------------------
 *	Tipe paket la Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Supplier extends CI_Controller
{

   private $company_code;
   private $company_id;

   /**
    * Construct
    */
   public function __construct()
   {
      parent::__construct();
      # Load user model
      $this->load->model('Model_supplier', 'model_supplier');
      # model fasilitas cud
      $this->load->model('Model_supplier_cud', 'model_supplier_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function daftar_supplier()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage',   '<b>Perpage<b>',    'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber',   '<b>pageNumber<b>',    'trim|xss_clean|min_length[1]|numeric');
      /*
			Validation process
		*/
      if ($this->form_validation->run()) {
         $search    = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total    = $this->model_supplier->get_total_daftar_supplier($search);
         $list    = $this->model_supplier->get_index_daftar_supplier($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar supplier tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar supplier berhasil ditemukan.',
               'total' => $total,
               'data' => $list,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      } else {
         if (validation_errors()) {
            // define return error
            $return = array(
               'error'         => true,
               'error_msg'    => validation_errors(),
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      }
      echo json_encode($return);
   }

   function get_info_supplier()
   {
      $error = 0;
      # get list bank
      $bank = $this->model_supplier->get_list_bank();
      if (count($bank) == 0) {
         $return = array(
            'error'   => true,
            'error_msg' => 'Data bank tidak ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'   => false,
            'error_msg' => 'Data bank berhasil ditemukan.',
            'bank' => $bank,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }

   function _ck_id_supplier_exist()
   {
      if ($this->input->post('id')) {
         if ($this->model_supplier->check_supplier_id_exist($this->input->post('id'))) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_id_supplier_exist', 'Supplier ID tidak ditemukan.');
            return FALSE;
         }
      } else {
         return TRUE;
      }
   }

   function _ck_bank_id_exist($bank_id)
   {
      if ($this->model_supplier->check_bank_id_exist($bank_id)) {
         return TRUE;
      } else {
         $this->form_validation->set_message('_ck_bank_id_exist', 'ID Bank tidak ditemukan.');
         return FALSE;
      }
   }

   function proses_addupdate_supplier()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Supplier<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_supplier_exist');
      $this->form_validation->set_rules('nama_supplier', '<b>Nama Supplier<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('alamat_supplier', '<b>Alamat supplier<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('bank', '<b>Bank<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_bank_id_exist');
      $this->form_validation->set_rules('nomor_rekening', '<b>Nomor Rekening<b>', 'trim|required|numeric|xss_clean|min_length[1]');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         #  receive data
         $data = array();
         $data['supplier_name'] = $this->input->post('nama_supplier');
         $data['address'] = $this->input->post('alamat_supplier');
         $data['bank_id'] = $this->input->post('bank');
         $data['rekening_number'] = $this->input->post('nomor_rekening');
         $data['last_update'] = date('Y-m-d');
         // filter proses
         if ($this->input->post('id')) {
            if (!$this->model_supplier_cud->update_supplier($this->input->post('id'), $data)) {
               $error = 1;
               $error_msg = 'Data supplier gagal diperharui';
            }
         } else {
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d');
            if (!$this->model_supplier_cud->insert_supplier($data)) {
               $error = 1;
               $error_msg = 'Data supplier gagal disimpan';
            }
         }
         # filter feedBack
         if ($error == 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data supplier berhasil disimpan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => $error_msg,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      } else {
         if (validation_errors()) {
            // define return error
            $return = array(
               'error'         => true,
               'error_msg'    => validation_errors(),
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      }
      echo json_encode($return);
   }

   function get_edit_info_supplier()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Supplier<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_supplier_exist');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         # get data bank
         $bank = $this->model_supplier->get_list_bank();
         # get data supplier
         $feedBack = $this->model_supplier->get_data_supplier($this->input->post('id'));
         # filter feedBack
         if (count($feedBack) >  0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data supplier berhasil ditemukan.',
               'bank' => $bank,
               'data' => $feedBack,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data supplier tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      } else {
         if (validation_errors()) {
            // define return error
            $return = array(
               'error'         => true,
               'error_msg'    => validation_errors(),
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      }
      echo json_encode($return);
   }


   function delete_supplier()
   {
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Id Supplier<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_supplier_exist');
      /*
			Validation process
		*/
      if ($this->form_validation->run()) {
         # filter feedBack
         if (!$this->model_supplier_cud->delete_supplier($this->input->post('id'))) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data supplier gagal dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data supplier berhasil dihapus.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      } else {
         if (validation_errors()) {
            // define return error
            $return = array(
               'error'         => true,
               'error_msg'    => validation_errors(),
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }
      }
      echo json_encode($return);
   }
}
