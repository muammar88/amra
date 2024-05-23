<?php

/**
 *  -----------------------
 *	Model kwitansi
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_kwitansi extends CI_Model
{
   private $company_id;

   function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function getSettingValue()
   {
      $param = array('invoice_title', 'invoice_address', 'telp', 'invoice_email', 'pos_code', 'invoice_note');
      $this->db->select('invoice_title, logo, invoice_address, telp, invoice_email, pos_code, invoice_note')
         ->from('company')
         ->where('id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->logo != '') :
               $logo = file_exists(FCPATH . 'image/company/invoice_logo/' . $row->logo) ? $row->logo : 'default.png';
            else :
               $logo = 'default.png';
            endif;
            $return['logo'] = $logo;
            $return['invoice_title'] = $row->invoice_title;
            $return['invoice_address'] = $row->invoice_address;
            $return['telp'] = $row->telp;
            $return['invoice_email'] = $row->invoice_email;
            $return['pos_code'] = $row->pos_code;
            $return['invoice_note'] = $row->invoice_note;
         }
      }
      return $return;
   }

   function getInvoiceContentTransactionCash()
   {
      $array = array();
      $sesi = $this->session->userdata('cetak_invoice');
      $this->db->select('p.kode, p.paket_name, p.departure_date, pt.total_paket_price, pt.diskon, pt.total_mahram_fee, pt.payment_methode,
                         pt.id AS paket_transaction_id, pt.price_per_pax, pth.deposit_name, pth.deposit_phone, pth.receiver,
                         pth.deposit_address, pth.input_date')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.no_register', $sesi['nomor_registrasi'])
         ->where('pth.invoice', $sesi['invoice']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $getJamaah = $this->_getJamaah($row->paket_transaction_id);
            $array['kode'] = $row->kode;
            $array['paket_name'] = $row->paket_name;
            $array['deposit_name'] = $row->deposit_name;
            $array['deposit_phone'] = $row->deposit_phone;
            $array['deposit_address'] = $row->deposit_address;
            $array['order_date'] = $row->input_date;
            $array['departure_date'] = $this->date_ops->change_date_t4($row->departure_date);
            $array['jamaah'] = $getJamaah['list'];
            $array['harga_per_pax'] = 'Rp.' . number_format($row->price_per_pax);
            $array['total_paket_price'] = 'Rp.' . number_format($row->price_per_pax * $getJamaah['count']);
            $array['diskon'] = 'Rp.' . number_format($row->diskon);
            $array['mahram_fee'] = 'Rp.' . number_format($row->total_mahram_fee);
            $array['receiver'] = $row->receiver;
            $array['total_tagihan'] = 'Rp.' . number_format($row->total_paket_price);
            $totalSisa = $this->totalSisaPembayaranCash($row->paket_transaction_id, $sesi['invoice'], $row->total_paket_price);
            $array['total_pembayaran'] = 'Rp.' . number_format($totalSisa['total_pembayaran']);
            $array['sisa'] = 'Rp.' . number_format($totalSisa['sisa_tagihan']);
         }
      }

      $array['no_register'] = $sesi['nomor_registrasi'];
      $array['invoice'] = $sesi['invoice'];

      return $array;
   }

   function totalSisaPembayaranCash($paket_transaction_id, $invoice, $total_tagihan)
   {
      $total_pembayaran = 0;
      $sisa_tagihan = 0;
      $this->db->select('pth.invoice, pth.paid, pth.ket')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pth.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $total_pembayaran = $total_pembayaran + $row->paid;
            } elseif ($row->ket == 'refund') {
               $total_pembayaran = $total_pembayaran - $row->paid;
            }
            if ($row->invoice == $invoice) {
               break;
            }
         }
      }
      $sisa_tagihan = $total_tagihan - $total_pembayaran;
      return array('total_pembayaran' => $total_pembayaran, 'sisa_tagihan' => $sisa_tagihan);
   }

   # get jamaah
   function _getJamaah($paket_transaction_id)
   {
      $this->db->select('p.fullname')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('ptj.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $list = '<ul class="pl-3 list">';
      $count = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list .= '<li>' . $row->fullname . '</li>';
            $count++;
         }
      }
      $list .= '</ul>';
      return array('list' => $list, 'count' => $count);
   }

   function getInfoKwitansiReturnedBarang($paket_transaction_id, $jamaah_id, $invoice)
   {

      $receiver_returned = '';
      $receiver_returned_identity = '';
      $receiver_returned_hp = '';
      $receiver_returned_address = '';
      $date_returned = '';
      $petugas = '';
      $jabatan = '';
      $namaJamaah = '';
      $noIdentitasJamaah = '';
      $noHPJamaah = '';
      $addressJamaah = '';
      $listItem = '';

      $this->db->select('hi.invoice_returned, hi.item_name,
                         hi.receiver_returned,
                         hi.receiver_returned_identity,
                         hi.receiver_returned_hp,
                         hi.receiver_returned_address,
                         hi.date_returned,
                         (SELECT CONCAT_WS(\'$\', p.fullname, g.nama_group)
                           FROM base_users AS u
                           INNER JOIN personal AS p ON u.personal_id=p.personal_id
                           INNER JOIN base_groups AS g ON u.group_id=g.group_id
                           WHERE u.user_id=hi.giver_returned) AS petugasJabatan,
                         (SELECT CONCAT_WS(\'$\', p.fullname, p.identity_number, p.nomor_whatsapp, p.address)
                           FROM jamaah AS j
                           INNER JOIN personal AS p ON j.personal_id=p.personal_id
                           WHERE j.id=hi.jamaah_id) AS jamaah   ')
         ->from('handover_item AS hi')
         ->where('hi.invoice_returned', $invoice)
         ->where('hi.paket_transaction_id', $paket_transaction_id)
         ->where('hi.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $num = 1;
         foreach ($q->result() as $row) {
            // $ex = explode('$', $row->petugasJabatan);
            if ($row->petugasJabatan == '') {
               $petugas = 'Admin ' . $this->session->userdata($this->config->item('apps_name'))['company_name'];
               $jabatan = 'Administrator';
            } else {
               $ex = explode('$', $row->petugasJabatan);
               $petugas = $ex[0];
               $jabatan = $ex[1];
            }

            $exJamaah = explode('$', $row->jamaah);

            $receiver_returned = $row->receiver_returned;
            $receiver_returned_identity = $row->receiver_returned_identity;
            $receiver_returned_hp = $row->receiver_returned_hp;
            $receiver_returned_address = $row->receiver_returned_address;
            $date_returned = $row->date_returned;

            $listItem .= '<div class="col-2"><p style="display:inline-block">' . $num . '. ' . $row->item_name . '</p></div>';

            $namaJamaah = $exJamaah[0];
            $noIdentitasJamaah = $exJamaah[1] == '' ? '-' : $exJamaah[1];
            $noHPJamaah = $exJamaah[2] == '' ? '-' : $exJamaah[2];
            $addressJamaah = $exJamaah[3] == '' ? '-' : $exJamaah[3];

            $num++;
         }
      }

      $data = array();
      $data['invoice'] = $invoice;
      $data['receiver_returned'] = $receiver_returned;
      $data['receiver_returned_identity'] = $receiver_returned_identity;
      $data['receiver_returned_hp'] = $receiver_returned_hp;
      $data['receiver_returned_address'] = $receiver_returned_address;
      $data['order_date'] = $date_returned;
      $data['petugas'] = $petugas;
      $data['jabatan'] = $jabatan;
      $data['nama_jamaah'] = $namaJamaah;
      $data['no_identitas_jamaah'] = $noIdentitasJamaah;
      $data['no_hp_jamaah'] = $noHPJamaah;
      $data['address_jamaah'] = $addressJamaah;
      $data['list_item'] = $listItem;

      return $data;
   }


   function getInfoKwitansiFasilitas($paket_transaction_id, $jamaah_id, $invoice)
   {

      $this->db->select('hf.id, hf.facilities_id, m.facilities_name, hf.receiver_name, hf.receiver_identity, hf.date_transaction,
                           (SELECT CONCAT_WS(\'$\', p.fullname, g.nama_group)
                               FROM base_users AS u
                               INNER JOIN personal AS p ON u.personal_id=p.personal_id
                               INNER JOIN base_groups AS g ON u.group_id=g.group_id
                               WHERE u.user_id=hf.officer) AS petugasJabatan,
                            (SELECT CONCAT_WS(\'$\', p.fullname, p.identity_number, p.nomor_whatsapp, p.address)
                              FROM jamaah AS j
                              INNER JOIN personal AS p ON j.personal_id=p.personal_id
                              WHERE j.id=hf.jamaah_id) AS jamaah ')
         ->from('handover_facilities AS hf')
         ->join('mst_facilities AS m', 'hf.facilities_id=m.id', 'inner')
         ->where('hf.invoice', $invoice)
         ->where('hf.paket_transaction_id', $paket_transaction_id)
         ->where('hf.jamaah_id', $jamaah_id);
      $q = $this->db->get();

      $feedBack = array();

      $namaJamaah = '';
      $noIdentitasJamaah = '';
      $noHPJamaah = '';
      $addressJamaah = '';
      $num = 1;
      $list = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->petugasJabatan == '') {
               $feedBack['petugas'] = 'Admin ' . $this->session->userdata($this->config->item('apps_name'))['company_name'];
               $feedBack['jabatan'] = 'Administrator';
            } else {
               $ex = explode('$', $row->petugasJabatan);
               $feedBack['petugas'] = $ex[0];
               $feedBack['jabatan'] = $ex[1];
            }
            $exJamaah = explode('$', $row->jamaah);

            $list .= '<div class="col-2"><p style="display:inline-block">' . $num . '. ' . $row->facilities_name . '</p></div>';

            $feedBack['order_date'] = $row->date_transaction;
            $feedBack['receiver_name'] = $row->receiver_name;
            $feedBack['receiver_identity'] = $row->receiver_identity;
            $feedBack['nama_jamaah'] = $exJamaah[0];
            $feedBack['no_identitas_jamaah'] = $exJamaah[1] == '' ? '-' : $exJamaah[1];
            $feedBack['no_hp_jamaah'] = $exJamaah[2] == '' ? '-' : $exJamaah[2];
            $feedBack['address_jamaah'] = $exJamaah[3] == '' ? '-' : $exJamaah[3];
            $num++;
         }
      }
      $feedBack['invoice'] = $invoice;
      $feedBack['list_item'] = $list;

      return $feedBack;
   }

   function getInfoKwitansiHandoverBarang($paket_transaction_id, $jamaah_id, $invoice)
   {

      $giver_handover = '';
      $giver_handover_identity = '';
      $giver_handover_hp = '';
      $giver_handover_address = '';
      $date_taken = '';
      $petugas = '';
      $jabatan = '';
      $namaJamaah = '';
      $noIdentitasJamaah = '';
      $noHPJamaah = '';
      $addressJamaah = '';
      $listItem = '';

      $this->db->select('hi.invoice_handover, hi.item_name, hi.giver_handover, hi.giver_handover_identity,
                         hi.giver_handover_hp, hi.giver_handover_address, hi.date_taken,
                         (SELECT CONCAT_WS(\'$\', p.fullname, g.nama_group)
                           FROM base_users AS u
                           INNER JOIN personal AS p ON u.personal_id=p.personal_id
                           INNER JOIN base_groups AS g ON u.group_id=g.group_id
                           WHERE u.user_id=hi.receiver_handover) AS petugasJabatan,
                         (SELECT CONCAT_WS(\'$\', p.fullname, p.identity_number, p.nomor_whatsapp, p.address)
                           FROM jamaah AS j
                           INNER JOIN personal AS p ON j.personal_id=p.personal_id
                           WHERE j.id=hi.jamaah_id) AS jamaah   ')
         ->from('handover_item AS hi')
         ->where('hi.invoice_handover', $invoice)
         ->where('hi.paket_transaction_id', $paket_transaction_id)
         ->where('hi.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $num = 1;
         foreach ($q->result() as $row) {
            if ($row->petugasJabatan == '') {
               $petugas = 'Admin ' . $this->session->userdata($this->config->item('apps_name'))['company_name'];
               $jabatan = 'Administrator';
            } else {
               $ex = explode('$', $row->petugasJabatan);
               $petugas = $ex[0];
               $jabatan = $ex[1];
            }

            $exJamaah = explode('$', $row->jamaah);
            $giver_handover =  $row->giver_handover;
            $giver_handover_identity =  $row->giver_handover_identity;
            $giver_handover_hp =  $row->giver_handover_hp;
            $giver_handover_address =  $row->giver_handover_address;
            $date_taken =  $row->date_taken;

            $listItem .= '<div class="col-2"><p style="display:inline-block">' . $num . '. ' . $row->item_name . '</p></div>';
            $namaJamaah = $exJamaah[0];
            $noIdentitasJamaah = $exJamaah[1] == '' ? '-' : $exJamaah[1];
            $noHPJamaah = $exJamaah[2] == '' ? '-' : $exJamaah[2];
            $addressJamaah = $exJamaah[3] == '' ? '-' : $exJamaah[3];
            $num++;
         }
      }

      $data = array();
      $data['invoice'] = $invoice;
      $data['giver_handover'] = $giver_handover;
      $data['giver_handover_identity'] = $giver_handover_identity;
      $data['giver_handover_hp'] = $giver_handover_hp;
      $data['giver_handover_address'] = $giver_handover_address;
      $data['order_date'] = $date_taken;
      $data['petugas'] = $petugas;
      $data['jabatan'] = $jabatan;
      $data['nama_jamaah'] = $namaJamaah;
      $data['no_identitas_jamaah'] = $noIdentitasJamaah;
      $data['no_hp_jamaah'] = $noHPJamaah;
      $data['address_jamaah'] = $addressJamaah;
      $data['list_item'] = $listItem;

      return $data;
   }

   function getInfoKwitansiPindahPaket($pindahPaketId)
   {
      $this->db->select('pp.*, (SELECT CONCAT_WS(\'$\', p.fullname, p.address, p.nomor_whatsapp, p.identity_number)
                               FROM jamaah AS j INNER JOIN personal AS p ON j.personal_id=p.personal_id
                               WHERE j.id=pp.jamaah_id AND p.company_id="' . $this->company_id . '") AS dataJamaah,
                        pt.id AS paket_transaction_id, pt.total_paket_price, pt.total_mahram_fee')
         ->from('pindah_paket AS pp')
         ->join('paket_transaction AS pt', 'pp.no_register_paket_tujuan=pt.no_register', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pp.id', $pindahPaketId)
         ->where('p.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $infoJamaah = explode('$', $row->dataJamaah);
            $feedBack['nama_jamaah'] = $infoJamaah[0];
            $feedBack['identitas_jamaah'] = $infoJamaah[3];
            $feedBack['alamat_jamaah'] = $infoJamaah[1];
            $feedBack['no_hp_jamaah'] = $infoJamaah[2];

            $feedBack['kode_paket_asal'] = $row->kode_paket_asal;
            $feedBack['paket_asal'] = $row->paket_asal;
            $feedBack['tipe_paket_asal'] = $row->tipe_paket_asal;
            $feedBack['harga_paket_asal'] = $row->harga_paket_asal;
            $feedBack['no_register_asal'] = $row->no_register_asal;
            $feedBack['kode_paket_tujuan'] = $row->kode_paket_tujuan;
            $feedBack['paket_tujuan'] = $row->paket_tujuan;
            $feedBack['tipe_paket_tujuan'] = $row->tipe_paket_tujuan;
            $feedBack['no_register_paket_tujuan'] = $row->no_register_paket_tujuan;
            $feedBack['harga_paket_tujuan'] = $row->harga_paket_tujuan;
            $feedBack['biaya_yang_dipindahkan'] = $row->biaya_yang_dipindahkan;
            $feedBack['fee_mahram'] = $row->fee_mahram;
            $feedBack['refund'] = $row->refund;
            $feedBack['invoice_refund'] = $row->invoice_refund;
            $feedBack['invoice_tujuan'] = $row->invoice_tujuan;
            $feedBack['order_date'] = $row->transaction_date;

            // echo  $row->total_paket_price."<br>";
            // echo  $row->total_mahram_fee."<br>";

            $feedBack['sisa_pembayaran'] = $this->hitPembayaranByPaketTransactionID($row->paket_transaction_id, $row->total_paket_price);
         }
      }
      return $feedBack;
   }

   function hitPembayaranByPaketTransactionID($paket_transaction_id, $total_all_price)
   {
      $this->db->select('paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $dibayar = 0;
      $refund = 0;
      $pindah_paket = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $dibayar = $dibayar + $row->paid;
            } elseif ($row->ket == 'refund') {
               $refund = $refund + $row->paid;
            } elseif ($row->ket == 'pindah_paket') {
               $pindah_paket = $pindah_paket + $row->paid;
            }
         }
      }
      $totalPembayaran = $dibayar - ($refund + $pindah_paket);
      return $total_all_price - $totalPembayaran;
   }


   function getInvoiceContentTransactionCicilan()
   {
      $array = array();
      $sesi = $this->session->userdata('cetak_invoice');
      $invoice = $sesi['invoice'];
      // $invoice = 'NM168923264177';
      $dp = 0;
      $sisa_pembayaran_setelah_dp = 0;
      $this->db->select('pt.id, p.kode, p.paket_name, p.departure_date, pt.total_paket_price, pt.diskon,
                         pt.down_payment, pt.total_mahram_fee, pt.payment_methode, pt.tenor, pt.start_date,
                         pt.id AS paket_transaction_id, pt.price_per_pax,
                         (SELECT CONCAT_WS(\'$\', per.fullname, per.address, nomor_whatsapp)
                           FROM paket_transaction_jamaah AS ptj
                           INNER JOIN jamaah AS j ON ptj.jamaah_id=j.id
                           INNER JOIN personal AS per ON j.personal_id=per.personal_id
                           WHERE ptj.paket_transaction_id= pt.id AND ptj.leader=1) AS leader')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.no_register', $sesi['nomor_registrasi']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $due = $this->_dueDate($row->id);
            $exp = explode('$', $row->leader);

            $array['kode'] = $row->kode;
            $array['paket_name'] = $row->paket_name;
            $array['total_pinjaman'] = 'Rp. ' . number_format($row->total_paket_price - $row->down_payment);
            $array['tenor'] = $row->tenor;
            $array['duedate'] = $this->date_ops->change_date_t4($due['jatuh_tempo']);
            $array['angsuran'] = 'Rp. ' . number_format($due['angsuran']);
            $array['nama_penyetor'] = $exp[0];
            $array['hp_penyetor'] = $exp[1];
            $array['alamat_penyetor'] = $exp[2];

            $sisa_pembayaran_setelah_dp = $row->total_paket_price;

            if ($row->down_payment != 0) {
               $dp = $row->down_payment;
               $array['dp'] = array(
                  'term' => '#',
                  'ket' => 'Pembayaran DP',
                  'bayar' => 'Rp ' . number_format($row->down_payment),
                  'sisa' => 'Rp ' . number_format($row->total_paket_price - $row->down_payment)
               );
               $sisa_pembayaran_setelah_dp = $sisa_pembayaran_setelah_dp - $row->down_payment;
            }

            if (isset($sesi['invoice'])) {
               $array['invoice'] = $invoice;
               // skema pembayaranCicilan
               $skema = $this->skemaPembayaranCicilan($sesi['nomor_registrasi']);
               // pembayaran
               $pembayaran = $this->info_pembayaran_invoice($sesi['nomor_registrasi'], $invoice);

               $array['detailPembayaran'] = $pembayaran['detailPembayaran'];
               $pembayaran_cicilan = $pembayaran['pembayaran_cicilan'];

               $total_cicilan = $sisa_pembayaran_setelah_dp;
               $array['total_sudah_bayar'] =  'Rp. ' . number_format($pembayaran_cicilan + $dp);
               $list_skema_cicilan  = array();
               if ($pembayaran_cicilan != 0) {
                  $first = 1;
                  foreach ($skema as $key => $value) {
                     $pembayaran_cicilan_sebelum_dibayar = $pembayaran_cicilan;
                     $pembayaran_cicilan = $pembayaran_cicilan - $value['amount'];
                     if ($pembayaran_cicilan > 0) {
                        $list_skema_cicilan[] = array(
                           'term' => $key,
                           'ket' => 'Pembayaran ke ' . $key,
                           'bayar' => 'Rp. ' . number_format($value['amount']),
                           'sisa' => 'Rp. ' . number_format(abs(0))
                        );
                     } else if ($pembayaran_cicilan == 0) {
                        $list_skema_cicilan[] = array(
                           'term' => $key,
                           'ket' => 'Pembayaran ke ' . $key,
                           'bayar' => 'Rp. ' . number_format($value['amount']),
                           'sisa' => 'Rp. ' . number_format(abs(0))
                        );
                        break;
                     } else {
                        $list_skema_cicilan[] = array(
                           'term' => $key,
                           'ket' => 'Pembayaran ke ' . $key,
                           'bayar' => 'Rp. ' . number_format($pembayaran_cicilan_sebelum_dibayar),
                           'sisa' => 'Rp. ' . number_format(abs($pembayaran_cicilan))
                        );
                        break;
                     }
                  }
               }


               // $listBelumBayar = array();
               // if( $sudahBayar != 0 ) {
               //    $sisa = $sudahBayar;
               //    // get angsuran yang belum bayar
               //    $first = 0;
               //    foreach ( $skema as $key => $value ){
               //       // echo $sisa."<br>";
               //       $sisa = $sisa - $value['amount'];
               //       if( $sisa < 0 ){
               //          if( $first == 0 ){
               //             $listBelumBayar[$key] = array('amount' => abs($sisa));
               //          }else{
               //             $listBelumBayar[$key] = array('amount' => $value['amount']);
               //          }
               //          $first++;
               //       }
               //    }
               // }
               // else{
               //    foreach ( $skema as $key => $value ) {
               //       $listBelumBayar[$key] = array('amount' => $value['amount']);
               //    }
               // }
               // $sisa = $bayarSekarang;
               // $listPembayaran = array();
               // foreach ($listBelumBayar as $key => $value) {
               //    $sisaSebelum = $sisa;
               //    $sisa = $sisa - $value['amount'];
               //    if( $sisa < 0 ){
               //       $listPembayaran[] = array('term' => $key,
               //                                 'ket' => 'Pembayaran ke '.$key,
               //                                 'bayar' => 'Rp. '.number_format($sisaSebelum),
               //                                 'sisa' => 'Rp. '.number_format(abs($sisa)));
               //       break;
               //    }else{
               //       $listPembayaran[] = array('term' => $key,
               //                                 'ket' => 'Pembayaran ke '.$key,
               //                                 'bayar' => 'Rp. '.number_format($value['amount']),
               //                                 'sisa' => 'Rp. 0');
               //    }
               // }
               $array['listPembayaran'] = $list_skema_cicilan;
            }
         }
      }

      $array['no_register'] = $sesi['nomor_registrasi'];
      return $array;
   }

   # get skema
   function skemaPembayaranCicilan($noRegister)
   {
      $this->db->select('term, amount')
         ->from('paket_installment_scheme  AS pis')
         ->join('paket_transaction AS pt', 'pis.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.no_register', $noRegister);
      $skema = array();
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $sumAmount = 0;
         foreach ($q->result() as $row) {
            $sumAmount = $sumAmount + $row->amount;
            $skema[$row->term] = array('amount' => $row->amount, 'sumAmount' => $sumAmount);
            //$skema[$row->term] = array('min' => ($sumAmount - $row->amount + 1), 'max' => $sumAmount);
         }
      }

      // echo "<br>=====Skema=======<br>";
      // print_r( $skema );
      return $skema;
   }

   # get info pembayaran invoice
   function info_pembayaran_invoice($noRegister, $invoice)
   {
      $this->db->select('invoice, paid, receiver, ptih.input_date, ptih.ket')
         ->from('paket_transaction_installement_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.no_register', $noRegister)
         ->where('p.company_id', $this->company_id)
         ->order_by('ptih.input_date', 'asc');
      $q = $this->db->get();
      $pembayaran_cicilan = 0;
      $detail_pembayaran = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket != 'dp') {
               $pembayaran_cicilan = $pembayaran_cicilan + $row->paid;
            }
            if ($row->invoice == $invoice) {
               $detail_pembayaran = array(
                  'paid' => 'Rp. ' . number_format($row->paid),
                  'penerima' => $row->receiver,
                  'tanggal_transaksi' => $this->date_ops->change_date_t5($row->input_date)
               );
               break;
            }
         }
      }
      return array(
         'pembayaran_cicilan' => $pembayaran_cicilan,
         'detailPembayaran' => $detail_pembayaran
      );
   }

   function _dueDate($paket_transaction_id)
   {
      $this->db->select('amount, duedate')
         ->from('paket_installment_scheme')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $listFormatPembayaranan = array();
      $listFormatTanggal = array();
      $totalAmount = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $totalAmount = $totalAmount + $row->amount;
            $listFormatPembayaranan[] = $totalAmount;
            $listFormatTanggal[] = $row->duedate;
         }
      }

      $angsuran = 0;
      $jatuh_tempo = '';
      foreach ($listFormatTanggal as $key => $value) {
         if ($value >= date('Y-m-d')) {
            $angsuran = $listFormatPembayaranan[$key];
            $jatuh_tempo = $value;
            break;
         }
      }

      return array('angsuran' => $angsuran, 'jatuh_tempo' => $jatuh_tempo);
   }

   function getRiwayatCicilan()
   {
      $array = array();
      $sesi = $this->session->userdata('cetak_invoice');
      $this->db->select('pis.term, pis.amount, pis.duedate, p.paket_name, p.departure_date, pt.total_paket_price, pt.down_payment,
                           (SELECT CONCAT_WS(\'$\', per.fullname, per.address, per.nomor_whatsapp, per.identity_number )
                            FROM paket_transaction_jamaah AS ptj
                            INNER JOIN jamaah AS j ON ptj.jamaah_id=j.id
                            INNER JOIN personal AS per ON j.personal_id=per.personal_id
                            WHERE ptj.paket_transaction_id= pis.paket_transaction_id AND ptj.leader=1) AS leader')
         ->from('paket_installment_scheme AS pis')
         ->join('paket_transaction AS pt', 'pis.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.no_register', $sesi['nomor_registrasi']);
      $q = $this->db->get();
      $skema = array();
      $bulan = 0;
      $totalAmount = 0;
      $total_paket_price = 0;
      $angsuran = 0;
      $dp = 0;
      $pinjam = 0;
      $amoutPerMonth = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($angsuran == 0) {
               $angsuran = $row->amount;
            }
            $totalAmount = $totalAmount + $row->amount;
            $total_paket_price = $row->total_paket_price;
            $amoutPerMonth[] = $totalAmount;
            $array['paket_name'] = $row->paket_name;
            $array['departure_date'] = $this->date_ops->change_date_t4($row->departure_date);
            $exp = explode('$', $row->leader);
            $array['fullname'] = $exp[0];
            $array['alamat'] = $exp[1];
            $array['no_hp'] = $exp[2];
            $array['identity_number'] = $exp[3];
            $bulan++;
         }
      }
      $this->db->select('ptih.paid, ptih.invoice, ptih.deposit_name, ptih.receiver, ptih.input_date, ptih.ket')
         ->from('paket_transaction_installement_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.no_register', $sesi['nomor_registrasi']);
      $r = $this->db->get();
      $paid = 0;
      $riwayat_transaksi = array();
      $total_pembayaran = 0;
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $rows) {
            $total_pembayaran = $total_pembayaran + $rows->paid;
            if ($rows->ket != 'dp') {
               $paid = $paid + $rows->paid;
            } else {
               $dp = $dp + $rows->paid;
            }
            $riwayat_transaksi[] = array(
               'invoice' => $rows->invoice,
               'debet' => 'Rp. ' . number_format($rows->paid),
               'ket' => $rows->ket,
               'penyetor' => $rows->deposit_name,
               'penerima' => $rows->receiver,
               'tanggal' => $rows->input_date
            );
         }
      }
      $array['total_pembayaran'] = 'Rp. ' . number_format($total_pembayaran);
      $array['rata_rata_amount'] = 'Rp. ' . number_format($angsuran);
      $totalAmount = $total_paket_price - $dp;
      $array['totalAmount'] = 'Rp. ' . number_format($total_paket_price);
      $array['bulan'] = $bulan;
      $bulanSudahBayar = 0;
      foreach ($amoutPerMonth as $key => $value) {
         if ($value <= $paid) {
            $bulanSudahBayar++;
         }
      }
      $array['riwayatTransaksi'] = $riwayat_transaksi;
      $array['sisaBulan'] = $bulan - $bulanSudahBayar;
      $array['sisaPinjaman'] = 'Rp. ' . number_format($totalAmount - $paid);
      $array['no_register'] = $sesi['nomor_registrasi'];
      return $array;
   }

   function getSkemaCicilan()
   {
      $array = array();
      $sesi = $this->session->userdata('cetak_invoice');
      $this->db->select('pis.term, pis.amount, pis.duedate, p.paket_name, p.departure_date, pt.total_paket_price,
                           (SELECT CONCAT_WS(\'$\', per.fullname, per.address, per.nomor_whatsapp, per.identity_number )
                            FROM paket_transaction_jamaah AS ptj
                            INNER JOIN jamaah AS j ON ptj.jamaah_id=j.id
                            INNER JOIN personal AS per ON j.personal_id=per.personal_id
                            WHERE ptj.paket_transaction_id= pis.paket_transaction_id AND ptj.leader=1) AS leader')
         ->from('paket_installment_scheme AS pis')
         ->join('paket_transaction AS pt', 'pis.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('p.company_id', $this->company_id)
         ->where('pt.no_register', $sesi['nomor_registrasi']);
      $q = $this->db->get();
      $skema = array();
      $bulan = 0;
      $totalAmount = 0;
      $total_price = 0;
      $total_paket_price = 0;
      $angsuran = 0;
      $dp = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $skema[] = array(
               'term' => $row->term,
               'amount' =>  'Rp.' . number_format($row->amount),
               'duedate' =>  $this->date_ops->change_date_t4($row->duedate)
            );
            $totalAmount = $totalAmount + $row->amount;
            $total_price = $row->total_paket_price;
            if ($angsuran == 0) {
               $angsuran = $row->amount;
            }
            $array['paket_name'] = $row->paket_name;
            $array['departure_date'] = $this->date_ops->change_date_t4($row->departure_date);
            $exp = explode('$', $row->leader);
            $array['fullname'] = $exp[0];
            $array['alamat'] = $exp[1];
            $array['no_hp'] = $exp[2];
            $array['identity_number'] = $exp[3];

            $bulan++;
         }
      }
      $array['rata_rata_amount'] = 'Rp.' . number_format($angsuran);
      $array['totalAmount'] = 'Rp.' . number_format($totalAmount);
      $array['total_harga_paket'] = 'Rp.' . number_format($total_price);
      $array['bulan'] = $bulan;
      $array['skema'] = $skema;
      $array['no_register'] = $sesi['nomor_registrasi'];
      return $array;
   }

   function getInfoDataJamaah($jamaahID, $paket_transaction_id)
   {
      $bloodType = array(1 => 'O', 2 => 'A', 3 => 'B', 4 => 'AB');
      $this->db->select('pkt.paket_name, pkt.departure_date,
                         pt.no_register,
                         p.fullname, p.birth_place, p.birth_date, p.gender, p.photo,
                         j.blood_type, j.passport_number, j.passport_dateissue, j.passport_place,
                         j.validity_period, p.address, j.pos_code, j.telephone, p.nomor_whatsapp, p.email, j.hajj_experience, j.hajj_year, j.umrah_experience,
                         j.umrah_year, j.departing_from, j.desease, j.last_education, j.profession_name, j.profession_instantion_name, j.profession_instantion_address, j.status_nikah, j.tanggal_nikah, j.father_name,
                         j.nama_keluarga, j.alamat_keluarga, j.telephone_keluarga')
         ->from('paket_transaction AS pt')
         ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
         ->join('paket_transaction_jamaah AS ptj', 'pt.id=ptj.paket_transaction_id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $jamaahID)
         ->where('pkt.company_id', $this->company_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $feedBack['paketTransactionID'] = $paket_transaction_id;
            $feedBack['jamaahID'] = $jamaahID;
            $feedBack['namaPaket'] = $this->_isEmpty($rows->paket_name);
            $feedBack['noRegister'] = $this->_isEmpty($rows->no_register);
            $feedBack['namaJamaah'] = $this->_isEmpty($rows->fullname);
            $feedBack['namaAyahKandung'] = $this->_isEmpty($rows->father_name);
            $feedBack['tempatLahir'] = $this->_isEmpty($rows->birth_place);
            $feedBack['tanggalLahir'] = $this->_isEmpty($rows->birth_date);
            $feedBack['jenisKelamin'] = $rows->gender + 1;
            $feedBack['umur'] = $this->_isEmpty($rows->birth_date);
            $feedBack['golonganDarah'] = $bloodType[$this->_isEmpty($rows->blood_type)];
            $feedBack['nomorPassport'] = $this->_isEmpty($rows->passport_number);
            $feedBack['tanggalDikeluarkan'] = $this->_isEmpty($rows->passport_dateissue);
            $feedBack['tempatDikeluarkan'] = $this->_isEmpty($rows->passport_place);
            $feedBack['masaBerlaku'] = $this->_isEmpty($rows->validity_period);
            $feedBack['alamatTempatTinggal'] = $this->_isEmpty($rows->address);
            $feedBack['kodePos'] = $this->_isNool($rows->pos_code);
            $feedBack['telephone'] = $this->_isEmpty($rows->telephone);
            $feedBack['hp'] = $this->_isEmpty($rows->nomor_whatsapp);
            $feedBack['email'] = $this->_isEmpty($rows->email);

            $jumlahHaji = 0;
            $jumlahUmrah = 0;
            if ($rows->hajj_experience == 0) {
               $experience = 'A';
            } else {
               $experience = 'B';
               if ($rows->hajj_experience > 1) {
                  $jumlahHaji = $rows->hajj_experience - 1;
               } else {
                  $jumlahHaji = '-';
               }
            }

            if ($rows->umrah_experience == 0) {
               $experienceUmrah = 'A';
            } else {
               $experienceUmrah = 'B';
               if ($rows->umrah_experience > 1) {
                  $jumlahUmrah = $rows->umrah_experience - 1;
               } else {
                  $jumlahUmrah = '-';
               }
            }

            $feedBack['pengalamanHaji'] = $this->_isEmpty($experience);
            $feedBack['jumlahHaji'] = $this->_isEmpty($jumlahHaji);
            $feedBack['tahunHaji'] = $this->_isEmpty($rows->hajj_year);
            $feedBack['pengalamanUmrah'] = $this->_isEmpty($experienceUmrah);
            $feedBack['jumlahUmrah'] = $this->_isEmpty($jumlahUmrah);
            $feedBack['tahunUmrah'] = $this->_isEmpty($rows->umrah_year);
            $feedBack['tanggalKeberangkatan'] = $this->_isEmpty($rows->departure_date);
            $feedBack['berangkatDari'] = $this->_isEmpty($rows->departing_from);
            $feedBack['penyakit'] = $this->_isEmpty($rows->desease);
            $pendidikanTerakhir = 1;
            if ($rows->last_education > 2) {
               $pendidikanTerakhir = $rows->last_education - 1;
            }
            $feedBack['pendidikanTerakhir'] = $pendidikanTerakhir;
            $feedBack['pekerjaan'] = $this->_isEmpty($rows->profession_name);
            $feedBack['namaInstansiPekerjaan'] = $this->_isEmpty($rows->profession_instantion_name);
            $feedBack['alamatInstansiPekerjaan'] = $this->_isEmpty($rows->profession_instantion_address);
            $feedBack['statusNikah'] = $rows->status_nikah == 'belum nikah' ? '1' : '2';
            $feedBack['tanggalNikah'] = $this->_isNoolTglNikah($rows->tanggal_nikah);
            $feedBack['namaKeluarga'] = $this->_isNoolTglNikah($rows->nama_keluarga);
            $feedBack['alamatKeluarga'] = $this->_isNoolTglNikah($rows->alamat_keluarga);
            $feedBack['telephoneKeluarga'] = $this->_isNoolTglNikah($rows->telephone_keluarga);
            $feedBack['photo'] = $this->_isNoolTglNikah($rows->photo);
            $feedBack['keluargaBersama'] = $this->_isNoolTglNikah($rows->photo);
         }
      }
      return $feedBack;
   }

   function _isNoolTglNikah($value)
   {
      return $value != '' ? ($value == '0000-00-00' ? '-' : $value) : '-';
   }

   function _isEmpty($value)
   {
      return $value != '' ? ($value == '0000' ? '-' : $value) : '-';
   }

   function _isNool($value)
   {
      return $value != '' ? $value : '00000';
   }

   function getDownloadAbsensiJamaah($paket_id)
   {
      $this->db->select('per.fullname, per.address, per.nomor_whatsapp, p.paket_name, pt.no_register')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('ptj.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      $q = $this->db->get();
      $paket_name = '';
      $no_register = '';
      $array = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $array[] = array('nama' => $rows->fullname, 'alamat' => $rows->address, 'no_hp' => $rows->nomor_whatsapp);
            $paket_name = $rows->paket_name;
            $no_register = $rows->no_register;
         }
      }
      return array('data_jamaah' => $array, 'paket_name' => $paket_name, 'no_register' => $no_register);
   }

   function _getKeluarga($jamaah_id, $paket_transaction_id)
   {
      $this->db->select('p.fullname, p.nomor_whatsapp')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('ptj.paket_transaction_id', $paket_transaction_id)
         ->where('ptj.company_id', $this->company_id)
         ->where('pt.batal_berangkat', '0');
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array('nama' => $row->fullname, 'hubungan' => '', 'telpon' => $this->_isEmpty($row->nomor_whatsapp));
         }
      }
      return $array;
   }

   function getInfoCetakDaftarKamar($paket_id)
   {
      $this->db->select('rj.jamaah_id, p.fullname, p.gender,
                           (SELECT mpt.paket_type_name
                              FROM paket_transaction_jamaah AS ptj
                              INNER JOIN paket_transaction AS pt ON ptj.paket_transaction_id=pt.id
                              INNER JOIN mst_paket_type AS mpt ON pt.paket_type_id=mpt.id
                              WHERE pt.paket_id="' . $paket_id . '" AND ptj.jamaah_id=rj.jamaah_id) AS paket_type_name,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', r.room_number, h.city_id, c.city_name ) SEPARATOR \';\')
                              FROM rooms_jamaah AS rjam
                              INNER JOIN rooms AS r ON rjam.room_id=r.id
                              INNER JOIN mst_hotel AS h ON r.hotel_id=h.id
                              INNER JOIN mst_city AS c ON h.city_id=c.id
                              WHERE rjam.jamaah_id=rj.jamaah_id) AS roomNumber')
         ->from('rooms_jamaah AS rj')
         ->join('rooms AS r', 'rj.room_id=r.id', 'inner')
         ->join('jamaah AS j', 'rj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('r.paket_id', $paket_id)
         ->where('rj.company_id', $this->company_id)
         ->group_by('rj.jamaah_id');
      $q = $this->db->get();
      $list = array();
      $city_list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $room_number = array();
            if ($rows->roomNumber != '') {
               $exp = explode(';', $rows->roomNumber);
               foreach ($exp as $keyExp => $valueExp) {
                  $exp2 = explode('$', $valueExp);
                  $room_number[$exp2[1]] = $exp2[0];
                  if (!array_key_exists($exp2[1], $city_list)) {
                     $city_list[$exp2[1]] = $exp2[2];
                  }
               }
            }
            $list[] = array(
               'fullname' => $rows->fullname,
               'gender' => $rows->gender,
               'paket_type_name' => $rows->paket_type_name,
               'room_number' => $room_number
            );
         }
      }

      return array('data' => $list, 'city_list' => $city_list);
   }

   # get info kwitansi transaksi visa
   function getInfoKwitansiTransaksiVisa($invoice)
   {
      $this->db->select('vt.id, vt.receiver, vt.invoice, vt.payer, vt.payer_identity, vt.input_date,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', vtd.name, vtd.identity_number, vtd.gender,
                              vtd.birth_place, vtd.birth_date, rt.request_name,
                              c.city_name, vtd.passport_number, vtd.date_issued, vtd.price ) SEPARATOR \';\')
                           FROM visa_transaction_detail AS vtd
                           INNER JOIN request_type AS rt ON vtd.request_id=rt.id
                           INNER JOIN 	mst_city AS c ON vtd.profession_city=c.id
                           WHERE vtd.company_id="' . $this->company_id . '"  AND
                           transaction_visa_id=vt.id) AS transaksi_visa_detail')
         ->from('visa_transaction AS vt')
         ->where('vt.company_id', $this->company_id)
         ->where('vt.invoice', $invoice);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $visa_transaksi_detail = array();
            $total_harga = 0;
            foreach (explode(';', $row->transaksi_visa_detail) as $key => $value) {
               $exp2 = explode('$', $value);
               $visa_transaksi_detail[] = array(
                  'nama_pelanggan' => $exp2[0],
                  'nomor_identitas' => $exp2[1],
                  'jenis_kelamin' => $exp2[2],
                  'tempat_lahir' => $exp2[3],
                  'tanggal_lahir' => $exp2[4],
                  'nama_permohonan' => $exp2[5],
                  'nama_kota' => $exp2[6],
                  'nomor_passport' => $exp2[7],
                  'berlaku_sd' => $exp2[8],
                  'harga_paket' => $exp2[9]
               );
               $total_harga = $total_harga + $exp2[9];
            }

            $list['id'] = $row->id;
            $list['invoice'] = $row->invoice;
            $list['payer'] = $row->payer;
            $list['payer_identity'] = $row->payer_identity;
            $list['receiver'] = $row->receiver;
            $list['tanggal_transaksi'] = $row->input_date;
            $list['total'] = $total_harga;
            $list['detail'] = $visa_transaksi_detail;
         }
      }
      return $list;
   }

   function getInfoKwitansiTransaksiHotel($invoice)
   {
      $this->db->select('ht.id, ht.invoice, ht.receiver, ht.payer, ht.payer_identity, ht.input_date,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', htd.name, htd.identity_number, htd.birth_place,
                                                htd.birth_date, mh.hotel_name, c.city_name,
                                                htd.check_in_date, htd.check_out_date, htd.price ) SEPARATOR \';\')
                              FROM hotel_transaction_detail AS htd
                              INNER JOIN mst_hotel AS mh ON htd.hotel_id=mh.id
                              INNER JOIN 	mst_city AS c ON htd.city_id=c.id
                              WHERE htd.company_id="' . $this->company_id . '"  AND
                              transaction_hotel_id=ht.id) AS transaksi_hotel_detail')
         ->from('hotel_transaction AS ht')
         ->where('ht.company_id', $this->company_id)
         ->where('ht.invoice', $invoice);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $exp = explode(';', $rows->transaksi_hotel_detail);
            $hotel_transaksi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $hotel_transaksi_detail[] = array(
                  'nama_pelanggan' => $exp2[0],
                  'nomor_identitas' => $exp2[1],
                  'tempat_lahir' => $exp2[2],
                  'tanggal_lahir' => $exp2[3],
                  'nama_hotel' => $exp2[4],
                  'nama_kota' => $exp2[5],
                  'check_in' => $this->date_ops->change_date_t3($exp2[6]),
                  'check_out' => $this->date_ops->change_date_t3($exp2[7]),
                  'harga_paket' => $exp2[8]
               );
               $total_harga = $total_harga + $exp2[8];
            }
            $list = array(
               'id' => $rows->id,
               'invoice' => $rows->invoice,
               'receiver' => $rows->receiver,
               'payer' => $rows->payer,
               'payer_identity' => $rows->payer_identity,
               'tanggal_transaksi' => $this->date_ops->change_date_t3($rows->input_date),
               'detail' => $hotel_transaksi_detail,
               'total' => $total_harga
            );
         }
      }
      return $list;
   }

   # get info kwitansi transaksi passport
   function getInfoKwitansiTransaksiPassport($invoice)
   {
      $this->db->select('pt.id, pt.invoice, pt.payer, pt.payer_identity, pt.input_date, pt.receiver,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ptd.name, ptd.identity_number, ptd.birth_place,
                                                ptd.birth_date, c.city_name, ptd.price, ptd.address, ptd.kartu_keluarga_number ) SEPARATOR \';\')
                              FROM passport_transaction_detail AS ptd
                              INNER JOIN 	mst_city AS c ON ptd.city_id=c.id
                              WHERE ptd.company_id="' . $this->company_id . '"  AND
                              transaction_passport_id=pt.id) AS transaksi_passport_detail')
         ->from('passport_transaction AS pt')
         ->where('pt.company_id', $this->company_id)
         ->where('pt.invoice', $invoice);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $exp = explode(';', $rows->transaksi_passport_detail);
            $passport_transaksi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $passport_transaksi_detail[] = array(
                  'nama_pelanggan' => $exp2[0],
                  'nomor_identitas' => $exp2[1],
                  'tempat_lahir' => $exp2[2],
                  'tanggal_lahir' => $exp2[3],
                  'nama_kota' => $exp2[4],
                  'harga_paket' => $exp2[5],
                  'address' => $exp2[6],
                  'kartu_keluarga_number' => $exp2[7]
               );
               $total_harga = $total_harga + $exp2[5];
            }
            $list = array(
               'id' => $rows->id,
               'invoice' => $rows->invoice,
               'receiver' => $rows->receiver,
               'payer' => $rows->payer,
               'payer_identity' => $rows->payer_identity,
               'tanggal_transaksi' => $this->date_ops->change_date_t3($rows->input_date),
               'detail' => $passport_transaksi_detail,
               'total' => $total_harga
            );
         }
      }
      return $list;
   }

   function getInfoKwitansiTransaksiTransport($invoice)
   {

      $this->db->select('tt.id, tt.invoice, tt.payer, tt.payer_identity, tt.input_date, tt.address, tt.receiver,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', mc.car_name, ttd.car_number, ttd.price ) SEPARATOR \';\')
                              FROM transport_transaction_detail AS ttd
                              INNER JOIN 	mst_car AS mc ON ttd.car_id=mc.id
                              WHERE ttd.company_id="' . $this->company_id . '"  AND
                              ttd.transport_transaction_id=tt.id) AS transaksi_transport_detail')
         ->from('transport_transaction AS tt')
         ->where('tt.company_id', $this->company_id)
         ->where('tt.invoice', $invoice);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $exp = explode(';', $rows->transaksi_transport_detail);
            $transport_transaksi_detail = array();
            $total_harga = 0;
            foreach ($exp as $key => $value) {
               $exp2 = explode('$', $value);
               $transport_transaksi_detail[] = array(
                  'jenis_mobil' => $exp2[0],
                  'nomor_plat' => $exp2[1],
                  'harga_paket' => $exp2[2]
               );
               $total_harga = $total_harga + $exp2[2];
            }
            $list = array(
               'id' => $rows->id,
               'invoice' => $rows->invoice,
               'receiver' => $rows->receiver,
               'payer' => $rows->payer,
               'payer_identity' => $rows->payer_identity,
               'address' => $rows->address,
               'tanggal_transaksi' => $this->date_ops->change_date_t3($rows->input_date),
               'detail' => $transport_transaksi_detail,
               'total' => $total_harga
            );
         }
      }
      return $list;
   }

   function getInfoKwitansiPembayaranPaketLA($invoice)
   {
      $this->db->select('plt.register_number, plt.facilities, plt.discount, plt.total_price,
                         plt.departure_date, plt.arrival_date, plt.jamaah,
                         plth.paid, plth.receiver, plth.deposit_name, plth.deposit_hp_number, plth.deposit_address,
                         (SELECT SUM(paid) FROM paket_la_transaction_history
                           WHERE company_id="' . $this->company_id . '" AND status="payment") AS sudahBayar,
                         (SELECT SUM(paid) FROM paket_la_transaction_history
                           WHERE company_id="' . $this->company_id . '" AND status="refund") AS refund')
         ->from('paket_la_transaction_history AS plth')
         ->join('paket_la_transaction AS plt', 'plth.paket_la_transaction_id=plt.id', 'inner')
         ->where('plth.invoice', $invoice)
         ->where('plth.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['register_number'] = $rows->register_number;
            $list['facilities'] = unserialize($rows->facilities)['list_fasilitas'];
            $list['discount'] = $rows->discount;
            $list['total_price'] = $rows->total_price;
            $list['departure_date'] = $this->date_ops->change_date_t3($rows->departure_date);
            $list['arrival_date'] = $this->date_ops->change_date_t3($rows->arrival_date);
            $list['jamaah'] = $rows->jamaah;
            $list['paid'] = $rows->paid;
            $list['receiver'] = $rows->receiver;
            $list['payer'] = $rows->deposit_name;
            $list['payer_identity'] = $rows->deposit_hp_number;
            $list['payer_address'] = $rows->deposit_address;
            $list['sudah_dibayar'] = $rows->sudahBayar - $rows->refund;
         }
      }
      return $list;
   }

   function getInfoKwitansiRefundPaketLA($invoice)
   {
      $this->db->select('plt.register_number, plt.facilities, plt.discount, plt.total_price,
                         plt.departure_date, plt.arrival_date, plt.jamaah,
                         plth.paid, plth.receiver, plth.deposit_name, plth.deposit_hp_number, plth.deposit_address,
                         (SELECT SUM(paid) FROM paket_la_transaction_history
                           WHERE company_id="' . $this->company_id . '" AND status="payment" AND invoice != "' . $invoice . '" ) AS sudahBayar,
                         (SELECT SUM(paid) FROM paket_la_transaction_history
                           WHERE company_id="' . $this->company_id . '" AND status="refund" AND invoice != "' . $invoice . '" ) AS refund')
         ->from('paket_la_transaction_history AS plth')
         ->join('paket_la_transaction AS plt', 'plth.paket_la_transaction_id=plt.id', 'inner')
         ->where('plth.invoice', $invoice)
         ->where('plth.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['register_number'] = $rows->register_number;
            $list['facilities'] = unserialize($rows->facilities)['list_fasilitas'];
            $list['discount'] = $rows->discount;
            $list['total_price'] = $rows->total_price;
            $list['departure_date'] = $this->date_ops->change_date_t3($rows->departure_date);
            $list['arrival_date'] = $this->date_ops->change_date_t3($rows->arrival_date);
            $list['jamaah'] = $rows->jamaah;
            $list['paid'] = $rows->paid;
            $list['receiver'] = $rows->receiver;
            $list['payer'] = $rows->deposit_name;
            $list['payer_identity'] = $rows->deposit_hp_number;
            $list['payer_address'] = $rows->deposit_address;
            $list['sudah_dibayar'] = $rows->sudahBayar - $rows->refund;
         }
      }
      return $list;
   }

   function getInfoKwitansiPembayaranTiket($invoice)
   {
      $this->db->select('tth.id, t.no_register, t.total_transaksi, tth.costumer_name, tth.costumer_identity, tth.biaya, tth.ket, tth.receiver, tth.input_date,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ttd.pax, ttd.code_booking, ma.airlines_name, ttd.departure_date, ttd.costumer_price ) SEPARATOR \';\' )
                           FROM tiket_transaction_detail AS ttd
                           INNER JOIN mst_airlines AS ma ON ttd.airlines_id=ma.id
                           WHERE ttd.company_id="' . $this->company_id . '"
                              AND ttd.tiket_transaction_id=tth.tiket_transaction_id ) AS tiket_detail,
                        (SELECT SUM(biaya)
                           FROM tiket_transaction_history
                           WHERE company_id="' . $this->company_id . '"
                              AND tiket_transaction_id=tth.tiket_transaction_id
                              AND ket="cash") AS sum_cash,
                        (SELECT SUM(biaya)
                           FROM tiket_transaction_history
                           WHERE company_id="' . $this->company_id . '"
                              AND tiket_transaction_id=tth.tiket_transaction_id
                              AND ket="refund") AS sum_refund')
         ->from('tiket_transaction_history AS tth')
         ->join('tiket_transaction AS t', 'tth.tiket_transaction_id=t.id', 'inner')
         ->where('tth.company_id', $this->company_id)
         ->where('tth.invoice', $invoice);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $detail = array();
            if ($rows->tiket_detail != '') {
               foreach (explode(';', $rows->tiket_detail) as $key => $value) {
                  $exp = explode('$', $value);
                  $detail[] = array(
                     'pax' => $exp[0],
                     'code_booking' => $exp[1],
                     'airlines_name' => $exp[2],
                     'departure_date' => $exp[3],
                     'costumer_price' => $exp[4]
                  );
               }
            }
            $list['id'] = $rows->id;
            $list['no_register'] = $rows->no_register;
            $list['total_transaksi'] = $rows->total_transaksi;
            $list['costumer_name'] = $rows->costumer_name;
            $list['costumer_identity'] = $rows->costumer_identity;
            $list['biaya'] = $rows->ket;
            $list['receiver'] = $rows->receiver;
            $list['input_date'] = $rows->input_date;
            $list['total_pembayaran'] = $rows->sum_cash - $rows->sum_refund;
            $list['detail'] = $detail;
         }
      }
      return $list;
   }

   function getInfoKwitansiRefundTiket($invoice)
   {
      $this->db->select('tth.id, t.no_register, t.total_transaksi, tth.costumer_name, tth.costumer_identity, tth.biaya, tth.ket, tth.receiver, tth.input_date,
                        (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ttr.pax, ttr.code_booking, ttr.airlines_name, ttr.departure_date, ttr.costumer_price, ttr.refund ) SEPARATOR \';\' )
                           FROM tiket_transaction_refund AS ttr
                           WHERE ttr.company_id="' . $this->company_id . '"
                              AND ttr.invoice="' . $invoice . '" ) AS tiket_refund,
                        (SELECT SUM(biaya)
                           FROM tiket_transaction_history
                           WHERE company_id="' . $this->company_id . '"
                              AND tiket_transaction_id=tth.tiket_transaction_id
                              AND ket="cash") AS sum_cash,
                        (SELECT SUM(biaya)
                           FROM tiket_transaction_history
                           WHERE company_id="' . $this->company_id . '"
                              AND tiket_transaction_id=tth.tiket_transaction_id
                              AND ket="refund") AS sum_refund')
         ->from('tiket_transaction_history AS tth')
         ->join('tiket_transaction AS t', 'tth.tiket_transaction_id=t.id', 'inner')
         ->where('tth.company_id', $this->company_id)
         ->where('tth.ket', 'refund')
         ->where('tth.invoice', $invoice);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $detail = array();
            if ($rows->tiket_refund != '') {
               foreach (explode(';', $rows->tiket_refund) as $key => $value) {
                  $exp = explode('$', $value);
                  $detail[] = array(
                     'pax' => $exp[0],
                     'code_booking' => $exp[1],
                     'airlines_name' => $exp[2],
                     'departure_date' => $exp[3],
                     'costumer_price' => $exp[4],
                     'refund' => $exp[5]
                  );
               }
            }
            $list['id'] = $rows->id;
            $list['no_register'] = $rows->no_register;
            $list['total_transaksi'] = $rows->total_transaksi;
            $list['costumer_name'] = $rows->costumer_name;
            $list['costumer_identity'] = $rows->costumer_identity;
            $list['biaya'] = $rows->ket;
            $list['receiver'] = $rows->receiver;
            $list['input_date'] = $rows->input_date;
            $list['total_pembayaran'] = $rows->sum_cash - $rows->sum_refund;
            $list['detail'] = $detail;
         }
      }
      return $list;
   }

   function getInfoKwitansiRescheduleTiket($reschedule_id)
   {
      $this->db->select('tt.no_register, rth.invoice, rth.costumer_name, rth.costumer_identity, rth.receiver, rth.input_date,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', old_departure_date, old_travel_price, old_costumer_price, old_code_booking,
                              new_departure_date, new_travel_price, new_costumer_price, new_code_booking) SEPARATOR \';\')
                              FROM reschedule_tiket_history_detail AS rthd
                              WHERE rthd.history_id=rth.id) AS detail_reschedule,
                           (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', ttd.pax, ttd.code_booking, ma.airlines_name, ttd.departure_date, ttd.costumer_price) SEPARATOR \';\')
                              FROM tiket_transaction_detail AS ttd
                              INNER JOIN mst_airlines AS ma ON ttd.airlines_id=ma.id
                              WHERE ttd.tiket_transaction_id=rth.tiket_transaction_id) AS detail_new_schedule,
                           (SELECT SUM(biaya) FROM tiket_transaction_history
                              WHERE tiket_transaction_id=rth.tiket_transaction_id AND
                                    ket="cash") biaya_cash,
                           (SELECT SUM(biaya) FROM tiket_transaction_history
                              WHERE tiket_transaction_id=rth.tiket_transaction_id AND
                                    ket="refund") biaya_refund,            ')
         ->from('reschedule_tiket_history AS rth')
         ->join('tiket_transaction AS tt', 'rth.tiket_transaction_id=tt.id', 'inner')
         ->where('rth.company_id', $this->company_id)
         ->where('rth.id', $reschedule_id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $list['invoice'] = $rows->invoice;
            $list['tanggal_transaksi'] = $rows->input_date;
            $list['no_register'] = $rows->no_register;
            $list['costumer_name'] = $rows->costumer_name;
            $list['costumer_identity'] = $rows->costumer_identity;
            $list['receiver'] = $rows->receiver;
            # detail reschedule
            $detail_reschedule = array();
            if ($rows->detail_reschedule != '') {
               foreach (explode(';', $rows->detail_reschedule) as $key => $value) {
                  $exp = explode('$', $value);
                  $detail_reschedule[] = array(
                     'old_departure_date' => $exp[0],
                     'old_travel_price' => $exp[1],
                     'old_costumer_price' => $exp[2],
                     'old_code_booking' => $exp[3],
                     'new_departure_date' => $exp[4],
                     'new_travel_price' => $exp[5],
                     'new_costumer_price' => $exp[6],
                     'new_code_booking' => $exp[7]
                  );
               }
            }
            $list['detail_reschedule'] = $detail_reschedule;
            # detail_new_schedule
            $detail_new_schedule = array();
            if ($rows->detail_new_schedule != '') {
               foreach (explode(';', $rows->detail_new_schedule) as $key => $value) {
                  $exp = explode('$', $value);
                  $detail_new_schedule[] = array(
                     'pax' => $exp[0],
                     'code_booking' => $exp[1],
                     'airlines_name' => $exp[2],
                     'departure_date' => $exp[3],
                     'costumer_price' => $exp[4]
                  );
               }
            }
            $list['detail_new_schedule'] = $detail_new_schedule;
            $list['total_pembayaran'] = $rows->biaya_cash - $rows->biaya_refund;
         }
      }
      return $list;
   }

   # get info kwitansi payment fee
   function getInfoKwitansiPaymentFee($invoice)
   {
      $this->db->select('fee.invoice, fee.biaya, fee.applicant_name, fee.applicant_identity, fee.date_transaction, fee.receiver, p.fullname, p.identity_number')
         ->from('fee_keagenan_payment AS fee')
         ->join('agen AS a', 'fee.agen_id=a.id', 'inner')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner')
         ->where('fee.company_id', $this->company_id)
         ->where('fee.invoice', $invoice);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         $rows = $q->row();
         $list = (array) $rows;
      }
      $list['city'] = $this->get_city();
      return $list;
   }

   function get_city()
   {
      $this->db->select('city')
         ->from('company')
         ->where('id', $this->company_id);
      $q = $this->db->get();
      $city = '';
      if ($q->num_rows() > 0) {
         $city = $q->row()->city;
      }
      return $city;
   }

   # get info kwitansi
   function getInfoKwitansiDepositSaldo($deposit_id){
      $this->db->select('dt.personal_id, dt.id, dt.nomor_transaction, p.fullname, p.identity_number, p.nomor_whatsapp, dt.info, dt.transaction_requirement, dt.input_date, dt.approver, dt.debet, dt.kredit')
         ->from('deposit_transaction AS dt')
         ->join('personal AS p', 'dt.personal_id=p.personal_id', 'inner')
         ->where('dt.id', $deposit_id)
         ->where('dt.company_id', $this->company_id);
      $q = $this->db->get();
      $list =array();
      if( $q->num_rows() > 0 ){
         foreach ($q->result() as $rows) {
            if( $rows->transaction_requirement == 'deposit' || $rows->transaction_requirement == 'paket_deposit' ) {
               $saldo = $rows->debet;
            }else{
               $saldo = $rows->kredit;
            }
            $list = array('id' => $rows->id,
                          'nomor_transaction' => $rows->nomor_transaction,
                          'fullname' => $rows->fullname,
                          'identity_number' => $rows->identity_number,
                          'nomor_whatsapp' => $rows->nomor_whatsapp,
                          'info' => $rows->info,
                          'keperluan' => $rows->transaction_requirement,
                          'date_transaction' => $rows->input_date,
                          'penerima' => $rows->approver,
                          'saldo' => $saldo,
                          'last_saldo' => $this->lastSaldo($rows->id, $rows->personal_id, $rows->transaction_requirement));
         }
      }
      return $list;
   }

   function lastSaldo($deposit_id, $personal_id, $keperluan){
      if( $keperluan == 'paket_deposit'){
         $need = array('paket_deposit', 'paket_payment');
      }else{
         $need = array('deposit', 'transaction');
      }
      $this->db->select('id, debet, kredit, transaction_requirement')
         ->from('deposit_transaction')
         ->where('company_id', $this->company_id)
         ->where('personal_id', $personal_id)
         ->where_in('transaction_requirement', $need)
         ->order_by('input_date', 'asc');
      $q = $this->db->get();
      $saldo = 0;
      if( $q->num_rows() > 0 ) {
         foreach ($q->result() as $rows) {
            if( $rows->id == $deposit_id){
               break;
            }else{
               if( $rows->transaction_requirement == 'paket_deposit' || $rows->transaction_requirement == 'deposit'){
                  $saldo = $saldo + $rows->debet;
               }elseif ( $rows->transaction_requirement == 'paket_paymnet' || $rows->transaction_requirement == 'transaction' ) {
                  $saldo = $saldo - $rows->kredit;
               }
            }
         }
      }
      return $saldo;
   }


}