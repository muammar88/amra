<?php

/**
 *  -----------------------
 *	Slider Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Topik extends CI_Controller
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
      $this->load->model('Model_topik', 'model_topik');
      # model slider cud
      $this->load->model('Model_topik_cud', 'model_topik_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   # daftar topik
   function daftar_topik()
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
         $total    = $this->model_topik->get_total_daftar_topik($search);
         $list    = $this->model_topik->get_index_daftar_topik($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar topik tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar topik berhasil ditemukan.',
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

   # topik id
   function _ck_topik_id()
   {
      if ($this->input->post('id')) {
         if ($this->model_topik->check_topik_id($this->input->post('id'))) {
            return TRUE;
         } else {
            $this->form_validation->set_message('_ck_topik_id', 'Topik ID tidak ditemukan.');
            return FALSE;
         }
      } else {
         return TRUE;
      }
   }

   # add update topik
   function addupdate_topik()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Topik ID<b>', 'trim|xss_clean|numeric|callback__ck_topik_id');
      $this->form_validation->set_rules('topik', '<b>Topik<b>', 'trim|required|xss_clean');
      /*
       Validation process
      */
      if ($this->form_validation->run()) {
         # define data
         $data = array();
         $data['topik'] = $this->input->post('topik');
         $data['company_id'] = $this->company_id;
         $data['last_update'] = date('Y-m-d');
         if ($this->input->post('id')) {
            if (!$this->model_topik_cud->update_topik($this->input->post('id'), $data)) {
               $error = 1;
               $error_msg = 'Proses update topik gagal dilakukan.';
            } else {
               $error_msg = 'Proses update berhasil dilakukan.';
            }
         } else {
            $data['input_date'] = date('Y-m-d');
            if (!$this->model_topik_cud->insert_topik($data)) {
               $error = 1;
               $error_msg = 'Proses insert topik gagal dilakukan.';
            } else {
               $error_msg = 'Proses insert topik berhasil dilakukan.';
            }
         }
         # filter error
         if ($error == 0) {
            $return = array(
               'error'   => false,
               'error_msg' => $error_msg,
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

   # delete topik
   function delete_topik()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Topik ID<b>', 'trim|required|xss_clean|numeric|callback__ck_topik_id');
      /*
	    Validation process
	   */
      if ($this->form_validation->run()) {
         # filter error
         if ($this->model_topik_cud->delete_topik($this->input->post('id'))) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Proses delete topik berhasil dilakukan..',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Proses delete topik gagal dilakukan.',
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

   function info_edit_topik()
   {
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Topik ID<b>', 'trim|required|xss_clean|numeric|callback__ck_topik_id');
      /*
		 Validation process
		*/
      if ($this->form_validation->run()) {
         # get info topik
         $info_topik = $this->model_topik->get_info_topik($this->input->post('id'));
         # filter error
         if (count($info_topik) > 0) {
            $return = array(
               'error'   => false,
               'error_msg' => 'Data topik berhasil ditemukan.',
               'value' => $info_topik,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Data topik gagal ditemukan.',
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
