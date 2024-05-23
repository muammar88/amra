<?php

/**
 *  -----------------------
 *	Model trans tiket
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_tiket extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_total_trans_tiket($search)
   {
      $this->db->select('tt.id')
         ->from('tiket_transaction AS tt')
         ->where('tt.company_id', $this->company_id)
         ->where('tt.status', 'aktif');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('tt.no_register', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   function get_index_trans_tiket($limit = 6, $start = 0, $search = '')
   {
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
         ->where('tt.status', 'aktif');
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

   // get list airlines
   function get_list_airlines()
   {
      $this->db->select('id, airlines_name')
         ->from('mst_airlines')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array(); // list airlines
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[] = array('id' => $row->id, 'name' => $row->airlines_name);
         }
      }
      return $feedBack;
   }

   function check_airlines_exist($airlines_id)
   {
      $this->db->select('id')
         ->from('mst_airlines')
         ->where('company_id', $this->company_id)
         ->where('id', $airlines_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   // check nomor register exist
   function check_nomor_register_exist($nomor_register)
   {
      $this->db->select('id')
         ->from('tiket_transaction')
         ->where('company_id', $this->company_id)
         ->where('no_register', $nomor_register);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function check_nomor_invoice_exist($nomor_invoice)
   {
      $this->db->select('id')
         ->from('tiket_transaction_history')
         ->where('company_id', $this->company_id)
         ->where('invoice', $nomor_invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function get_list_name_airlines_by_array($param)
   {
      $this->db->select('id, airlines_name')
         ->from('mst_airlines')
         ->where('company_id', $this->company_id)
         ->where_in('id', $param);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $feedBack[$rows->id] = $rows->airlines_name;
         }
      }
      return $feedBack;
   }

   # get last periode
   function get_last_periode()
   {
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

   function get_akun_number($param)
   {
      $this->db->select('path, nomor_akun_secondary')
         ->from('akun_secondary')
         ->where('company_id', $this->company_id)
         ->where_in('path', $param);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[$row->path] = $row->nomor_akun_secondary;
         }
      }
      return $feedBack;
   }

   function get_riwayat_pembayaran_tiket($id)
   {
      $this->db->select('tt.total_transaksi,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', invoice, biaya, input_date, costumer_name, receiver, ket ) SEPARATOR \';\')
                               FROM tiket_transaction_history
                               WHERE tiket_transaction_id=tt.id) AS history_tiket_transaction')
         ->from('tiket_transaction AS tt')
         ->where('tt.company_id', $this->company_id)
         ->where('tt.id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {

            $total_tiket = 0;
            $riwayat_pembayaran = array();
            $total_pembayaran = 0;
            if ($row->history_tiket_transaction != '') {
               $list_history = $this->text_ops->extract_group_concat_2level($row->history_tiket_transaction);
               foreach ($list_history as $key => $value) {
                  $history = array();
                  $history['invoice'] = $value[0];
                  $history['biaya'] = $value[1];
                  $history['input_date'] = $value[2];
                  $history['costumer_name'] = $value[3];
                  $history['receiver'] = $value[4];
                  $history['ket'] = $value[5];
                  $riwayat_pembayaran[] = $history;
                  if ($value[5] == 'cash') {
                     $total_pembayaran = $total_pembayaran + $value[1];
                  } else {
                     $total_pembayaran = $total_pembayaran - $value[1];
                  }
               }
            }
            $feedBack['id'] = $id;
            $feedBack['riwayat_pembayaran'] = $riwayat_pembayaran;
            $feedBack['total_harga'] = $row->total_transaksi;
            $feedBack['total_pembayaran'] = $total_pembayaran;
            $feedBack['sisa'] = $row->total_transaksi - $total_pembayaran;
         }
      }
      return $feedBack;
   }

   function check_tiket_transaction_id_exist($id)
   {
      $this->db->select('id')
         ->from('tiket_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function check_pembayaran($dibayar, $id)
   {
      $this->db->select('tt.total_transaksi,
                           (SELECT SUM(biaya) FROM tiket_transaction_history WHERE tiket_transaction_id=tt.id AND ket="cash") AS pembayaran,
                           (SELECT SUM(biaya) FROM tiket_transaction_history WHERE tiket_transaction_id=tt.id AND ket="refund") AS refund,')
         ->from('tiket_transaction AS tt')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $sisa = 0;
      $feedBack = array();
      if ($q->num_rows() > 0) {
         $rows = $q->row();
         $total_sudah_bayar = (($rows->pembayaran != '' ? $rows->pembayaran : 0) - ($rows->refund != '' ? $rows->refund : 0));
         $total_harga = $rows->total_transaksi;
         $total_pembayaran = $dibayar + $total_sudah_bayar;
         $sisa = $total_harga - $total_pembayaran;
      }
      if ($sisa < 0) {
         $feedBack['error'] = true;
         $feedBack['error'] = 'Total pembayaran tidak boleh melebihi total harga tiket';
      } else {
         $feedBack['error'] = false;
      }
      return $feedBack;
   }

   function get_no_reg_tiket($id)
   {
      $this->db->select('no_register')
         ->from('tiket_transaction')
         ->where('id', $id);
      $q = $this->db->get();
      $no_register = '';
      if ($q->num_rows() > 0) {
         $rows = $q->row();
         $no_register = $rows->no_register;
      }
      return $no_register;
   }

   # get riwayat pembayaran tiket
   function riwayat_pembayaran_tiket($id)
   {
      $this->db->select('invoice, biaya, input_date, costumer_name, costumer_identity, receiver, ket ')
         ->from('tiket_transaction_history')
         ->where('company_id', $this->company_id)
         ->where('tiket_transaction_id', $id)
         ->order_by('input_date', 'desc');
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $feedBack[] = array(
               'invoice' => $rows->invoice,
               'biaya' => $rows->biaya,
               'tanggal_transaksi' => $rows->input_date,
               'nama_pelanggan' => $rows->costumer_name,
               'nomor_identitas' => $rows->costumer_identity,
               'nama_petugas' => $rows->receiver,
               'ket' => $rows->ket
            );
         }
      }
      return $feedBack;
   }

   function get_info_reschedule_tiket_transaction($id)
   {
      $this->db->select('ttd.id, ttd.pax, ttd.code_booking, ma.airlines_name, ttd.departure_date, ttd.travel_price, ttd.costumer_price')
         ->from('tiket_transaction_detail AS ttd')
         ->join('mst_airlines AS ma', 'ttd.airlines_id=ma.id', 'inner')
         ->where('ttd.company_id', $this->company_id)
         ->where('ttd.tiket_transaction_id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $feedBack[] = array(
               'id' => $rows->id,
               'pax' => $rows->pax,
               'code_booking' => $rows->code_booking,
               'airlines_name' => $rows->airlines_name,
               'departure_date' => $rows->departure_date,
               'travel_price' => $rows->travel_price,
               'costumer_price' => $rows->costumer_price,
               'subtotal' => $rows->pax  * $rows->costumer_price
            );
         }
      }
      return $feedBack;
   }

   function get_total_transaksi_tiket($id)
   {
      $this->db->select('costumer_price, pax')
         ->from('tiket_transaction_detail')
         ->where('company_id', $this->company_id)
         ->where('tiket_transaction_id', $id);
      $q = $this->db->get();
      $total = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $total = $total + ($rows->pax * $rows->costumer_price);
         }
      }
      return $total;
   }

   # get total pembayaran
   function get_total_pembayaran($id)
   {
      # get histori transaksi
      $this->db->select('biaya, ket')
         ->from('tiket_transaction_history')
         ->where('company_id', $this->company_id)
         ->where('tiket_transaction_id', $id);
      $q = $this->db->get();
      $cash = 0;
      $refund = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if ($rows->ket == 'cash') {
               $cash = $cash + $rows->biaya;
            } elseif ($rows->ket == 'refund') {
               $refund = $refund + $rows->biaya;
            }
         }
      }
      return $cash - $refund;
   }

   # get sudah bayar tiket
   function get_sudah_bayar_tiket($id)
   {
      # get total transaksi tiket
      $total_transaksi = $this->get_total_transaksi_tiket($id);
      $total_pembayaran = $this->get_total_pembayaran($id);

      return array('total_pembayaran' => $total_pembayaran, 'sisa' => $total_transaksi - $total_pembayaran);
   }

   function check_tiket_transaction_detail_id_exit($id)
   {
      $this->db->select('id')
         ->from('tiket_transaction_detail')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   # td = Transaction DateTime
   # dt = Depature date
   # tp = Travel Price
   # cp = Costumer Price
   function get_db_transaction_detail($id)
   {
      $this->db->select('ttd.id, ttd.pax, ttd.departure_date, ttd.travel_price, ttd.costumer_price, ttd.code_booking, tt.no_register, ttd.airlines_id')
         ->from('tiket_transaction_detail AS ttd')
         ->join('tiket_transaction AS tt', 'ttd.tiket_transaction_id=tt.id', 'inner')
         ->where('ttd.tiket_transaction_id', $id)
         ->where('ttd.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            // $reschedule_list = 'td$'.date('Y-m-d H:i:d').'|dt$'.$rows->departure_date.'|tp$'.$rows->travel_price.'|cp$'.$rows->costumer_price;
            $feedBack[$rows->id] = array(
               'id' => $rows->id,
               'pax' => $rows->pax,
               'departure_date' => $rows->departure_date,
               'travel_price' => $rows->departure_date,
               'costumer_price' => $rows->costumer_price,
               'code_booking' => $rows->code_booking,
               'no_register' => $rows->no_register,
               'airlines_id' => $rows->airlines_id
            );
         }
      }
      return $feedBack;
   }

   function get_tiket_transaction_detail_id($id)
   {
      $this->db->select('id')
         ->from('tiket_transaction_detail')
         ->where('tiket_transaction_id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list[] = $rows->id;
         }
      }
      return $list;
   }

   function get_info_refund_tiket_transaction($id)
   {
      $this->db->select('ttd.id, ttd.pax, ttd.code_booking, ttd.departure_date, ttd.travel_price, ttd.costumer_price, ma.airlines_name')
         ->from('tiket_transaction_detail AS ttd')
         ->join('mst_airlines AS ma', 'ttd.airlines_id=ma.id', 'inner')
         ->where('ttd.tiket_transaction_id', $id)
         ->where('ttd.company_id', $this->company_id);
      $q = $this->db->get();
      $list_detail = array();
      $total_transaksi = 0;
      $list_detail_id = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_detail[] = array(
               'id' => $row->id,
               'pax' => $row->pax,
               'code_booking' => $row->code_booking,
               'departure_date' => $row->departure_date,
               'travel_price' => $row->travel_price,
               'costumer_price' => $row->costumer_price,
               'airlines_name' => $row->airlines_name
            );
            $total_transaksi = $total_transaksi + ($row->pax * $row->costumer_price);
            $list_detail_id[] = $row->id;
         }
      }
      $total_pembayaran = $this->get_total_pembayaran($id);
      $sisa = $total_transaksi - $total_pembayaran;

      return array(
         'list_detail' => $list_detail,
         'total_pembayaran' => $total_pembayaran,
         'total_transaksi' => $total_transaksi,
         'sisa' => $sisa
      );
   }

   // get data detail transaksi tiket
   function get_data_detail_transaksi_tiket($tiket_transaction_id, $list_id)
   {
      $this->db->select('ttd.id, ttd.airlines_id, ma.airlines_name, ttd.pax, ttd.code_booking, ttd.departure_date, ttd.travel_price, ttd.costumer_price')
         ->from('tiket_transaction_detail AS ttd')
         ->join('mst_airlines AS ma', 'ttd.airlines_id=ma.id', 'inner')
         ->where('ttd.company_id', $this->company_id)
         ->where_in('ttd.tiket_transaction_id', $tiket_transaction_id);
      $feedBack = array();
      $q = $this->db->get();
      $total = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (in_array($rows->id, $list_id)) {
               $feedBack[$rows->id] = array(
                  'airlines_id' => $rows->airlines_id,
                  'airlines_name' => $rows->airlines_name,
                  'pax' => $rows->pax,
                  'code_booking' => $rows->code_booking,
                  'departure_date' => $rows->departure_date,
                  'travel_price' => $rows->travel_price,
                  'costumer_price' => $rows->costumer_price
               );
            } else {
               $total = $total + ($rows->pax * $rows->costumer_price);
            }
         }
      }
      return array('list_detail' => $feedBack, 'total_transaksi' => $feedBack);
   }

   function get_no_register($id)
   {
      $this->db->select('no_register')
         ->from('tiket_transaction')
         ->where('id', $id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $no_register = '';
      if ($q->num_rows() > 0) {
         $no_register = $q->row()->no_register;
      }
      return $no_register;
   }

   # get info detail tiket
   function get_info_detail_tiket($tiket_transaction_id)
   {
      $this->db->select('ttd.id, ttd.airlines_id, ma.airlines_name, ttd.pax, ttd.code_booking,
                         ttd.departure_date, ttd.travel_price, ttd.costumer_price')
         ->from('tiket_transaction_detail AS ttd')
         ->join('mst_airlines AS ma', 'ttd.airlines_id=ma.id', 'inner')
         ->where('ttd.company_id', $this->company_id)
         ->where_in('tiket_transaction_id', $tiket_transaction_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $file_name = $q->list_fields();
         foreach ($q->result() as $rows) {
            $arr = array();
            foreach ($file_name as $key => $value) {
               $arr[$value] = $rows->$value;
            }
            $list[$rows->id] = $arr;
         }
      }
      return $list;
   }

   function get_total_pembayaran_tiket($tiket_transaction_id)
   {
      $this->db->select('biaya, ket')
         ->from('tiket_transaction_history')
         ->where('tiket_transaction_id', $tiket_transaction_id)
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $cash = 0;
      $refund = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if ($rows->ket == 'cash') {
               $cash = $cash + $rows->biaya;
            } elseif ($rows->ket == 'refund') {
               $refund = $refund + $rows->biaya;
            }
         }
      }
      return $cash - $refund;
   }
}
