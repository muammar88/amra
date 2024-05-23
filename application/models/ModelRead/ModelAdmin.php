<?php

/**
 *  -----------------------
 *	Modeladmin Model
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class ModelAdmin extends CI_Model
{

   /**
    * Get property
    * @return Array property
    */
   public function getProperty($param)
   {
      $feedBack = array();
      $q =    $this->db->select('setting_value, setting_name')
         ->from('base_setting')
         ->where_in('setting_name', $param)
         ->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[$row->setting_name] = $row->setting_value;
         }
      }
      return $feedBack;
   }

   /**
    * Get Access
    * @return Array Access
    */
   public function getModulAccess($param)
   {
      $feedBack = array();
      $this->db->select('modul_id, modul_name, modul_path, modul_icon')
         ->from('base_modules')
         ->where_in('modul_id', $param['modul'])
         ->order_by('modul_id', 'asc');
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $subaccess = array();
            $subaccess['modul_icon'] = $row->modul_icon;
            $subaccess['modul_name'] = $row->modul_name;
            $subaccess['modul_path'] = $row->modul_path;
            if ($row->modul_path == "#") {
               $this->db->select('submodules_name,submodules_path')
                  ->from('base_submodules')
                  ->where('modul_id', $row->modul_id)
                  ->where_in('submodules_id', $param['submodul'])
                  ->order_by('submodules_id', 'asc');
               $r = $this->db->get();
               if ($r->num_rows() > 0) {
                  $submodul = array();
                  foreach ($r->result() as $rowr) {
                     $submodul[] = array('submodules_name' => $rowr->submodules_name, 'submodules_path' => $rowr->submodules_path);
                  }
                  $subaccess['submodul'] = $submodul;
               }
            }
            $feedBack[] = $subaccess;
         }
      }
      return $feedBack;
   }

   /**
    * Get Info Profil
    * @return Array Info Profil
    */
   public function getInfoProfil($id)
   {
      $feedBack = array();
      $this->db->select('p.fullname, p.photo, u.username')
         ->from('base_users AS u')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
         ->where('u.user_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         // photo filterisasi
         $photo = 'avatar.svg';
         if ($row->photo != '') {
            $src = FCPATH . 'image/personal/' . $row->photo;
            if (file_exists($src)) {
               $photo = $row->photo;
            }
         }
         $feedBack = array('fullname' => $row->fullname, 'photo' => $photo, 'username' => $row->username);
      }
      return $feedBack;
   }

   // get setting value
   function getSettingValue()
   {
      $param = array('title_invoice', 'alamat_invoice', 'telp_invoice', 'email_invoice', 'kode_pos_invoice', 'note_invoice');
      $this->db->select('setting_name, setting_value')
         ->from('base_setting')
         ->where_in('setting_name', $param);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->setting_name] = $row->setting_value;
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
      $this->db->select('invoice, paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
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

   function _getJamaah($paket_transaction_id)
   {
      $this->db->select('p.fullname')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
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

   function getInvoiceContentTransactionCicilan()
   {

      $array = array();
      $sesi = $this->session->userdata('cetak_invoice');
      $this->db->select('pt.id, p.kode, p.paket_name, p.departure_date, pt.total_paket_price, pt.diskon,
                         pt.down_payment, pt.total_mahram_fee, pt.payment_methode, pt.tenor, pt.start_date,
                         pt.id AS paket_transaction_id, pt.price_per_pax,
                         (SELECT CONCAT_WS(\'$\', per.fullname, per.address, no_hp)
                           FROM paket_transaction_jamaah AS ptj
                           INNER JOIN jamaah AS j ON ptj.jamaah_id=j.id
                           INNER JOIN personal AS per ON j.personal_id=per.personal_id
                           WHERE ptj.paket_transaction_id= pt.id AND ptj.leader=1) AS leader')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
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

            if ($row->down_payment != 0) {
               $array['term'] = '#';
               $array['ket'] = 'Pembayaran DP';
               $array['bayar'] = 'Rp. ' . number_format($row->down_payment);
               $array['sisa'] = 'Rp. ' . number_format($row->total_paket_price - $row->down_payment);
            }

            if (isset($sesi['invoice'])) {
               $array['invoice'] = $sesi['invoice'];
               // skema pembayaranCicilan
               $skema = $this->skemaPembayaranCicilan($sesi['nomor_registrasi']);

               // pembayaran
               $pembayaran = $this->info_pembayaran_invoice($sesi['nomor_registrasi'], $sesi['invoice']);

               $array['detailPembayaran'] = $pembayaran['detailPembayaran'];
               $bayarSekarang = $pembayaran['bayarSekarang'];
               $sudahBayar = $pembayaran['sudahBayar'];
               $listBelumBayar = array();
               if ($sudahBayar != 0) {
                  $sisa = $sudahBayar;
                  // get angsuran yang belum bayar
                  $first = 0;
                  foreach ($skema as $key => $value) {
                     // echo $sisa."<br>";
                     $sisa = $sisa - $value['amount'];
                     if ($sisa < 0) {
                        if ($first == 0) {
                           $listBelumBayar[$key] = array('amount' => abs($sisa));
                        } else {
                           $listBelumBayar[$key] = array('amount' => $value['amount']);
                        }
                        $first++;
                     }
                  }
               } else {
                  foreach ($skema as $key => $value) {
                     $listBelumBayar[$key] = array('amount' => $value['amount']);
                  }
               }
               // echo "<br>";
               // print_r( $listBelumBayar );
               // echo "<br>";

               $sisa = $bayarSekarang;
               $listPembayaran = array();
               foreach ($listBelumBayar as $key => $value) {
                  $sisaSebelum = $sisa;
                  $sisa = $sisa - $value['amount'];
                  if ($sisa < 0) {
                     $listPembayaran[] = array(
                        'term' => $key,
                        'ket' => 'Pembayaran ke ' . $key,
                        'bayar' => 'Rp. ' . number_format($sisaSebelum),
                        'sisa' => 'Rp. ' . number_format(abs($sisa))
                     );
                     break;
                  } else {
                     $listPembayaran[] = array(
                        'term' => $key,
                        'ket' => 'Pembayaran ke ' . $key,
                        'bayar' => 'Rp. ' . number_format($value['amount']),
                        'sisa' => 'Rp. 0'
                     );
                  }
               }
               $array['listPembayaran'] = $listPembayaran;
            }
         }
      }

      $array['no_register'] = $sesi['nomor_registrasi'];
      return $array;
   }

   # get info pembayaran invoice
   function info_pembayaran_invoice($noRegister, $invoice)
   {
      $this->db->select('invoice, paid, receiver, ptih.input_date')
         ->from('paket_transaction_installement_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->where('pt.no_register', $noRegister)
         ->where('ket != \'dp\' ')
         ->order_by('ptih.input_date', 'asc');
      $q = $this->db->get();
      $sudahBayar = 0;
      $bayarSekarang = 0;
      $detail_pembayaran = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $sudahBayar = $sudahBayar + $row->paid;
            if ($row->invoice == $invoice) {

               $detail_pembayaran = array(
                  'paid' => 'Rp. ' . number_format($row->paid),
                  'penerima' => $row->receiver,
                  'tanggal_transaksi' => $this->date_ops->change_date_t5($row->input_date)
               );
               $bayarSekarang = $row->paid;
               break;
            }
         }
      }

      // echo 'Sudah Bayar : '.$sudahBayar."<br>";
      // echo 'Bayar Sekarang: '.$bayarSekarang."<br>";
      return array('sudahBayar' => $sudahBayar - $bayarSekarang, 'bayarSekarang' => $bayarSekarang, 'detailPembayaran' => $detail_pembayaran);
   }

   # get skema
   function skemaPembayaranCicilan($noRegister)
   {
      $this->db->select('term, amount')
         ->from('paket_installment_scheme  AS pis')
         ->join('paket_transaction AS pt', 'pis.paket_transaction_id=pt.id', 'inner')
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
      return $skema;
   }

   # info cicilan
   // function infoCicilan( $paket_transaction_id, $invoice ){
   //
   //
   //    # get sudah dibayar
   //    $sudah_dibayar = 0;
   //    $paid = 0;
   //    $this->db->select('invoice, paid')
   //       ->from('paket_transaction_installement_history AS ptih')
   //       ->where('paket_transaction_id', $paket_transaction_id)
   //       ->where('ket != \'dp\' ')
   //       ->order_by('input_date', 'desc');
   //    $r = $this->db->get();
   //    if( $r->num_rows() > 0 )
   //    {
   //       foreach( $r->result() AS $ror)
   //       {
   //          $paid = $ror->paid;
   //          $sudah_dibayar = $sudah_dibayar + $ror->paid;
   //          if( $ror->invoice == $invoice)
   //          {
   //             break;
   //          }
   //       }
   //    }
   //
   //    $sisa = $sudah_dibayar;
   //    $listFormatPembayaranan = array();
   //    $listFormatTanggal = array();
   //    $totalAmount = 0;
   //    $riwayat_angsuran = array();
   //    # get seluruh skema cicilan
   //    $this->db->select('term, amount, duedate')
   //       ->from('paket_installment_scheme')
   //       ->where('paket_transaction_id', $paket_transaction_id);
   //    $q = $this->db->get();
   //    if( $q->num_rows() > 0 )
   //    {
   //       foreach( $q->result() AS $row )
   //       {
   //          $bayar = $sisa;
   //          $sisa = $sisa - $row->amount;
   //          if( $sisa <= 0 ){
   //             $riwayat_angsuran = array('term' => $row->term,
   //                                       'bayar' => 'Rp. '.number_format( $bayar ),
   //                                       'sisa' => 'Rp. '.number_format( abs($sisa) ),
   //                                       'ket' => 'Pembayaran angsuran '.$row->term );
   //             break;
   //          }else{
   //             $riwayat_angsuran = array('term' => $row->term,
   //                                       'bayar' => 'Rp. '.number_format( $row->amount ),
   //                                       'sisa' => 'Rp. 0',
   //                                       'ket' => 'Pembayaran angsuran '. $row->term );
   //          }
   //       }
   //    }
   //    return $riwayat_angsuran;
   //
   // }

   function getRiwayatCicilan()
   {
      $array = array();
      $sesi = $this->session->userdata('cetak_invoice');
      $this->db->select('pis.term, pis.amount, pis.duedate, p.paket_name, p.departure_date, pt.total_paket_price, pt.down_payment,
                           (SELECT CONCAT_WS(\'$\', per.fullname, per.address, per.no_hp, per.identity_number )
                            FROM paket_transaction_jamaah AS ptj
                            INNER JOIN jamaah AS j ON ptj.jamaah_id=j.id
                            INNER JOIN personal AS per ON j.personal_id=per.personal_id
                            WHERE ptj.paket_transaction_id= pis.paket_transaction_id AND ptj.leader=1) AS leader')
         ->from('paket_installment_scheme AS pis')
         ->join('paket_transaction AS pt', 'pis.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
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
         ->where('pt.no_register', $sesi['nomor_registrasi']);
      $r = $this->db->get();
      $paid = 0;
      $riwayat_transaksi = array();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $rows) {
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

      $array['rata_rata_amount'] = 'Rp. ' . number_format($angsuran);
      $totalAmount = $total_paket_price - $dp;
      $array['totalAmount'] = 'Rp. ' . number_format($totalAmount);
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
      $this->db->select('pis.term, pis.amount, pis.duedate, p.paket_name, p.departure_date,
                           (SELECT CONCAT_WS(\'$\', per.fullname, per.address, per.no_hp, per.identity_number )
                            FROM paket_transaction_jamaah AS ptj
                            INNER JOIN jamaah AS j ON ptj.jamaah_id=j.id
                            INNER JOIN personal AS per ON j.personal_id=per.personal_id
                            WHERE ptj.paket_transaction_id= pis.paket_transaction_id AND ptj.leader=1) AS leader')
         ->from('paket_installment_scheme AS pis')
         ->join('paket_transaction AS pt', 'pis.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.no_register', $sesi['nomor_registrasi']);
      $q = $this->db->get();
      $skema = array();
      $bulan = 0;
      $totalAmount = 0;
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
      $array['bulan'] = $bulan;
      $array['skema'] = $skema;
      $array['no_register'] = $sesi['nomor_registrasi'];
      return $array;
   }

   function getInvoiceContentTransactionRefund()
   {
      $array = array();
      $sesi = $this->session->userdata('cetak_invoice');
      $this->db->select('p.kode, p.paket_name, p.departure_date, pt.batal_berangkat, pt.total_paket_price, pt.diskon, pt.total_mahram_fee, pt.payment_methode,
                         pt.id AS paket_transaction_id, pt.price_per_pax, pth.paid, pth.deposit_name, pth.deposit_phone, pth.receiver,
                         pth.deposit_address, pth.input_date')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.no_register', $sesi['nomor_registrasi'])
         ->where('pth.invoice', $sesi['invoice']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $getJamaah = $this->_getJamaah($row->paket_transaction_id);
            $get_paid = $this->get_paid($row->paket_transaction_id);


            $array['kode'] = $row->kode;
            $array['batal_berangkat'] = $row->batal_berangkat;
            $array['refund'] = 'Rp.' . number_format($row->paid);
            $array['paket_name'] = $row->paket_name;
            $array['deposit_name'] = $row->deposit_name;
            $array['deposit_phone'] = $row->deposit_phone;
            $array['deposit_address'] = $row->deposit_address;
            $array['order_date'] = $row->input_date;
            $array['departure_date'] = $this->date_ops->change_date_t4($row->departure_date);
            $array['jamaah'] = $getJamaah['list'];
            $array['total_paket_price'] = 'Rp.' . number_format($row->price_per_pax * $getJamaah['count']);
            $array['receiver'] = $row->receiver;
            $array['sudah_bayar'] = 'Rp.' . number_format($get_paid['cash'] - $get_paid['refund'] + $row->paid);
            $array['sisa_bayar'] = 'Rp.' . number_format($get_paid['cash'] - $get_paid['refund']);
         }
      }

      $array['no_register'] = $sesi['nomor_registrasi'];
      $array['invoice'] = $sesi['invoice'];

      return $array;
   }

   # get paid not refund
   function get_paid($paket_transaction_id)
   {
      $this->db->select('paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $cash = 0;
      $refund = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $cash = $cash + $row->paid;
            } elseif ($row->ket == 'refund') {
               $refund = $refund + $row->paid;
            }
         }
      }
      return array('cash' => $cash, 'refund' => $refund);
   }

   #
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
                         (SELECT CONCAT_WS(\'$\', p.fullname, p.identity_number, p.no_hp, p.address)
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
            $ex = explode('$', $row->petugasJabatan);
            $exJamaah = explode('$', $row->jamaah);
            $giver_handover =  $row->giver_handover;
            $giver_handover_identity =  $row->giver_handover_identity;
            $giver_handover_hp =  $row->giver_handover_hp;
            $giver_handover_address =  $row->giver_handover_address;
            $date_taken =  $row->date_taken;
            $petugas = $ex[0];
            $jabatan = $ex[1];
            // $listItem .= '<li>'.$row->item_name.'</li>';
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
                         (SELECT CONCAT_WS(\'$\', p.fullname, p.identity_number, p.no_hp, p.address)
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
            $ex = explode('$', $row->petugasJabatan);
            $exJamaah = explode('$', $row->jamaah);

            $receiver_returned = $row->receiver_returned;
            $receiver_returned_identity = $row->receiver_returned_identity;
            $receiver_returned_hp = $row->receiver_returned_hp;
            $receiver_returned_address = $row->receiver_returned_address;
            $date_returned = $row->date_returned;

            $petugas = $ex[0];
            $jabatan = $ex[1];
            // $listItem .= '<li>'.$row->item_name.'</li>';
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
                         (SELECT CONCAT_WS(\'$\', p.fullname, p.identity_number, p.no_hp, p.address)
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
            $ex = explode('$', $row->petugasJabatan);
            $exJamaah = explode('$', $row->jamaah);

            $list .= '<div class="col-2"><p style="display:inline-block">' . $num . '. ' . $row->facilities_name . '</p></div>';

            $feedBack['order_date'] = $row->date_transaction;

            $feedBack['receiver_name'] = $row->receiver_name;
            $feedBack['receiver_identity'] = $row->receiver_identity;
            $feedBack['petugas'] = $ex[0];
            $feedBack['jabatan'] = $ex[1];

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

   function getInfoKwitansiPindahPaket($pindahPaketId)
   {
      $this->db->select('pp.*, (SELECT CONCAT_WS(\'$\', p.fullname, p.address, p.no_hp, p.identity_number)
                               FROM jamaah AS j INNER JOIN personal AS p ON j.personal_id=p.personal_id
                               WHERE j.id=pp.jamaah_id) AS dataJamaah, pt.id AS paket_transaction_id,
                               pt.total_paket_price, pt.total_mahram_fee')
         ->from('pindah_paket AS pp')
         ->join('paket_transaction AS pt', 'pp.no_register_paket_tujuan=pt.no_register', 'inner')
         // ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pp.id', $pindahPaketId);
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

            $feedBack['sisa_pembayaran'] = $this->hitPembayaranByPaketTransactionID($row->paket_transaction_id, $row->total_paket_price + $row->total_mahram_fee);
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


   function getInfoDataJamaah($jamaahID, $paket_transaction_id)
   {
      $bloodType = array(1 => 'O', 2 => 'A', 3 => 'B', 4 => 'AB');
      $this->db->select('pkt.paket_name, pkt.departure_date,
                         pt.no_register,
                         p.fullname, p.birth_place, p.birth_date, p.gender, p.photo,
                         j.blood_type, j.passport_number, j.passport_dateissue, j.passport_place,
                         j.validity_period, p.address, j.pos_code, j.telephone, p.no_hp, p.email, j.hajj_experience, j.hajj_year, j.umrah_experience,
                         j.umrah_year, j.departing_from, j.desease, j.last_education, j.profession_name, j.profession_instantion_name, j.profession_instantion_address, j.status_nikah, j.tanggal_nikah, j.father_name,
                         j.nama_keluarga, j.alamat_keluarga, j.telephone_keluarga')
         ->from('paket_transaction AS pt')
         ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
         ->join('paket_transaction_jamaah AS ptj', 'pt.id=ptj.paket_transaction_id', 'inner')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $jamaahID);
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
            $feedBack['hp'] = $this->_isEmpty($rows->no_hp);
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
            // $tgl_keberangkatan

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

   function _getKeluarga($jamaah_id, $paket_transaction_id)
   {
      $this->db->select('p.fullname, p.no_hp')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('ptj.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array('nama' => $row->fullname, 'hubungan' => '', 'telpon' => $this->_isEmpty($row->no_hp));
         }
      }
      return $array;
   }

 // <img src="http://malemdiwa.com/amra/image/sign_in_logo.svg" alt="sign up logo" class="img-fluid mx-auto">

   function getCompanyData($value){
      $this->db->select('logo, name, icon')
        ->from('company')
        ->where('code', $value);
        $q = $this->db->get();
        $logo = 'sign_in_logo.svg';
        $company_name = 'AMRA :: Aplikasi Manajemen Travel Haji dan Umrah';
        $icon = 'icon.ico';
        if( $q->num_rows() > 0 ) {
          foreach ($q->result() as $rows) {
            $logo = $rows->logo != '' ? $rows->logo : 'sign_in_logo.svg' ;
            $company_name = $rows->name != '' ? $rows->name . ' :: Aplikasi Manajemen Travel Haji dan Umrah' : 'AMRA :: Aplikasi Manajemen Travel Haji dan Umrah';
            $icon = $rows->icon != '' ? 'company/icon/'.$rows->icon : 'icon.ico';
          }
        }
        return array('logo' => $logo, 'title' => $company_name, 'icon' => $icon  );
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

   public function transPaket()
   {
   }

   public function transPaketLA()
   {
   }

   public function transTiket()
   {
   }

   public function transDeposit()
   {
   }

   public function summary()
   {
   }
}
