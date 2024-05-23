<?php

/**
 *  -----------------------
 *	Model notif
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_notif extends CI_Model
{
    private $company_id;
    private $feedBack;

    function __construct()
    {
        parent::__construct();
        $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
    }

    // check id
    function check_id( $id ){
        $this->db->select('id')
            ->from('notif')
            ->where('company_id', $this->company_id)
            ->where('id', $id);
        $q = $this->db->get();
        if( $q->num_rows() > 0 ) {
            return true;
        }else{
            return false;
        }
    }

    // get total daftar notification
    function get_total_daftar_notification($search){
        $this->db->select('id')
               ->from('notif')
               ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('title', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
    }

   # get index daftar notification
   function get_index_daftar_notification($limit = 6, $start = 0, $search = ''){
      $this->db->select('id, title, message, last_update')
               ->from('notif')
               ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('title', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id,
                            'title' => $row->title,
                            'message' => $row->message,
                            'last_update' => $row->last_update);
         }
      }
      return $list;
   }

    # check paket yang akan berangkat
    function check_paket_berangkat()
    {
        $this->db->select('id, departure_date')
            ->from('paket');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $num = 0;
            foreach ($q->result() as $rows) {
                $day = strtotime($rows->departure_date) - time();
                $day = round($day / (60 * 60 * 24));
                if ($day <= 30 and $day >= 0) {
                    $num = $num + 1;
                    $list[] = $rows->id;
                }
            }
            if ($num != 0) {
                $this->feedBack = array('name' => "Paket akan Berangkat", 'num' => $num, 'list' => $list, 'title' => 'Paket', 'icon' => 'fas fa-box');
                return true;
            }
        } else {
            return false;
        }
    }

    # check kelengkapan
    function check_kelengkapan()
    {
        $this->db->select('j.id, p.gender, p.birth_date, p.birth_place, j.passport_number, j.passport_dateissue, j.validity_period')
            ->from('jamaah AS j')
            ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
            ->where('j.company_id', $this->company_id);
        $q = $this->db->get();
        $list = array();
        $status_lengkap = true;
        $jamaah_tidak_lengkap = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $list_tidak_lengkap = array();
                if ($row->gender == 1) {
                    $num_results = $this->db->select('jamaah_id')
                        ->from('mahram')
                        ->where('company_id', $this->company_id)
                        ->where('jamaah_id', $row->id)
                        ->count_all_results();
                    if ($num_results == 0) {
                        $list_tidak_lengkap[] = 'Mahram';
                        $status_lengkap = false;
                    }
                }
                if ($row->birth_date == '') {
                    $list_tidak_lengkap[] = 'Tanggal Lahir';
                    $status_lengkap = false;
                }
                if ($row->birth_place == '') {
                    $list_tidak_lengkap[] = 'Tempat Lahir';
                    $status_lengkap = false;
                }
                if ($row->passport_number == '') {
                    $list_tidak_lengkap[] = 'Nomor Passport';
                    $status_lengkap = false;
                }
                if ($row->passport_dateissue == '0000-00-00') {
                    $list_tidak_lengkap[] = 'Nomor Passport';
                    $status_lengkap = false;
                }
                if ($row->validity_period == '0000-00-00') {
                    $list_tidak_lengkap[] = 'Nomor Passport';
                    $status_lengkap = false;
                }
                # filter list
                if (count($list_tidak_lengkap) > 0) {
                    $list[$row->id] = $list_tidak_lengkap;
                    $jamaah_tidak_lengkap++;
                }
            }
            # feedBack
            $this->feedBack = array('name' => "Jamaah Belum Lengkap", 'lengkap' => $status_lengkap, 'num' => $jamaah_tidak_lengkap, 'list' => $list, 'title' => 'Jamaah', 'icon' => 'fas fa-user-minus');
        }
        return $status_lengkap;
    }

    # check transaksi jamaah
    function check_transaksi_jamaah()
    {
        $this->db->select('pt.id, pt.no_register, pt.payment_methode, pt.total_paket_price, pt.batal_berangkat, p.departure_date')
            ->from('paket_transaction AS pt')
            ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
            ->where('payment_methode', '0')
            ->where('pt.company_id', $this->company_id)
            ->where('p.departure_date < curdate()');
        $q = $this->db->get();
        $list_transaction = array();
        $transaksi_belum_lunas = 0;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
                # history
                $this->db->select('paid, ket')
                    ->from('paket_transaction_history')
                    ->where('company_id', $this->company_id)
                    ->where('paket_transaction_id', $rows->id);
                $r = $this->db->get();
                $cash = 0;
                $refund = 0;
                if ($r->num_rows() > 0) {
                    foreach ($r->result() as $row) {
                        if ($row->ket == 'cash') {
                            $cash = $cash + $row->paid;
                        } elseif ($row->ket == 'refund') {
                            $refund = $refund + $row->paid;
                        }
                    }
                    if ($rows->total_paket_price > ($cash - $refund)) {
                        $transaksi_belum_lunas++;
                        $list_transaction[] = $rows->no_register;
                    }
                }
            }
        }
        # check transaksi belum lunas
        if ($transaksi_belum_lunas > 0) {
            # get array
            $this->feedBack = array('name' => "Trans Paket Belum Lunas", 'num' => $transaksi_belum_lunas, 'list' => $list_transaction, 'title' => 'Jamaah', 'icon' => 'fas fa-money-bill');
            # return true
            return true;
        } else {
            # return false
            return false;
        }
    }

    # checking agen request
    function check_agen_request()
    {
        $this->db->select('id')
            ->from('agen_request')
            ->where('company_id', $this->company_id)
            ->where('status_request', 'diproses');
        $q = $this->db->get();
        if ($q->num_rows() > 0) {
            $list = array();
            foreach ($q->result() as $row) {
                $list[] = $row->id;
            }
            $this->feedBack = array('name' => "Request Keagenan", 'num' => $q->num_rows(), 'list' => $list, 'title' => 'Member', 'icon' => 'fas fa-user-plus');
            return true;
        } else {
            return false;
        }
    }

    function check_pengisian_klaim_saldo()
    {
        $this->db->select('id, activity_type')
            ->from('member_transaction_request')
            ->where('status_request', 'diproses')
            ->where('company_id', $this->company_id);
        $q = $this->db->get();
        $list = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                if ($row->activity_type == 'deposit') {
                    $list['deposit'][] = $row->id; // pengisian saldo
                } elseif ($row->activity_type == 'withdraw') {
                    $list['withdraw'][] = $row->id; // pencairan saldo
                } elseif ($row->activity_type == 'claim') {
                    $list['claim'][] = $row->id; // claim fee agen
                } elseif ($row->activity_type == 'buying_paket') {
                    $list['buying_paket'][] = $row->id; // pembayaran paket
                } elseif ($row->activity_type == 'payment_paket') {
                    $list['payment_paket'][] = $row->id; // pembayaran paket
                } elseif ($row->activity_type == 'purchase') {
                    $list['purchase'][] = $row->id; // pembelian lain
                }
            }
        }

        if (count($list) > 0) {
            return true;
            $this->feedBack = $list;
        } else {
            return false;
        }
        //return $list;
    }

    # get feedBack
    function feedBack()
    {
        return $this->feedBack;
    }
}
