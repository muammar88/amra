<?php

/**
 *  -----------------------
 *	Model trans paket la
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_paket_la extends CI_Model
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

   function verify_trans_paket_la( $paket_la_id ) {
      // total price
      $this->db->select('total_price, status')
               ->from('paket_la_transaction')
               ->where('id', $paket_la_id)
               ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $total_price = 0;
      $status_paket_la = '';
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $total_price = $rows->total_price;
            $status_paket_la = $rows->status;
         }
      }
      // paid
      $this->db->select('paid')
               ->from('paket_la_transaction_history')
               ->where('company_id', $this->company_id)
               ->where('paket_la_transaction_id', $paket_la_id);
      $q = $this->db->get();
      $paid = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            $paid = $paid + $rows->paid;
         }
      }         
      // FILTER
      $status = TRUE;
      if( ($total_price - $paid) > 0 ) {
         $status = FALSE;
      }

      return array('status' => $status, 'total_price' => $total_price, 'status_paket_la' => $status_paket_la);
   }

   function get_last_periode(){
       $this->db->select('id')
         ->from('jurnal_periode')
         ->where('company_id', $this->company_id)
         ->order_by('id', 'desc')
         ->limit('1');
      $q = $this->db->get();
      $id = '';
      if ($q->num_rows() > 0) {
         $id = $q->row()->id;
      }
      return $id;
   }

   function check_paket_la_id($id){
      $this->db->select('id')
               ->from('paket_la_transaction')
               ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }

   // get paket la id by register number paket la
   function get_paket_la_id_by_register_number( $register_number ) {
      $this->db->select('id')
         ->from('paket_la_transaction')
         ->where('company_id', $this->company_id)
         ->where('register_number', $register_number);
      $q = $this->db->get();
      $id = 0;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $id = $rows->id;
         }
      }
      return $id;
   }

   function get_total_daftar_transaksi_paket_la($search)
   {
      $this->db->select('id')
         ->from('paket_la_transaction')
         ->where('company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('register_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_daftar_transaksi_paket_la($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('p.id, p.register_number, p.client_name, p.client_hp_number, p.client_address, p.status,
                        p.description, p.facilities, p.discount, p.total_price, p.departure_date, p.arrival_date, p.jamaah, p.input_date, p.last_update,
                        (SELECT SUM(paid) FROM paket_la_transaction_history
                        WHERE company_id="' . $this->company_id . '" AND status="payment" AND paket_la_transaction_id=p.id) AS wasPayment,
                        (SELECT SUM(paid) FROM paket_la_transaction_history
                        WHERE company_id="' . $this->company_id . '" AND status="refund" AND paket_la_transaction_id=p.id) AS wasRefund,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', id, paket_type_name ) SEPARATOR \';\')
                           FROM mst_paket_type_la  WHERE company_id="' . $this->company_id . '") AS list_tipe_paket')
         ->from('paket_la_transaction AS p')
         ->where('p.company_id', $this->company_id);
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.register_number', $search)
            ->group_end();
      }
      $this->db->order_by('p.id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $tipe_paket_name = array();
            foreach (explode(';', $rows->list_tipe_paket) as $key => $value) {
               $exp = explode('$', $value);
               $tipe_paket_name[$exp[0]] = $exp[1];
            }
            $unserialize = unserialize($rows->facilities);
            if (isset($unserialize['tipe_paket_la_id'])) {
               $tipe_paket = $tipe_paket_name[$unserialize['tipe_paket_la_id']];
            } else {
               $tipe_paket = 'Tipe paket tidak ditemukan.';
            }

            $fasilitas = $unserialize['list_fasilitas'];
            $list[] = array(
               'id' => $rows->id,
               'register_number' => $rows->register_number,
               'client_name' => $rows->client_name,
               'client_hp_number' => $rows->client_hp_number,
               'client_address' => $rows->client_address,
               'description' => $rows->description,
               'tipe_paket' => $tipe_paket,
               'fasilitas' => $fasilitas,
               'discount' => $rows->discount,
               'total_price' => $rows->total_price,
               'departure_date' => $this->date_ops->change_date_t3($rows->departure_date),
               'arrival_date' => $this->date_ops->change_date_t3($rows->arrival_date),
               'jamaah' => $rows->jamaah,
               'sudah_dibayar' => ($rows->wasPayment - $rows->wasRefund),
               'sisa' => ($rows->total_price - ($rows->wasPayment - $rows->wasRefund)),
               'status' => $rows->status
            );
         }
      }
      return $list;
   }

   # paket type paket la
   function paket_type_la()
   {
      $this->db->select('mptl.id, mptl.paket_type_name,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ptlf.facilities_la_id, ptlf.pax, mfl.facilities_name, mfl.price ) SEPARATOR \';\')
                              FROM paket_type_la_facilities AS ptlf
                              INNER JOIN mst_facilities_la AS mfl ON ptlf.facilities_la_id=mfl.id
                              WHERE ptlf.company_id="' . $this->company_id . '" AND ptlf.paket_type_id=mptl.id) AS listfasilitas')
         ->from('mst_paket_type_la AS mptl')
         ->where('mptl.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $fasilitas = array();
            if ($rows->listfasilitas != '') {
               foreach (explode(';', $rows->listfasilitas) as $key => $value) {
                  $fasilitas[] = explode('$', $value);
               }
            }
            $feedBack[$rows->id] = array(
               'name' =>  $rows->paket_type_name,
               'list_fasilitas' => $fasilitas
            );
         }
      }
      return $feedBack;
   }

   #
   function fasilitas_paket_la()
   {
      $this->db->select('id, facilities_name, price')
         ->from('mst_facilities_la')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $feedBack[$rows->id] = $rows->facilities_name . ' (Harga :Rp ' . number_format($rows->price) . ')';
         }
      }
      return $feedBack;
   }

   function check_id_trans_paket_la_exist($id)
   {
      $this->db->select('id')
         ->from('paket_la_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function checking_no_register($nomor_register, $id = '')
   {
      $this->db->select('id')
         ->from('paket_la_transaction')
         ->where('company_id', $this->company_id)
         ->where('register_number', $nomor_register);
      if ($id != '') {
         $this->db->where('id', $id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_jenis_paket_id($jenis_paket_id)
   {
      $this->db->select('id')
         ->from('mst_paket_type_la')
         ->where('company_id', $this->company_id)
         ->where('id', $jenis_paket_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   # get daftar fasilitas
   function get_name_fasilitas()
   {
      $this->db->select('id, facilities_name')
         ->from('mst_facilities_la')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[$rows->id] = $rows->facilities_name;
         }
      }
      return $list;
   }

   # check jenis fasilitas id
   function check_jenis_fasilitas_id($id)
   {
      $this->db->select('id')
         ->from('mst_facilities_la')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_data_trans_paket_la($id)
   {
      $this->db->select('id, register_number, client_name, client_hp_number, client_address, status,
                        description, facilities, discount, total_price, departure_date, arrival_date, jamaah, input_date, last_update,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', id, facilities_name, price) SEPARATOR \';\')
                           FROM mst_facilities_la WHERE company_id="' . $this->company_id . '" ) AS list_fasilitas')
         ->from('paket_la_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $fasilitas = array();
            foreach (explode(';', $rows->list_fasilitas) as $key => $value) {
               $exp = explode('$', $value);
               $fasilitas[$exp[0]] = $exp[1] . ' (Harga :Rp ' . number_format($exp[2]) . ')';
            }
            $unserialize = unserialize($rows->facilities);
            if (isset($unserialize['tipe_paket_la_id'])) {
               $tipe_paket_la = $unserialize['tipe_paket_la_id'];
            } else {
               $tipe_paket_la = 0;
            }
            $data = array(
               'id' => $rows->id,
               'register_number' => $rows->register_number,
               'client_name' => $rows->client_name,
               'client_hp_number' => $rows->client_hp_number,
               'client_address' => $rows->client_address,
               'description' => $rows->description,
               'tipe_paket_la' => $tipe_paket_la,
               'facilities' => $unserialize['list_fasilitas'],
               'discount' => $rows->discount,
               'total_price' => $rows->total_price,
               'departure_date' => $rows->departure_date,
               'arrival_date' => $rows->arrival_date,
               'jamaah' => $rows->jamaah
            );
            $list['value'] = $data;
            $list['list_tipe_paket_la'] = $this->paket_type_la();
            $list['list_fasilitas'] = $fasilitas;
         }
      }
      return $list;
   }

   function get_data_history_trans_paket_la($id)
   {
      $this->db->select('plt.id, plt.total_price,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', plth.id, plth.invoice, plth.paid, plth.receiver,
                              plth.deposit_name,  plth.deposit_hp_number, plth.deposit_address, plth.status, plth.input_date) SEPARATOR \';\')
                              FROM paket_la_transaction_history AS plth
                              WHERE plth.company_id="' . $this->company_id . '" AND plth.paket_la_transaction_id=plt.id ) AS history_paket_la_transaction')
         ->from('paket_la_transaction AS plt')
         ->where('plt.company_id', $this->company_id)
         ->where('plt.id', $id);
      $q = $this->db->get();
      $total_bayar = 0;
      $total_price = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $riwayat_pembayaran = array();
            if ($rows->history_paket_la_transaction != '') {
               foreach (explode(';', $rows->history_paket_la_transaction) as $key => $value) {
                  $exp = explode('$', $value);
                  $riwayat_pembayaran[] = array(
                     'id' => $exp[0],
                     'invoice' => $exp[1],
                     'paid' => $exp[2],
                     'receiver' => $exp[3],
                     'deposit_name' => $exp[4],
                     'deposit_hp_number' => $exp[5],
                     'deposit_address' => $exp[6],
                     'status' => $exp[7],
                     'tanggal_transaksi' => $exp[8]
                  );
                  if( $exp[7] != 'refund'){
                     $total_bayar = $total_bayar + $exp[2];
                  }
                  if( $exp[7] == 'refund'){
                     $total_bayar = $total_bayar - $exp[2];
                  }
               }
            }
            $total_price = $rows->total_price;
         }
      }

      return array(
         'invoice' => $this->random_code_ops->generated_invoice_history_paket_la(),
         'total_harga' => $total_price,
         'total_bayar' => $total_bayar,
         'sisa' => ($total_price - $total_bayar),
         'riwayat' => $riwayat_pembayaran
      );
   }

   # check exist invoice paket la
   function check_exist_invoice_paket_la($invoice)
   {
      $this->db->select('id')
         ->from('paket_la_transaction_history')
         ->where('company_id', $this->company_id)
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() >  0) {
         return true;
      } else {
         return false;
      }
   }

   function get_total_pembayaran($id)
   {
      $this->db->select('paid, status')
         ->from('paket_la_transaction_history')
         ->where('company_id', $this->company_id)
         ->where('paket_la_transaction_id', $id);
      $q = $this->db->get();
      $payment = 0;
      $refund = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if ($rows->status == 'payment') {
               $payment = $payment + $rows->paid;
            } elseif ($rows->status == 'refund') {
               $refund = $refund + $rows->paid;
            }
         }
      }
      return $payment - $refund;
   }

   function get_info_kas_transaksi_paket_la($id)
   {
      $this->db->select('plt.id, plt.client_name, plt.discount, plt.total_price, plt.facilities,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', plth.paid, plth.status) SEPARATOR \';\')
                           FROM paket_la_transaction_history AS plth
                           WHERE plth.company_id="' . $this->company_id . '" AND plth.paket_la_transaction_id=plt.id ) AS sudahBayar,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', kpl.id, kpl.fasilitas_la_id, kpl.uraian, kpl.price, kpl.ket) SEPARATOR \';\')
                           FROM kas_paket_la AS kpl
                           WHERE kpl.company_id="' . $this->company_id . '" AND kpl.paket_la_transaction_id=plt.id ) AS kas_transaction_pakets')
         ->from('paket_la_transaction AS plt')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $name_fasilitas = array();
            $fasilitas = array();
            $total_aktualisasi = 0;
            foreach (unserialize($rows->facilities)['list_fasilitas'] as $key => $value) {
               $fasilitas[$value['id']] = array('id' => $value['id'], 'name' => $value['name'], 'harga' => '0', 'ket' => 'fasilitas', 'action' => 'insert');
            }
            if ($rows->kas_transaction_pakets != '') {
               foreach (explode(";", $rows->kas_transaction_pakets) as $key => $value) {
                  $exp = explode('$', $value);
                  if ($exp[1] != 0) {
                     if (array_key_exists($exp[1], $fasilitas)) {
                        $fasilitas[$exp[1]] = array('id' => $exp[1],  'name' => $fasilitas[$exp[1]]['name'], 'harga' => $exp[3], 'ket' => 'fasilitas', 'action' => 'update');
                     }
                  } else {
                     $fasilitas[] = array('id' => $exp[0], 'name' => $exp[2], 'harga' => $exp[3], 'ket' => 'add', 'action' => 'update');
                  }
                  $total_aktualisasi = $total_aktualisasi + $exp[3];
               }
            }
            $bayar = 0;
            $refund = 0;

            if( $rows->sudahBayar != '' ){
               foreach (explode(';', $rows->sudahBayar) as $key => $value) {
                  $exp = explode('$', $value);
                  if ($exp[1] == 'payment') {
                     $bayar = $bayar + $exp[0];
                  } elseif ($exp[1] == 'refund') {
                     $refund = $refund + $exp[0];
                  }
               }

            }
            
            $list['id'] = $rows->id;
            $list['client_name'] = $rows->client_name;
            $list['discount'] = $rows->discount;
            $list['total_price'] = $rows->total_price;
            $list['sudah_bayar'] = $bayar - $refund;
            $list['aktualisasi'] = $fasilitas;
            $list['total_aktualisasi'] = $total_aktualisasi;
            $list['keuntungan'] = $rows->total_price - $total_aktualisasi;
         }
      }
      return $list;
   }

   # check kas transaction paket la id
   function check_kas_trans_paket_la($id, $ket = '', $action = '')
   {
      $this->db->select('id');
      if ($ket != '') {
         if ($ket == 'fasilitas') {
            if ($action == 'update') {
               $this->db->from('kas_paket_la')
                  ->where('company_id', $this->company_id)
                  ->where('fasilitas_la_id', $id)
                  ->where('ket', 'fasilitas');
            } else {
               $this->db->from('mst_facilities_la')
                  ->where('company_id', $this->company_id)
                  ->where('id', $id);
            }
         } else {
            $this->db->from('kas_paket_la')
               ->where('company_id', $this->company_id)
               ->where('id', $id);
         }
      } else {
         $this->db->from('kas_paket_la')
            ->where('company_id', $this->company_id)
            ->where('id', $id);
      }
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get last info kwitansi pembayaran
   function getLastInfoKwitansiPembayaran($id)
   {
      $this->db->select('invoice')
         ->from('paket_la_transaction_history')
         ->where('company_id', $this->company_id)
         ->where('paket_la_transaction_id', $id)
         ->order_by('input_date', 'asc');
      $q = $this->db->get();
      $feedBack = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack = $row->invoice;
         }
      }
      return $feedBack;
   }

   // check invoice exist
   function check_invoice_exist( $invoice ) {
      $this->db->select('id')
               ->from('paket_la_transaction_history')
               ->where('company_id', $this->company_id)
               ->where('invoice', $invoice);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         return true;
      }else{
         return false;
      }
   }
}