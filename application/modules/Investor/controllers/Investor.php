<?php

/**
 *  -----------------------
 *	Investor Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Investor extends CI_Controller
{

    private $company_code;
    private $company_id;
    private $sesi;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
        # Load user model
        $this->load->model('Model_investor', 'model_investor');
        # load cud
        $this->load->model('Model_investor_cud', 'model_investor_cud');
        # checking is not Login
        $this->auth_library->Is_not_login();
        # get company id
        $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        # receive company code value
        $this->company_code = $this->input->get('company_code');
        # set date timezone
        ini_set('date.timezone', 'Asia/Jakarta');
    }

    # daftar investor
    function daftar_investor()
    {
        $return     = array();
        $error         = 0;
        $error_msg = '';
        $this->form_validation->set_rules('search',    '<b>Search<b>',     'trim|xss_clean|min_length[1]');
        $this->form_validation->set_rules('perpage',    '<b>Perpage<b>',     'trim|required|xss_clean|min_length[1]|numeric');
        $this->form_validation->set_rules('pageNumber',    '<b>pageNumber<b>',     'trim|xss_clean|min_length[1]|numeric');
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
            $total     = $this->model_investor->get_total_daftar_investor($search);
            $list     = $this->model_investor->get_index_daftar_investor($perpage, $start_at, $search);
            if ($total == 0) {
                $return = array(
                    'error'    => true,
                    'error_msg' => 'Daftar investor tidak ditemukan.',
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error'    => false,
                    'error_msg' => 'Daftar investor berhasil ditemukan.',
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

    # check id investor exist
    function _ck_id_investor_exist()
    {
        if ($this->input->post('id')) {
            if (!$this->model_investor->check_investor_exist($this->input->post('id'))) {
                $this->form_validation->set_message('_ck_id_investor_exist', 'ID Investor tidak ditemukan.');
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return TRUE;
        }
    }

    function _ck_saham_investor($saham){
        if( ! $this->model_investor->check_saham_investor( $saham ) ){
            $this->form_validation->set_message('_ck_saham_investor', 'Persentase saham tidak boleh lebih dari 100%.');
            return FALSE;
        }else{
            return TRUE;
        }
    }

    // akun
    // $data_jurnal[] = array('company_id' => $this->company_id,
    //                           'source' => 'deposittabungan:notransaction:'.$this->input->post('nomor_transaksi'),
    //                           'ref' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
    //                           'ket' => 'Deposit Tabungan Umrah Jamaah Dengan No Transaction :'.$this->input->post('nomor_transaksi'),
    //                           'akun_debet' => '23000',
    //                           'akun_kredit' => '24000',
    //                           'saldo' => $this->text_ops->hide_currency($this->input->post('biaya_deposit')),
    //                           'periode_id' => 0,
    //                           'input_date' => date('Y-m-d H:i:s'),
    //                           'last_update'  => date('Y-m-d H:i:s'));

    # proses add update investor
    function proses_addupdate_investor()
    {
        $return = array();
        $error = 0;
        $error_msg = '';
        $this->form_validation->set_rules('id', '<b>Id Investor<b>', 'trim|xss_clean|numeric|min_length[1]|callback__ck_id_investor_exist');
        $this->form_validation->set_rules('nama', '<b>Nama Investor<b>', 'trim|required|xss_clean|min_length[1]');
        $this->form_validation->set_rules('nomor_identitas', '<b>Nomor Identitas<b>', 'trim|required|xss_clean|numeric|min_length[1]');
        $this->form_validation->set_rules('no_hp', '<b>No HP<b>', 'trim|required|xss_clean|min_length[1]');
        $this->form_validation->set_rules('alamat', '<b>Alamat<b>', 'trim|required|xss_clean|min_length[1]');
        $this->form_validation->set_rules('investasi', '<b>Investasi<b>', 'trim|xss_clean|min_length[1]');
        $this->form_validation->set_rules('saham', '<b>Sahan Investasi<b>', 'trim|required|xss_clean|min_length[1]|callback__ck_saham_investor');
        // proses_addupdate_investor
        /*
			Validation process
		*/
        if ($this->form_validation->run()) {
            #  receive data
            $data = array();
            $data['investor']['nama'] = $this->input->post('nama');
            $data['investor']['nomor_identitas'] = $this->input->post('nomor_identitas');
            $data['investor']['company_id'] = $this->company_id;
            $data['investor']['no_hp'] = $this->input->post('no_hp');
            $data['investor']['alamat'] = $this->input->post('alamat');
            $data['investor']['investasi'] = $this->text_ops->hide_currency($this->input->post('investasi'));
            $data['investor']['saham'] = $this->input->post('saham');
            $data['investor']['last_update'] = date('Y-m-d');
            # filter proses insert or update
            if ( $this->input->post('id') ) {
                # filter
                $get_investasi = $this->model_investor->get_investasi_investor( $this->input->post('id') );
                # filter
                if( $get_investasi > $this->text_ops->hide_currency($this->input->post('investasi')) ){
                    # total invest
                    $total_invest = $get_investasi - $this->text_ops->hide_currency($this->input->post('investasi'));
                    # jurnal dikuran
                    $data['jurnal']['company_id'] = $this->company_id;
                    $data['jurnal']['source'] = 'investasi:id:'.$this->input->post('id');
                    $data['jurnal']['ref'] = 'investasi:id:'.$this->input->post('id');
                    $data['jurnal']['ket'] = 'investasi:id:'.$this->input->post('id');
                    $data['jurnal']['akun_debet'] = '31000';
                    $data['jurnal']['akun_kredit'] = '11010';
                    $data['jurnal']['saldo'] = $total_invest;
                    $data['jurnal']['periode_id'] = 0;
                    $data['jurnal']['input_date'] = date('Y-m-d H:i:s');
                    $data['jurnal']['last_update'] = date('Y-m-d H:i:s');
                } elseif( $get_investasi < $this->text_ops->hide_currency($this->input->post('investasi'))  ){
                    # total invest
                    $total_invest = $this->text_ops->hide_currency($this->input->post('investasi')) - $get_investasi;
                    # ditambah
                    $data['jurnal']['company_id'] = $this->company_id;
                    $data['jurnal']['source'] = 'investasi:id:'.$this->input->post('id');
                    $data['jurnal']['ref'] = 'investasi:id:'.$this->input->post('id');
                    $data['jurnal']['ket'] = 'investasi:id:'.$this->input->post('id');
                    $data['jurnal']['akun_debet'] = '11010';
                    $data['jurnal']['akun_kredit'] = '31000';
                    $data['jurnal']['saldo'] = $total_invest;
                    $data['jurnal']['periode_id'] = 0;
                    $data['jurnal']['input_date'] = date('Y-m-d H:i:s');
                    $data['jurnal']['last_update'] = date('Y-m-d H:i:s');
                }
                # update process
                if ( ! $this->model_investor_cud->update_investor( $data, $this->input->post('id') ) ) {
                    $error = 1;
                    $error_msg = 'Proses update data investor gagal dilakukan.';
                } else {
                    $error_msg = 'Proses update data investor berhasil dilakukan.';
                }
            } else {
                # investor
                $data['investor']['input_date'] = date('Y-m-d');
                # jurnal 
                $data['jurnal']['company_id'] = $this->company_id;
                $data['jurnal']['source'] = 'investasi:id:';
                $data['jurnal']['ref'] = 'investasi:id:';
                $data['jurnal']['ket'] = 'investasi:id:';
                $data['jurnal']['akun_debet'] = '11010';
                $data['jurnal']['akun_kredit'] = '31000';
                $data['jurnal']['saldo'] = $this->text_ops->hide_currency($this->input->post('investasi'));
                $data['jurnal']['periode_id'] = 0;
                $data['jurnal']['input_date'] = date('Y-m-d H:i:s');
                $data['jurnal']['last_update'] = date('Y-m-d H:i:s');
                # insert process
                if ( ! $this->model_investor_cud->insert_investor( $data ) ) {
                    $error = 1;
                    $error_msg = 'Proses insert data investor gagal dilakukan.';
                } else {
                    $error_msg = 'Proses insert data investor berhasil dilakukan.';
                }
            }
            # filter error
            if ( $error == 0 ) {
                $return = array(
                    'error'    => false,
                    'error_msg' => $error_msg,
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error'    => true,
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

    # delete investor
    function delete_investor()
    {
        $return = array();
        $error = 0;
        $error_msg = '';
        $this->form_validation->set_rules('id', '<b>Id Investor<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_investor_exist');
        /*
			Validation process
		*/
        if ($this->form_validation->run()) {
            # filter error
            if (!$this->model_investor_cud->delete_investor($this->input->post('id'))) {
                $return = array(
                    'error'    => true,
                    'error_msg' => "Proses delete data investor gagal dilakukan.",
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error'    => false,
                    'error_msg' => "Proses delete data investor berhasil dilakukan.",
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

    function get_info_edit_investor()
    {
        $return = array();
        $error = 0;
        $error_msg = '';
        $this->form_validation->set_rules('id', '<b>Id Investor<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_investor_exist');
        /*
			Validation process
		*/
        if ($this->form_validation->run()) {
            # get info edit investor
            $info_value = $this->model_investor->get_info_edit_investor($this->input->post('id'));
            # filter error
            if (count($info_value) > 0) {
                $return = array(
                    'error'    => false,
                    'error_msg' => "Data investor berhasil ditemukan.",
                    'data' => $info_value,
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error'    => true,
                    'error_msg' => "Data investor gagal ditemukan.",
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

    // get investor
    function get_investor(){
        # filter
        if (  $this->model_investor->total_saham() < 100 ) {
             $return = array(
                'error' => false,
                'error_msg' => 'Success.',
                $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
            
        } else {
           $return = array(
                'error' => true,
                'error_msg' => 'Anda tidak dapat menambah investor, karena total saham sudah mencapai 100%.',
                $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
            );
        }
        echo json_encode($return);
    }

    function cabut_investor(){
        $return = array();
        $error = 0;
        $error_msg = '';
        $this->form_validation->set_rules('id', '<b>Id Investor<b>', 'trim|required|xss_clean|numeric|min_length[1]|callback__ck_id_investor_exist');
        /*
            Validation process
        */
        if ($this->form_validation->run()) {
            // total
            $info = $this->model_investor->info_investasi_investor( $this->input->post('id') );
            # filter error
            if ( $this->model_investor_cud->cabut_investor($this->input->post('id'), $info)  ) {
                $return = array(
                    'error'    => false,
                    'error_msg' => "Proses cabut investasi investor berhasil dilakukan.",
                    $this->security->get_csrf_token_name() => $this->security->get_csrf_hash()
                );
            } else {
                $return = array(
                    'error'    => true,
                    'error_msg' => "Proses cabut investasi investor gagal dilakukan.",
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
