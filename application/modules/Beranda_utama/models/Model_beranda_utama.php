<?php

/**
*  -----------------------
*	Model beranda utama
*	Created by Muammar Kadafi
*  -----------------------
*/

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_beranda_utama extends CI_Model
{
    private $company_id;
    private $status;
    private $content;
    private $error;
    private $write_log;

    public function __construct()
    {
        parent::__construct();
        $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
        $this->error = 0;
        $this->write_log = 1;
    }

    function get_jamaah(){
        $q = $this->db->select('j.id')
        ->from('jamaah AS j')
        ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
        ->where('j.company_id', $this->company_id)
        ->get();
        return $q->num_rows();
    }

    function paket_berangkat(){
        $this->db->select('id')
        ->from('paket')
        ->where('company_id', $this->company_id)
        ->where('departure_date > NOW()');
        $q = $this->db->get();
        $num = $q->num_rows();
        $list = array();
        if( $num > 0 ) {
            foreach ($q->result() as $rows) {
                $list[] = $rows->id;
            }
        }
        return array('num' => $num, 'list_paket' => $list);
    }

    function jamaah_berangkat($list_paket){
        $this->db->distinct()
        ->select('ptj.jamaah_id')
        ->from('paket_transaction_jamaah AS ptj')
        ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
        ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
        ->where('ptj.company_id', $this->company_id)
        ->where_in('p.id', $list_paket);
        $q = $this->db->get();
        return $q->num_rows();
    }

    # get terjual
    function get_terjual(){
        $thisMonth = date('Y-m');
        $this->db->select('id')
        ->from('tiket_transaction')
        ->where('company_id', $this->company_id)
        ->where('status', 'aktif')
        ->like('input_date', $thisMonth);
        $q = $this->db->get();
        return $q->num_rows();
    }

    #  get saldo perusahaan
    function get_saldo(){
        $this->db->select('saldo')
        ->from('company')
        ->where('id', $this->company_id);
        $q = $this->db->get();
        $saldo = 0;
        if( $q->num_rows() > 0 )   {
            $saldo = $q->row()->saldo;
        }
        return $saldo;
    }

    function get_total_daftar_jamaah_terdaftar($search)
    {
        $this->db->select('j.id')
        ->from('jamaah AS j')
        ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
        ->where('j.company_id', $this->company_id);
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
        }
        $q = $this->db->get();
        return $q->num_rows();
    }

    function get_index_daftar_jamaah_terdaftar($limit = 6, $start = 0, $search = '')
    {
        $this->db->select('j.id, p.fullname, p.identity_number, p.birth_place, p.birth_date, j.passport_number')
        ->from('jamaah AS j')
        ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
        ->where('j.company_id', $this->company_id);
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
        }
        $this->db->order_by('j.last_update', 'desc')->limit($limit, $start);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $list[] = array(
                    'id' => $row->id,
                    'fullname' => $row->fullname,
                    'identity_number' => $row->identity_number,
                    'birth_place' => $row->birth_place,
                    'birth_date' => $this->date_ops->change_date($row->birth_date),
                    'passport_number' => ($row->passport_number != '' ? $row->passport_number : '-'),
                    'total_pembelian' => $this->_totalBeli($row->id)
                );
            }
        }
        return $list;
    }

    // count total pembelian
    function _totalBeli($id)
    {
        $this->db->select('COUNT(DISTINCT(paket_transaction_id)) AS total')
        ->from('paket_transaction_jamaah')
        ->where('company_id', $this->company_id)
        ->where('jamaah_id', $id);
        $q = $this->db->get();
        return $q->row()->total;
    }

    function get_total_daftar_paket_berangkat($search){
        $this->db->select('id')
        ->from('paket')
        ->where('company_id', $this->company_id)
        ->where('departure_date > NOW()');
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
            ->like('kode', $search)
            ->or_like('paket_name', $search)
            ->group_end();
        }
        $r    = $this->db->get();
        return $r->num_rows();
    }

    function get_index_daftar_paket_berangkat($limit = 6, $start = 0, $search = ''){
        $this->db->select('p.id, p.kode, p.jenis_kegiatan, p.photo, p.paket_name, p.description, p.departure_date, p.return_date,
        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', mpt.paket_type_name, pp.price ) SEPARATOR \';\' )
        FROM paket_price AS pp
        INNER JOIN mst_paket_type AS mpt ON pp.paket_type_id=mpt.id
        WHERE pp.company_id="' . $this->company_id . '" AND pp.paket_id=p.id ) AS tipe_paket,
        (SELECT COUNT(ptj.jamaah_id)
        FROM paket_transaction_jamaah AS ptj
        INNER JOIN paket_transaction AS pt ON ptj.paket_transaction_id=pt.id
        WHERE ptj.company_id="' . $this->company_id . '" AND pt.paket_id=p.id) AS jumlahJamaah ')
        ->from('paket AS p')
        ->where('p.company_id', $this->company_id)
        ->where('p.departure_date > NOW()');
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
            ->like('p.kode', $search)
            ->or_like('p.paket_name', $search)
            ->group_end();
        }
        $this->db->order_by('p.id', 'desc')->limit($limit, $start);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $paket_type = array();
                if ($rows->tipe_paket != '') {
                    foreach (explode(';', $rows->tipe_paket) as $key => $value) {
                        $exp = explode('$', $value);
                        $paket_type[] = array('paket_type_name' => $exp[0], 'price' => $exp[1]);
                    }
                }
                $list[] = array(
                    'id' => $rows->id,
                    'kode' => $rows->kode,
                    'jenis_kegiatan' => $rows->jenis_kegiatan,
                    'photo' => $rows->photo,
                    'paket_name' => $rows->paket_name,
                    'description' => $rows->description,
                    'departure_date' => $this->date_ops->change_date($rows->departure_date),
                    'return_date' => $this->date_ops->change_date($rows->return_date),
                    'paket_type' => $paket_type,
                    'jumlah_jamaah' => $rows->jumlahJamaah
                );
            }
        }
        return $list;
    }


    function get_total_daftar_jamaah_berangkat($search){
        $this->db->select('ptj.jamaah_id')
        ->from('paket_transaction_jamaah AS ptj')
        ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
        ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
        ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
        ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
        ->where('ptj.company_id', $this->company_id)
        ->where('p.departure_date > NOW()');
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
        }
        $r    = $this->db->get();
        return $r->num_rows();
    }

    # get index
    function get_index_daftar_jamaah_berangkat( $limit = 6, $start = 0, $search = '' ) {
        $this->db->select('ptj.jamaah_id, per.fullname, per.identity_number, per.birth_place, per.birth_date,
        j.passport_number, p.paket_name, p.departure_date, p.return_date, p.kode, pt.total_paket_price')
        ->from('paket_transaction_jamaah AS ptj')
        ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
        ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
        ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
        ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
        ->where('ptj.company_id', $this->company_id)
        ->where('p.departure_date > NOW()');
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
        }
        $this->db->order_by('ptj.jamaah_id', 'desc')->limit($limit, $start);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list[] = array(
                    'id' => $rows->jamaah_id,
                    'fullname' => $rows->fullname,
                    'identity_number' => $rows->identity_number,
                    'birth_place' => $rows->birth_place,
                    'birth_date' => $this->date_ops->change_date($rows->birth_date),
                    'passport_number' => $rows->passport_number,
                    'price' => $rows->total_paket_price,
                    'kode' => $rows->kode,
                    'paket_name' => $rows->paket_name,
                    'return_date' => $this->date_ops->change_date($rows->return_date),
                    'departure_date' => $this->date_ops->change_date($rows->departure_date)
                );
            }
        }
        return $list;
    }


    function get_total_daftar_tiket_terjual($search){
        $this->db->select('tt.id')
        ->from('tiket_transaction AS tt')
        ->where('tt.company_id', $this->company_id)
        ->where('tt.status', 'aktif')
        ->like('tt.input_date', date('Y-m-'));
        if ( $search != '' or $search != null or !empty($search) ) {
            $this->db->group_start()
            ->like('tt.no_register', $search)
            ->group_end();
        }
        $r    = $this->db->get();
        return $r->num_rows();
    }

    function get_index_daftar_tiket_terjual( $limit = 6, $start = 0, $search = '' ){
        $this->db->select('tt.id, tt.no_register, tt.input_date, tt.total_transaksi,
        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ttd.pax, ttd.code_booking, ma.airlines_name,
        ttd.departure_date, ttd.travel_price, ttd.costumer_price ) SEPARATOR \';\')
        FROM tiket_transaction_detail AS ttd
        INNER JOIN mst_airlines AS ma ON ttd.airlines_id=ma.id
        WHERE ttd.tiket_transaction_id=tt.id) AS tiket_transaction_detail,
        (SELECT SUM(biaya) FROM tiket_transaction_history WHERE tiket_transaction_id=tt.id AND ket="cash") AS bayar_cash,
        (SELECT SUM(biaya) FROM tiket_transaction_history WHERE tiket_transaction_id=tt.id AND ket="refund") AS bayar_refund,
        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', input_date, invoice, biaya, costumer_name,
        costumer_identity, receiver, ket ) ORDER BY id DESC )
        FROM tiket_transaction_history
        WHERE tiket_transaction_id=tt.id) AS history_tiket_transaction')
        ->from('tiket_transaction AS tt')
        ->where('tt.company_id', $this->company_id)
        ->where('tt.status', 'aktif')
        ->like('tt.input_date', date('Y-m-'));
        if ($search != '' or $search != null or !empty($search)) {
            $this->db->group_start()
            ->like('tt.no_register', $search)
            ->group_end();
        }
        $this->db->order_by('tt.id', 'desc')->limit($limit, $start);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $detail_transaction  = array();
                $total = 0;
                if ($row->tiket_transaction_detail != '') {
                    $exp = explode(';', $row->tiket_transaction_detail);
                    foreach ($exp as $key => $value) {
                        $exp2 = explode('$', $value);
                        $detail_transaction[] = array(
                            'pax' => $exp2[0],
                            'code_booking' => $exp2[1],
                            'airlines_name' => $exp2[2],
                            'departure_date' => $this->date_ops->change_date($exp2[3]),
                            'travel_price' => $exp2[4],
                            'costumer_price' => $exp2[5],
                            'total' => ($exp2[0] * $exp2[5])
                        );
                        $total = $total + ($exp2[0] * $exp2[5]);
                    }
                }

                $riwayat_transaksi_tiket = array();
                if ($row->history_tiket_transaction != '') {
                    $expRWYT = explode(';', $row->history_tiket_transaction);
                    foreach ($expRWYT as $keyRWYT => $valueRWYT) {
                        $expRWYT2 = explode('$', $valueRWYT);
                        $riwayat_transaksi_tiket[] = array(
                            'tanggal_transaksi' => $expRWYT2[0],
                            'invoice' => $expRWYT2[1],
                            'biaya' => $expRWYT2[2],
                            'nama_petugas' => $expRWYT2[5],
                            'nama_pelanggan' => $expRWYT2[3],
                            'nomor_identitas' => $expRWYT2[4],
                            'ket' => $expRWYT2[6]
                        );
                    }
                }

                $total_sudah_bayar = ($row->bayar_cash != '' ? $row->bayar_cash : 0) - ($row->bayar_refund != '' ? $row->bayar_refund : 0);
                $list[] = array(
                    'id' => $row->id,
                    'nomor_register' => $row->no_register,
                    'transaction_date' => $row->input_date,
                    'detail_transaction' => $detail_transaction,
                    'total' => $total,
                    'total_sudah_bayar' => $total_sudah_bayar,
                    'sisa' => $total - $total_sudah_bayar,
                    'riwayat_transaksi_tiket' => $riwayat_transaksi_tiket
                );
            }
        }
        return $list;
    }

    # total
    function get_total_daftar_riwayat_saldo( $start_date, $end_date ) {
        $this->db->select('cst.id')
        ->from('company_saldo_transaction AS cst')
        ->where('cst.company_id', $this->company_id);
        if( $start_date !='' OR $end_date != '' ) {
            if( $start_date == '' AND $end_date != '' ) {
                $this->db->where('cst.last_update <=', $end_date);
            }elseif ( $start_date != '' AND $end_date == '' ) {
                $this->db->where('cst.last_update >=', $start_date);
            }else {
                $this->db->where('cst.last_update >=', $start_date)
                ->where('cst.last_update <=', $end_date);
            }
        }
        $r = $this->db->get();
        return $r->num_rows();
    }

    # index
    function get_index_daftar_riwayat_saldo( $limit = 6, $start = 0, $start_date, $end_date ) {
        $this->db->select('cst.id, cst.saldo, cst.request_type, cst.status, cst.last_update')
        ->from('company_saldo_transaction AS cst')
        ->where('cst.company_id', $this->company_id);
        //  filter
        if( $start_date !='' OR $end_date != '' ) {
            if( $start_date == '' AND $end_date != '' ) {
                $this->db->where('cst.last_update <=', $end_date);
            }elseif ( $start_date != '' AND $end_date == '' ) {
                $this->db->where('cst.last_update >=', $start_date);
            }else{
                $this->db->where('cst.last_update >=', $start_date)
                ->where('cst.last_update <=', $end_date);
            }
        }
        $this->db->order_by('cst.last_update', 'desc')->limit($limit, $start);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            foreach ($q->result() as $rows) {
                $list[] = array('id' => $rows->id,
                'saldo' => $rows->saldo,
                'request_type' => strtoupper($rows->request_type),
                'status' => '<b style="color:'.($rows->status == 'accepted' ? 'green' : ($rows->status == 'rejected' ? 'red' : 'orange') ).'">'.strtoupper($rows->status).'</b>',
                'tanggal_transaksi' => $rows->last_update);
            }
        }
        return $list;
    }


    function gen_order_id(){
        $feedBack = false;
        $rand = '';
        do {
            $rand = rand(0, 99999);
            $q = $this->db->select('id')
            ->from('subscribtion_payment_history')
            ->where('order_id', $rand)
            ->get();
            if ($q->num_rows() == 0) {
                $feedBack = true;
            }
        } while ($feedBack == false);
        return $rand;
    }

    function get_total_daftar_headline(){
        $this->db->select('id')
        ->from('headline')
        ->where('company_id', $this->company_id);
        $q = $this->db->get();
        return $q->num_rows();
    }

    function get_index_daftar_headline($limit, $start){
        $this->db->select('id, headline, tampilkan, last_update')
        ->from('headline')
        ->where('company_id', $this->company_id);
        $this->db->order_by('last_update', 'desc')->limit($limit, $start);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            foreach ($q->result() as $rows) {
                $list[] = array('id' => $rows->id,
                'headline' => $rows->headline,
                'tampilkan' => $rows->tampilkan,
                'last_update' => $rows->last_update);
            }
        }
        return $list;
    }

    function get_total_daftar_request_deposit($search){
        $this->db->select('mr.id')
        ->from('member_transaction_request AS mr')
        ->join('personal AS p', 'mr.personal_id=p.personal_id', 'inner')
        ->where('mr.company_id', $this->company_id)
        ->where('mr.status_request', 'diproses')
        ->where('mr.sending_payment_status', 'sudah_dikirim');
        if ( $search != '' or $search != null or !empty($search) ) {
            $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
        }
        $r    = $this->db->get();
        return $r->num_rows();
    }

    function get_index_daftar_request_deposit($limit = 6, $start = 0, $search = '' ){
        $this->db->select('mr.id, mr.last_update, p.fullname, p.identity_number, mr.activity_type, mr.amount, mr.amount_code, b.nama_bank, 
                           b.logo_bank, mr.bank_account, mr.payment_source')
        ->from('member_transaction_request AS mr')
        ->join('mst_bank_transfer AS b', 'mr.bank_id=b.id', 'inner')
        ->join('personal AS p', 'mr.personal_id=p.personal_id', 'inner')
        ->where('mr.company_id', $this->company_id)
        ->where('mr.status_request', 'diproses')
        ->where('mr.sending_payment_status', 'sudah_dikirim');
        if ( $search != '' or $search != null or !empty($search) ) {
            $this->db->group_start()
            ->like('p.fullname', $search)
            ->or_like('p.identity_number', $search)
            ->group_end();
        }
        $this->db->order_by('mr.id', 'desc')->limit( $limit, $start );
        $q = $this->db->get();

        $activity = array('deposit' => "Deposit",
                          'deposit_paket' => 'Tabungan Umrah',
                          'withdraw' => 'Withdraw',
                          'claim' => 'Claim',
                          'buying_paket' => 'Buying Paket',
                          'payment_paket' => 'Payment Paket',
                          'purchase' => 'Purchase');
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                $list[] = array('id' => $rows->id,
                'fullname' => $rows->fullname,
                'identity_number' => $rows->identity_number,
                'activity_type' => $activity[$rows->activity_type],
                'amount' => $rows->amount,
                'amount_code' => $rows->amount_code,
                'total_amount' => $rows->amount + $rows->amount_code,
                'nama_bank' => $rows->nama_bank,
                'sumber_biaya' => $rows->payment_source == 'deposit' ? 'Deposit' : "Transfer",
                'logo_bank' => base_url().'image/bank_logo/'.$rows->logo_bank,
                'bank_account' => $rows->bank_account,
                'last_update' => $rows->last_update);
            }
        }
        return $list;
    }

    function check_request_member_id($id){
        $this->db->select('id')
        ->from('member_transaction_request')
        ->where('id', $id)
        ->where('status_request', 'diproses')
        ->where('sending_payment_status', 'sudah_dikirim')
        ->where('company_id', $this->company_id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            return true;
        }else{
            return false;
        }
    }

    function get_info_request($id){
        $this->db->select('id, personal_id, amount_code, amount, activity_type, payment_source')
        ->from('member_transaction_request')
        ->where('id', $id)
        ->where('company_id', $this->company_id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ){
            foreach ($q->result() as $rows) {
                $list['id'] = $rows->id;
                $list['personal_id'] = $rows->personal_id;
                $list['amount'] = $rows->amount;
                $list['amount_code'] = $rows->amount_code;
                $list['activity_type'] = $rows->activity_type;
                $list['payment_source'] = $rows->payment_source;
            }
        }
        return $list;
    }

    function check_headline_id($id){
        $this->db->select('id')
        ->from('headline')
        ->where('company_id', $this->company_id)
        ->where('id', $id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ){
            return true;
        }else{
            return false;
        }
    }

    function get_info_headline($id){
        $this->db->select('id, headline, tampilkan')
        ->from('headline')
        ->where('id', $id);
        $q = $this->db->get();
        $list = array();
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
                $list['id'] = $rows->id;
                $list['headline'] = $rows->headline;
                $list['tampilkan'] = $rows->tampilkan;
            }
        }
        return $list;
    }

}
