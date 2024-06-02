<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Pesan Whatsapp Controller
 *	Created by Muammar Kadafi
 */
class Pesan_whatsapp extends CI_Controller
{

   public function __construct()
   {
      parent::__construct();
      # Load model pesan whatsapp
      $this->load->model('Model_pesan_whatsapp', 'model_pesan_whatsapp');
      #load modal perangkat whatsapp cud
      $this->load->model('Model_pesan_whatsapp_cud', 'model_pesan_whatsapp_cud');
      # checking is not Login
      $this->auth_library->Is_not_login();
      # get company id
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # receive company code value
      $this->company_code = $this->input->get('company_code');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }


   function daftar_pesan_whatsapp(){
      $return  = array();
      $error      = 0;
      $error_msg = '';
      $this->form_validation->set_rules('search',  '<b>Search<b>',   'trim|xss_clean|min_length[1]');
      $this->form_validation->set_rules('perpage', '<b>Perpage<b>',  'trim|required|xss_clean|min_length[1]|numeric');
      $this->form_validation->set_rules('pageNumber', '<b>pageNumber<b>',  'trim|xss_clean|min_length[1]|numeric');
      /*
         Validation process
      */
      if ($this->form_validation->run()) {
         $search  = $this->input->post('search');
         $perpage = $this->input->post('perpage');
         $start_at = 0;
         if ($this->input->post('pageNumber')) {
            $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
         }
         $total   = $this->model_pesan_whatsapp->get_total_daftar_pesan_whatsapp($search);
         $list    = $this->model_pesan_whatsapp->get_index_daftar_pesan_whatsapp($perpage, $start_at, $search);
         if ($total == 0) {
            $return = array(
               'error'  => true,
               'error_msg' => 'Daftar Template Pesan Whatsapp Tidak Ditemukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'  => false,
               'error_msg' => 'Daftar Template Pesan Whatsapp Berhasil Ditemukan.',
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

   # get info kirim pesan
   function get_info_kirim_pesan(){
      $error = 0;
      # info nomor asal
      $info_nomor_asal = $this->model_pesan_whatsapp->get_nomor_asal();
      # filter
      if ( $info_nomor_asal == '' ) {
         $return = array(
            'error'  => true,
            'error_msg' => 'Data info nomor asal tidak ditemukan.',
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      } else {
         $return = array(
            'error'  => false,
            'error_msg' => 'Data info nomor asal berhasil ditemukan.',
            'data' => $info_nomor_asal,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
         );
      }
      echo json_encode($return);
   }


   function get_info_nomor_tujuan_and_template_info(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('jenis_pesan', '<b>Jenis Pesan<b>', 'trim|required|xss_clean|min_length[1]|in_list[pesan_biasa,agen,semua_jamaah,staff,jamaah_paket,jamaah_sudah_berangkat,jamaah_tabungan_umrah,jamaah_utang_koperasi]');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # define feedBack
         $feedBack = array();
         # count nomor tujuan
         if( $this->input->post('jenis_pesan') != 'pesan_biasa' ) {
            $feedBack['c_nomor_tujuan'] = count($this->model_pesan_whatsapp->get_nomor_tujuan_by_jenis_pesan( $this->input->post('jenis_pesan') ));
         }
         # error filter
         if( $error == 0 ) {
            if( $this->input->post('jenis_pesan') == 'jamaah_paket' ) {
               $feedBack['list_paket'] = $this->model_pesan_whatsapp->get_paket_active_non_active();
            }
            # get template by jenis pesan
            $feedBack['list_template'] = $this->model_pesan_whatsapp->get_template_by_jenis_pesan( $this->input->post('jenis_pesan') );
         }
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Info nomor tujuan dan template berhasil ditemukan.',
               'data' => $feedBack,
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

   function _ck_template_id($id){
      if( ! $this->model_pesan_whatsapp->get_template_id( $id ) ) {
         $this->form_validation->set_message('_ck_template_id', 'Template ID tidak ditemukan.');
         return FALSE;
      }else{
         return TRUE;
      }
   }

   function get_pesan_template(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('template_pesan', '<b>Template ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_template_id');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # get pesan
         $pesan = $this->model_pesan_whatsapp->get_pesan_template_by_template_id( $this->input->post('template_pesan') );
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Pesan template berhasil ditemukan.',
               'data' => $pesan,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Pesan template gagal ditemukan.',
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

   # paket id
   function _ck_paket_id($paket_id){
      if( ! $this->model_pesan_whatsapp->check_paket_id( $paket_id ) ) {
         $this->form_validation->set_message('_ck_paket_id', 'Paket ID tidak ditemukan.');
         return FALSE;
      }else{
         return TRUE;
      }  
   }


   function get_nomor_tujuan_by_paket(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('paket_id', '<b>Template ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_paket_id');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # feedBack
         $feedBack = array();
         # get pesan
         $feedBack['c_nomor_tujuan'] = count( $this->model_pesan_whatsapp->get_nomor_tujuan_by_paket( $this->input->post('paket_id') ) );
         # list paket
         $feedBack['list_paket'] = $this->model_pesan_whatsapp->get_paket_active_non_active();
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Pesan template berhasil ditemukan.',
               'data' => $feedBack,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Pesan template gagal ditemukan.',
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


   function _ck_paket_id_kirim_pesan(){
      if( $this->input->post('jenis_pesan') == 'jamaah_paket' AND $this->input->post('paket') != 0 ){
         if( $this->model_pesan_whatsapp->check_paket_id( $this->input->post('paket') ) ){
            return TRUE;
         }else{ 
            $this->form_validation->set_message('_ck_paket_id_kirim_pesan', 'Paket ID tidak ditemukan.');  
            return FALSE;
         }
      }else{
         return TRUE;
      }
   }

   function _ck_template_id_kirim_pesan(){
      if( $this->input->post('template_pesan') != 0 ) {
         if( ! $this->model_pesan_whatsapp->check_template_id( $this->input->post('template_pesan') ) ){
            $this->form_validation->set_message('_ck_template_id_kirim_pesan', 'Template ID tidak ditemukan.');  
            return FALSE;
         }else{
            return TRUE;
         }
      }else{
         return TRUE;
      }
   }

   # kirim pesan whatsapp
   function kirimPesanWhatsapp(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('jenis_pesan', '<b>Jenis Pesan<b>', 'trim|required|xss_clean|min_length[1]|in_list[pesan_biasa,agen,semua_jamaah,staff,jamaah_paket,jamaah_tabungan_umrah,jamaah_utang_koperasi]');
      if( $this->input->post('jenis_pesan') == 'jamaah_paket' ) {
         $this->form_validation->set_rules('paket', '<b>Paket ID<b>', 'trim|xss_clean|min_length[1]|callback__ck_paket_id_kirim_pesan');
      }
      if( $this->input->post('jenis_pesan') == 'pesan_biasa' ) {
         $this->form_validation->set_rules('nomor_tujuan', '<b>Nomor Tujuan<b>', 'trim|required|xss_clean|min_length[1]');
      }
      $this->form_validation->set_rules('template_pesan', '<b>Template ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_template_id_kirim_pesan');
      if( $this->input->post('template_pesan') == 0 ) {
         $this->form_validation->set_rules('pesan', '<b>Pesan<b>', 'trim|required|xss_clean|min_length[1]');
      }
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # api key 
         $this->whatsapp_ops->define_api();
         # get info device 
         $this->whatsapp_ops->define_device_key();
          # check api key
         if( $this->whatsapp_ops->check_api_key() ) {
            # check device key
            if( $this->whatsapp_ops->check_device_key() ) {
               # check whatsapp number
               if( $this->whatsapp_ops->check_whatsapp_number() ) {
                  # filter template pesan   
                  if( $this->input->post('template_pesan') AND $this->input->post('template_pesan') != 0 ) {
                     $pesan = $this->model_pesan_whatsapp->get_pesan_template_by_template_id( $this->input->post('template_pesan') );   
                  }else{
                     $pesan = $this->input->post('pesan');
                  }
                  //print('1<br>');
                  # define variable
                  $data = array();
                  $data['company_id'] = $this->company_id;
                  $data['jenis_pesan'] = $this->input->post('jenis_pesan');
                  $data['nomor_asal'] = $this->whatsapp_ops->get_whatsapp_number();
                  if( $this->input->post('template_pesan') != 0 ) {
                     $data['template_pesan'] = $pesan;
                     $data['template_id'] = $this->input->post('template_pesan');   
                  }
                  $data['tanggal_input'] = date('Y-m-d H:i:s');
                  // echo "<br>========<br>";
                  // print_r($data);
                  // echo "<br>========<br>";
                  # insert process
                  if( $this->model_pesan_whatsapp_cud->save_pesan_whatsapp( $data ) ) {
                     # receive jenis pesan
                     $jenis_pesan = $this->input->post('jenis_pesan');
                     # define list nomor tujuan pesan
                     $list_nomor_tujuan_pesan = array();
                     # get nomor tujuan
                     if( $jenis_pesan == 'pesan_biasa' ) {
                        # receive nomor tujuan
                        $nomor_tujuan = $this->input->post('nomor_tujuan');
                        $daftar_nomor_tujuan = array();
                        if (strpos($nomor_tujuan, ',') !== false) {
                           $daftar_nomor_tujuan = explode( ',', $nomor_tujuan );
                        }else{
                           $daftar_nomor_tujuan[] = $nomor_tujuan;
                        }
                        # looping
                        foreach ($daftar_nomor_tujuan as $key => $value) {
                           # define nomor tujuan pesan
                           $list_nomor_tujuan_pesan[] = array('nomor_tujuan' => $value, 'pesan' => $pesan);
                        }
                     } elseif( $jenis_pesan == 'semua_jamaah') {
                        $this->db->select('p.nomor_whatsapp, p.identity_number, p.fullname')
                           ->from('jamaah AS j')
                           ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                           ->where('j.company_id', $this->company_id)
                           ->where('p.nomor_whatsapp !=', '');
                        $q = $this->db->get();
                        if( $q->num_rows() > 0 ) {
                           foreach ($q->result() as $rows) {
                              $pattern_1 = '/{{nama_jamaah}}/';
                              $pattern_2 = '/{{nomor_identitas}}/';
                              $pesan_send = preg_replace( $pattern_1, $rows->fullname , $pesan );
                              $pesan_send = preg_replace( $pattern_2, $rows->identity_number, $pesan_send );
                              $list_nomor_tujuan_pesan[] = array('nomor_tujuan' => $rows->nomor_whatsapp, 'pesan' => $pesan_send); 
                           }
                        }  

                     } elseif( $jenis_pesan == 'staff') {
                        $this->db->select('p.nomor_whatsapp, p.fullname')
                           ->from('base_users AS u')
                           ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
                           ->where('u.company_id', $this->company_id)
                           ->where('p.nomor_whatsapp !=', '');
                        $q = $this->db->get();
                        if( $q->num_rows() > 0 ) {
                           foreach ($q->result() as $rows) {
                              $pattern_1 = '/{{nama_staff}}/';
                              $pesan_send = preg_replace( $pattern_1, $rows->fullname , $pesan );
                              $list_nomor_tujuan_pesan[] = array('nomor_tujuan' => $rows->nomor_whatsapp, 'pesan' => $pesan_send); 
                           }
                        }
                     } elseif( $jenis_pesan == 'agen') {
                        // nomor whatsapp
                        $this->db->select('p.nomor_whatsapp, p.fullname, p.identity_number, lk.nama AS level_agen')
                           ->from('agen AS a')
                           ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
                           ->join('level_keagenan AS lk', 'a.level_agen_id=lk.id', 'inner')
                           ->where('a.company_id', $this->company_id)
                           ->where('p.nomor_whatsapp !=', '');
                        $q = $this->db->get();
                        if( $q->num_rows() > 0 ) {
                           foreach ( $q->result() as $rows ) {

                              $pattern_1 = '/{{nama}}/';
                              $pattern_2 = '/{{level}}/';
                              $pattern_3 = '/{{no_hp}}/';

                              $pesan_send = preg_replace( $pattern_1, $rows->fullname , $pesan );
                              $pesan_send = preg_replace( $pattern_2, $rows->level_agen, $pesan_send );
                              $pesan_send = preg_replace( $pattern_3, $rows->nomor_whatsapp, $pesan_send );

                               $list_nomor_tujuan_pesan[] = array('nomor_tujuan' => $rows->nomor_whatsapp, 'pesan' => $pesan_send); 
                           }
                        }   
                     } elseif( $jenis_pesan == 'jamaah_paket') {

                        $this->db->select('p.nomor_whatsapp, p.fullname, p.identity_number, pkt.paket_name, pkt.kode, pt.no_register ')
                           ->from('paket_transaction_jamaah AS ptj')
                           ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
                           ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
                           ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
                           ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                           ->where('ptj.company_id', $this->company_id)
                           ->where('p.nomor_whatsapp !=', '');
                        if( $this->input->post('paket') ) {
                           $this->db->where('pkt.id', $this->input->post('paket'));
                        }
                        $q = $this->db->get();
                        if( $q->num_rows() > 0 ) {
                           foreach ( $q->result() as $rows ) {

                              $pattern_1 = '/{{nama_jamaah}}/';
                              $pattern_2 = '/{{nomor_identitas}}/';
                              $pattern_3 = '/{{nama_paket}}/';
                              $pattern_4 = '/{{kode_paket}}/';
                              $pattern_5 = '/{{nomor_register}}/';

                              $pesan_send = preg_replace( $pattern_1, $rows->fullname , $pesan );
                              $pesan_send = preg_replace( $pattern_2, $rows->identity_number, $pesan_send );
                              $pesan_send = preg_replace( $pattern_3, $rows->paket_name, $pesan_send );
                              $pesan_send = preg_replace( $pattern_4, $rows->kode, $pesan_send );
                              $pesan_send = preg_replace( $pattern_5, $rows->no_register, $pesan_send );

                              $list_nomor_tujuan_pesan[] = array('nomor_tujuan' => $rows->nomor_whatsapp, 'pesan' => $pesan_send); 

                           }
                        } 

                     } elseif( $jenis_pesan == 'jamaah_tabungan_umrah') {

                        $this->db->select('po.id, p.nomor_whatsapp, p.fullname, p.identity_number')
                           ->from('pool AS po')
                           ->join('jamaah AS j', 'po.jamaah_id=j.id', 'inner')
                           ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                           ->where('po.company_id', $this->company_id)
                           ->where('po.active','active')
                           ->where('p.nomor_whatsapp !=', '');
                        $q = $this->db->get();
                        if( $q->num_rows() > 0 ) {
                           foreach ($q->result() as $rows) {

                              # total tabungan
                              $total_tabungan = $this->model_pesan_whatsapp->total_tabungan($rows->id);

                              $pattern_1 = '/{{nama_jamaah}}/';
                              $pattern_2 = '/{{nomor_identitas}}/';
                              $pattern_3 = '/{{total_tabungan}}/';

                              $pesan_send = preg_replace( $pattern_1, $rows->fullname , $pesan );
                              $pesan_send = preg_replace( $pattern_2, $rows->identity_number, $pesan_send );
                              $pesan_send = preg_replace( $pattern_3, $this->session->userdata($this->config->item('apps_name'))['kurs'].number_format($total_tabungan), $pesan_send );

                              $list_nomor_tujuan_pesan[] = array('nomor_tujuan' => $rows->nomor_whatsapp, 'pesan' => $pesan_send); 

                           }
                        }   

                     } elseif( $jenis_pesan == 'jamaah_utang_koperasi') {

                        $this->db->select('pem.id, p.nomor_whatsapp, p.fullname, p.identity_number, pem.biaya, pem.tenor')
                           ->from('peminjaman AS pem')
                           ->join('jamaah AS j', 'pem.jamaah_id=j.id', 'inner')
                           ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                           ->where('pem.company_id', $this->company_id)
                           ->where('pem.status_peminjaman','belum_lunas')
                           ->where('p.nomor_whatsapp !=', '');
                        $q = $this->db->get();
                        if( $q->num_rows() > 0 ) {
                           foreach ($q->result() as $rows) {

                              $sudah_bayar = $this->model_pesan_whatsapp->get_info_sudah_bayar_cicilan( $rows->id );

                              $pattern_1 = '/{{nama_jamaah}}/';
                              $pattern_2 = '/{{nomor_identitas}}/';
                              $pattern_3 = '/{{total_hutang}}/';
                              $pattern_4 = '/{{total_tenor}}/';
                              $pattern_5 = '/{{sudah_bayar}}/';
                              $pattern_6 = '/{{sisa_hutang}}/';

                              $pesan_send = preg_replace( $pattern_1, $rows->fullname , $pesan );
                              $pesan_send = preg_replace( $pattern_2, $rows->identity_number, $pesan_send );
                              $pesan_send = preg_replace( $pattern_3, $this->session->userdata($this->config->item('apps_name'))['kurs'].number_format($rows->biaya), $pesan_send );
                              $pesan_send = preg_replace( $pattern_4, $rows->tenor, $pesan_send );
                              $pesan_send = preg_replace( $pattern_5, $this->session->userdata($this->config->item('apps_name'))['kurs'].number_format($sudah_bayar) , $pesan_send );
                              $pesan_send = preg_replace( $pattern_6, $this->session->userdata($this->config->item('apps_name'))['kurs'].number_format($rows->biaya - $sudah_bayar), $pesan_send );

                              $list_nomor_tujuan_pesan[] = array('nomor_tujuan' => $rows->nomor_whatsapp, 'pesan' => $pesan_send); 
                           }
                        }  
                     }
                     # proses kirim pesan
                     foreach ($list_nomor_tujuan_pesan as $key => $value) {
                        # define whatsapp message
                        $this->whatsapp_ops->message = $value['pesan'];
                        $this->whatsapp_ops->destination_number = $value['nomor_tujuan'];
                        # sending proses
                        $this->whatsapp_ops->send_message();
                        # filter 
                        if( $this->whatsapp_ops->status_response() == 'ok' ) {
                           # get response 
                           $response = $this->whatsapp_ops->response();
                           # define variable
                           $data = array();
                           $data['company_id'] = $this->company_id;
                           $data['pesan_whatsapp_id'] = $this->model_pesan_whatsapp_cud->pesan_whatsapp_id();
                           $data['nomor_tujuan'] = $value['nomor_tujuan'];
                           $data['message_id'] = $response->data->id;
                           $data['status'] = $response->data->status;
                           $data['pesan'] = $value['pesan'];
                           $data['device_key'] = $this->whatsapp_ops->get_device_key();
                           // $data['send_at'] = date('Y-m-d H:i:s');
                           # save detail pesan whatsapp
                           if( ! $this->model_pesan_whatsapp_cud->save_detail_pesan_whatsapp($data) ){
                              $error = 1;
                              $error_msg = 'Proses penyimpanan data detail pesan whatsapp gagal dilakukan.';
                           }
                        }
                        // sleep(61);
                     }
                     $this->model_pesan_whatsapp_cud->update_selesai_proses_pengiriman($this->model_pesan_whatsapp_cud->pesan_whatsapp_id());
                  }else{
                     $error = 1;
                     $error_msg = 'Proses penyimpanan data pesan whatsapp gagal dilakukan.';
                  }
               }else{
                  $error = 1;
                  $error_msg = 'Nomor Whatsapp Tidak Ditemukan';
               }
            }else{
                $error = 1;
               $error_msg = 'Info Device Tidak Ditemukan';
            }
         }else{
            $error = 1;
            $error_msg = 'API Key Tidak Ditemukan';
         }
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Proses pengiriman pesan berhasil dilakukan.',
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Proses pengiriman pesan gagal dilakukan.',
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

   function _ck_pesan_whatsapp_id($id){
      if( ! $this->model_pesan_whatsapp->check_pesan_whatsapp_id( $id ) ){
         $this->form_validation->set_message('_ck_pesan_whatsapp_id', 'Pesan Whatsapp ID tidak ditemukan.');  
         return FALSE;
      }else{
         return TRUE;
      }
   }

   function get_detail_pesan_whatsapp(){
      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('id', '<b>Pesan Whatsapp ID<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_pesan_whatsapp_id');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         # api key 
         $this->whatsapp_ops->define_api();
         # get info device 
         $this->whatsapp_ops->define_device_key();
          # check api key
         if( $this->whatsapp_ops->check_api_key() ) {
            # check device key
            if( $this->whatsapp_ops->check_device_key() ) {
               # check whatsapp number
               if( $this->whatsapp_ops->check_whatsapp_number() ) {
                  // get detail pesan whatsapp
                  $data = $this->model_pesan_whatsapp->get_detail_pesan_whatsapp( $this->input->post('id') );
                  # check
                  foreach ($data as $key => $value) {
                     if( $value['status'] == 'pending' ) {
                        # check status message
                        $this->whatsapp_ops->check_status_message( $value['message_id'] );
                        # filter 
                        if( $this->whatsapp_ops->status_response() == 'ok' ) {
                           # get response 
                           $response = $this->whatsapp_ops->response();
                           # filter
                           if( $response->data->status == 'sent' ) {
                              $data = array();
                              $data['status'] = 'terkirim';
                              $data['send_at'] = $response->data->send_at;
                              # save detail pesan whatsapp
                              if( ! $this->model_pesan_whatsapp_cud->update_status_pesan_whatsapp($value['message_id'], $data) ){
                                 $error = 1;
                                 $error_msg = 'Proses update status pesan whatsapp gagal dilakukan.';
                              }
                           }
                        }
                     }
                  }
               }else{
                  $error = 1;
                  $error_msg = 'Nomor Whatsapp Tidak Ditemukan';
               }
            }else{
                $error = 1;
               $error_msg = 'Info Device Tidak Ditemukan';
            }
         }else{
            $error = 1;
            $error_msg = 'API Key Tidak Ditemukan';
         }
         # get data 
         $data = $this->model_pesan_whatsapp->get_detail_pesan_whatsapp( $this->input->post('id') );
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Detail Pesan Whatsapp berhasil ditemukan.',
               'data' => $data,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Detail Pesan Whatsapp gagal ditemukan.',
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

   function countNumberWhatsappPaket(){

      $return = array();
      $error = 0;
      $error_msg = '';
      $this->form_validation->set_rules('status_paket', '<b>Status Paket<b>', 'trim|required|xss_clean|min_length[1]|in_list[semua,belum_berangkat,sudah_berangkat]');
      /*
         Validation process
      */
      if ( $this->form_validation->run() ) {
         // count
         $count = $this->model_pesan_whatsapp->countNumberWhatsappPaket( $this->input->post('status_paket') );
         # filter
         if ( $error == 0 ) {
            # get return
            $return = array(
               'error'      => false,
               'error_msg' => 'Jumlah Pesan Whatsapp berhasil ditemukan.',
               'data' => $count,
               $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
         } else {
            $return = array(
               'error'   => true,
               'error_msg' => 'Jumlah Pesan Whatsapp gagal ditemukan.',
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

