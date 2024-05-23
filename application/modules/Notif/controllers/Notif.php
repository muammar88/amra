<?php

/**
 *  -----------------------
 *	Notif Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Notif extends CI_Controller
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
        $this->load->model('Model_notif', 'model_notif');
        # model notif
        $this->load->model('Model_notif_cud', 'model_notif_cud');
        # checking is not Login
        $this->auth_library->Is_not_login();
        # get company id
        $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        # receive company code value
        $this->company_code = $this->input->get('company_code');
        # set date timezone
        ini_set('date.timezone', 'Asia/Jakarta');
    }

    function server_side(){
        $return     = array();
        $error      = 0;
        $error_msg = '';
        $this->form_validation->set_rules('search', '<b>Search<b>',     'trim|xss_clean|min_length[1]');
        $this->form_validation->set_rules('perpage',    '<b>Perpage<b>',    'trim|required|xss_clean|min_length[1]|numeric');
        $this->form_validation->set_rules('pageNumber', '<b>pageNumber<b>',     'trim|xss_clean|min_length[1]|numeric');
        /*
            Validation process
        */
        if ($this->form_validation->run()) {
            $search     = $this->input->post('search');
            $perpage = $this->input->post('perpage');
            $start_at = 0;
            if ($this->input->post('pageNumber')) {
                $start_at = ($this->input->post('pageNumber') > 1) ? ($this->input->post('pageNumber') *  $perpage) - $perpage : 0;
            }
            $total  = $this->model_notif->get_total_daftar_notification($search);
            $list   = $this->model_notif->get_index_daftar_notification($perpage, $start_at, $search);
            if ($total == 0) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Daftar pesan tidak ditemukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Daftar pesan berhasil ditemukan.',
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

    // proses insert
    function proses_addupdate_notification(){
        $return = array();
        $error = 0;
        $error_msg = '';
        $this->form_validation->set_rules('judul',  '<b>Judul Pesan<b>',  'trim|required|xss_clean|min_length[1]');
        $this->form_validation->set_rules('pesan',  '<b>Pesan<b>',  'trim|required|xss_clean|min_length[1]');
        /*
            Validation process
        */
        if ($this->form_validation->run()) {
            $data = array();
            $data['title'] = $this->input->post('judul');
            $data['message'] = $this->input->post('pesan');
            $data['company_id'] = $this->company_id;
            $data['input_date'] = date('Y-m-d H:i:s');
            $data['last_update'] = date('Y-m-d H:i:s');
            // insert proses
            if( ! $this->model_notif_cud->insert_notif( $data ) ) {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Proses insert gagal dilakukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Proses insert berhasil dilakukan.',
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


    function _ck_id_notification_exist($id){
        if( ! $this->model_notif->check_id($id) ){
            $this->form_validation->set_message('_ck_id_notification_exist', 'ID Pesan Notifikasi tidak ditemukan.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    // delete
    function delete(){
        $return = array();
        $error = 0;
        $error_msg = '';
        $this->form_validation->set_rules('id', '<b>Id Pesan Notifikasi<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_notification_exist');
        /*
            Validation process
        */
        if ($this->form_validation->run()) {
            // delete process
            if ( $this->model_notif_cud->delete( $this->input->post('id') ) ) {
                $return = array(
                    'error' => false,
                    'error_msg' => 'Proses delete notifikasi berhasil dilakukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error' => true,
                    'error_msg' => 'Proses delete notifikasi gagal dilakukan.',
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

    # checking notification
    function checking_notif()
    {
        $error = 0;
        $feedBack = array();
        # paket yang berangkat
        if ($this->model_notif->check_paket_berangkat()) { # ada paket yang akan berangkat
            $feedBack['paket_akan_berangkat'] = $this->model_notif->feedBack();
        }
        # jamaah yang belum lengkap syarat 
        if (!$this->model_notif->check_kelengkapan()) { # tidak lengkap
            $feedBack['kelengkapan'] = $this->model_notif->feedBack();
        }
        # check transansi jamaah yang jatuh tempo 
        if ($this->model_notif->check_transaksi_jamaah()) {
            $feedBack['jatuh_tempo'] = $this->model_notif->feedBack();
        }
        # permintaan menjadi agen 
        if ($this->model_notif->check_agen_request()) {
            $feedBack['agen_request'] = $this->model_notif->feedBack();
        }
        # pengisian saldo dan klaim saldo
        if ($this->model_notif->check_pengisian_klaim_saldo()) {
            # pengisian saldo
            if (isset($this->feedBack['deposit'])) {
                $feedBack['deposit'] = array('num' => count($this->feedBack['deposit']), 'list' => $this->feedBack['deposit'], 'title' => 'Request', 'icon' => 'fas fa-hand-holding-usd');
            }
            # withdraw
            if (isset($this->feedBack['withdraw'])) {
                $feedBack['withdraw'] = array('num' => count($this->feedBack['withdraw']), 'list' => $this->feedBack['withdraw'], 'title' => 'Request', 'icon' => 'fas fa-comment-dollar');
            }
            # claim
            if (isset($this->feedBack['claim'])) {
                $feedBack['claim'] = array('num' => count($this->feedBack['claim']), 'list' => $this->feedBack['claim'], 'title' => 'Request', 'icon' => 'fas fa-exclamation-circle');
            }
            # buying_paket
            if (isset($this->feedBack['buying_paket'])) {
                $feedBack['buying_paket'] = array('num' => count($this->feedBack['buying_paket']), 'list' => $this->feedBack['buying_paket'], 'title' => 'Request', 'icon' => 'fas fa-shopping-cart');
            }
            # payment_paket
            if (isset($this->feedBack['payment_paket'])) {
                $feedBack['payment_paket'] = array('num' => count($this->feedBack['payment_paket']), 'list' => $this->feedBack['payment_paket'], 'title' => 'Request', 'icon' => 'fas fa-file-invoice-dollar');
            }
            # purchase
            if (isset($this->feedBack['purchase'])) {
                $feedBack['purchase'] = array('num' => count($this->feedBack['purchase']), 'list' => $this->feedBack['purchase'], 'title' => 'Request', 'icon' => 'fas fa-cart-plus');
            }
        }

        if ($error == 0) {
            $return = array(
                'error'    => false,
                'num' => count($feedBack),
                'list' => $feedBack,
                'error_msg' => 'Proses checking berhasil dilakukan.',
                $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
        } else {
            $return = array(
                'error'    => true,
                'error_msg' => 'Proses checking gagal dilakukan.',
                $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
        }
        echo json_encode($return);
    }
}
