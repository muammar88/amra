<?php

/**
*  -----------------------
*	Model api
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_api extends CI_Model
{

    private $kurs;

    public function __construct()
    {
        parent::__construct();

        # kurs
        $this->kurs = $this->session->userdata($this->config->item('apps_name'))['kurs'];
    }

    // get info transaksi by id transaksi
    function get_info_transaksi_by_id_transaksi($transaction_code, $trxid){
        $this->db->select('pth.id, pth.transaction_code, pth.trxid, pth.nomor_tujuan, pth.product_code, pth.application_price, pc.company_id, ppc.category_code')
                 ->from('ppob_transaction_history AS pth')
                 ->join('ppob_prabayar_product AS ppp', 'pth.product_code=ppp.product_code', 'inner')
                 ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                 ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                 ->join('ppob_transaction_history_company AS pc', 'pth.id=pc.ppob_transaction_history_id', 'inner')
                 ->where('pth.transaction_code', $transaction_code)
                 ->where('pth.trxid', $trxid)
                 ->where('pth.server', 'tripay')
                 ->where('pth.status', 'process');
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $row) {
                $list['company_id'] = $row->company_id;
                $list['nomor_tujuan'] = $row->nomor_tujuan;
                $list['product_code'] = $row->product_code;
                $list['application_price'] = $row->application_price;
                $list['category_code'] = $row->category_code;
                $list['status'] = 'ada';
            }
        }else{
            $list['status'] = 'tidak';
        }
        return $list;
    }

    function check_jumlah_deposit_member($company_id, $personal_id, $nominal){
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

        $total_deposit = $debet_deposit + $kredit_deposit;

        if( $total_deposit < $nominal ){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    # check bank exist
    function check_bank_exist( $nama_bank, $company_code ){
        $this->db->select('mbt.id')
                 ->from('mst_bank_transfer AS mbt')
                 ->join('company_bank_transfer AS cbt', 'mbt.id=cbt.bank_id', 'inner')
                 ->join('company AS c', 'cbt.company_id=c.id', 'inner')
                 ->where('mbt.nama_bank', $nama_bank)
                 ->where('c.code', $company_code);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        }else{
            return false;
        }
    }

    # get info akun
    function get_info_akun($token, $company_code)
    {
        $this->db->select('p.fullname, p.identity_number, p.birth_place, p.birth_date, p.nomor_whatsapp, account_name,  number_account, bank_id')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner' )
        ->where('p.token', $token)
        ->where('c.code', $company_code);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list['fullname'] = $rows->fullname;
                $list['identity_number'] = $rows->identity_number;
                $list['birth_place'] = $rows->birth_place;
                $list['birth_date'] = $rows->birth_date;
                $list['nomor_whatsapp'] = $rows->nomor_whatsapp;
            }
        }
        return $list;
    }

    // check bank exist
    function check_bank_exist_by_name( $bank_name ) {
        $this->db->select('id')
            ->from('mst_bank_transfer')
            ->where('nama_bank', $bank_name);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // check password lama
    function check_password_lama( $token, $company_code, $password_lama ) {
        $this->db->select('p.password')
            ->from('personal AS p')
            ->join('company AS c', 'p.company_id=c.id', 'inner')
            ->where('p.token', $token)
            ->where('c.code', $company_code);
        $q = $this->db->get();
        $error = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                if ( ! password_verify( $password_lama . '_' . $this->systems->getSalt(), $rows->password ) ) {
                    $error = 1;
                }
            }
        }
        if( $error == 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // get bank id
    function get_bank_id_by_bank_name( $bank_name ) {
        $this->db->select('id')
            ->from('mst_bank_transfer')
            ->where('nama_bank', $bank_name);
        $q = $this->db->get();
        $bank_id = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $bank_id = $rows->id;
            }
        } 
        return $bank_id;
    }

    function get_info_personal( $company_id, $personal_id ){
         $this->db->select('p.fullname, p.identity_number, p.birth_place, p.birth_date, p.account_name, p.number_account, mt.nama_bank ')
                  ->from('personal AS p')
                  ->join('mst_bank_transfer AS mt', 'p.bank_id=mt.id', 'left')
                  ->where('p.company_id', $company_id)
                  ->where('p.personal_id', $personal_id);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list['fullname'] = $rows->fullname;
                $list['identity_number'] = $rows->identity_number;
                $list['birth_place'] = $rows->birth_place;
                $list['birth_date'] = $rows->birth_date;
                $list['account_name'] = $rows->account_name;
                $list['number_account'] = $rows->number_account;
                $list['bank_id'] = $rows->nama_bank;
            }
        }
        return $list;
    }


    // get token info
    function get_token_info( $token, $company_code ){
        $this->db->select('p.personal_id, p.token_expired_datetime')
            ->from('personal AS p')
            ->join('company AS c', 'p.company_id=c.id', 'inner' )
            ->where('c.code', $company_code )
            ->where('p.token', $token);
        $q = $this->db->get();
        $feedBack = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $feedBack['token_expired_datetime'] = $rows->token_expired_datetime;
            }
        }
        return $feedBack;
    }


    function check_path_panduan($path)
    {
        $this->db->select('id')
        ->from('panduan_manasik')
        ->where('part', $path);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function check_company_code($company_code)
    {
        $this->db->select('id')
        ->from('company')
        ->where('code', $company_code);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function check_nomor_wa_by_company_code($nomor_whatsapp, $company_code)
    {
        // echo $nomor_whatsapp."<br>";
        // echo $company_code."<br>";
        $this->db->select('p.personal_id')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('c.code', $company_code)
        ->where('p.nomor_whatsapp', $nomor_whatsapp);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function get_panduan($path)
    {
        $this->db->select('pmd.content')
        ->from('panduan_manasik AS pm')
        ->join('panduan_manasik_detail AS pmd', 'pm.id=pmd.panduan_manasik_id', 'inner')
        ->where('pm.part', $path);
        $q = $this->db->get();
        $list = '';
        if ($q->num_rows() > 0) {
            $list = $q->row()->content;
        }
        return $list;
    }

    # get personal id
    function get_personal_id($nomor_whatsapp, $kode_perusahaan)
    {
        $this->db->select('p.personal_id')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('p.nomor_whatsapp', $nomor_whatsapp)
        ->where('c.code', $kode_perusahaan);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return $q->row()->personal_id;
        } else {
            return 0;
        }
    }


    function gen_otp_member($nomor_whatsapp, $company_code)
    {
        $feedBack = false;
        $rand = '';
        $personal_id = $this->get_personal_id($nomor_whatsapp, $company_code);

        do {
            $rand = $this->random_numeric(6);
            $q = $this->db->select('personal_id')
            ->from('personal')
            ->where('personal_id', $personal_id)
            ->where('otp', $rand)
            ->get();
            if ($q->num_rows() == 0) {
                $feedBack = true;
            }
        } while ($feedBack == false);

        $date = strtotime(date('Y-m-d H:i:s'));
        $date = strtotime("+3 minute", $date);
        $otp_expire =  date('Y-m-d H:i:s', $date);

        $this->db->where('personal_id', $personal_id)
        ->update('personal', array('otp' => $rand, 'otp_expire' => $otp_expire));

        return $rand;
    }

    function random_alpha_numeric($size)
    {

        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        //;
        $alpha_key = '';
        $keys = range('A', 'Z');
        for ($i = 0; $i < 2; $i++) {
            $alpha_key .= $keys[array_rand($keys)];
        }

        $length = $size;

        $key = '';
        $keys = range(0, 9);
        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return substr(str_shuffle($alpha_key . $key . $str_result), 0, $size);
    }


    function random_numeric($size)
    {
        $key = '';
        $keys = range(0, 9);
        for ($i = 0; $i < $size; $i++) {
            $key .= $keys[array_rand($keys)];
        }
        return $key;
    }

    # check_token
    function check_token($otp, $nomor_whatsapp, $company_code)
    {
        $error = false;
        $error_msg = '';
        if (substr($nomor_whatsapp, 0, 1) == '0') {
            $nomor_whatsapp = '62' . substr($nomor_whatsapp, 1);
        }
        $this->db->select('p.personal_id, p.otp_expire')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('p.otp', $otp)
        ->where('c.code', $company_code)
        ->where('p.nomor_whatsapp', $nomor_whatsapp);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $row = $q->row();
            if ($row->otp_expire < date('Y-m-d H:i:s')) {
                $error = true;
                $error_msg = 'OTP Sudah Expired.';
            }
        } else {
            $error = true;
            $error_msg = 'OTP tidak ditemukan.';
        }
        return array('error' => $error, 'error_msg' => $error_msg);
    }


    function username_password_authentication($code_company, $whatsappnumber, $password)
    {
        $return = array();
        $this->db->select('c.id, p.personal_id, p.password, c.verified, c.company_type, c.start_date_subscribtion, c.end_date_subscribtion,')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('c.code', $code_company)
        ->where('p.nomor_whatsapp', $whatsappnumber);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $row =  $q->row();
            if( $row->verified == 'verified') {
                if ($row->end_date_subscribtion < date('Y-m-d') and $row->company_type == 'limited') {
                    $return = array('error' => true, 
                                    'error_msg' => 'Masa berlangganan akun perusahaan ini sudah berakhir. Anda kembali dapat menggunakan aplikasi AMRA setelah masa berlangganan sudah diperpanjang.');
                } else {
                    if ( password_verify( $password . '_' . $this->systems->getSalt(), $row->password ) ) {
                        // token
                        $token = str_shuffle($this->random_alpha_numeric(200));
                        // define expire datetime
                        //$date = strtotime("+1 day", date('Y-m-d H:i:s'));
                        $timestamp = $_SERVER['REQUEST_TIME'];
                        $dateNow = date('d/m/y', $timestamp);
                        $expire_datetime = date('d/m/y', strtotime('+1 day', $timestamp));
                        // echo $date."<br>";
                        // $expire_datetime = date('Y-m-d H:i:s', $date);
                        // update token
                        $this->db->where('personal_id', $row->personal_id)
                                 ->update('personal', array('token' => $token, 
                                                            'token_expired_datetime' => $expire_datetime));
                        // return variable
                        $return = array('error' => false, 
                                        'error_msg' => 'Success.', 
                                        'token' => $token);
                    } else {
                        $return = array('error' => true, 
                                        'error_msg' => 'Verifikasi gagal dilakukan.');
                    }
                }
            }else{
                $return = array('error' => true, 
                                'error_msg' => 'Akun perusahaan ini belum terverifikasi.');
            }
        } else {
            $return = array('error' => true, 
                            'error_msg' => 'Akun tidak ditemukan.');
        }
        return $return;
    }

    function get_info_akun_by_token($token, $company_code)
    {
        $this->db->select('c.id, p.personal_id, p.fullname, c.company_type, c.start_date_subscribtion, c.end_date_subscribtion')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('c.code', $company_code)
        ->where('p.token', $token);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list['personal_id'] = $rows->personal_id;
                $list['company_id'] = $rows->id;
                $list['fullname'] = $rows->fullname;
                $list['start_date_subscribtion'] = ($rows->company_type == 'limited' ? $rows->start_date_subscribtion : 'unlimited');
                $list['end_date_subscribtion'] = ($rows->company_type == 'limited' ? $rows->end_date_subscribtion : 'unlimited');
            }
        }
        return $list;
    }

    function check_token_info($token, $company_code)
    {
        $error = 0;
        $error_msg = '';
        $this->db->select('p.personal_id, p.fullname, c.start_date_subscribtion, c.end_date_subscribtion, c.company_type')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('p.token', $token)
        ->where('c.code', $company_code);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $row = $q->row();
            $return = array(
                'error' => false,
                'error_msg' => 'Success',
                'personal_id' => $row->personal_id,
                'fullname' => $row->fullname,
                'start_date_subscribtion' =>  $row->company_type == 'limited' ? $row->start_date_subscribtion : 'unlimited',
                'end_date_subscribtion' => $row->company_type == 'limited' ? $row->end_date_subscribtion : 'unlimited'
            );
        } else {
            $return = array(
                'error' => true,
                'error_msg' => 'Token tidak valid.'
            );
        }
        return $return;
    }

    function validation_token($kode_perusahaan, $token)
    {
        $this->db->select('personal_id')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('c.code', $kode_perusahaan)
        ->where('p.token', $token)
        ->where('p.personal_id', $this->session->userdata($this->config->item('apps_name'))['user_id']);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // get company 
    function get_company_id_personal_id( $token, $company_code ) {

        $this->db->select('p.personal_id, p.company_id')
            ->from('personal AS p')
            ->join('company AS c', 'p.company_id=c.id', 'inner')
            ->where('p.token', $token)
            ->where('c.code', $company_code);
        $q = $this->db->get();
        $arr = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $arr['id'] = $rows->personal_id;
                $arr['company_id'] = $rows->company_id;
            }
        }
        return $arr;
    }

    function check_nomor_tujuan_exist_today( $value, $personal_id, $company_id ){
        $this->db->select('ppob.id')
                 ->from('ppob_transaction_history AS ppob')
                 ->join('ppob_transaction_history_company AS hs', 'ppob.id=hs.ppob_transaction_history_id', 'inner')
                 ->where('hs.personal_id', $personal_id)
                 ->where('hs.company_id', $company_id)
                 ->where('ppob.nomor_tujuan', $value)
                 ->where('DATE(ppob.created_at) = DATE(CURDATE())')
                 ->where('ppob.status != "failed"');
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
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

    function get_info_paket($company_code)
    {
        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        # list city name
        $this->db->select('ct.id, ct.city_name')
        ->from('mst_city AS ct')
        ->join('company AS c', 'ct.company_id=c.id', 'inner')
        ->where('c.code', $company_code);
        $q = $this->db->get();
        $list_city = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list_city[$rows->id] = $rows->city_name;
            }
        }
        # list hotel
        $this->db->select('h.id, h.hotel_name, h.star_hotel, ct.city_name')
        ->from('mst_hotel AS h')
        ->join('mst_city AS ct', 'h.city_id=ct.id', 'inner')
        ->join('company AS c', 'ct.company_id=c.id', 'inner')
        ->where('c.code', $company_code);
        $q = $this->db->get();
        $list_hotel = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list_hotel[$rows->id] = array(
                    'name' => $rows->hotel_name,
                    'star_hotel' => (int) $rows->star_hotel,
                    'city_name' => $rows->city_name
                );
            }
        }
        # list
        $this->db->select('p.description, p.company_id, p.id, p.kode, p.paket_name, p.photo, p.departure_date, p.return_date, 
                           p.description, p.jamaah_quota, p.city_visited, p.hotel')
                ->from('paket AS p')
                ->where('p.departure_date >= NOW()')
                ->join('company AS c', 'p.company_id=c.id', 'inner')
                ->where('c.code', $company_code);
                // ->where('p.show_homepage', 'tampilkan');
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            $i = 0;
            foreach ($q->result() as $rows) {
                $info_paket_type = $this->get_paket_type($rows->id, $rows->company_id);

                $photo = 'image_not_found.png';
                if( $rows->photo != '' ){
                     $src = FCPATH . 'image/paket/' . $rows->photo;
                    if (file_exists($src)) {
                         $photo = $rows->photo;
                    }
                }

                $list[$i]['kode'] = $rows->kode;
                $list[$i]['paket_name'] = $rows->paket_name;
                $list[$i]['photo'] = $photo;
                $list[$i]['tanggal_pelaksanaan'] =  $this->date_ops->change_date($rows->departure_date) . ' - ' . $this->date_ops->change_date($rows->return_date);
                $list[$i]['tipe_paket'] = $info_paket_type['list'];
                $list[$i]['description'] = $rows->description;
                $list[$i]['harga_terendah'] = $info_paket_type['low_price'];
                $list[$i]['description'] = $rows->description;
                $list[$i]['jamaah_quota'] = $rows->jamaah_quota . ' Jamaah';
                $list[$i]['city_visited'] = $rows->city_visited != '' ? $this->extract_city($rows->city_visited, $list_city) : 'Tidak ditemukan';
                $list[$i]['hotel'] = $rows->hotel != '' ? $this->extract_hotel($rows->hotel, $list_hotel) : array();
                $i++;
            }
        }
        return $list;
    }

    function get_paket_type($id, $company_id)
    {
        $this->db->select('pp.price, pt.paket_type_name')
        ->from('paket_price AS pp')
        ->join('mst_paket_type AS pt', 'pp.paket_type_id=pt.id', 'inner')
        ->where('pp.paket_id', $id)
        ->where('pp.company_id', $company_id)
        ->order_by('pp.price', 'asc');
        $q = $this->db->get();
        $list = array();
        $low_price = '';
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                if ($low_price == '') {
                    $low_price = $this->kurs . ' ' . $this->text_ops->nominal_currency($rows->price);
                }
                $list[] = $this->kurs . ' ' . number_format($rows->price) . ',00 (Tipe Paket :' . $rows->paket_type_name . ')';
            }
        }
        return array('list' => $list, 'low_price' => $low_price == '' ? $this->kurs . ' 0' : $low_price);
    }

    function extract_hotel($arr, $list_hotel)
    {
        $arr = unserialize($arr);
        $list_arr = array();
        foreach ($arr as $key => $value) : $list_arr[] = $list_hotel[$value];endforeach;
        return $list_arr;
    }

    function extract_city($arr, $list_city)
    {
        $arr = unserialize($arr);
        $list_arr = '';
        $i = 0;
        foreach ($arr as $key => $value) :
            if ($i != 0) {
                $list_arr .= ' - ';
            }
            $list_arr .= strtoupper($list_city[$value]);
            $i++;
        endforeach;
        return $list_arr;
    }

    function get_company_id($company_code)
    {
        $this->db->select('id')
        ->from('company')
        ->where('code', $company_code);
        $company_id = '';
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $company_id = $rows->id;
            }
        }
        return $company_id;
    }

    function get_headline($company_id)
    {
        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $this->db->select('id, headline')
        ->from('headline')
        ->where('company_id', $company_id)
        ->where('tampilkan', 'tampilkan');
        $q = $this->db->get();
        $list = '';
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                if ($list != '') {
                    $list .= ' , ';
                }
                $list .= $rows->headline;
            }
        }
        return $list;
    }

    # update akun
    function update_data_akun($personal_id, $data)
    {
        # disable write log
        $this->write_log = 0;
        # start update
        $this->db->trans_start();
        # update personal data
        $this->db->where('personal_id', $personal_id)->update('personal', $data);
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
            $this->content = '.';
        }
        return $this->status;
    }

    function get_personal_id_by_token($company_code, $token)
    {
        $this->db->select('p.personal_id')
        ->from('personal AS p')
        ->join('company AS c', 'p.company_id=c.id', 'inner')
        ->where('c.code', $company_code)
        ->where('p.token', $token);
        $q = $this->db->get();
        $personal_id = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $personal_id = $rows->personal_id;
            }
        }
        return $personal_id;
    }

    function get_rek_info($company_id)
    {
        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $this->db->select('cbt.id, cbt.account_number, mbt.nama_bank')
        ->from('company_bank_transfer AS cbt')
        ->join('mst_bank_transfer AS mbt', 'cbt.bank_id=mbt.id', 'inner')
        ->where('cbt.company_id', $company_id);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list[] = $rows->nama_bank;
                //$list[] = array('id' => $rows->id, 'name' => $rows->nama_bank.' ('.$rows->account_number.')');
            }
        }
        return $list;
    }

    function get_list_tambah_saldo($company_id,$token)
    {
        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $activity_type = array('deposit', 'deposit_paket');
        $this->db->select('mtr.transaction_number, mtr.amount, mtr.amount_code, mtr.sending_payment_status, mtr.status_request, mtr.account_name,
        mtr.status_note, mtr.bank_account, mbt.nama_bank, DATEDIFF(NOW(), mtr.last_update) AS day, mtr.last_update ')
        ->from('member_transaction_request AS mtr')
        ->join('personal AS p', 'mtr.personal_id=p.personal_id', 'inner')
        ->join('mst_bank_transfer AS mbt', 'mtr.bank_id=mbt.id', 'inner')
        ->where('p.token', $token)
        ->where('mtr.company_id', $company_id)
        ->where('DATEDIFF(NOW(), mtr.last_update) <= 2')
        ->where_in('mtr.activity_type', $activity_type)
        ->order_by('mtr.last_update', 'desc')
        ->limit(5);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list[] = array(
                    'trx_number' => $rows->transaction_number,
                    'amount' => $this->kurs . ' '. number_format($rows->amount + $rows->amount_code),
                    'sending_payment_status' => $rows->sending_payment_status,
                    'status_request' => $rows->status_request,
                    'status_note' => $rows->status_note,
                    'bank_account' => $rows->bank_account,
                    'nama_account' => $rows->account_name == '' ? '-' : $rows->account_name,
                    'nama_bank' => $rows->nama_bank,
                    'day' => $rows->day,
                    'trx_date' => $rows->last_update
                );
            }
        }
        return $list;
    }

    function check_nama_bank_exist($nama_bank)
    {
        $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $this->db->select('cbt.id')
        ->from('company_bank_transfer AS cbt')
        ->join('mst_bank_transfer AS mbt', 'cbt.bank_id=mbt.id', 'inner')
        ->where('mbt.nama_bank', $nama_bank)
        ->where('cbt.company_id', $company_id);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function get_bank_info($company_id, $nama_bank)
    {
        $this->db->select('mbt.id, cbt.account_name, cbt.account_number')
        ->from('mst_bank_transfer AS mbt')
        ->join('company_bank_transfer AS cbt', 'mbt.id=cbt.bank_id', 'inner')
        ->where('mbt.nama_bank', $nama_bank)
        ->where('cbt.company_id', $company_id);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list = array(
                    'bank_id' => $rows->id,
                    'account_name' => $rows->account_name == '' ? '-' : $rows->account_name,
                    'account_number' => $rows->account_number
                );
            }
        }
        return $list;
    }


    function get_list_amount_code_exist($company_id, $personal_id)
    {
        $this->db->select('amount_code')
        ->from('member_transaction_request')
        ->where('company_id', $company_id)
        ->where('personal_id', $personal_id);
        $list_code = array();
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list_code[] = $rows->amount_code;
            }
        }
        return $list_code;
    }

    function gen_amount_code($company_id, $personal_id)
    {

        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $list_amount_code = $this->get_list_amount_code_exist($company_id, $personal_id);
        $rand = '';
        do {
            $rand = $this->random_numeric(3);
            $q = $this->db->select('id')
            ->from('member_transaction_request')
            ->where('personal_id', $personal_id)
            ->where('amount_code', $rand)
            ->where('status_request', 'diproses')
            ->where('DATEDIFF(NOW(), last_update) <= 2')
            ->get();
            if ($q->num_rows() == 0) {
                $feedBack = true;
            }
        } while ($feedBack == false);
        return $rand;
    }


    function gen_transction_number( $company_id )
    {
        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $rand = '';
        do {
            $rand = $this->random_alpha_numeric(10);
            $q = $this->db->select('id')
            ->from('member_transaction_request')
            ->where('company_id', $company_id)
            ->where('transaction_number', $rand)
            ->get();
            if ($q->num_rows() == 0) {
                $feedBack = true;
            }
        } while ($feedBack == false);
        return $rand;
    }

    function insert_member_transaction_request( $data )
    {
        # start transaction
        $this->db->trans_start();
        # insert artikel
        $this->db->insert('member_transaction_request', $data);
        # Transaction Complete
        $this->db->trans_complete();
        # Filter Status
        if ($this->db->trans_status() === FALSE) {
            # Something Went Wrong.
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

    function check_request_proses_axist( $personal_id, $company_id ) {
        $this->db->select('id')
        ->from('member_transaction_request')
        ->where('status_request', 'diproses')
        ->where('sending_payment_status', 'belum_dikirim')
        ->where('company_id', $company_id)
        ->where('personal_id', $personal_id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ){
            return true;
        }  else{
            return false;
        }
    }

    function deleteRequestTiket($personal_id, $company_id)
    {
        # start transaction
        $this->db->trans_start();
        # delete slider data
        $this->db->where('personal_id', $personal_id)
        ->where('company_id', $company_id)
        ->where('status_request', 'diproses')
        ->where('sending_payment_status', 'belum_dikirim')
        ->delete('member_transaction_request');
        # Transaction Complete
        $this->db->trans_complete();
        # Filter Status
        if ($this->db->trans_status() === FALSE) {
            # Something Went Wrong.
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

    function check_transaction_number_axist($trx_number, $token, $company_code)
    {
        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $this->db->select('mtr.id')
        ->from('member_transaction_request AS mtr')
        ->join('personal AS p', 'mtr.personal_id=p.personal_id', 'inner')
        ->join('company AS c', 'mtr.company_id=c.id', 'inner')
        ->where('c.code', $company_code)
        ->where('mtr.transaction_number', $trx_number)
        ->where('p.token', $token);
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    # sudah kirim
    function sudahdikirim($data, $trx_number, $personal_id, $company_id)
    {
        // $company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        # start transaction
        $this->db->trans_start();
        # update process
        $this->db->where('company_id', $company_id)
        ->where('personal_id', $personal_id)
        ->where('transaction_number', $trx_number)
        ->update('member_transaction_request', $data);
        # Transaction Complete
        $this->db->trans_complete();
        # Filter Status
        if ($this->db->trans_status() === FALSE) {
            # Something Went Wrong.
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

    function get_list_withdraw( $company_id, $personal_id ) {
        $this->db->select('id, transaction_number, amount, status_request, status_note, account_name, account_number, last_update')
                 ->from('withdraw_member')
                 ->where('company_id', $company_id)
                 ->where('personal_id', $personal_id)
                 ->order_by('last_update', 'desc')
                 ->limit(5);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
                $list[] = array('id' => $rows->id, 
                                'transaction_number' => $rows->transaction_number, 
                                'amount' => $this->kurs . ' ' . number_format($rows->amount).',-',
                                'status_request' => $rows->status_request, 
                                'account_name' => $rows->account_name,
                                'account_number' => $rows->account_number,
                                'status_note' => $rows->status_note,
                                'last_update' => $rows->last_update);
            }
        }
        return $list;
    }

    // check status proses exist
    function check_status_proses_exist($company_id, $personal_id){
        $this->db->select('id')
             ->from('member_transaction_request')
             ->where('status_request', 'diproses')
             ->where('company_id', $company_id)
             ->where('personal_id', $personal_id);
        $q = $this->db->get();
        if( $q->num_rows () > 1 ){
            return true;
        }else{
            return false;
        } 
    }

    function check_with_draw_exist($company_id, $personal_id){
         $this->db->select('id')
             ->from('withdraw_member')
             ->where('status_request', 'diproses')
             ->where('company_id', $company_id)
             ->where('personal_id', $personal_id);
        $q = $this->db->get();
        if( $q->num_rows () > 0  ){
            return true;
        }else{
            return false;
        }   
    }

    function gen_transction_number_withdraw( $company_id ) {
        $rand = '';
        do {
            $rand = $this->random_alpha_numeric(10);
            $q = $this->db->select('id')
                          ->from('withdraw_member')
                          ->where('company_id', $company_id)
                          ->where('transaction_number', $rand)
                          ->get();
            if ($q->num_rows() == 0) {
                $feedBack = true;
            }
        } while ($feedBack == false);
        return $rand;
    }

    function get_markup_withdraw($company_id){
        $this->db->select('markup_withdraw')
                 ->from('company')
                 ->where('id', $company_id);
        $q = $this->db->get();
        $markup = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $markup = $rows->markup_withdraw;
            }
        }
        return $markup;
    }

    // check maksimal withd
    function check_maksimal_with_draw( $company_id, $personal_id, $nominal) {
        // count deposit 
        $this->db->select('debet, kredit')
                 ->from('deposit_transaction')
                 ->where('company_id', $company_id)
                 ->where('personal_id', $personal_id)
                 ->where('transaction_requirement', 'deposit');
        $q = $this->db->get();
        $debet = 0;
        $kredit = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $debet = $debet + $rows->debet;
                $kredit = $kredit + $rows->kredit;
            }
        }
        $total_saldo_deposit = $debet  - $kredit ;
        // filter
        if( $nominal > ( $total_saldo_deposit ) ) {
            return false;
        }else{
            return true;
        }

    }

    // get info akun member
    function get_info_akun_member( $company_id, $personal_id ) {

         $this->db->select('p.fullname, p.identity_number, p.birth_place, p.birth_date, p.account_name, p.number_account, p.bank_id ')
                  ->from('personal AS p')
                  ->join('mst_bank_transfer AS mt', 'p.bank_id=mt.id', 'inner')
                  ->where('p.company_id', $company_id)
                  ->where('p.personal_id', $personal_id);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list['fullname'] = $rows->fullname;
                $list['identity_number'] = $rows->identity_number;
                $list['birth_place'] = $rows->birth_place;
                $list['birth_date'] = $rows->birth_date;
                $list['account_name'] = $rows->account_name;
                $list['number_account'] = $rows->number_account;
                $list['bank_id'] = $rows->bank_id;
            }
        }
        return $list;

    }

    // insert withdraw
    function saveWithDraw( $data ){
        # start transaction
        $this->db->trans_start();
        # insert withdraw
        $this->db->insert('withdraw_member', $data);
        # Transaction Complete
        $this->db->trans_complete();
        # Filter Status
        if ($this->db->trans_status() === FALSE) {
            # Something Went Wrong.
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


    function check_paket_kode_exist( $kode_paket, $company_id) {
        $this->db->select('id')
                ->from('paket')
                ->where('company_id', $company_id)
                ->where('kode', $kode_paket);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // get list tipe paket
    function get_list_tipe_paket($company_id, $kode_paket){
        $this->db->select('pp.paket_type_id, pp.price, mpt.paket_type_name')
            ->from('paket_price AS pp')
            ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
            ->join('mst_paket_type AS mpt', 'pp.paket_type_id=mpt.id', 'inner')
            ->where('pp.company_id', $company_id)
            ->where('p.kode', $kode_paket);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $list[] = array('id' => $rows->paket_type_id, 'name' => $rows->paket_type_name. ' ('. $this->kurs . ' ' . number_format($rows->price).')' );
            }
        }
        return $list;
    }

    // check tipe paket id
    function check_tipe_paket_id( $company_id, $kode_paket, $tipe_paket_id ) {
        $this->db->select('pp.paket_type_id')
            ->from('paket_price AS pp')
            ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
            ->join('company AS c', 'pp.company_id=c.id', 'inner')
            ->where('pp.company_id', $company_id)
            ->where('p.kode', $kode_paket)
            ->where('pp.paket_type_id', $tipe_paket_id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    function get_info_tipe_paket( $company_id, $paket_code, $tipe_paket_id ) {
        $this->db->select('pp.paket_type_id, pp.paket_id, price, p.paket_name')
                 ->from('paket_price AS pp')
                 ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
                 ->join('company AS c', 'pp.company_id=c.id', 'inner')
                 ->where('pp.company_id', $company_id)
                 ->where('p.kode', $paket_code)
                 ->where('pp.paket_type_id', $tipe_paket_id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $list = array('paket_id' => $rows->paket_id, 
                              'tipe_paket_id' => $rows->paket_type_id, 
                              'price' => $rows->price,
                              'paket_name' => $rows->paket_name);
            }
        }
        return $list;
    }

    function check_paket_sudah_dibeli( $company_id, $paket_id, $tipe_paket_id, $personal_id ) {
        $this->db->select('ptj.jamaah_id')
                 ->from('paket_transaction_jamaah AS ptj')
                 ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
                 ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
                 ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                 ->where('ptj.company_id', $company_id)
                 ->where('pt.paket_id', $paket_id)
                 ->where('pt.paket_type_id', $tipe_paket_id)
                 ->where('p.personal_id', $personal_id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    # info tabungan member jamaah
    function get_info_tabungan_member_jamaah($company_id, $personal_id){
        $this->db->select('debet, kredit')
                 ->from('deposit_transaction')
                 ->where('transaction_requirement', 'paket_deposit')
                 ->where('company_id', $company_id)
                 ->where('personal_id', $personal_id);
        $q = $this->db->get();
        $debet = 0;
        $kredit = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $debet = $debet + $rows->debet;
                $kredit = $kredit + $rows->kredit;
            }
        }
        return $debet - $kredit;
    }

    # get jamaah id by personal id
    function get_jamaah_id_by_personal_id($company_id, $personal_id){
        $this->db->select('id')
            ->from('jamaah')
            ->where('company_id', $company_id)
            ->where('personal_id', $personal_id);
        $q = $this->db->get();
        $jamaah_id = 0;
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $jamaah_id = $rows->id;
            }
        }
        return $jamaah_id;
    }




    # get info need mahram
    function get_info_need_mahram( $jamaah_id, $company_id )
    {
        $this->db->select('gender, birth_date')
                 ->from('jamaah AS j')
                 ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                 ->where('id', $jamaah_id)
                 ->where('p.company_id', $company_id);
        $q = $this->db->get();
        $return = false;
        if ( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $row ) {
                $umur = $this->date_ops->get_umur($row->birth_date);
                if ($row->gender == '0') {
                    if ($umur <= 17) {
                        $return = true;
                    }
                } elseif ($row->gender == '1') {
                    if ($umur <= 46) {
                        $return = true;
                    }
                }
            }
        }
      return $return;
    }

    // get biaya mahram
    function getBiayaMahram($paket_id,$company_id)
    {
      $this->db->select('mahram_fee')
         ->from('paket')
         ->where('company_id', $company_id)
         ->where('id', $paket_id);
      $r = $this->db->get();
      $biaya_mahram = 0;
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $row) {
            $biaya_mahram = $row->mahram_fee;
         }
      }
      return $biaya_mahram;
    }

    #
    function get_total_deposit_paket($jamaah_id, $company_id)
    {
      $this->db->select('dt.debet, dt.kredit')
         ->from('deposit_transaction AS dt')
         ->join('pool_deposit_transaction AS pdt', 'dt.id=pdt.deposit_transaction_id', 'inner')
         ->join('pool AS p', 'pdt.pool_id=p.id', 'inner')
         ->where('dt.company_id', $company_id)
         ->where('p.jamaah_id', $jamaah_id)
         ->where('p.active', 'active');
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      // $total_deposit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $debet = $debet + $rows->debet;
            $kredit = $kredit + $rows->kredit;
         }
      }

      return ($debet - $kredit);
    }

    // get info deposit by jamaah id
    function getInfoDepositorByJamaahId($jamaah_id,$company_id)
    {
      $this->db->select('p.fullname, p.nomor_whatsapp, p.address')
               ->from('jamaah AS j')
               ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
               ->where('j.id', $jamaah_id)
               ->where('j.company_id', $company_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $array['fullname'] = $row->fullname;
         $array['nomor_whatsapp'] = $row->nomor_whatsapp;
         $array['address'] = $row->address;
      }
      return $array;
    }

    // get pool info
    function get_info_pool($jamaah_id,$company_id)
    {
      $this->db->select('id, fee_keagenan_id')
         ->from('pool')
         ->where('company_id', $company_id)
         ->where('jamaah_id', $jamaah_id)
         ->where('active', 'active');
      $q = $this->db->get();
      $pool_id = 0;
      $fee_keagenan_id = 0;
      $handover_facilities = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $pool_id = $rows->id;
            $fee_keagenan_id = $rows->fee_keagenan_id;
            $this->db->select('invoice, facilities_id, officer, receiver_name, receiver_identity, date_transaction')
               ->from('pool_handover_facilities')
               ->where('company_id', $company_id)
               ->where('pool_id', $rows->id);
            $r = $this->db->get();
            if ($r->num_rows() > 0) {
               foreach ($r->result() as $rowr) {
                  $handover_facilities[] = array(
                     'invoice' => $rowr->invoice,
                     'facilities_id' => $rowr->facilities_id,
                     'officer' => $rowr->officer,
                     'receiver_name' => $rowr->receiver_name,
                     'receiver_identity' => $rowr->receiver_identity,
                     'date_transaction' => $rowr->date_transaction
                  );
               }
            }
         }
      }
      return array('pool_id' => $pool_id, 'fee_keagenan_id' => $fee_keagenan_id, 'handover_facilities' => $handover_facilities);
   }

   // get index daftar paket
   function get_index_daftar_paket($limit = 6, $company_id, $personal_id){
        $this->db->select('p.id, p.paket_name, p.departure_date, p.description')
                 ->from('paket_transaction_jamaah AS ptj')
                 ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
                 ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
                 ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
                 ->where('ptj.company_id', $company_id)
                 ->where('j.personal_id', $personal_id)
                 ->order_by('pt.input_date', 'desc')
                 ->limit($limit);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $list[] = array('id' => $row->id, 
                                'paket_name' => $row->paket_name, 
                                'departure_date' => $this->date_ops->change_date($row->departure_date),
                                'status' => ($row->departure_date <= date('Y-m-d') ? 'sudah_berangkat' : 'belum_berangkat'),
                                'deskripsi' => $row->description);
            }
        }

        return $list;
   }

    // check paket id
    function check_paket_id( $company_id, $paket_id ) {
        $this->db->select('id')
            ->from('paket')
            ->where('company_id', $company_id)
            ->where('id', $paket_id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // get paket info
    function get_paket_info( $company_id, $personal_id, $paket_id ) {
        $this->db->select('p.id, p.paket_name, p.kode, p.description, p.departure_date, pt.total_paket_price, 
                (SELECT COUNT(jamaah_id) 
                    FROM paket_transaction_jamaah AS pj
                    INNER JOIN jamaah AS jj ON pj.jamaah_id=jj.id 
                    INNER JOIN paket_transaction AS pt ON pj.paket_transaction_id=pt.id
                WHERE pj.company_id="'.$company_id.'" AND jj.personal_id="'.$personal_id.'" AND pt.paket_id="'.$paket_id.'" ) AS number_member')
            ->from('paket_transaction_jamaah AS ptj')
            ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
            ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
            ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
            ->where('ptj.company_id', $company_id)
            ->where('j.personal_id', $personal_id)
            ->where('p.id', $paket_id);
        $list = array();    
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $list['id'] = $rows->id;
                $list['paket_name'] = $rows->paket_name;
                $list['kode'] = $rows->kode;
                $list['departure_date'] = $rows->departure_date;
                $list['description'] = $rows->description;
                $list['price'] = $rows->total_paket_price;
                $list['number_member'] = $rows->number_member;
            }
        }
        return $list;
    }


    function get_index_riwayat_transaksi( $limit = 6, $company_id, $personal_id ){
        $this->db->select('nomor_transaction, debet, kredit, transaction_requirement, info, input_date')
                 ->from('deposit_transaction')
                 ->where('company_id', $company_id)
                 ->where('personal_id', $personal_id)
                 ->order_by('input_date', 'desc')
                 ->limit($limit);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $status = '';
                if( $rows->transaction_requirement == 'deposit'){
                    $status = 'DEPOSIT';
                }elseif ( $rows->transaction_requirement == 'paket_deposit' ) {
                    $status = 'TABUNGAN UMRAH';
                }elseif ( $rows->transaction_requirement == 'paket_payment' ) {
                    $status = 'PEMBELIAN PAKET';
                }elseif ( $rows->transaction_requirement == 'transaction' ) {
                    $status = 'PPOB';
                }

                $biaya =  $this->kurs . ' '.number_format($rows->debet != 0 ? $rows->debet : $rows->kredit).',-';
                $list[] = array('invoice' => $rows->nomor_transaction, 
                                'biaya' => $biaya, 
                                'status' => $status,
                                'info' => $rows->info, 
                                'date' => $rows->input_date);
            }
        }

        return $list;
    }

    // get notif
    function get_notif( $company_id, $personal_id ) {
        // notif read
        $this->db->select('notif_id')
            ->from('notif_reader')
            ->where('company_id', $company_id)
            ->where('personal_id', $personal_id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $list[] = $rows->notif_id;
            }
        }
        // notif
        $this->db->select('id')
            ->from('notif')
            ->where('company_id', $company_id);
        if( count($list) > 0 ){
            $this->db->where_not_in('id', $list);
        }    
        $r = $this->db->get();
        // num rows
        return $r->num_rows(); 
    }

    // get index notif
    function get_index_notif( $limit = 6, $company_id, $personal_id ) {
        # notif
        $this->db->select('notif_id')
                 ->from('notif_reader')
                 ->where('company_id', $company_id)
                 ->where('personal_id', $personal_id);
        $notif_id = array();
        $q = $this->db->get();
        if( $q->num_rows() > 0 ){
            foreach ( $q->result() as $rows) {
                $notif_id []= $rows->notif_id;
            }
        }
        # message
        $this->db->select('id, message, title')
            ->from('notif')
            ->where('company_id', $company_id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            foreach ( $q->result() as $rows ) {
                $list[] = array('id' => $rows->id, 
                                'title' => $rows->title,
                                'messages' => $rows->message,
                                'status' => in_array($rows->id, $notif_id) ? 'read' : 'unread' );
            }
        }
        # return 
        return $list;    
    }

    // check message id
    function check_message_id( $company_id, $id ) {
        $this->db->select('id')
                 ->from('notif')
                 ->where('company_id', $company_id)
                 ->where('id', $id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // get detail message
    function get_detail_message( $company_id, $message_id ) {
        $this->db->select('title, message')
            ->from('notif')
            ->where('company_id', $company_id)
            ->where('id', $message_id);
        $list = array();
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $list['title'] = $rows->title;
                $list['message'] = $rows->message;
            }
        }
        return $list;
    }

    function check_category_ppob( $type = 'prabayar', $category ) {
        if( $type == 'prabayar' ){
             $this->db->select('id')
                      ->from('ppob_prabayar_category')
                      ->where('category_code', $category);
        }else {
            $this->db->select('id')
                     ->from('ppob_pascabayar_category')
                     ->where('category_code', $category);
        }
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // chec kode product
    function check_kode_product_ppob( $type, $category, $prefix, $kode_produk ) {
        if( $type == 'prabayar' ) {
            $this->db->select('ppp.id')
                     ->from('ppob_prabayar_product AS ppp')
                     ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                     ->join('prefix_operator AS po', 'ppo.id=po.operator_id', 'inner')
                     ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                     ->where('ppp.product_code', $kode_produk)
                     ->where('po.prefix', $prefix)
                     ->where('ppc.category_code', $category);

        } else {
            $this->db->select('ppp.id')
                     ->from('ppob_pascabayar_product AS ppp')
                     ->join('ppob_pascabayar_category AS ppc', 'ppp.category_id=ppc.id', 'inner')
                     ->where('ppp.product_code', $kode_produk)
                     ->where('ppc.category_code', $category);

        }
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // get markup per product
    function get_markup_per_product( $type, $kode_produk ) {
        $markup = 0;
        if( $type == 'prabayar' ) {
            $this->db->select('application_markup')
                     ->from('ppob_prabayar_product')
                     ->where('product_code', $kode_produk);
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
                foreach ( $q->result() as $row) {
                    $markup = $row->application_markup;
                }
            }         
        }elseif ( $type == 'pascabayar' ) {
            // application_markup
             $this->db->select('application_markup')
                     ->from('ppob_pascabayar_product')
                     ->where('product_code', $kode_produk);
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
                foreach ( $q->result() as $row) {
                    $markup = $row->application_markup;
                }
            }  
        }
        return $markup;
    }

    // gen transaction number ppob
    function gen_transction_number_ppob(){
        $feedBack = false;
        $rand = '';
        do {
            $rand = $this->random_numeric(6);
            $q = $this->db->select('id')
                          ->from('ppob_transaction_history')
                          ->where('transaction_code', $rand)
                          ->get();
            if ($q->num_rows() == 0) {
                $feedBack = true;
            }
        } while ($feedBack == false);
        return $rand;
    }

    // get company markup
    function get_company_markup( $company_id ) { 
        $this->db->select('markup_company, product_code')
                 ->from('ppob_company_markup')
                 ->where('company_id', $company_id);
        $q = $this->db->get();
        $markup = array();
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
                $markup[$rows->product_code]= $rows->markup_company;
            }
        }
        $this->db->select('company_markup')
                     ->from('company')
                     ->where('id', $company_id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $markup ['all']= $rows->company_markup;
            }
        }

        return $markup;
    }

    // check ref id
    function check_ref_id(  $ref_id, $product_id ) {
        $this->db->select('id')
                 ->from('ppob_transaction_history')
                 ->where('transaction_code', $ref_id)
                 ->where('product_code', $product_id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // check category ppob
    function check_category_ppob_2( $type, $category ) {
        if( $type == 'prabayar' ) {
            $this->db->select('id')
                ->from('ppob_prabayar_category')
                ->where('category_code', $category);
            $q = $this->db->get();    
        }else{
            $this->db->select('id')
                ->from('ppob_pascabayar_category')
                ->where('category_code', $category);
            $q = $this->db->get();
        }
        // num rows
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // check nomor tujuan ppob
    function check_nomor_tujuan_ppob( $category, $prefix ) {
        $this->db->select('po.id')
            ->from('prefix_operator AS po')
            ->join('ppob_prabayar_operator AS ppo', 'po.operator_id=ppo.id', 'inner')
            ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
            ->where('po.prefix', $prefix)
            ->where('ppc.category_code', $category);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // get operator code
    function get_operator_code( $category, $nomor_tujuan ) {
        $list = array('TL');
        if( ! in_array( $category, $list ) ) {
            // print("123");
            // print($nomor_tujuan);
            // print("123");
            $prefix = substr($nomor_tujuan,0,4);
            $this->db->select('ppo.operator_code')
                     ->from('prefix_operator AS po')
                     ->join('ppob_prabayar_operator AS ppo', 'po.operator_id=ppo.id', 'inner')
                     ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                     ->where('ppc.category_code', $category)
                     ->where('po.prefix', $prefix);
        }else{
            $this->db->select('ppo.operator_code')
                     ->from('ppob_prabayar_operator AS ppo')
                     ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                     ->where('ppc.category_code', $category);
        }
        $q = $this->db->get();
        $operator_code = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $operator_code[] = $rows->operator_code;
            }
        }
        return $operator_code;
    }

    function get_markup_all_product($type){
        $feedBack = array();
        if( $type == 'prabayar' ) {
            $this->db->select('product_code, price, application_markup')
                     ->from('ppob_prabayar_product');
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
                foreach ( $q->result() as $rows ) {
                    $feedBack[$rows->product_code] = $rows->application_markup;
                }
            }
        }else{
            $this->db->select('product_code, product_fee, application_markup')
                     ->from('ppob_pascabayar_product');
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
                foreach ( $q->result() as $rows ) {
                    $feedBack[$rows->product_code] = $rows->application_markup;
                }
            }
        }
        return $feedBack;
    }

    // get saldo perusahaan xxxx
    function get_saldo_perusahaan( $company_id ){
        $this->db->select('saldo')
            ->from('company')
            ->where('id', $company_id);
        $q = $this->db->get();
        $saldo = 0;
        if( $q->num_rows() > 0 ){
            foreach ( $q->result() as $rows ) {
                $saldo = $rows->saldo;
            }
        }
        return $saldo;
    }

    // get index riwayat ppob
    function get_index_riwayat_ppob($limit = 6, $company_id, $personal_id){
        $this->db->select('pth.id, pth.transaction_code, ppc.category_code, pth.nomor_tujuan, pth.product_code, pth.application_price, pth.status, pth.created_at, pthc.company_price, pth.server')
            ->from('ppob_transaction_history AS pth')
            ->join('ppob_transaction_history_company AS pthc', 'pth.id=pthc.ppob_transaction_history_id', 'inner')
            ->join('ppob_prabayar_product AS ppp', 'pth.product_code=ppp.product_code', 'inner')
             ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
             ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
            ->where('pthc.personal_id', $personal_id)
            ->where('pthc.company_id', $company_id)
            ->order_by('pth.created_at', 'desc')
            ->limit($limit);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $status = $row->status;
                if( $row->nomor_tujuan != '' ) {
                    if( $row->status == 'process' ) {
                        if( $row->server == 'iak') {
                             $check_status = $this->iak->check_status_transaksi($row->transaction_code);
                            if ( isset( $check_status->data->status ) AND $check_status->data->status == 1  ) {
                                $data_update = array();
                                $data_update['status'] = 'success';
                                if ( isset( $check_status->data->sn ) ) {
                                     $data_update['ket'] = $check_status->data->sn;  
                                }else{
                                     $data_update['ket'] = 'Pembelian '.$row->product_code.' ke '. $row->nomor_tujuan.' Berhasil dilakukan';;
                                }
                                // update
                                if( $this->update_status_transaksi_ppob( $row->transaction_code, $data_update ) ){
                                    $status = 'success';
                                };
                            } else if( isset( $check_status->data->status ) AND $check_status->data->status == 2 ) {
                                $data_update = array();
                                $data_update['status'] = 'failed';
                                $data_update['ket'] = 'Pembelian '.$row->product_code.' ke '.$row->nomor_tujuan.' Gagal dilakukan';
                                // update data
                                if( $this->update_status_transaksi_ppob( $row->transaction_code, $data_update ) ){
                                    // saldo company now
                                    $saldo_company_now = $this->get_saldo_company_now($company_id);
                                    # get saldo
                                    $get_back_saldo = $saldo_company_now + $row->application_price;
                                    // delete failed ppob
                                    $this->delete_failed_ppob_transaction($company_id, $row->transaction_code, $get_back_saldo);
                                    // get status
                                    $status = 'failed';
                                };
                            }
                        } elseif ( $row->server == 'tripay' ) {
                            $check_status = $this->tripay->check_status_transaksi($row->transaction_code);

                            $feedBack = array();
                            $feedBack['transaction_code'] = $row->transaction_code;
                            $feedBack['product_code'] = $row->product_code;
                            $feedBack['nomor_tujuan'] = $row->nomor_tujuan;
                            $feedBack['price'] = $this->kurs . ' '.number_format($row->company_price);
                            if( $row->category_code == 'TL' ) {
                                if ( isset( $check_status->success ) AND $check_status->success == true  ) {
                                    if ( isset( $check_status->data->status ) AND $check_status->data->status == 1 ) { // sukses
                                        $feedBack['pesan'] = $check_status->data->note;
                                        $feedBack['status'] = 'Sukses';
                                        $status = 'success';
                                    } else if ( isset( $check_status->data->status ) AND ( $check_status->data->status == 2 || $check_status->data->status == 3 ) ) {
                                        $feedBack['pesan'] = 'Pembelian '.$row->product_code.' ke '.$row->nomor_tujuan.' Gagal dilakukan';
                                        $feedBack['status'] = 'Gagal';
                                        // saldo company now
                                        $saldo_company_now = $this->get_saldo_company_now($company_id);
                                        # get saldo
                                        $get_back_saldo = $saldo_company_now + $row->application_price;
                                        $feedBack['get_back_saldo'] = $get_back_saldo;
                                        $status = 'failed';
                                    } else {
                                        $feedBack['pesan'] = 'Proses';
                                        $feedBack['status'] = 'Proses';
                                    }
                                }
                            }else{
                                if ( isset( $check_status->data->status ) AND $check_status->data->status == 1 ) { // sukses
                                    $feedBack['pesan'] = 'Pembelian '.$row->product_code.' ke '.$row->nomor_tujuan.' Berhasil dilakukan';
                                    $feedBack['status'] = 'Sukses';
                                    $status = 'success';
                                } else if ( isset( $check_status->data->status ) AND ( $check_status->data->status == 2 || $check_status->data->status == 3 ) ) {
                                    $feedBack['pesan'] = 'Pembelian '.$row->product_code.' ke '.$row->nomor_tujuan.' Gagal dilakukan';
                                    $feedBack['status'] = 'Gagal';
                                    // saldo company now
                                    $saldo_company_now = $this->get_saldo_company_now($company_id);
                                    # get saldo
                                    $get_back_saldo = $saldo_company_now + $row->application_price;
                                    $feedBack['get_back_saldo'] = $get_back_saldo;
                                    $status = 'failed';
                                } else {
                                    $feedBack['pesan'] = 'Proses';
                                    $feedBack['status'] = 'Proses';
                                }
                            }

                            $this->update_status_ppob($feedBack, $company_id );
                        }
                    }
                    $list[] = array('id' => $row->id, 
                                    'transaction_code' => $row->transaction_code, 
                                    'product_code' => $row->product_code, 
                                    'company_price' => $this->kurs . ' ' .number_format($row->company_price).',-', 
                                    'status' => strtoupper($status), 
                                    'created_at' => $row->created_at);
                }
            }
        }

        return $list;
    }

    function update_status_ppob($feedBack, $company_id){
        # Starting Transaction
      $this->db->trans_start();  
      // status
      if( $feedBack['status'] == 'Gagal' ) {
         # update data ppob_transaction_history
         $this->db->where('transaction_code', $feedBack['transaction_code'])
                  ->update('ppob_transaction_history', array('ket' => $feedBack['pesan'], 'status' => 'failed'));
         # delete deposit transaction
         $this->db->where('info', 'Pembelian Produk PPOB dengan Nomor Transaksi:'.$feedBack['transaction_code'])->delete('deposit_transaction');
         # delete jurnal
         $this->db->where('source', 'ppob:transaction_code:'.$feedBack['transaction_code'])->delete('jurnal');
         # update data saldo company
         $this->db->where('id', $company_id)
                  ->update('company', array('saldo' => $feedBack['get_back_saldo']));
      }else if( $feedBack['status'] == 'Sukses' ){
         # update data ppob_transaction_history
         $this->db->where('transaction_code', $feedBack['transaction_code'])
                  ->update('ppob_transaction_history', array('ket' => $feedBack['pesan'], 'status' => 'success'));
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

    // get transaction
    function get_transaction($transaction_code){
        $this->db->select('*')
                 ->from('ppob_transaction_history')
                 ->where('transaction_code', $transaction_code);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            foreach ( $q->result() as $row ) {
                $list['transaction_code'] = $row->transaction_code;
                $list['nomor_tujuan'] = $row->nomor_tujuan;
                $list['product_code'] = $row->product_code;
                $list['server'] = $row->server;
                $list['server_price'] = $row->server_price;
                $list['application_price'] = $row->application_price;
                $list['status'] = $row->status;
                $list['ket'] = $row->ket;
            }
        }
        return $list;       
    }

    function get_saldo_company_now( $id ){
        $this->db->select('saldo')
            ->from('company')
            ->where('id', $id);
        $sald0 = 0;
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $saldo = $rows->saldo;
            }
        }
        return $saldo;
    }

    // delete failed ppob transaction
    function delete_failed_ppob_transaction($company_id, $transaction_code, $price){
        # Starting Transaction
        $this->db->trans_start();
        # delete deposit
        $this->db->where('info', 'Pembelian Produk PPOB dengan Nomor Transaksi:' . $transaction_code )
            ->where('company_id', $company_id)
            ->delete('deposit_transaction');
        # delete jurnal
        $this->db->where('source', 'ppob:transaction_code:' . $transaction_code )
            ->where('company_id', $company_id)
            ->delete('jurnal');
        # company saldo transaction    
        $this->db->where('ket', 'PPOB:transaction_code:' . $transaction_code )
            ->where('company_id', $company_id)
            ->delete('company_saldo_transaction');    
        # update data saldo company
        $this->db->where('id', $company_id)
               ->update('company', array('saldo' => $price));
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

    // update status transaksi ppob
    function update_status_transaksi_ppob( $transaction_code, $data ){
        # Starting Transaction
        $this->db->trans_start();
        # update data ppob_transaction_history
        $this->db->where('transaction_code', $transaction_code)
                 ->update('ppob_transaction_history', $data);
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


    // function check_order_status_prabayar($destination, $kode_produk, $ref_id){
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //       CURLOPT_URL => $this->config->item('serpul_main_url').'/prabayar/history/'.$ref_id,
    //       CURLOPT_RETURNTRANSFER => true,
    //       CURLOPT_ENCODING => '',
    //       CURLOPT_MAXREDIRS => 10,
    //       CURLOPT_TIMEOUT => 0,
    //       CURLOPT_FOLLOWLOCATION => true,
    //       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //       CURLOPT_CUSTOMREQUEST => 'GET',
    //       CURLOPT_HTTPHEADER => array(
    //         'Accept: application/json',
    //             'Authorization: '. $this->config->item('serpul_api_key')
    //       ),
    //     ));
    //     $response = curl_exec($curl);
    //     curl_close($curl);
    //     return json_decode($response);
    // }

    // check kode transaksi
    function check_kode_transaksi( $kode_transaksi, $company_id ){
        $this->db->select('pth.id')
                 ->from('ppob_transaction_history AS pth')
                 ->join('ppob_transaction_history_company AS pthc', 'pth.id=pthc.ppob_transaction_history_id', 'inner')
                 ->where('pthc.company_id', $company_id)
                 ->where('pth.transaction_code', $kode_transaksi);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }


    function get_info_transaksi($transaction_code, $company_id){
        $this->db->select('pth.id, pth.transaction_code, pth.product_code, pth.nomor_tujuan, ppc.category_code, pthc.company_price, pth.application_price, pth.server, pth.status, pth.ket')
                 ->from('ppob_transaction_history AS pth')
                 ->join('ppob_transaction_history_company AS pthc', 'pth.id=pthc.ppob_transaction_history_id', 'inner')
                 ->join('ppob_prabayar_product AS ppp', 'pth.product_code=ppp.product_code', 'inner')
                 ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                 ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                 ->where('pthc.company_id', $company_id)
                 ->where('pth.transaction_code', $transaction_code);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $list['transaction_code'] = $rows->transaction_code;
                $list['product_code'] = $rows->product_code;
                $list['nomor_tujuan'] = $rows->nomor_tujuan;
                $list['category_code'] = $rows->category_code;
                $list['company_price'] = $rows->company_price;
                $list['application_price'] = $rows->application_price;
                $list['server'] = $rows->server;
                $list['status'] = $rows->status;
                $list['pesan'] = $rows->ket;
            }
        }
        return $list;
    }

    // check kode produk pascabayar
    function check_kode_produk_pascabayar( $kode_produk, $category ) {
        $this->db->select('ppp.id')
                ->from('ppob_pascabayar_product AS ppp')
                ->join('ppob_pascabayar_category AS ppc', 'ppp.category_id=ppc.id', 'inner')
                ->where('ppp.product_code', $kode_produk)
                ->where('ppc.category_code', $category);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // check kode operator uang digital
    function check_kode_operator_uang_digital($kode_operator){
        $this->db->select('ppo.id')
                 ->from('ppob_prabayar_operator AS ppo')
                 ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                 ->where('ppo.operator_code', $kode_operator)
                 ->where('ppc.category_code', 'UD');
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
           return true;
        }else{
            return false;
        }
    }

    // harga terakhir
    function harga_terakhir( $kode_produk, $harga_aplikasi, $company_id ){
        $this->db->select('markup_company')
                 ->from('ppob_company_markup')
                 ->where('company_id', $company_id)
                 ->where('product_code', $kode_produk);
        $q = $this->db->get();
        $markup = 0;
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
                $markup = $rows->markup_company;
            }
        }else{
            $this->db->select('company_markup')
                     ->from('company')
                     ->where('id', $company_id);
            $q = $this->db->get();
            if( $q->num_rows() > 0 ) {
                foreach ( $q->result() as $rows ) {
                    $markup = $rows->company_markup;
                }
            }
        }
        return $harga_aplikasi + $markup;
    } 

    // get list uang digital
    function get_list_uang_digital($kode_operator, $company_id){
        $this->db->select('ppp.id, ppo.operator_code, ppo.operator_name, ppp.product_code, ppp.product_name, ppp.price, ppp.application_markup')
                 ->from('ppob_prabayar_product AS ppp')
                 ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                 ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                 ->where('ppo.operator_code', $kode_operator)
                 ->where('ppc.category_code', 'UD');
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                // harga
                $price = $this->harga_terakhir($rows->product_code, $rows->price + $rows->application_markup, $company_id);
                // list
                $list[] = array('operator_name' => $rows->operator_name, 
                                'product_code' =>  $rows->product_code, 
                                'product_name' => $rows->product_name, 
                                'product_price' => $this->kurs . ' ' . number_format($price));
            }
        }
        // list
        return $list;
    }

    // get list produk ppob
    function get_list_produk_ppob($kode_operator, $company_markup){
        $this->db->select('ppp.id, ppp.product_code, ppp.product_name, ppp.price, ppp.application_markup, ppp.status, ppp.server')
                 ->from('ppob_prabayar_product AS ppp')
                 ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                 ->where('ppp.status', 'active')
                 ->where('ppp.server !=', 'none')
                 ->where_in('ppo.operator_code', $kode_operator)
                 ->order_by('ppp.price');
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $markup_company = 0;
                if( isset( $company_markup[$rows->product_code] ) ){
                    $markup_company = $company_markup[$rows->product_code];
                }else{
                    $markup_company = $company_markup['all'];
                }
                $list[] = array('id' => $rows->id, 
                                'product_code' => $rows->product_code, 
                                'product_name' => $rows->product_name, 
                                'product_price' => $this->kurs . ' ' . number_format($rows->price + $rows->application_markup + $markup_company),
                                'status' => $rows->status);
            }
        }
        return $list;   
    }

    // get info product
    function get_info_product($product_code){
        $this->db->select('id, product_code, product_name, price, application_markup, status, server')
                 ->from('ppob_prabayar_product')
                 ->where('product_code', $product_code);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows()  > 0 ) {
            foreach ( $q->result() as $row ) {
                $list['id'] = $row->id;
                $list['product_code'] = $row->product_code;
                $list['product_name'] = $row->product_name;
                $list['price'] = $row->price;
                $list['markup_amra'] = $row->application_markup;
                $list['status'] = $row->status;
                $list['server'] = $row->server;
            }
        }
        return $list;
    }   

    // get info saldo by ref id
    function get_info_saldo_by_ref_id( $transaction_code ) {
        $this->db->select('c.saldo AS company_saldo, c.id AS company_id,  cs.saldo AS costumer_saldo, cs.id AS costumer_id ')
                 ->from('ppob_transaction_history AS pth')
                 ->join('ppob_transaction_history_company AS com', 'pth.id=com.ppob_transaction_history_id', 'left')
                 ->join('company AS c', 'com.company_id=c.id', 'left')
                 ->join('ppob_transaction_history_costumer AS cos', 'pth.id=cos.ppob_transaction_history_id', 'left')
                 ->join('ppob_costumer cs', 'cos.ppob_costumer_id=cs.id', 'left')
                 ->where('pth.transaction_code', $transaction_code);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) { 
            foreach ( $q->result() as $rows) {
                if( $rows->company_id != null ) {
                    $list['company_id'] = $rows->company_id;
                    $list['company_saldo'] = $rows->company_saldo; 
                }else if ( $rows->costumer_id != null ) {
                    $list['costumer_id'] = $rows->costumer_id;
                    $list['costumer_saldo'] = $rows->costumer_saldo;
                }
            }
        }
        return $list;         
    }

}  