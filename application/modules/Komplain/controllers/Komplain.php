<?php

/**
 *  -----------------------
 *	Komplain Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Komplain extends CI_Controller
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
      $this->load->model('Model_komplain', 'model_komplain');
      # model fasilitas cud
      $this->load->model('Model_komplain_cud', 'model_komplain_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   // server daftar komplain
   function server_daftar_komplain(){
      $return    = array();
      $error       = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',   '<b>Search<b>',    'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage',   '<b>Perpage<b>',    'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber',   '<b>pageNumber<b>',    'trim|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('status',   '<b>Status Komplain<b>', 'trim|required|xss_clean|min_length[1]|in_list[all,proses,selesai,ditolak]');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         $search    = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $status_komplain = $this->input->post('status');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
           $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total    = $this->model_komplain->get_total_komplain($search, $status_komplain);
         $list    = $this->model_komplain->get_index_komplain($perpage, $start_at, $search, $status_komplain);
         if ($total == 0) {
            $return = array(
               'error'   => true,
               'error_msg' => 'Daftar komplain tidak ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => false,
               'error_msg' => 'Daftar komplain berhasil ditemukan.',
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

   // get info tambah komplain
   function get_info_tambah_komplain(){
      $error = 0;
      # generated invoice
      $tab = $this->model_komplain->get_tab();
      # filter error
      if ($error != 0) {
         $return = array(
            'error'   => true,
            'error_msg' => 'Data info komplain tidak ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'data' => $tab,
            'error'   => false,
            'error_msg' => 'Data info komplain berhasil ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }

   // check tab
   function _ck_tab_id($tab_id){
      if( $this->model_komplain->check_tab_id($tab_id) ){
         return TRUE;
      }else{
         $this->form_validation->set_message('_ck_tab_id', 'Tab ID tidak ditemukan.');
         return FALSE;
      }
   }

   //  tambah komplain
   function proses_add_komplain(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('tab', '<b>Tab<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_tab_id');
      $this->form_validation->set_rules('komplain', '<b>Komplain<b>', 'trim|required|xss_clean|min_length[1]');
      $this->form_validation->set_rules('deskripsi_photo', '<b>Deskripsi Photo<b>', 'trim|required|xss_clean|min_length[1]');
      
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         // upload photo
         if (isset($_FILES['photo']) and $_FILES['photo']['size'] > 0) {
             # receive  data
            $data = array();
            $data['komplain']['company_id'] = $this->company_id;
            $data['komplain']['tab_id'] = $this->input->post('tab');
            $data['komplain']['komplain'] = $this->input->post('komplain');
            $data['komplain']['status'] = 'proses';
            $data['komplain']['info_penolakan'] = '';
            $data['komplain']['tanggal_komplain'] = date('Y-m-d');
            $data['komplain']['input_date'] = date('Y-m-d');
            $data['komplain']['last_update'] = date('Y-m-d');
            # define photo name
            $photo_with_extention = '';
            # photo name
            $photo_name = md5(date('Y-m-d H:i:s')); #  generateed photo name
            # define config photo
            $path = 'image/komplain/';
            $config['upload_path'] = FCPATH . $path;
            $config['allowed_types'] = 'jpg|jpeg|png';
            $config['file_name'] = $photo_name;
            $config['overwrite'] = TRUE;
            $config['max_size'] = 400;
            $this->load->library('upload', $config);
            $this->upload->overwrite = true;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            if ($this->upload->do_upload('photo')) {
               $fileData = $this->upload->data();
               $data['bukti_error']['company_id'] = $this->company_id;
               $data['bukti_error']['img_path'] = $fileData['file_name'];
               $data['bukti_error']['command'] = $this->input->post('deskripsi_photo');
               $data['bukti_error']['input_date'] = date('Y-m-d');
               $data['bukti_error']['last_update'] = date('Y-m-d');
            } else {
               $error      = 1;
               $error_msg  = $this->upload->display_errors();
            }
            # filter input process
            if ( $error == 0 ) {
               if( $this->model_komplain_cud->insert_komplain( $data )  ){
                  # create return
                  $return = array(
                     'error'   => false,
                     'error_msg' => 'Proses submit komplain berhasil dilakukan.',
                     $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                  );
               }else{
                  $return = array(
                     'error'   => true,
                     'error_msg' => 'Proses submit komplain gagal dilakukan.',
                     $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                  );
               }
           } else {
              $return = array(
                'error'   => true,
                'error_msg' => $error_msg,
                $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
              );
           }
         }else{
            $return = array(
               'error'   => true,
               'error_msg' => 'Anda Wajib Menyertakan Photo Bukti Komplain.',
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


   function _ck_komplain_id($komplain_id){
      if( $this->model_komplain->check_komplain_id($komplain_id) ){
         return TRUE;
      }else{
         $this->form_validation->set_message('_ck_komplain_id', 'Komplain ID tidak ditemukan.');
         return FALSE;
      }
   }


   function delete_komplain(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID Komplain<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_komplain_id');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         // image path
         $this->db->select('id, img_path')
            ->from('bukti_error')
            ->where('company_id', $this->company_id)
            ->where('komplain_id', $this->input->post('id') );
         $q = $this->db->get();
         $image_path = '';
         $bukti_error_id = '';
         if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $row ) {
               $image_path = $row->img_path;
               $bukti_error_id = $row->id;
            }
         }
         // delete komplain
         if ($this->model_komplain_cud->delete_komplain($this->input->post('id'),$bukti_error_id)) {
            $src = FCPATH . 'image/komplain/' . $image_path;
            // check file exist
            if ( file_exists($src) ) {
               unlink($src);
            }
            $return = array(
               'error'   => false,
               'error_msg' => 'Proses submit komplain berhasil dilakukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'   => true,
               'error_msg' => '',
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

   // 
   function detail_komplain(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>ID Komplain<b>', 'trim|required|xss_clean|min_length[1]|numeric|callback__ck_komplain_id');
      /*
        Validation process
      */
      if ($this->form_validation->run()) {
         // image path
         $this->db->select('id, img_path, command')
            ->from('bukti_error')
            ->where('company_id', $this->company_id)
            ->where('komplain_id', $this->input->post('id') );
         $q = $this->db->get();
         $image_path = '';
         $comment = '';
         if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $row ) {
               $image_path = $row->img_path;
               $comment = $row->command;
            }
         }else{
            $error = 1;
         }
         // delete komplain
         if ( $error == 0 ) {
            $return = array(
               'data' => array('path' => $image_path, 'comment' => $comment),
               'error'   => false,
               'error_msg' => 'Image Berhasil Ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         }else{
            $return = array(
               'error'   => true,
               'error_msg' => 'Image Tidak Ditemukan',
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