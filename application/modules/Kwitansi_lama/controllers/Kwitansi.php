<?php

/**
 *  -----------------------
 *	Users Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Kwitansi extends CI_Controller
{
   private $company_code;
   private $setting = array();
   private $tempVar;

   public function __construct()
   {
      parent::__construct();
      # checking is not Login
      $this->auth_library->Is_not_login();
      # model kwitasi
      $this->load->model('Model_kwitansi', 'model_kwitansi');
      $this->load->model('ModelRead/ModelAdmin', 'model_admin');

      # run setting value
      $this->_getSettingValue();
      $this->tempVar = array();
   }

   function _getSettingValue()
   {
      $this->setting = $this->model_kwitansi->getSettingValue();
   }

   function index()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      if ($sesi['type'] == 'paket') {
         if ($sesi['metode_pembayaran'] ==  0) : if (isset($sesi['ket']) and $sesi['ket'] == 'refund') : $this->_paketTransactionRefund();
            else : $this->_paketTransactionCash();
            endif;
         else : $this->_paketTransactionCicilan();
         endif;
      } elseif ($sesi['type'] == 'cetak_handover_paket') {
         if ($sesi['status'] == 'diambil') : $this->_printHandoverTaken();
         elseif ($sesi['status'] == 'dikembalikan') : $this->_printHandoverReturn();
         endif;
      } elseif ($sesi['type'] == 'cetak_handover_fasilitas') {
         $this->_printHandoverFasilitas();
      } elseif ($sesi['type'] == 'cetak_riwayat_paket') {
         if ($sesi['metode_pembayaran'] ==  0) : $this->_paketRiwayatCash();
         else : $this->_paketRiwayatCicilan();
         endif;
      } elseif ($sesi['type'] == 'cetak_skema_cicilan') {
         $this->_skemaCicilan();
      } elseif ($sesi['type'] == 'invoice_pindah_paket') {
         $this->_invoicePindahPaket();
      } elseif ($sesi['type'] == 'cetak_data_jamaah') {
         $this->_cetakDataJamaah();
      } elseif ($sesi['type'] == 'download_absensi') {
         $this->_downloadAbsesiJamaah();
      } elseif ($sesi['type'] == 'cetak_daftar_kamar_jamaah') {
         $this->_cetakDaftarKamarJamaah();
      } elseif ($sesi['type'] == 'cetak_kwitansi_visa') {
         $this->_cetakKwitansiTransaksiVisa();
      } elseif ($sesi['type'] == 'cetak_kwitansi_hotel') {
         $this->_cetakKwitansiTransaksiHotel();
      } elseif ($sesi['type'] == 'cetak_kwitansi_passport') {
         $this->_cetakKwitansiTransaksiPassport();
      } elseif ($sesi['type'] == 'cetak_kwitansi_transport') {
         $this->_cetakKwitansiTransaksiTransport();
      } elseif ($sesi['type'] == 'pembayaran_paket_la') {
         $this->_cetakKwitansiPembayaranPaketLa();
      } elseif ($sesi['type'] == 'refund_paket_la') {
         $this->_cetakKwitansiRefundPaketLa();
      } elseif ($sesi['type'] == 'trans_tiket') {
         $this->_cetakKwitansiPembayaranTiket();
      } elseif ($sesi['type'] == 'refund_trans_tiket') {
         $this->_cetakKwitansiRefundTiket();
      } elseif ($sesi['type'] == 'reschedule') {
         $this->_cetakKwitansiRescheduleTiket();
      } elseif ($sesi['type'] == 'payment_fee') {
         $this->_cetakKwitansiPaymentFee();
      } elseif ($sesi['type'] == 'cetak_kwitansi_deposit_saldo') {
        $this->_cetakKwitansiDepositSaldo();
     }
   }

   function _cetakKwitansiDepositSaldo(){
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiDepositSaldo($sesi['deposit_id']);
      $html  = $this->Header();
      $html .= $this->TitleDepositPayment();
      $html .= $this->OrderPaymentDeposit();
      $html .= $this->_contentTransaksiDepositPayment();
      $this->Templating($html);
   }

   function _cetakKwitansiPaymentFee()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiPaymentFee($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitlePaymentFeeMember('KWITANSI PEMBAYARAN FEE AGEN', $this->tempVar['invoice']);
      //$html .= $this->OrderTransaksiPaymentFee();
      $html .= $this->_contentTransaksiPaymentFee();

      $this->Templating($html);
   }

   function _cetakKwitansiRescheduleTiket()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiRescheduleTiket($sesi['reschedule_id']);
      $html  = $this->Header();
      $html .= $this->TitleTiket('KWITANSI RESCHEDULE TIKET', $this->tempVar['no_register'], $this->tempVar['invoice']);
      $html .= $this->OrderTransaksiRescheduleTiket();
      $html .= $this->_contentTransaksiRescheduleTiket();
      $this->Templating($html);
   }

   function _cetakKwitansiRefundTiket()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiRefundTiket($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleTiket('KWITANSI REFUND TIKET', $this->tempVar['no_register'], $sesi['invoice']);
      $html .= $this->OrderTransaksiPembayaranTiketRefund();
      $html .= $this->_contentTransaksiPembayaranTiketRefund();
      $this->Templating($html);
   }

   function _cetakKwitansiPembayaranTiket()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiPembayaranTiket($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleTiket('KWITANSI PEMBAYARAN TIKET', $sesi['no_register'],  $sesi['invoice']);
      $html .= $this->OrderTransaksiPembayaranTiket();
      $html .= $this->_contentTransaksiPembayaranTiket();
      $this->Templating($html);
   }

   function _cetakKwitansiRefundPaketLa()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiRefundPaketLA($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleLeft('KWITANSI REFUND PAKET LA', $sesi['invoice']);
      $html .= $this->OrderTransaksiRefundPaketLA();
      $html .= $this->_contentTransaksiRefundPaketLA();
      $this->Templating($html);
   }

   function _cetakKwitansiPembayaranPaketLa()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiPembayaranPaketLA($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleLeft('KWITANSI PEMBAYARAN PAKET LA', $sesi['invoice']);
      $html .= $this->OrderTransaksiPembayaranPaketLA();
      $html .= $this->_contentTransaksiPembayaranPaketLA();
      $this->Templating($html);
   }

   function _cetakKwitansiTransaksiTransport()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiTransaksiTransport($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleLeft('DETAIL TRANSAKSI TRANSPORT', $sesi['invoice']);
      $html .= $this->OrderTransaksiTransport();
      $html .= $this->_contentTransaksiTransport();
      $this->Templating($html);
   }

   function _cetakKwitansiTransaksiPassport()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiTransaksiPassport($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleLeft('DETAIL TRANSAKSI PASSPORT', $sesi['invoice']);
      $html .= $this->OrderTransaksiPassport();
      $html .= $this->_contentTransaksiPassport();
      $this->Templating($html);
   }

   function _cetakKwitansiTransaksiHotel()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiTransaksiHotel($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleLeft('DETAIL TRANSAKSI HOTEL', $sesi['invoice']);
      $html .= $this->OrderTransaksiHotel();
      $html .= $this->_contentTransaksiHotel();
      $this->Templating($html);
   }

   function _cetakKwitansiTransaksiVisa()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiTransaksiVisa($sesi['invoice']);
      $html  = $this->Header();
      $html .= $this->TitleLeft('DETAIL TRANSAKSI VISA', $sesi['invoice']);
      $html .= $this->OrderTransaksiVisa();
      $html .= $this->_contentTransaksiVisa();
      $this->Templating($html);
   }

   function _cetakDaftarKamarJamaah()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoCetakDaftarKamar($sesi['paket_id']);
      $html  = $this->Header();
      $html .= $this->TitleMiddle('DAFTAR JAMAAH SETIAP KAMAR');
      $html .= $this->_contentCetakDaftarKamar();
      $this->Templating($html);
   }

   function _downloadAbsesiJamaah()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar['data'] = $this->model_kwitansi->getDownloadAbsensiJamaah($sesi['paket_id']);
      $this->tempVar['jabatan_petugas'] = $sesi['jabatan_petugas'];
      $this->tempVar['nama_petugas'] = $sesi['nama_petugas'];
      $html  = $this->Header();
      $html .= $this->TitleAbsensi('ABSENSI JAMAAH');
      $html .= $this->_ContentDownloadAbsensiJamaah();
      $this->Templating($html);
   }

   function _cetakDataJamaah()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoDataJamaah($sesi['jamaah_id'], $sesi['paket_transaction_id']);
      $this->tempVar['jabatan_petugas'] = $sesi['jabatan_petugas'];
      $this->tempVar['nama_petugas'] = $sesi['nama_petugas'];
      $html  = $this->Header();
      $html .= $this->TitleMiddle('FORMULIR PENDAFTARAN UMRAH');
      $html .= $this->_ContentDataJamaah();
      $this->Templating($html);
   }


   function _invoicePindahPaket()
   {
      $sesi = $this->session->userdata('cetak_invoice');

      $this->tempVar = $this->model_kwitansi->getInfoKwitansiPindahPaket($sesi['pindahPaketID']);

      $html = $this->Header();
      $html .= $this->TitlePindahPaket('Pindah Paket');
      $html  .= $this->OrderPindahPaket();
      $html  .= $this->_ContentPindahPaket();

      $this->Templating($html);
   }

   function _printHandoverFasilitas()
   {
      $sesi = $this->session->userdata('cetak_invoice');

      $this->tempVar = $this->model_kwitansi->getInfoKwitansiFasilitas($sesi['paket_transaction_id'], $sesi['jamaah_id'], $sesi['invoice']);

      $html   = $this->Header();
      $html  .= $this->TitleHandoverReturned('Bukti Serah Terima Fasilitas');
      $html  .= $this->OrderFasilitas();
      $html  .= $this->_ContentFasilitas();

      $this->Templating($html);
   }

   function _printHandoverReturn()
   {
      $sesi = $this->session->userdata('cetak_invoice');

      $this->tempVar = $this->model_kwitansi->getInfoKwitansiReturnedBarang($sesi['paket_transaction_id'], $sesi['jamaah_id'], $sesi['invoice_returned']);

      $html   = $this->Header();
      $html  .= $this->TitleHandoverReturned('Bukti Pengembalian Barang');
      $html  .= $this->OrderReturned();
      $html  .= $this->_ContentReturned();

      $this->Templating($html);
   }

   function _printHandoverTaken()
   {
      $sesi = $this->session->userdata('cetak_invoice');
      $this->tempVar = $this->model_kwitansi->getInfoKwitansiHandoverBarang($sesi['paket_transaction_id'], $sesi['jamaah_id'], $sesi['invoice_handover']);

      $html   = $this->Header();
      $html  .= $this->TitleHandoverReturned('Bukti Serah Terima Barang');
      $html  .= $this->OrderHandover();
      $html  .= $this->_ContentHandover();

      $this->Templating($html);
   }

   // cetak kwitansi paket
   function _paketTransactionCash()
   {
      $this->tempVar = $this->model_kwitansi->getInvoiceContentTransactionCash();

      $html   = $this->Header();
      $html  .= $this->RegisterWithTitle('Kwitansi Cash');
      $html  .= $this->Order();
      $html  .= $this->ContentPaketTransaksi();
      $html  .= $this->Note();

      $this->Templating($html);
   }

   function _paketTransactionRefund()
   {

      $this->tempVar = $this->model_admin->getInvoiceContentTransactionRefund();

      $html   = $this->Header();
      $html  .= $this->RegisterWithTitle('Kwitansi Refund');
      $html  .= $this->Order();
      $html  .= $this->ContentTransaksiRefund();

      $this->Templating($html);
   }

   function _paketTransactionCicilan()
   {
      $this->tempVar = $this->model_kwitansi->getInvoiceContentTransactionCicilan();

      $html   = $this->Header();
      $html  .= $this->Register();
      $html  .= $this->OrderCicilan();
      $html  .= $this->ContentPaketCicilan();
      $html  .= $this->Note();

      $this->Templating($html);
   }

   function _paketRiwayatCicilan()
   {
      $this->tempVar = $this->model_kwitansi->getRiwayatCicilan();

      $html   = $this->Header();
      $html  .= $this->RegisterWithTitle('Riwayat Pembayaran Cicilan');
      $html  .= $this->ContentRiwayatCicilan();
      $html  .= $this->RiwayatPembayaranCicilan();
      $html  .= $this->Note();

      $this->Templating($html);
   }

   function _skemaCicilan()
   {
      $this->tempVar = $this->model_kwitansi->getSkemaCicilan();

      $html   = $this->Header();
      $html  .= $this->RegisterWithTitle('Skema Pembayaran Cicilan');
      $html  .= $this->ContentSkemaCicilan();
      $html  .= $this->Note();

      $this->Templating($html);
   }


   function _contentTransaksiDepositPayment(){
      $html = '<div class="row mt-4">
                  <p class="justify-content-left container-fluid text-left mb-2" style="color: #848484 !important;">
                     <b>Detail Transaksi</b>
                  </p>
                  <div class="col-12 px-0">
                     <style>

                        .table tbody td {
                            font-size: 12px !important;
                            border:1px solid #dee2e6;
                        }
                        .table tfoot td {
                            font-size: 12px !important;
                            border:1px solid #dee2e6;
                        }
                        .table thead th {
                            font-size: 12px !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th style="width:20%">Waktu Transaksi</th>
                              <th style="width:15%">Keperluan</th>
                              <th style="width:20%">Penerima</th>
                              <th style="width:25%">Info</th>
                              <th style="width:20%">Jumlah</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td >' . $this->tempVar['date_transaction'] . '</td>
                              <td >' . $this->tempVar['keperluan'] . '</td>
                              <td >' . $this->tempVar['penerima'] . '</td>
                              <td >' . $this->tempVar['info'] . '</td>
                              <td class="text-right">Rp ' . number_format($this->tempVar['saldo']) . '</td>
                           </tr>
                        </tbody>
                        <tfoot>
                           <tr>
                              <td colspan="4"><b>Total Pembayaran Deposit</b></td>
                              <td class="text-right">Rp ' . number_format($this->tempVar['saldo']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4"><b>Total Deposit Sebelumnya</b></td>
                              <td class="text-right">Rp ' . number_format($this->tempVar['last_saldo']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4"><b>Total Deposit Sekarang</b></td>
                              <td class="text-right">Rp ' . number_format(($this->tempVar['last_saldo'] + $this->tempVar['saldo'])) . '</td>
                           </tr>
                        </tfoot>
                     </table>
                  </div>
                  <div class="w-100 mb-3"></div>
                  <div class="col-3">
                     <p class="justify-content-left container-fluid text-center mb-2" style="color: #848484 !important;">
                        <b>Member/Jamaah</b>
                     </p>
                  </div>
                  <div class="col-3">
                     <p class="justify-content-left container-fluid text-center mb-2" style="color: #848484 !important;">
                        <b>Penerima</b>
                     </p>
                  </div>
                  <div class="w-100 my-4"></div>
                  <div class="col-3">
                     <p class="justify-content-left container-fluid text-center mb-2" style="color: #848484 !important;">
                        <b>(_______________)</b>
                     </p>
                  </div>
                  <div class="col-3">
                     <p class="justify-content-left container-fluid text-center mb-2" style="color: #848484 !important;">
                        <b>(_______________)</b>
                     </p>
                  </div>
               </div>
            </div>
         </div>';
      return $html;

   }






   // <tbody>
   //    <tr>
   //       <td style="width:24%;" class="text-left py-0">NO INVOICE</td>
   //       <td style="width:1%;" class="p-0">:</td>
   //       <td style="width:75%;" class="text-left py-0">' . $this->tempVar['invoice'] . '</td>
   //    </tr>
   //    <tr>
   //       <td class="text-left py-0">NAMA AGEN</td>
   //       <td class="p-0">:</td>
   //       <td class="text-left py-0">' . $this->tempVar['fullname'] . '</td>
   //    </tr>
   //    <tr>
   //       <td class="text-left py-0">NOMOR IDENTITAS AGEN</td>
   //       <td class="p-0">:</td>
   //       <td class="text-left py-0">' . $this->tempVar['identity_number'] . '</td>
   //    </tr>
   //    <tr>
   //       <td class="text-left py-0">TELAH DITERIMA DARI</td>
   //       <td class="p-0">:</td>
   //       <td class="text-left py-0">' . $this->tempVar['receiver'] . '</td>
   //    </tr>
   //    <tr>
   //       <td class="text-left py-0">UANG SEBESAR</td>
   //       <td class="p-0">:</td>
   //       <td class="text-left py-0">Rp ' . number_format($this->tempVar['biaya']) . '</td>
   //    </tr>
   //    <tr>
   //       <td class="text-left py-0">TERBILANG</td>
   //       <td class="p-0">:</td>
   //       <td class="text-left py-0">' . ucwords($this->text_ops->terbilang($this->tempVar['biaya'])) . ' Rupiah</td>
   //    </tr>
   //    <tr>
   //       <td class="text-left py-0">UNTUK PEMBAYARAN</td>
   //       <td class="p-0">:</td>
   //       <td class="text-left py-0">Fee Keagenan</td>
   //    </tr>
   // </tbody>

   // <div class="row pt-5">
   //    <div class="col-8">
   //    </div>
   //    <div class="col-4 text-center">
   //       ' . $this->tempVar['city'] . ', ' . $this->date_ops->change_date(date('Y-m-d')) . '
   //    </div>
   //    <div class="col-4 text-center">
   //       Paraf Petugas
   //    </div>
   //    <div class="col-4">
   //    </div>
   //    <div class="col-4 text-center">
   //       Penerima
   //    </div>
   // </div>
   // <div class="row pt-5">
   //    <div class="col-4 text-center">
   //       ( ' . $this->tempVar['receiver'] . ' )
   //    </div>
   //    <div class="col-4">
   //    </div>
   //    <div class="col-4 text-center">
   //       ' . $this->tempVar['applicant_name'] . ' <br>
   //       ( ' . $this->tempVar['applicant_identity'] . ' )
   //    </div>
   // </div>

   function _contentTransaksiPaymentFee()
   {
      $html = '<div class="row mt-2">
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .table tbody td {
                            font-size: 15px !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <tbody>
                           <tr>
                              <td style="width:24%;" class="text-left py-0">NO INVOICE</td>
                              <td style="width:1%;" class="p-0">:</td>
                              <td style="width:75%;" class="text-left py-0">' . $this->tempVar['invoice'] . '</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0">NAMA AGEN</td>
                              <td class="p-0">:</td>
                              <td class="text-left py-0">' . $this->tempVar['fullname'] . '</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0">NOMOR IDENTITAS AGEN</td>
                              <td class="p-0">:</td>
                              <td class="text-left py-0">' . $this->tempVar['identity_number'] . '</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0">TELAH DITERIMA DARI</td>
                              <td class="p-0">:</td>
                              <td class="text-left py-0">' . $this->tempVar['receiver'] . '</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0">UANG SEBESAR</td>
                              <td class="p-0">:</td>
                              <td class="text-left py-0">Rp ' . number_format($this->tempVar['biaya']) . '</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0">TERBILANG</td>
                              <td class="p-0">:</td>
                              <td class="text-left py-0">' . ucwords($this->text_ops->terbilang($this->tempVar['biaya'])) . ' Rupiah</td>
                           </tr>
                           <tr>
                              <td class="text-left py-0">UNTUK PEMBAYARAN</td>
                              <td class="p-0">:</td>
                              <td class="text-left py-0">Fee Keagenan</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div class="w-100"></div>
               </div>

               <div class="row pt-5">
                  <div class="col-8">
                  </div>
                  <div class="col-4 text-center">
                     ' . $this->tempVar['city'] . ', ' . $this->date_ops->change_date(date('Y-m-d')) . '
                  </div>
                  <div class="col-4 text-center">
                     Paraf Petugas
                  </div>
                  <div class="col-4">
                  </div>
                  <div class="col-4 text-center">
                     Penerima
                  </div>
               </div>
               <div class="row pt-5">
                  <div class="col-4 text-center">
                     ( ' . $this->tempVar['receiver'] . ' )
                  </div>
                  <div class="col-4">
                  </div>
                  <div class="col-4 text-center">
                     ' . $this->tempVar['applicant_name'] . ' <br>
                     ( ' . $this->tempVar['applicant_identity'] . ' )
                  </div>
               </div>
            </div>
         </div>';
      return $html;
   }



   function _contentTransaksiRescheduleTiket()
   {
      $html = '<div class="row mt-2">
                     <div class="col-12 px-0">
                        <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                           DETAIL RESCHEDULE :
                        </span>
                     </div>
                     <div class="col-10">
                        <style>
                           .table td {
                              border: none !important;
                           }
                           .table tbody td {
                               font-size: 15px !important;
                           }
                           .values::before {
                             content: ": ";
                           }
                           .border-t1{
                              border: 1px solid black;
                           }
                           .box-checking{
                              height: 14px;
                              width:20px;
                              border: 1px solid #9a9a9a;
                              border-radius: 3px;
                              display:inline-block;
                           }
                        </style>
                        <table class="table table-hover ">
                           <thead>
                              <tr>
                                 <th scope="col" style="width:35%;font-weight: normal;">KODE BOOKING</th>
                                 <th scope="col" style="width:35%;font-weight: normal;">TGL BERANGKAT</th>
                                 <th scope="col" style="width:30%;font-weight: normal;">HARGA</th>
                              </tr>
                           <thead>
                           <tbody>';
      foreach ($this->tempVar['detail_reschedule'] as $key => $value) {
         $html .= '<tr>
                                 <td class="text-left">LAMA : ' . $value['old_code_booking'] . '</td>
                                 <td class="text-left">LAMA : ' . $this->date_ops->change_date($value['old_departure_date']) . '</td>
                                 <td class="text-left">LAMA : Rp ' . number_format($value['old_costumer_price']) . '</td>
                              </tr>
                              <tr>
                                 <td class="text-left">BARU : ' . $value['new_code_booking'] . '</td>
                                 <td class="text-left">BARU : ' . $this->date_ops->change_date($value['new_departure_date']) . '</td>
                                 <td class="text-left">BARU : Rp ' . number_format($value['new_costumer_price']) . '</td>
                              </tr>';
      }
      $html .=   '</tbody>
                        </table>
                     </div>
                     <div class="w-100"></div>
                  </div>
                  <div class="row mt-2">
                     <div class="col-12 px-0">
                        <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                           NEW SCHEDULE :
                        </span>
                     </div>
                     <div class="col-12">
                        <style>
                           .table td {
                              border: none !important;
                           }
                           .table tbody td {
                               font-size: 15px !important;
                           }
                           .values::before {
                             content: ": ";
                           }
                           .border-t1{
                              border: 1px solid black;
                           }
                           .box-checking{
                              height: 14px;
                              width:20px;
                              border: 1px solid #9a9a9a;
                              border-radius: 3px;
                              display:inline-block;
                           }
                        </style>
                        <table class="table table-hover ">
                           <thead>
                              <tr>
                                 <th scope="col" style="width:5%;font-weight: normal;">PAX</th>
                                 <th scope="col" style="width:20%;font-weight: normal;">KODE BOOKING</th>
                                 <th scope="col" style="width:20%;font-weight: normal;">NAMA MASKAPAI</th>
                                 <th scope="col" style="width:20%;font-weight: normal;">TGL BERANGKAT</th>
                                 <th scope="col" style="width:15%;font-weight: normal;">HARGA</th>
                                 <th scope="col" style="width:20%;font-weight: normal;">SUBTOTAL</th>
                              </tr>
                           <thead>
                           <tbody>';
      $total = 0;
      foreach ($this->tempVar['detail_new_schedule'] as $key => $value) {
         $html .= '<tr>
                                 <td >' . $value['pax'] . '</td>
                                 <td >' . $value['code_booking'] . '</td>
                                 <td >' . $value['airlines_name'] . '</td>
                                 <td >' . $this->date_ops->change_date($value['departure_date']) . '</td>
                                 <td class="text-right">Rp ' . number_format($value['costumer_price']) . '</td>
                                 <td class="text-right">Rp ' . number_format($value['pax'] * $value['costumer_price']) . '</td>
                              </tr>';
         $total = $total + ($value['pax'] * $value['costumer_price']);
      }
      $html .=   '</tbody>
                           <tfoot>
                              <tr>
                                 <td colspan="5" class="text-right">TOTAL HARGA</td>
                                 <td>Rp ' . number_format($total) . '</td>
                              </tr>
                              <tr>
                                 <td colspan="5" class="text-right">TOTAL PEMBAYARAN</td>
                                 <td>Rp ' . number_format($this->tempVar['total_pembayaran']) . '</td>
                              </tr>
                              <tr>
                                 <td colspan="5" class="text-right">SISA PEMBAYARAN</td>
                                 <td>Rp ' . number_format($total - $this->tempVar['total_pembayaran']) . '</td>
                              </tr>
                           </tfoot>
                        </table>
                     </div>
                     <div class="w-100"></div>
                  </div>
                  <div class="row pt-5">
                     <div class="col-4 text-center">
                        PELANGGAN
                     </div>
                     <div class="col-4">
                     </div>
                     <div class="col-4 text-center">
                        PETUGAS
                     </div>
                  </div>
                  <div class="row pt-5">
                     <div class="col-4 text-center">
                        (' . $this->tempVar['costumer_name'] . ')
                     </div>
                     <div class="col-4">
                     </div>
                     <div class="col-4 text-center">
                        (' . $this->tempVar['receiver'] . ')
                     </div>
                  </div>
               </div>
            </div>';
      return $html;
   }

   function _contentTransaksiPembayaranTiketRefund()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL REFUND TIKET :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .table tbody td {
                            font-size: 15px !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:15%;font-weight: normal;">KODE<br>BOOKING</th>
                              <th scope="col" style="width:5%;font-weight: normal;">PAX</th>
                              <th scope="col" style="width:20%;font-weight: normal;">MASKAPAI</th>
                              <th scope="col" style="width:15%;font-weight: normal;">TGL BERANGKAT</th>
                              <th scope="col" style="width:15%;font-weight: normal;">HARGA</th>
                              <th scope="col" style="width:15%;font-weight: normal;">TOTAL HARGA</th>
                              <th scope="col" style="width:15%;font-weight: normal;">REFUND</th>
                           </tr>
                        <thead>
                        <tbody>';
      $total = 0;
      $total_refund = 0;
      foreach ($this->tempVar['detail'] as $key => $value) {
         $html .= '<tr>
                           <td>' . $value['code_booking'] . '</td>
                           <td>' . $value['pax'] . '</td>
                           <td>' . $value['airlines_name'] . '</td>
                           <td>' . $this->date_ops->change_date($value['departure_date']) . '</td>
                           <td class="text-right"> Rp ' . number_format($value['costumer_price']) . '</td>
                           <td class="text-right"> Rp ' . number_format($value['pax'] * $value['costumer_price']) . '</td>
                           <td class="text-right"> Rp ' . number_format($value['refund']) . '</td>
                         </tr>';
         $total = $total + ($value['pax'] * $value['costumer_price']);
         $total_refund = $total_refund + $value['refund'];
      }

      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="6" class="text-right">TOTAL HARGA</td>
                              <td>Rp ' . number_format($total) . '</td>
                           </tr>
                           <tr>
                              <td colspan="6" class="text-right">TOTAL REFUND</td>
                              <td>Rp ' . number_format($total_refund) . '</td>
                           </tr>
                           <tr>
                              <td colspan="6" class="text-right">TOTAL TIDAK DAPAT DIREFUND</td>
                              <td>Rp ' . number_format($total - $total_refund) . '</td>
                           </tr>
                        </tfoot>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['costumer_name'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _contentTransaksiPembayaranTiket()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL PEMBAYARAN TIKET :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:15%;font-weight: normal;">KODE BOOKING</th>
                              <th scope="col" style="width:5%;font-weight: normal;">PAX</th>
                              <th scope="col" style="width:20%;font-weight: normal;">MASKAPAI</th>
                              <th scope="col" style="width:20%;font-weight: normal;">TGL BERANGKAT</th>
                              <th scope="col" style="width:20%;font-weight: normal;">HARGA</th>
                              <th scope="col" style="width:20%;font-weight: normal;">TOTAL HARGA</th>
                           </tr>
                        <thead>
                        <tbody>';

      $total = 0;
      foreach ($this->tempVar['detail'] as $key => $value) {
         $html .= '<tr>
                           <td>' . $value['code_booking'] . '</td>
                           <td>' . $value['pax'] . '</td>
                           <td>' . $value['airlines_name'] . '</td>
                           <td>' . $this->date_ops->change_date($value['departure_date']) . '</td>
                           <td class="text-right"> Rp ' . number_format($value['costumer_price']) . '</td>
                           <td class="text-right"> Rp ' . number_format($value['pax'] * $value['costumer_price']) . '</td>
                         </tr>';
         $total = $total + ($value['pax'] * $value['costumer_price']);
      }

      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="5" class="text-right">TOTAL HARGA</td>
                              <td>Rp ' . number_format($total) . '</td>
                           </tr>
                           <tr>
                              <td colspan="5" class="text-right">TOTAL PEMBAYARAN</td>
                              <td>Rp ' . number_format($this->tempVar['total_pembayaran']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="5" class="text-right">SISA PEMBAYARAN</td>
                              <td>Rp ' . number_format($total - $this->tempVar['total_pembayaran']) . '</td>
                           </tr>
                        </tfoot>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['costumer_name'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }


   // <tbody>';
   // $no = 1;
   // $total = 0;
   // foreach ($this->tempVar['facilities'] as $key => $value) {
   // $html .='<tr>
   //          <td>'.$no.'</td>
   //          <td>'.$value['name'].'</td>
   //          <td>'.$value['pax'].'</td>
   //          <td>Rp '.number_format($value['harga']).'</td>
   //          <td class="text-right">
   //             Rp. '. number_format($value['pax'] * $value['harga']) .'
   //          </td>
   //       </tr>';
   // $total = $total + ( $value['pax'] * $value['harga'] );
   // $no++;
   // }
   // $real_total = $total * $this->tempVar['jamaah'];
   //
   // $now_sudah_dibayar = ($real_total - $this->tempVar['discount']) - ($this->tempVar['sudah_dibayar'] - $this->tempVar['paid']);
   //
   // $html .=   '</tbody>

   function _contentTransaksiRefundPaketLA()
   {

      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL INFO TRANSAKSI PEMBAYARAN PAKET LA :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:5%;font-weight: normal;">NO</th>
                              <th scope="col" style="width:50%;font-weight: normal;">KETERANGAN</th>
                              <th scope="col" style="width:5%;font-weight: normal;">QT</th>
                              <th scope="col" style="width:20%;font-weight: normal;">AMOUNT</th>
                              <th scope="col" style="width:20%;font-weight: normal;">TOTAL AMOUNT</th>
                           </tr>
                        <thead>
                        <tbody>';
      $no = 1;
      $total = 0;
      foreach ($this->tempVar['facilities'] as $key => $value) {
         $html .= '<tr>
                                 <td>' . $no . '</td>
                                 <td>' . $value['name'] . '</td>
                                 <td>' . $value['pax'] . '</td>
                                 <td>Rp ' . number_format($value['harga']) . '</td>
                                 <td class="text-right">
                                    Rp. ' . number_format($value['pax'] * $value['harga']) . '
                                 </td>
                              </tr>';
         $total = $total + ($value['pax'] * $value['harga']);
         $no++;
      }
      $real_total = $total * $this->tempVar['jamaah'];

      $now_sudah_dibayar = ($real_total - $this->tempVar['discount']) - ($this->tempVar['sudah_dibayar'] - $this->tempVar['paid']);

      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="4" class="text-right">TOTAL</td>
                              <td>Rp ' . number_format($real_total) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4" class="text-right">DISKON</td>
                              <td>Rp ' . number_format($this->tempVar['discount']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4" class="text-right">SUDAH DIBAYAR</td>
                              <td>Rp ' . number_format($this->tempVar['sudah_dibayar']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4" class="text-right">REFUND</td>
                              <td>Rp ' . number_format($this->tempVar['paid']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4" class="text-right">SISA PEMBAYARAN</td>
                              <td>Rp ' . number_format($now_sudah_dibayar) . '</td>
                           </tr>
                        </tfoot>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['payer'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }


   function _contentTransaksiPembayaranPaketLA()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL INFO TRANSAKSI PEMBAYARAN PAKET LA :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:5%;font-weight: normal;">NO</th>
                              <th scope="col" style="width:50%;font-weight: normal;">KETERANGAN</th>
                              <th scope="col" style="width:5%;font-weight: normal;">QT</th>
                              <th scope="col" style="width:20%;font-weight: normal;">AMOUNT</th>
                              <th scope="col" style="width:20%;font-weight: normal;">TOTAL AMOUNT</th>
                           </tr>
                        <thead>
                        <tbody>';
      $no = 1;
      $total = 0;
      foreach ($this->tempVar['facilities'] as $key => $value) {
         $html .= '<tr>
                                 <td>' . $no . '</td>
                                 <td>' . $value['name'] . '</td>
                                 <td>' . $value['pax'] . '</td>
                                 <td>Rp ' . number_format($value['harga']) . '</td>
                                 <td class="text-right">
                                    Rp. ' . number_format($value['pax'] * $value['harga']) . '
                                 </td>
                              </tr>';
         $total = $total + ($value['pax'] * $value['harga']);
         $no++;
      }
      $real_total = $total * $this->tempVar['jamaah'];

      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="4" class="text-right">JUMLAH JAMAAH</td>
                              <td>' . number_format($this->tempVar['jamaah']) . ' Orang</td>
                           </tr>
                           <tr>
                              <td colspan="4" class="text-right">DISKON</td>
                              <td>Rp ' . number_format($this->tempVar['discount']) . '</td>
                           </tr>
                              <tr>
                              <td colspan="4" class="text-right">TOTAL</td>
                              <td>Rp ' . number_format($real_total - $this->tempVar['discount']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4" class="text-right">SUDAH DIBAYAR</td>
                              <td>Rp ' . number_format($this->tempVar['sudah_dibayar']) . '</td>
                           </tr>
                           <tr>
                              <td colspan="4" class="text-right">SISA</td>
                              <td>Rp ' . number_format(($real_total - $this->tempVar['discount']) - $this->tempVar['sudah_dibayar']) . '</td>
                           </tr>
                        </tfoot>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['payer'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _contentTransaksiTransport()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL TRANSAKSI :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:40%;font-weight: normal;">JENIS MOBIL</th>
                              <th scope="col" style="width:40%;font-weight: normal;">NOMOR PLAT</th>
                              <th scope="col" style="width:20%;font-weight: normal;">HARGA PAKET</th>
                           </tr>
                        <thead>
                        <tbody>';
      foreach ($this->tempVar['detail'] as $key => $value) {
         $html .= '<tr>
                                 <td>' . $value['jenis_mobil'] . '</td>
                                 <td>' . $value['nomor_plat'] . '</td>
                                 <td class="text-right">
                                    Rp. ' . number_format($value['harga_paket']) . '
                                 </td>
                              </tr>';
      }
      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="2" class="text-right">
                                 TOTAL
                              </td>
                              <td>
                                 Rp ' . number_format($this->tempVar['total']) . '
                              </td>
                           </tr>
                        </tfoot>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['payer'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _contentTransaksiPassport()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL TRANSAKSI :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:33%;font-weight: normal;">NAMA/NOMOR IDENTITAS PEMBAYAR</th>
                              <th scope="col" style="width:50%;font-weight: normal;">INFO PASSPORT</th>
                              <th scope="col" style="width:17%;font-weight: normal;">HARGA PAKET</th>
                           </tr>
                        <thead>
                        <tbody>';

      foreach ($this->tempVar['detail'] as $key => $value) {
         $html .= '<tr>
                                 <td>' . $value['nama_pelanggan'] . '/<br>' . $value['nomor_identitas'] . '</td>
                                 <td>
                                    <table class="table table-hover">
                                       <tbody>
                                          <tr>
                                             <td class="text-left py-0" style="width:25%;border:none;">Nama Kota</td>
                                             <td class="text-left py-0 px-0" style="width:75%;border:none;">: ' . $value['nama_kota'] . '</td>
                                          </tr>

                                          <tr>
                                             <td class="text-left py-0" style="border:none;">Nomor KK</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['kartu_keluarga_number'] . '</td>
                                          </tr>

                                          <tr>
                                             <td class="text-left py-0" style="border:none;">TTL</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['tempat_lahir'] . ', ' . $value['tanggal_lahir'] . '</td>
                                          </tr>
                                          <tr>
                                             <td class="text-left py-0" style="border:none;">Alamat</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['address'] . '</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                                 <td class="text-right">
                                    Rp. ' . number_format($value['harga_paket']) . '
                                 </td>
                              </tr>';
      }

      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="2" class="text-right">
                                 TOTAL
                              </td>
                              <td>
                                 Rp ' . number_format($this->tempVar['total']) . '
                              </td>
                           </tr>
                        </tfoot>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['payer'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _contentTransaksiHotel()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL TRANSAKSI :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:33%;font-weight: normal;">NAMA/NOMOR IDENTITAS PEMBAYAR</th>
                              <th scope="col" style="width:50%;font-weight: normal;">INFO HOTEL</th>
                              <th scope="col" style="width:17%;font-weight: normal;">HARGA PAKET</th>
                           </tr>
                        <thead>
                        <tbody>';

      foreach ($this->tempVar['detail'] as $key => $value) {
         $html .= '<tr>
                                 <td>' . $value['nama_pelanggan'] . '/<br>' . $value['nomor_identitas'] . '</td>
                                 <td>
                                    <table class="table table-hover">
                                       <tbody>
                                          <tr>
                                             <td class="text-left py-0" style="width:35%;border:none;">Nama Hotel</td>
                                             <td class="text-left py-0 px-0" style="width:65%;border:none;">: ' . $value['nama_hotel'] . '</td>
                                          </tr>
                                          <tr>
                                             <td class="text-left py-0" style="border:none;">TTL</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['tempat_lahir'] . ', ' . $value['tanggal_lahir'] . '</td>
                                          </tr>
                                          <tr>
                                             <td class="text-left py-0" style="border:none;">Tanggal Check In</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['check_in'] . '</td>
                                          </tr>
                                          <tr>
                                             <td class="text-left py-0" style="border:none;">Tanggal Check Out</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['check_out'] . '</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                                 <td class="text-right">
                                    Rp. ' . number_format($value['harga_paket']) . '
                                 </td>
                              </tr>';
      }

      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="2" class="text-right">
                                 TOTAL
                              </td>
                              <td>
                                 Rp ' . number_format($this->tempVar['total']) . '
                              </td>
                           </tr>
                        </tfoot>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['payer'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _contentTransaksiVisa()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 px-0">
                     <span class="justify-content-center container-fluid title-order" style="font-weight: normal;">
                        DETAIL TRANSAKSI :
                     </span>
                  </div>
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:33%;font-weight: normal;">NAMA/NOMOR IDENTITAS PEMBAYAR</th>
                              <th scope="col" style="width:50%;font-weight: normal;">INFO VISA</th>
                              <th scope="col" style="width:17%;font-weight: normal;">HARGA PAKET</th>
                           </tr>
                        <thead>
                        <tbody>';

      foreach ($this->tempVar['detail'] as $key => $value) {
         $html .= '<tr>
                                 <td>' . $value['nama_pelanggan'] . '/<br>' . $value['nomor_identitas'] . '</td>
                                 <td>
                                    <table class="table table-hover">
                                       <tbody>
                                          <tr>
                                             <td class="text-left py-0" style="width:35%;border:none;">Jenis Permohonan</td>
                                             <td class="text-left py-0 px-0" style="width:65%;border:none;">: ' . $value['nama_permohonan'] . '</td>
                                          </tr>
                                          <tr>
                                             <td class="text-left py-0" style="border:none;">TTL</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['tempat_lahir'] . ', ' . $value['tanggal_lahir'] . '</td>
                                          </tr>
                                          <tr>
                                             <td class="text-left py-0" style="border:none;">Nomor Passport</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['nomor_passport'] . '</td>
                                          </tr>
                                          <tr>
                                             <td class="text-left py-0" style="border:none;">Berlaku S/D</td>
                                             <td class="text-left py-0 px-0" style="border:none;">: ' . $value['berlaku_sd'] . '</td>
                                          </tr>
                                       </tbody>
                                    </table>
                                 </td>
                                 <td class="text-right">
                                    Rp. ' . number_format($value['harga_paket']) . '
                                 </td>
                              </tr>';
      }
      $html .=   '</tbody>
                        <tfoot>
                           <tr>
                              <td colspan="2" class="text-right">
                                 TOTAL
                              </td>
                              <td>
                                 Rp ' . number_format($this->tempVar['total']) . '
                              </td>
                           </tr>
                        </tfoot>';
      //             foreach ($this->tempVar['city_list'] as $key => $value) {
      //                $html .=   '<th scope="col">'.$value.'</th>';
      //             }
      //          $html .=   '</tr>
      //                   </thead>
      //                   <tbody>';
      // $no = 1;
      // foreach ($this->tempVar['data'] as $key => $value) {
      //    $html .= '<tr style="border-bottom: 1px solid #dee1e6;">
      //                <td>'.$no.'</td>
      //                <td>'.$value['fullname'].'</td>
      //                <td>'.($value['gender'] == '0' ? 'Laki-laki' : 'Perempuan') .'</td>
      //                <td>'.$value['paket_type_name'].'</td>';
      //    $room_number = $value['room_number'];
      //    foreach ($this->tempVar['city_list'] as $keyCity => $valueCity) {
      //       $html .='<td class="text-right">
      //                   '.(isset($room_number[$keyCity]) ?
      //                   '<span class="mt-1">'.$room_number[$keyCity].'</span>
      //                    <span class="box-checking mt-1"></span>' : '-' ).'
      //                </td>';
      //    }
      //    $html .= '</tr>';
      //    $no++;
      // }
      $html .=      '</table>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           Penerima
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           Penyetor
                        </div>
                     </div>
                     <div class="row pt-5">
                        <div class="col-4 text-center">
                           (' . $this->tempVar['receiver'] . ')
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4 text-center">
                           (' . $this->tempVar['payer'] . ')
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _contentCetakDaftarKamar()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                        .box-checking{
                           height: 14px;
                           width:20px;
                           border: 1px solid #9a9a9a;
                           border-radius: 3px;
                           display:inline-block;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:3%;" rowspan="2">NO </th>
                              <th scope="col" style="width:30%;" rowspan="2">NAMA JAMAAH</th>
                              <th scope="col" style="width:10%;" rowspan="2">JENIS KELAMIN</th>
                              <th scope="col" style="width:15%;" rowspan="2">TIPE</th>
                              <th scope="col" style="width:42%;" colspan="' . count($this->tempVar['city_list']) . '">NOMOR KAMAR</th>
                           </tr>
                           <tr>';
      foreach ($this->tempVar['city_list'] as $key => $value) {
         $html .=   '<th scope="col">' . $value . '</th>';
      }
      $html .=   '</tr>
                        </thead>
                        <tbody>';
      $no = 1;
      foreach ($this->tempVar['data'] as $key => $value) {
         $html .= '<tr style="border-bottom: 1px solid #dee1e6;">
                     <td>' . $no . '</td>
                     <td>' . $value['fullname'] . '</td>
                     <td>' . ($value['gender'] == '0' ? 'Laki-laki' : 'Perempuan') . '</td>
                     <td>' . $value['paket_type_name'] . '</td>';
         $room_number = $value['room_number'];
         foreach ($this->tempVar['city_list'] as $keyCity => $valueCity) {
            $html .= '<td class="text-right">
                        ' . (isset($room_number[$keyCity]) ?
               '<span class="mt-1">' . $room_number[$keyCity] . '</span>
                         <span class="box-checking mt-1"></span>' : '-') . '
                     </td>';
         }
         $html .= '</tr>';
         $no++;
      }
      $html .=         '</tbody>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _ContentDownloadAbsensiJamaah()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                     </style>
                     <table class="table table-hover ">
                        <thead>
                           <tr>
                              <th scope="col" style="width:3%;" rowspan="2">NO </th>
                              <th scope="col" style="width:17%;" rowspan="2">NAMA JAMAAH</th>
                              <th scope="col" style="width:22%;" rowspan="2">ALAMAT</th>
                              <th scope="col" style="width:18%;" rowspan="2">NO HP</th>
                              <th scope="col" style="width:30%;" colspan="3">PERTEMUAN</th>
                              <th scope="col" style="width:10%;" rowspan="2">KETERANGAN</th>
                           </tr>
                           <tr>
                              <th scope="col">1 </th>
                              <th scope="col">2</th>
                              <th scope="col">3</th>
                           </tr>
                        </thead>
                        <tbody>';
      $no = 1;
      foreach ($this->tempVar['data']['data_jamaah'] as $key => $value) {
         $html .= '<tr style="border-bottom:1px solid #dee1e6;">
                     <td>' . $no . '</td>
                     <td>' . $value['nama'] . '</td>
                     <td>' . $value['alamat'] . '</td>
                     <td>' . $value['no_hp'] . '</td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td></td>
                  </tr>';
         $no++;
      }
      $html .=         '</tbody>
                     </table>
                     <div class="row pt-5">
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                        </div>
                        <div class="col-4">
                           <div class="row">
                              <div class="col-12 mb-5">
                                 PETUGAS
                              </div>
                              <div class="col-12 mt-5">
                                 ( ' . $this->tempVar['nama_petugas'] . ' )<br> Jabatan : ' . $this->tempVar['jabatan_petugas'] . '
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function _ContentDataJamaah()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12">
                     <style>
                        .table td {
                           border: none !important;
                        }
                        .values::before {
                          content: ": ";
                        }
                        .border-t1{
                           border: 1px solid black;
                        }
                     </style>
                     <table class="table table-hover">
                        <tbody>';

      $html .= $this->_RowContent('', $this->_ValueRow10($this->tempVar['namaPaket'], $this->tempVar['noRegister']));
      $html .= $this->_RowContent('Nama Lengkap', $this->_ValueRow($this->tempVar['namaJamaah']));
      $html .= $this->_RowContent('Nama Ayah Kandung', $this->_ValueRow($this->tempVar['namaAyahKandung']));
      $html .= $this->_RowContent('Tempat Lahir', $this->_ValueRow2($this->tempVar['tempatLahir'], 'Tanggal', $this->tempVar['tanggalLahir']));
      $html .= $this->_RowContent('Jenis Kelamin', $this->_ValueRow3($this->tempVar['jenisKelamin'], '1. Laki-laki, 2. Perempuan', 'Umur', $this->date_ops->get_umur($this->tempVar['tanggalLahir']), 'GD', $this->tempVar['golonganDarah']));
      $html .= $this->_RowContent('Data Passport', $this->_ValueRow4(
         'Nomor Passport',
         $this->tempVar['nomorPassport'],
         'Tanggal Dikeluarkan',
         $this->tempVar['tanggalDikeluarkan'],
         'Tempat Dikeluarkan',
         $this->tempVar['tempatDikeluarkan'],
         'Masa Berlaku',
         $this->tempVar['masaBerlaku']
      ));
      $html .= $this->_RowContent('Alamat Tempat Tinggal', $this->_ValueRow($this->tempVar['alamatTempatTinggal']));
      $html .= $this->_RowContent('Desa/Kelurahan', $this->_ValueRow('Lampeudaya '));
      $html .= $this->_RowContent('Kecamatan', $this->_ValueRow('Darussalam '));
      $html .= $this->_RowContent('Kabupaten Kota', $this->_ValueRow('Aceh Besar'));
      $html .= $this->_RowContent('Provinsi', $this->_ValueRow2('Aceh', 'Kode Pos', $this->tempVar['kodePos'], '0'));
      $html .= $this->_RowContent('Telepon', $this->_ValueRow5($this->tempVar['telephone'], 'HP', $this->tempVar['hp']));
      $html .= $this->_RowContent('Email', $this->_ValueRow($this->tempVar['email']));
      $html .= $this->_RowContent('Pengalaman Haji', $this->_ValueRow6($this->tempVar['pengalamanHaji'], 'A. Belum Pernah B. Sudah ' . $this->tempVar['jumlahHaji'] . ' Kali', 'Tahun Terakhir', $this->tempVar['tahunHaji']));
      $html .= $this->_RowContent('Pengalaman Umrah', $this->_ValueRow6($this->tempVar['pengalamanUmrah'], 'A. Belum Pernah B. Sudah ' . $this->tempVar['jumlahUmrah'] . ' Kali', 'Tahun Terakhir', $this->tempVar['tahunUmrah']));
      $html .= $this->_RowContent('Program yang Dipilih', $this->_ValueRow7($this->tempVar['tanggalKeberangkatan'], '1'));
      $html .= $this->_RowContent('Berangkat Dari', $this->_ValueRow($this->tempVar['berangkatDari']));
      $html .= $this->_RowContent('Penyakit yang Diderita', $this->_ValueRow($this->tempVar['penyakit']));
      $html .= $this->_RowContent('Pendidikan Terakhir', $this->_ValueRow8($this->tempVar['pendidikanTerakhir'], '1. SD  2. SLTP  3. SLTA  4. D1/D2/D3  5. S1  6. S2  7. S3'));
      $html .= $this->_RowContent('Pekerjaan', $this->_ValueRow($this->tempVar['pekerjaan']));
      $html .= $this->_RowContent('Nama Instansi Pekerjaan', $this->_ValueRow($this->tempVar['namaInstansiPekerjaan']));
      $html .= $this->_RowContent('Alamat dan Telpon Pekerjaan', $this->_ValueRow($this->tempVar['alamatInstansiPekerjaan']));
      $html .= $this->_RowContent('Status', $this->_ValueRow6($this->tempVar['statusNikah'], '1. Menikah, 2. Tidak Menikah', 'Tanggal Nikah', $this->tempVar['tanggalNikah']));
      $html .= $this->_RowContent('Keluarga yang ikut bersama', $this->_ValueRow9($this->model_kwitansi->_getKeluarga(
         $this->tempVar['jamaahID'],
         $this->tempVar['paketTransactionID']
      )));
      $html .= $this->_RowContent('Keluarga yang dapat dihubungi', '');
      $html .= $this->_RowContent('Nama', $this->_ValueRow($this->tempVar['namaKeluarga']));
      $html .= $this->_RowContent('Alamat dan Telpon', $this->_ValueRow($this->tempVar['alamatKeluarga'] . ' ' . $this->tempVar['telephoneKeluarga']));

      $html .=         '<tr>
                           <td colspan="2">
                              <div class="row pt-5">
                                 <div class="col-4">
                                    <div class="float-left" style="border:1px solid black;display:inline-block;width:113px;height:151px;padding-left: 0px !important;margin-right:20px;">
                                    <img src="' . base_url() . '/image/personal/' . $this->tempVar['photo'] . '" style="width:113px;height:151px;float:left">
                                    </div>
                                 </div>
                                 <div class="col-4">
                                    <div class="row">
                                       <div class="col-12 mb-5">
                                          CALON JAMAAH
                                       </div>
                                       <div class="col-12 mt-5">
                                          (' . $this->tempVar['namaJamaah'] . ')
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-4">
                                    <div class="row">
                                       <div class="col-12 mb-5">
                                          PETUGAS
                                       </div>
                                       <div class="col-12 mt-5">
                                          (' . $this->tempVar['nama_petugas'] . ')<br> ' . $this->tempVar['jabatan_petugas'] . '
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </td>
                        </tr>
                        </tbody>
                     </table>
                  </div>
               </div>';
      return $html;
   }

   function _chank($values, $px = 1)
   {
      $exp  = str_split($values);
      $html = '<table class="px-' . $px . '" style="display:inline-block">
                  <tbody>
                     <tr>';
      foreach ($exp as $key => $value) {
         $html .= '<td style="border:1px solid black !important;">' . $value . '</td>';
      }
      $html .=   '<tr>
                  </tbody>
               </table>';
      return $html;
   }

   function _singleBox($value)
   {
      return '<div class="p-1" style="border:1px solid black;display:inline-block;width:25px;height:25px;padding-left: 9px !important;margin-right:20px;">' . $value . '</div>';
   }

   function _singleBox2($value)
   {
      return '<div class="p-1 ml-3" style="border:1px solid black;display:inline-block;width:35px;height:25px;padding-left: 9px !important;margin-right:20px;">' . $value . '</div>';
   }

   function _singleBox100($value)
   {
      return '<div class="p-1" style="border:1px solid black;display:inline-block;height:25px;padding-left: 9px !important;margin-right: 0px;width: 100%;">' . $value . '</div>';
   }

   function _ValueRow10($paketName, $noRegister)
   {
      $html = '<div class="row">
                  <div class="col-2 text-right">Nama Paket</div>
                  <div class="col-4">: ' . $paketName . '</div>
                  <div class="col-2 text-right">No Register</div>
                  <div class="col-4">: ' . $noRegister . '</div>
               </div>';
      return $html;
   }

   function _ValueRow9($valueArray)
   {
      $html = '<div class="row">
                  <table class="table table-hover">
                     <thead>
                        <tr><td style="width:40%;border:1px solid black !important;">NAMA</td>
                            <td style="width:30%;border:1px solid black !important;">HUBUNGAN</td>
                            <td style="width:30%;border:1px solid black !important;">TELPON/HP</td></tr>
                     </thead>
                     <tbody>';
      foreach ($valueArray as $key => $value) {
         $html .= '<tr><td style="border:1px solid black !important;">' . $value['nama'] . '</td>
                            <td style="border:1px solid black !important;">' . $value['hubungan'] . '</td>
                            <td style="border:1px solid black !important;">' . $value['telpon'] . '</td></tr>';
      }
      $html .=   '<tbody>
                  </table>
               </div>';
      return $html;
   }

   function _tableBox2($label, $value)
   {
      $exp  = str_split($value);
      $html = '<table style="display:inline-block;width:100%;">
                  <tbody>
                     <tr><td style="border:1px solid black !important;text-transform: uppercase;font-weight: bold;">' . $label . '<td></tr>
                     <tr><td style="border:1px solid black !important;text-transform: uppercase;font-weight: bold;">' . $value . '<td><tr>
                  </tbody>
               </table>';
      return $html;
   }

   function _tableBox($label, $value)
   {
      $exp  = str_split($value);
      $html = '<table style="display:inline-block">
                  <tbody>
                     <tr>
                        <td colspan="' . count($exp) . '" style="border:1px solid black !important;text-transform: uppercase;font-weight: bold;">' . $label . '<td>
                     </tr>
                     <tr>';
      foreach ($exp as $key => $value) {
         $html .= '<td style="border:1px solid black !important;">' . $value . '</td>';
      }
      $html .=   '<tr>
                  </tbody>
               </table>';
      return $html;
   }


   function _ValueRow8($value, $labelvalue)
   {
      return  '<div class="row">
                  <div class="col-12 py-1 px-0">
                     ' . $this->_singleBox($value) . '
                     ' . $labelvalue . '
                  </div>
               </div>';
   }

   function _ValueRow7($value, $px = 1)
   {
      $feedBack = '';
      if (strpos($value, '-') !== false) {
         $exp = explode('-', $value);
         $feedBack  = $this->_chank($exp[0], $px);
         $feedBack .= $this->_chank($exp[1], $px);
         $feedBack .= $this->_chank($exp[2], $px);
      } else {
         $feedBack  = $this->_chank($value, $px);
      }
      return  '<div class="row">
                  <div class="col-12 py-0 px-0">' . $feedBack . '</div>
               </div>';
   }

   # jenis kelamin, umur, golongan darah
   function _ValueRow6($value, $labelvalue, $label2, $value2)
   {
      return  '<div class="row">
                  <div class="col-6 py-1 px-0">
                     ' . $this->_singleBox($value) . '
                     ' . $labelvalue . '
                  </div>
                  <div class="col-2 py-1 text-left">
                     <b>' . $label2 . '</b>
                  </div>
                  <div class="col-4 py-1 px-0 text-left">
                     ' . $this->_singleBox100($value2) . '
                  </div>
               </div>';
   }

   # tanggal lahir
   function _ValueRow5($value, $label, $value2)
   {
      return  '<div class="row">
                  <div class="col-4 border-t1 py-1">' . $value . '</div>
                  <div class="col-2 py-1 text-right"><b>' . $label . '</b></div>
                  <div class="col-6 border-t1 py-1">' . $value2 . '</div>
               </div>';
   }

   function _ValueRow4($label, $value, $label2, $value2, $label3, $value3, $label4, $value4)
   {
      $html =  '<div class="row">
                  <div class="col-5 py-1 px-0">';
      $html .= $this->_tableBox($label, $value);
      $html .=   '</div>
                  <div class="col-5 py-1 text-left">';
      $html .= $this->_tableBox($label2, $value2);
      $html .=   '</div>
                  <div class="col-2"></div>
                  <div class="col-5 py-1 px-0">';
      $html .= $this->_tableBox2($label3, $value3);
      $html .=   '</div>
                  <div class="col-5 py-1 text-left">';
      $html .= $this->_tableBox($label4, $value4);
      $html .=   '</div>
               </div>';
      return $html;
   }

   # jenis kelamin, umur, golongan darah
   function _ValueRow3($value, $labelvalue, $label2, $value2, $label3, $value3)
   {
      return  '<div class="row">
                  <div class="col-4 py-1 px-0">
                     ' . $this->_singleBox($value) . '
                     ' . $labelvalue . '
                  </div>
                  <div class="col-3 py-1 text-left">
                     <b>' . $label2 . '</b>
                     ' . $this->_singleBox2($value2) . '
                  </div>
                  <div class="col-3 py-1 text-left">
                     <b>' . $label3 . '</b>
                     ' . $this->_singleBox2($value3) . '
                  </div>
               </div>';
   }

   # tanggal lahir
   function _ValueRow2($value, $label, $value2, $px = 1)
   {
      $feedBack = '';
      if (strpos($value2, '-') !== false) {
         $exp = explode('-', $value2);
         $feedBack  = $this->_chank($exp[0], $px);
         $feedBack .= $this->_chank($exp[1], $px);
         $feedBack .= $this->_chank($exp[2], $px);
      } else {
         $feedBack  = $this->_chank($value2, $px);
      }

      return  '<div class="row">
                  <div class="col-4 border-t1 py-1">' . $value . '</div>
                  <div class="col-2 py-1 text-right"><b>' . $label . '</b></div>
                  <div class="col-6 py-0 px-0">' . $feedBack . '</div>
               </div>';
   }

   function _ValueRow($value)
   {
      return  '<div class="row">
                  <div class="col-12 border-t1 py-1">
                     ' . $value . '
                  </div>
               </div>';
   }

   function _RowContent($title, $value)
   {
      return  '<tr>
                  <td style="width:25%" class="text-left pt-1"><div class="mt-1"><b>' . $title . '</b></div></td>
                  <td style="width:75%" class="text-left py-1">' . $value . '</td>
               </tr>';
   }

   function _ContentPindahPaket()
   {
      $html = '<div class="row mt-3">
                  <div class="col-8">
                     <div class="row">
                        <div class="col-12">
                           <label>INFO PAKET ASAL</label>
                           <table class="table table-hover">
                              <thead>
                                 <tr><th scope="col" style="width:20%;">KODE/<br>NO_REG</th>
                                     <th scope="col" style="width:40%;">NAMA PAKET</th>
                                     <th scope="col" style="width:15%;">TIPE PAKET</th>
                                     <th scope="col" style="width:25%;">HARGA PAKET</th></tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td><b>' . $this->tempVar['kode_paket_asal'] . '</b>/<br><b>' . $this->tempVar['no_register_asal'] . '</b></td>
                                    <td>' . $this->tempVar['paket_asal'] . '</td>
                                    <td>' . $this->tempVar['tipe_paket_asal'] . '</td>
                                    <td>Rp. ' . number_format($this->tempVar['harga_paket_asal']) . '</td>
                                 </tr>
                                 </tbody>
                           </table>
                        </div>
                        <div class="col-12">
                           <label>INFO PAKET TUJUAN</label>
                           <table class="table table-hover">
                              <thead>
                                 <tr>
                                    <th scope="col" style="width:20%;">KODE/<br>NO_REG</th>
                                    <th scope="col" style="width:40%;">NAMA PAKET</th>
                                    <th scope="col" style="width:15%;">TIPE PAKET</th>
                                    <th scope="col" style="width:25%;">HARGA PAKET</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 <tr>
                                    <td><b>' . $this->tempVar['kode_paket_tujuan'] . '</b>/<br><b>' . $this->tempVar['no_register_paket_tujuan'] . '</b></td>
                                    <td>' . $this->tempVar['paket_tujuan'] . '</td>
                                    <td>' . $this->tempVar['tipe_paket_tujuan'] . '</td>
                                    <td>Rp. ' . number_format($this->tempVar['harga_paket_tujuan']) . '</td>
                                 </tr>
                                 </tbody>
                           </table>
                        </div>
                     </div>
                  </div>

                  <div class="col-4">
                     <label>INFO PINDAH PAKET</label>
                     <table class="table table-hover">
                        <tbody>
                           <tr>
                              <td style="width:55%;" class="text-right">BIAYA YG DIPINDAH</td>
                              <td style="width:45%;" class="text-left">: Rp. ' . number_format($this->tempVar['biaya_yang_dipindahkan']) . '</td>
                           </tr>
                           <tr>
                              <td style="width:55%;" class="text-right">FEE MAHRAM</td>
                              <td style="width:45%;" class="text-left">: Rp. ' . number_format($this->tempVar['fee_mahram']) . '</td>
                           </tr>
                           <tr>
                              <td style="width:55%;" class="text-right">SISA PEMBAYARAN</td>
                              <td style="width:45%;" class="text-left">: Rp. ' . number_format($this->tempVar['sisa_pembayaran']) . '</td>
                           </tr>
                           <tr>
                              <td style="width:55%;" class="text-right">REFUND</td>
                              <td style="width:45%;" class="text-left">: Rp. ' . number_format($this->tempVar['refund']) . '</td>
                           </tr>
                           </tbody>
                     </table>
                  </div>
               </div>';
      return $html;
   }


   function ContentSkemaCicilan()
   {
      $html = '<div class="row">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <label class="col-12" style="font-size:15px;">INFO PAKET</label>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['paket_name'] . '</p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">KEBERANGKATAN : ' . $this->tempVar['departure_date'] . '</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="row">
                        <div class="col-12">
                           <label class="col-12" style="font-size:15px;">INFO JAMAAH</label>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['fullname'] . '</p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">ID : ' . $this->tempVar['identity_number'] . '</p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['alamat'] . '</p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['no_hp'] . '</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-5">
                     <div class="row">
                        <div class="col-12">
                           <label class="col-12" style="font-size:15px;">INFO CICILAN</label>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">TOTAL PINJAMAN</p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['total_harga_paket'] . '</p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">ANGSURAN</p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['rata_rata_amount'] . '</p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">AKAD</p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['bulan'] . ' BULAN</p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <table class="table table-hover mt-4">
                     <thead>
                        <tr>
                           <th scope="col" style="width:7%;">TERM</th>
                           <th scope="col" style="width:43%;">KET</th>
                           <th scope="col" style="width:25%;">JATUH TEMPO</th>
                           <th scope="col" style="width:25%;">ANGSURAN</th>
                        </tr>
                     </thead>
                     <tbody>';
      foreach ($this->tempVar['skema'] as $key => $value) {
         $html .= '<tr>
                           <td>#' . $value['term'] . '</td>
                           <td>Pembayaran angsuran ' . $value['term'] . '</td>
                           <td> ' . $value['duedate'] . ' </td>
                           <td> ' . $value['amount'] . ' </td>
                        </tr>';
      }
      $html    .= '
                        <tr>
                           <td colspan="3" style="text-align:left;font-weight:bold;">TOTAL ANGSURAN</td>
                           <td style="font-weight:bold;">' . $this->tempVar['totalAmount'] . '</td>
                        </tr>
                     </tbody>
                  </table>
               </div>';
      return $html;
   }

   function ContentRiwayatCicilan()
   {
      $html = '<div class="row">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <label class="col-12" style="font-size:15px;">INFO PAKET</label>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">' . $this->tempVar['paket_name'] . '</p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">KEBERANGKATAN : ' . $this->tempVar['departure_date'] . '</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <label class="col-12" style="font-size:15px;">INFO JAMAAH</label>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['fullname'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ID : ' . $this->tempVar['identity_number'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['alamat'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['no_hp'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <label class="col-12" style="font-size:15px;">INFO CICILAN</label>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              PINJAMAN
                           </p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['totalAmount'] . '
                           </p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ANGSURAN
                           </p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['rata_rata_amount'] . '
                           </p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              SISA PINJAMAN
                           </p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['sisaPinjaman'] . '
                           </p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              SISA BULAN
                           </p>
                        </div>
                        <div class="col-6">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['sisaBulan'] . ' BULAN
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function RiwayatPembayaranCicilan()
   {
      $html =  '<div class="row mt-4">
                  <label style="font-size: 14px !important;">TOTAL PEMBAYARAN CICILAN : ' . $this->tempVar['total_pembayaran'] . '</label>
                  <table class="table table-hover ">
                     <thead>
                        <tr>
                           <th scope="col" style="width:20%;">NO INVOICE</th>
                           <th scope="col" style="width:20%;">BAYAR</th>
                           <th scope="col" style="width:10%;">KET</th>
                           <th scope="col" style="width:15%;">PENYETOR</th>
                           <th scope="col" style="width:15%;">PENERIMA</th>
                           <th scope="col" style="width:20%;">TANGGAL</th>
                        </tr>
                     </thead>
                     <tbody>';
      if (count($this->tempVar['riwayatTransaksi']) > 0) {
         foreach ($this->tempVar['riwayatTransaksi'] as $key => $value) {
            $html .=   '<tr>
                                 <td>' . $value['invoice'] . '</td>
                                 <td>' . $value['debet'] . '</td>
                                 <td>' . $value['ket'] . '</td>
                                 <td>' . $value['penyetor'] . '</td>
                                 <td>' . $value['penerima'] . '</td>
                                 <td>' . $value['tanggal'] . '</td>
                              </tr>';
         }
      } else {
         $html .=   '<tr>
                              <td colspan="6">Riwayat cicilan tidak ditemukan.</td>
                           </tr>';
      }

      $html .= '</tbody>
                  </table>
               </div>';

      return $html;
   }


   function ContentPaketCicilan()
   {
      $html = '<div class="row mt-3">
                  <div class="col-6">
                     <div class="row">
                        <div class="col-12">
                           <label>INFO PEMBAYARAN</label>
                           <table class="table table-hover">
                              <thead>
                                 <tr><th scope="col" style="width:30%;">TANGGAL</th>
                                     <th scope="col" style="width:35%;">BAYAR</th>
                                     <th scope="col" style="width:35%;">PENERIMA</th></tr>
                              </thead>
                              <tbody>';
      if (isset($this->tempVar['detailPembayaran']) and count($this->tempVar['detailPembayaran']) > 0) {
         $html .= '<tr><td>' . $this->tempVar['detailPembayaran']['tanggal_transaksi'] . '</td>
                            <td>' . $this->tempVar['detailPembayaran']['paid'] . '</td>
                            <td>' . $this->tempVar['detailPembayaran']['penerima'] . '</td></tr>';
      } else {
         $html .= '<tr><td colspan="3">Info pembayaran tidak ditemukan</td></tr>';
      }
      $html .=         '</tbody>
                           </table>
                        </div>
                        <div class="col-12">
                           <div class="row mt-3">
                              <div class="col-6">
                                 <p class="my-0 justify-content-center container-fluid info-order">ORDER BY:</p>
                              </div>
                              <div class="col-6">
                                 <p class="my-0 justify-content-center container-fluid info-order">PENERIMA :</p>
                              </div>
                              <div class="col-6 mt-3">
                                 <p class="my-0 justify-content-center container-fluid info-order">
                                    (................................)
                                 </p>
                              </div>
                              <div class="col-6 mt-3">
                                 <p class="my-0 justify-content-center container-fluid info-order">
                                    (................................)
                                 </p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
                  <div class="col-6">
                     <label>INFO CICILAN</label>';
      if (isset($this->tempVar['total_sudah_bayar'])) {
         $html .= '<label style="float:right;"> TOTAL SUDAH BAYAR : ' . $this->tempVar['total_sudah_bayar'] . '</label>';
      }
      $html .= '<table class="table table-hover">
                        <thead>
                           <tr><th scope="col" style="width:7%;">TERM</th>
                               <th scope="col" style="width:37%;">KET</th>
                               <th scope="col" style="width:28%;">BAYAR</th>
                               <th scope="col" style="width:28%;">SISA</th></tr>
                        </thead>
                        <tbody>';
      if (isset($this->tempVar['dp'])) {
         $html .= '<tr><td>' . $this->tempVar['dp']['term'] . '</td>
                               <td style="text-align:left;"><p class="mb-1">' . $this->tempVar['dp']['ket'] . '</p></td>
                               <td>' . $this->tempVar['dp']['bayar'] . '</td>
                               <td>' . $this->tempVar['dp']['sisa'] . '</td></tr>';
      }
      if (isset($this->tempVar['listPembayaran'])) {
         foreach ($this->tempVar['listPembayaran'] as $key => $value) {
            $html .= '<tr><td>' . $value['term'] . '</td>
                                  <td style="text-align:left;"><p class="mb-1">' . $value['ket'] . '</p></td>
                                  <td>' . $value['bayar'] . '</td>
                                  <td>' . $value['sisa'] . '</td></tr>';
         }
      } else {
         $html .= '<tr><td colspan="5">Info cicilan tidak ditemukan</td></tr>';
      }
      $html .=   '</tbody>
                     </table>
                  </div>
               </div>';
      return $html;
   }

   function Header()
   {
      $html = '<div class="row">
                  <div class="col-4 mt-2" style="text-align: right;">
                     <img src="' . base_url() . 'image/company/invoice_logo/' . $this->setting['logo'] . '" class="img-fluid list_upper container-fluid" style="width:7cm;float: left;">
                  </div>
                  <div class="col-8 mt-2" >
                     <div class="row">
                        <div class="col-12" style="text-align: right;">
                           <span class="justify-content-center container-fluid main-title">
                              ' . $this->setting['invoice_title'] . '
                           </span>
                        </div>
                        <div class="col-12"  style="text-align: right;" >
                           <span class="justify-content-center container-fluid main-second" id="jalan">
                              ' . $this->setting['invoice_address'] . '
                           </span>
                        </div>
                        <div class="col-12" style="text-align: right;">
                           <span class="justify-content-center container-fluid main-second">
                              Kode Pos: ' . $this->setting['pos_code'] . ', Email: ' . $this->setting['invoice_email'] . ', Telp: ' . $this->setting['telp'] . '
                           </span>
                        </div>
                     </div>
                  </div>
                  <hr class="mt-1" style="width: 100%;height: 3px;">
               </div>';
      return $html;
   }
// style="border:1px solid #d5d5d5;"
   function  TitleDepositPayment(){
      $html = '<div class="row">
                  <div class="col-12 justify-content-center container-fluid py-2 " >
                     <p class="justify-content-left container-fluid text-left mb-0">
                        <b>KWITANSI PEMBAYARAN DEPOSIT SALDO</b>
                     </p>
                  </div>
                  <div class="w-100"></div>
               </div>';
      return $html;
   }

   function TitlePaymentFeeMember($title,  $no_invoice)
   {
      $html = '<div class="row">
                  <div class="col-5 justify-content-center container-fluid ">
                     <p class="justify-content-center container-fluid text-center register_invoice_number">
                        KWITANSI <br> TANDA BUKTI PEMBAYARAN FEE AGEN
                     </p>
                  </div>
                  <div class="w-100"></div>

               </div>';
      return $html;
   }

   // <div class="col-2"></div>
   // <div class="col-5 text-right">
   //    <div class="row" style="font-size: 19px;font-weight: bold;line-height: 21px;">
   //       <div class="col-12">NOMOR INVOICE : #'.$no_invoice.'</div>
   //    </div>
   // </div>

   function TitleTiket($title, $nomor_register, $no_invoice)
   {
      $html = '<div class="row">
                  <div class="col-5 ">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        ' . $title . '
                     </span>
                  </div>
                  <div class="col-2"></div>
                  <div class="col-5 text-right">
                     <div class="row" style="font-size: 19px;font-weight: bold;line-height: 21px;">
                        <div class="col-12">NOMOR REGISTER : #' . $nomor_register . '</div>
                        <div class="col-12">NOMOR INVOICE : #' . $no_invoice . '</div>
                     </div>
                  </div>
               </div>';
      return $html;
   }

   function TitleLeft($title, $invoice)
   {
      $html =    '<div class="row">
                     <div class="col-6 text-left">
                        <span class="justify-content-center container-fluid register_invoice_number">
                           ' . $title . '
                        </span>
                     </div>
                     <div class="col-6 text-right">
                        <span class="justify-content-center container-fluid register_invoice_number">
                           INVOICE : #' . $invoice . '
                        </span>
                     </div>
                  </div>';
      return $html;
   }

   function TitleMiddle($title)
   {
      $html =    '<div class="row">
                     <div class="col-12 text-center">
                        <span class="justify-content-center container-fluid register_invoice_number">
                           ' . $title . '
                        </span>
                     </div>';
      $html .=   '</div>';
      return $html;
   }

   function TitleAbsensi($title)
   {
      $html = '<div class="row">
                  <div class="col-9">
                     <div class="row" style="font-size: 19px;font-weight: bold;line-height: 21px;">
                        <div class="col-4">NAMA PAKET </div><div class="col-8">: ' . $this->tempVar['data']['paket_name'] . '</div>
                        <div class="col-4">NOMOR REGISTER </div><div class="col-8">: ' . $this->tempVar['data']['no_register'] . '</div>
                     </div>
                  </div>
                  <div class="col-3 text-right">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        ' . $title . '
                     </span>
                  </div>';
      $html .=   '</div>';
      return $html;
   }

   function TitlePindahPaket($title)
   {
      $html = '<div class="row">
                  <div class="col-6"></div>
                  <div class="col-6 text-right">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        ' . $title . '
                     </span>
                  </div>';
      $html .=   '</div>';
      return $html;
   }

   function TitleHandoverReturned($title)
   {
      $html = '<div class="row">
                  <div class="col-6 text-left">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        INVOICE: #' . $this->tempVar['invoice'] . '
                     </span>
                  </div>
                  <div class="col-6 text-right">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        ' . $title . '
                     </span>
                  </div>';
      $html .=   '</div>';
      return $html;
   }


   function TitleHandoverFasilitas($title)
   {
      $html = '<div class="row">
                  <div class="col-12 text-center">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        ' . $title . '
                     </span>
                  </div>';
      $html .=   '</div>';
      return $html;
   }


   function _ContentFasilitas()
   {

      $html = '<div class="row mt-4">
                     <div class="col-12 mx-2" style="font-size: 16px;">
                        <label>Daftar item yang diserahkan:</label>
                        <div class="row">
                           <div class="col-12">
                              <hr class="mt-0" style="width: auto;border-top: 2px dashed rgb(0 0 0);">
                           </div>
                              ' . $this->tempVar['list_item'] . '
                           <div class="col-12">
                              <hr class="mt-0" style="width: auto;border-top: 2px dashed rgb(0 0 0);">
                           </div>
                        </div>

                     </div>
                  </div>
                  <div class="row mt-5">
                     <div class="col-6 text-center" style="font-size: 16px;">
                        <label>Yang Menyerahkan</label>
                        <label>PIHAK PERTAMA</label>
                        <br>
                        <label class="pt-5 mt-5">(_____________________)</label>
                     </div>
                     <div class="col-6 text-center" style="font-size: 16px;">
                        <label>Yang Menerima</label>
                        <label>PIHAK KEDUA</label>
                        <br>
                        <label class="pt-5 mt-5">(_____________________)</label>
                     </div>
                  </div>';
      return $html;
   }


   function _ContentReturned()
   {
      $html = '<div class="row mt-4">
                  <div class="col-12 mx-2" style="font-size: 16px;">
                     <label>Daftar item yang dikembalikan:</label>
                     <div class="row">
                        <div class="col-12">
                           <hr class="mt-0" style="width: auto;border-top: 2px dashed rgb(0 0 0);">
                        </div>
                           ' . $this->tempVar['list_item'] . '
                        <div class="col-12">
                           <hr class="mt-0" style="width: auto;border-top: 2px dashed rgb(0 0 0);">
                        </div>
                     </div>

                  </div>
               </div>
               <div class="row mt-5">
                  <div class="col-6 text-center" style="font-size: 16px;">
                     <label>Yang Menyerahkan</label>
                     <label>PIHAK PERTAMA</label>
                     <br>
                     <label class="pt-5 mt-5">(_____________________)</label>
                  </div>
                  <div class="col-6 text-center" style="font-size: 16px;">
                     <label>Yang Menerima</label>
                     <label>PIHAK KEDUA</label>
                     <br>
                     <label class="pt-5 mt-5">(_____________________)</label>
                  </div>
               </div>';
      return $html;
   }


   function _ContentHandover()
   {
      $html = '<div class="row mt-5">
                  <div class="col-12 mx-2" style="font-size: 16px;">
                     <label>Daftar item yang diserahkan:</label>
                     <div class="row">
                        <div class="col-12">
                           <hr class="mt-0" style="width: auto;border-top: 2px dashed rgb(0 0 0);">
                        </div>
                           ' . $this->tempVar['list_item'] . '
                        <div class="col-12">
                           <hr class="mt-0" style="width: auto;border-top: 2px dashed rgb(0 0 0);">
                        </div>
                     </div>

                  </div>
               </div>
               <div class="row mt-5">
                  <div class="col-6 text-center" style="font-size: 16px;">
                     <label>Yang Menyerahkan</label>
                     <label>PIHAK PERTAMA</label>
                     <br>
                     <label class="pt-5 mt-5">(_____________________)</label>
                  </div>
                  <div class="col-6 text-center" style="font-size: 16px;">
                     <label>Yang Menerima</label>
                     <label>PIHAK KEDUA</label>
                     <br>
                     <label class="pt-5 mt-5">(_____________________)</label>
                  </div>
               </div>';
      return $html;
   }

   function RegisterWithTitle($title)
   {
      $html = '<div class="row">
                  <div class="col-6 text-left">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        NO REGISTER : #' . $this->tempVar['no_register'] . '
                     </span>
                  </div>
                  <div class="col-6 text-right">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        ' . $title . '
                     </span>
                  </div>';
      if (isset($this->tempVar['invoice'])) {
         $html  .=  '<div class="col-12" style="text-align: left;">
                     <span class="justify-content-center container-fluid register_invoice_number">
                        INVOICE : #' . $this->tempVar['invoice'] . '
                     </span>
                  </div>';
      } else {
         $html  .=  '<div class="col-12" style="text-align: left;">
                     <span class="justify-content-center container-fluid register_invoice_number"></span>
                  </div>';
      }
      $html .=   '</div>';
      return $html;
   }

   function Register()
   {
      $html = '<div class="row">
                  <div class="col-6">
                     <div class="row">
                        <div class="col-12" style="text-align: left;">
                           <span class="justify-content-center container-fluid register_invoice_number">
                              NO REGISTER : #' . $this->tempVar['no_register'] . '
                           </span>
                        </div>';
      if (isset($this->tempVar['invoice'])) {
         $html  .=     '<div class="col-12" style="text-align: left;">
                              <span class="justify-content-center container-fluid register_invoice_number">
                                 INVOICE : #' . $this->tempVar['invoice'] . '
                              </span>
                           </div>';
      } else {
         $html  .=     '<div class="col-12" style="text-align: left;">
                              <span class="justify-content-center container-fluid register_invoice_number"></span>
                           </div>';
      }
      $html .=      '</div>
                  </div>
                  <div class="col-6"></div>
               </div>';
      return $html;
   }

   function OrderPaymentDeposit(){
      return  '<div class="row py-2" style="border: 2px solid #dee2e6;">
                  <div class="col-3">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid " style="color: #848484 !important;">
                              <b>Kode Transaksi</b>
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid " style="color: #848484 !important;">
                              ' . $this->tempVar['nomor_transaction'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid " style="color: #848484 !important;">
                              <b>Status Transaksi</b>
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid " style="color: #848484 !important;">
                              Sukses
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid " style="color: #848484 !important;">
                              <b>Keperluan</b>
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid " style="color: #848484 !important;">
                              '. $this->tempVar['keperluan']  .'
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid " style="color: #848484 !important;">
                              <b>Info '. ($this->tempVar['keperluan'] == 'deposit' ? 'Member' : 'Calon Jamaah') .'  </b>
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid " style="color: #848484 !important;">
                              ' . $this->tempVar['fullname'] . ' <br>
                              (WA : ' . $this->tempVar['nomor_whatsapp'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiPaymentFee()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              INFO AGEN :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['fullname'] . '<br>
                              (' . $this->tempVar['identity_number'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              INFO PETUGAS :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order mb-0">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                           <p class="justify-content-center container-fluid info-order">' . $this->tempVar['date_transaction'] . '</p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiRescheduleTiket()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              INFO PELANGGAN :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['costumer_name'] . '<br>
                              (' . $this->tempVar['costumer_identity'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              INFO PETUGAS :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order mb-0">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                           <p class="justify-content-center container-fluid info-order">' . $this->tempVar['tanggal_transaksi'] . '</p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiPembayaranTiketRefund()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA OLEH :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['costumer_name'] . '<br>
                              (' . $this->tempVar['costumer_identity'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA DARI :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order mb-0">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                           <p class="justify-content-center container-fluid info-order">' . $this->tempVar['input_date'] . '</p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiPembayaranTiket()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA OLEH :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order mb-0">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                           <p class="justify-content-center container-fluid info-order">' . $this->tempVar['input_date'] . '</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA DARI :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['costumer_name'] . '<br>
                              (' . $this->tempVar['costumer_identity'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiRefundPaketLA()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA OLEH :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA DARI :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['payer'] . '<br>
                              (' . $this->tempVar['payer_identity'] . ') <br>
                              ' . $this->tempVar['payer_address'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              TANGGAL KEBERANGKATAN :
                           </span>
                        </div>
                        <div class="col-12 ">
                           <span class="justify-content-center container-fluid title-order float-right text-right" style="font-weight:normal;">
                              ' . $this->tempVar['departure_date'] . '
                           </span>
                        </div>
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              TANGGAL KEPULANGAN :
                           </span>
                        </div>
                        <div class="col-12 ">
                           <span class="justify-content-center container-fluid title-order float-right text-right" style="font-weight:normal;">
                              ' . $this->tempVar['arrival_date'] . '
                           </span>
                        </div>
                     </div>
                  </div>
               </div>';
   }


   function OrderTransaksiPembayaranPaketLA()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA OLEH :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              DITERIMA DARI :
                           </span>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['payer'] . '<br>
                              (' . $this->tempVar['payer_identity'] . ') <br>
                              ' . $this->tempVar['payer_address'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              TANGGAL KEBERANGKATAN :
                           </span>
                        </div>
                        <div class="col-12 ">
                           <span class="justify-content-center container-fluid title-order float-right text-right" style="font-weight:normal;">
                              ' . $this->tempVar['departure_date'] . '
                           </span>
                        </div>
                        <div class="col-12">
                           <span class="justify-content-center container-fluid title-order">
                              TANGGAL KEPULANGAN :
                           </span>
                        </div>
                        <div class="col-12 ">
                           <span class="justify-content-center container-fluid title-order float-right text-right" style="font-weight:normal;">
                              ' . $this->tempVar['arrival_date'] . '
                           </span>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiTransport()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA OLEH
                     </span>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA DARI
                     </span>
                  </div>
               </div>
               <div class="row mt-0">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['payer'] . '<br>
                              (' . $this->tempVar['payer_identity'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }


   function OrderTransaksiPassport()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA OLEH
                     </span>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA DARI
                     </span>
                  </div>
               </div>
               <div class="row mt-0">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['payer'] . '<br>
                              (' . $this->tempVar['payer_identity'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiHotel()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA OLEH
                     </span>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA DARI
                     </span>
                  </div>
               </div>
               <div class="row mt-0">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['payer'] . '<br>
                              (' . $this->tempVar['payer_identity'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderTransaksiVisa()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA OLEH
                     </span>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <span class="justify-content-center container-fluid title-order">
                        DITERIMA DARI
                     </span>
                  </div>
               </div>
               <div class="row mt-0">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3"></div>
                  <div class="col-5">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['payer'] . '<br>
                              (' . $this->tempVar['payer_identity'] . ')
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderCicilan()
   {
      return  '<div class="row mt-3">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER
                     </span>
                  </div>
                  <div class="col-3">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER BY
                     </span>
                  </div>
                  <div class="col-5">
                     <span class="justify-content-center container-fluid title-order">
                        BIODATA JAMAAH
                     </span>
                  </div>
               </div>
               <div class="row mt-3">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="justify-content-center container-fluid info-order">
                              Order Date : 2021-01-01 11:30:21
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-3">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['nama_penyetor'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['alamat_penyetor'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['hp_penyetor'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-5">
                     <div class="row">
                        <div class="col-5">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              TOTAL PINJAMAN
                           </p>
                        </div>
                        <div class="col-7">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['total_pinjaman'] . '
                           </p>
                        </div>
                        <div class="col-5">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ANGSURAN
                           </p>
                        </div>
                        <div class="col-7">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['angsuran'] . '
                           </p>
                        </div>
                        <div class="col-5">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              JATUH TEMPO
                           </p>
                        </div>
                        <div class="col-7">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['duedate'] . '
                           </p>
                        </div>
                        <div class="col-5">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              AKAD
                           </p>
                        </div>
                        <div class="col-7">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['tenor'] . ' Bulan
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderReturned()
   {
      return  '<div class="row mt-5">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        YANG MENYERAHKAN (PIHAK I)
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        YANG MENERIMA (PIHAK II)
                     </span>
                  </div>
               </div>
               <div class="row mt-1">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid info-order">
                              Order Date : ' . $this->tempVar['order_date'] . '
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['petugas'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['jabatan'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver_returned'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver_returned_identity'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver_returned_hp'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver_returned_address'] . '
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderPindahPaket()
   {
      return  '<div class="row mt-5">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER BY
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        TANDA TANGAN
                     </span>
                  </div>
               </div>
               <div class="row mt-1">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid info-order">
                              Order Date : ' . $this->tempVar['order_date'] . '
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['nama_jamaah'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['identitas_jamaah'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['no_hp_jamaah'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['alamat_jamaah'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12 mt-3">
                           <span class="justify-content-center container-fluid info-order py-5">
                              (_________________)
                           </span>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function OrderFasilitas()
   {
      return  '<div class="row mt-5">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        YANG MENYERAHKAN (PIHAK I)
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        YANG MENERIMA (PIHAK II)
                     </span>
                  </div>
               </div>
               <div class="row mt-1">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid info-order">
                              Order Date : ' . $this->tempVar['order_date'] . '
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['petugas'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['jabatan'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver_name'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['receiver_identity'] . '
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }


   function OrderHandover()
   {
      return  '<div class="row mt-5">
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        YANG MENYERAHKAN (PIHAK I)
                     </span>
                  </div>
                  <div class="col-4">
                     <span class="justify-content-center container-fluid title-order">
                        YANG MENERIMA (PIHAK II)
                     </span>
                  </div>
               </div>
               <div class="row mt-1">
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid info-order">
                              Order Date : ' . $this->tempVar['order_date'] . '
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['giver_handover'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['giver_handover_identity'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['giver_handover_hp'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['giver_handover_address'] . '
                           </p>
                        </div>
                     </div>
                  </div>
                  <div class="col-4">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['petugas'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['jabatan'] . '
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function Order()
   {
      return  '<div class="row mt-3">
                  <div class="col-6">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER
                     </span>
                  </div>
                  <div class="col-6">
                     <span class="justify-content-center container-fluid title-order">
                        ORDER BY
                     </span>
                  </div>
               </div>
               <div class="row mt-3">
                  <div class="col-6">
                     <div class="row">
                        <div class="col-12">
                           <span class="justify-content-center container-fluid info-order">
                              Order Date : ' . $this->tempVar['order_date'] . '
                           </span>
                        </div>
                     </div>
                  </div>
                  <div class="col-6">
                     <div class="row">
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['deposit_name'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['deposit_address'] . '
                           </p>
                        </div>
                        <div class="col-12">
                           <p class="my-0 justify-content-center container-fluid info-order">
                              ' . $this->tempVar['deposit_phone'] . '
                           </p>
                        </div>
                     </div>
                  </div>
               </div>';
   }

   function ContentTransaksiRefund()
   {
      $batalBerangkat = '';
      if ($this->tempVar['batal_berangkat'] == 1) {
         $batalBerangkat = '<b>(BATAL BERANGKAT)</b><br>';
      }
      $html = '<div class="row mt-5">
                  <table class="table table-hover">
                     <thead>
                        <tr>
                           <th scope="col" style="width:15%;">KODE PAKET</th>
                           <th scope="col" style="width:25%;">NAMA PAKET</th>
                           <th scope="col" style="width:20%;">JAMAAH</th>
                           <th scope="col" style="width:20%;">TGL BERANGKAT</th>
                           <th scope="col" style="width:20%;">HARGA TOTAL PAKET</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr >
                           <td>' . $this->tempVar['kode'] . '</td>
                           <td style="text-transform:uppercase"><b>REFUND</b> <br> ' . $batalBerangkat . $this->tempVar['paket_name'] . '</td>
                           <td>' . $this->tempVar['jamaah'] . '</td>
                           <td>' . $this->tempVar['departure_date'] . '</td>
                           <td>' . $this->tempVar['total_paket_price'] . '</td>
                        </tr>
                     </tbody>
                     <tfoot>
                        <tr >
                           <td colspan="3" rowspan="3" class="py-0">
                              <div class="row">
                                 <div class="col-6">
                                    <div class="row">
                                       <div class="col-12 my-4 text-center">
                                          Order By
                                       </div>
                                       <div class="col-12 mt-4 mb-1 text-center">
                                          ( ' . $this->tempVar['deposit_name'] . ' )
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-6">
                                    <div class="row">
                                       <div class="col-12 my-4 text-center">
                                          Receive
                                       </div>
                                       <div class="col-12 mt-4 mb-1 text-center">
                                          ( ' . $this->tempVar['receiver'] . ' )
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </td>
                           <td class="greys">REFUND</td>
                           <td class="greys">' . $this->tempVar['refund'] . '</td>
                        </tr>
                        <tr>
                           <td class="greys">SUDAH BAYAR</td>
                           <td class="greys">' . $this->tempVar['sudah_bayar'] . '</td>
                        </tr>
                        <tr>
                           <td class="greys">SISA PEMBAYARAN</td>
                           <td class="greys">' . $this->tempVar['sisa_bayar'] . '</td>
                        </tr>
                     </tfoot>
                  </table>
               </div>
               ';
      return $html;
   }

   function ContentPaketTransaksi()
   {
      $html = '<div class="row mt-5">
                  <table class="table table-hover">
                     <thead>
                        <tr>
                           <th scope="col" style="width:15%;">KODE PAKET</th>
                           <th scope="col" style="width:25%;">NAMA PAKET</th>
                           <th scope="col" style="width:20%;">JAMAAH</th>
                           <th scope="col" style="width:20%;">TGL BERANGKAT</th>
                           <th scope="col" style="width:20%;">HARGA PAKET</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr >
                           <td>' . $this->tempVar['kode'] . '</td>
                           <td style="text-transform:uppercase">' . $this->tempVar['paket_name'] . '</td>
                           <td>' . $this->tempVar['jamaah'] . '</td>
                           <td>' . $this->tempVar['departure_date'] . '</td>
                           <td>' . $this->tempVar['harga_per_pax'] . '</td>
                        </tr>
                     </tbody>
                     <tfoot>
                        <tr >
                           <td colspan="3" rowspan="6" class="py-0">
                              <div class="row">
                                 <div class="col-6">
                                    <div class="row">
                                       <div class="col-12 mb-4 text-center">
                                          Order By
                                       </div>
                                       <div class="col-12 mt-5 mb-4 text-center">
                                          ( ' . $this->tempVar['deposit_name'] . ' )
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-6">
                                    <div class="row">
                                       <div class="col-12 mb-4 text-center">
                                          Receive
                                       </div>
                                       <div class="col-12 mt-5 mb-4 text-center">
                                          ( ' . $this->tempVar['receiver'] . ' )
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </td>
                           <td class="greys">TOTAL HARGA PAKET</td>
                           <td class="greys">' . $this->tempVar['total_paket_price'] . '</td>
                        </tr>
                        <tr>
                           <td class="greys">DISKON</td>
                           <td class="greys">' . $this->tempVar['diskon'] . '</td>
                        </tr>
                        <tr>
                           <td class="greys">BIAYA MAHRAM</td>
                           <td class="greys">' . $this->tempVar['mahram_fee'] . '</td>
                        </tr>
                        <tr>
                           <td class="greys" >TOTAL TAGIHAN</td>
                           <td class="greys" >' . $this->tempVar['total_tagihan'] . '</td>
                        </tr>
                        <tr>
                           <td class="greys">TOTAL PEMBAYARAN</td>
                           <td class="greys">' . $this->tempVar['total_pembayaran'] . '</td>
                        </tr>
                        <tr>
                           <td class="greys" ><b>SISA TAGIHAN</b></td>
                           <td class="greys"><b>' . $this->tempVar['sisa'] . '</b></td>
                        </tr>
                     </tfoot>
                  </table>
               </div>
               ';
      return $html;
   }

   function Note()
   {
      return  '<div class="row">
                  <div class="col-12 mt-5">
                     <p class="my-0 justify-content-center container-fluid note">
                        Note : ' . $this->setting['invoice_note'] . '
                     </p>
                  </div>
               </div>';
   }

   function Templating($Content)
   {

      $html = '<style type = "text/css">
   					.main-title{
   						font-size: 28px;
   					    font-weight: bold;
   					    line-height: 36px;
   					    text-transform:uppercase;
   					}
                  .register_invoice_number{
                     font-size: 19px;
                     font-weight: bold;
                     line-height: 21px;
                     text-transform: uppercase;
                  }
                  .title-order{
                     font-size: 15px;
                     font-weight: bold;
                     line-height: 21px;
                     text-transform: uppercase;
                  }

                  .info-order{
                     font-size: 15px;
                     font-weight: normal;
                     line-height: 21px;
                     text-transform: uppercase;
                  }
   					.main-second{
                     font-size: 15px;
                     font-weight: normal;
   					}
   					.image-kop{
   						float:left;
   						text-align:left;
   						margin:auto;
   						height:auto;
   					}
   					.image-header{
   					    padding: 0px;
   					    padding-bottom: 10px;
   					    border-bottom: 1px solid black;
   					    margin-bottom: 5px;
   					}
   					.image-div{
   						text-align: left;
   					    width: 220px;
   					    padding-top: 8px;
   					    padding-left: 0px;
   					}
   					.title{
   						padding:0px;
   						font-weight:bold;
   						font-size:15px;
   						float:right;
   						text-align:right;
   					}
   					.alamat{
   						font-size:12px;
   						text-align:right;
   						padding:0px;
   					}
   					.images-logos{
   						width:218px !important;
   					}
                  .table thead th {
                      vertical-align: bottom;
                      border: 1px solid #dee2e6;
                      text-align: center;
                      color: #848484 !important;
                      font-size: 15px !important;
                  }
                  .table tbody td {
                      font-size: 13px !important;
                  }
                  .table tfoot td {
                      vertical-align: bottom;
                      border: none;
                      text-align: right;
                      color: #848484 !important;
                      font-size: 15px !important;
                  }
                  @media print {
                     .greys {
                         background-color: #d8d8d8 !important;
                     }
                  }
                  .note{
                     font-size: 15px;
                     font-weight: normal;
                     line-height: 21px;
                  }
                  .greys {
                      background-color: #d8d8d8;
                  }
                  @page {
                     size: legal !important;
                     margin:0px;
                  }
   				</style>
   				<link rel="icon" href="' . base_url("image/icon.ico") . '" type="image/x-icon">
   				<link rel="stylesheet" href="' . base_url("assets/material_template/plugins/icheck-bootstrap/icheck-bootstrap.min.css") . '" >
   				<link rel="stylesheet" href="' . base_url("assets/material_template/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css") . '">
   				<link rel="stylesheet" href="' . base_url("assets/material_template/dist/css/adminlte.min.css") . '">
   				<link href="https://fonts.googleapis.com/css2?family=Tinos&display=swap" rel="stylesheet">
   				<link rel="stylesheet" href="' . base_url("assets/material_template/app/css/main.css") . '">
               <body>
                  <div class="col-12" style="font-family: \'Rubik\', sans-serif;width:100%;height:100%;" id="printArea">
                     <div class="row">
                        <div class="col-12 py-3 px-5 justify-content-center container-fluid" >
                           <div class="row">
                              <div class="col-12">
                                 ' . $Content . '
                              </div>
                           </div>
                        </div>
                     </div>
                   </div>
               </body>
               <script src="' . base_url('assets/material_template/plugins/jquery/jquery.min.js') . '"></script>
               <script src="' . base_url('assets/material_template/plugins/jquery-ui/jquery-ui.min.js') . '"></script>
               <script src="' . base_url('assets/material_template/plugins/bootstrap/js/bootstrap.bundle.min.js') . '"></script>
               <script type="text/javascript">
   					printDiv(\'printArea\');
   					function printDiv(divName) {
   			             var printContents = document.getElementById(divName).innerHTML;
   			             var originalContents = document.body.innerHTML;
   			             document.body.innerHTML = printContents;
   			             window.print();
   			             document.body.innerHTML = originalContents;
   			         }
                     window.onafterprint = function(){
                        window.open(\'\',\'_parent\',\'\');
                        window.close();
                     }
   			   </script>';
      echo $html;
   }
}
