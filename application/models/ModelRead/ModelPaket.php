<?php

/**
 *  -----------------------
 *	Model Paket
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class ModelPaket extends CI_Model
{

   # get total paket
   public function get_total_paket($search)
   {
      $this->db->select('id')->from('paket');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->like('paket_name', $search);
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index paket
   public function get_index_paket($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, kode,	paket_name, description, departure_date, return_date,	input_date, last_update')
         ->from('paket');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->like('paket_name', $search);
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'kode_paket' => $row->kode,
               'paket_name' => $row->paket_name,
               'description' => $row->description,
               'departure_date' => $this->date_ops->change_date($row->departure_date),
               'return_date' => $this->date_ops->change_date($row->return_date),
               'total_jamaah' => $this->_countTotalJamaah($row->id) . ' Orang',
               'price_list' => $this->_convertArrayToULPrice($this->getPriceListPaket($row->id)),
            );
         }
      }
      return array('list' => $list, 'total' => $this->get_total_paket($search));
   }

   # count Total Jamaah
   public function _countTotalJamaah($paketID)
   {
      $this->db->select('ptj.jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->where('pt.paket_id', $paketID);
      $q = $this->db->get();
      return $q->num_rows();
   }

   # convert array to price UL HTML
   public function _convertArrayToULPrice($array)
   {
      $return = '<ul class="pl-3 list">';
      if (count($array) > 0) {
         foreach ($array as $key => $value) {
            $return .= '<li>' . $value['paket_type_name'] . ' : Rp. ' . number_format($value['price']) . '</li>';
         }
      } else {
         $return .= '<li><Tipe Paket Tidak Ditemukan</li>';
      }
      $return .= '<ul>';
      return $return;
   }

   # get list price paket
   public function getPriceListPaket($paketID)
   {
      $this->db->select('pp.price, mpt.paket_type_name')
         ->from('paket_price AS pp')
         ->join('mst_paket_type AS mpt', 'pp.paket_type_id=mpt.id', 'inner')
         ->where('pp.paket_id', $paketID);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[] = array('price' => $row->price, 'paket_type_name' => $row->paket_type_name);
         }
      }
      return $return;
   }

   # get total tipe paket
   public function get_total_tipe_paket($search)
   {
      $this->db->select('id')->from('mst_paket_type');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->like('paket_type_name', $search);
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index tipe paket
   public function get_index_tipe_paket($limit = 6, $start = 0, $search = '')
   {
      $this->db->select('id, 	paket_type_name, 	input_date, last_update')
         ->from('mst_paket_type');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->like('paket_type_name', $search);
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array(
               'id' => $row->id,
               'tipe_paket' => $row->paket_type_name,
               'input_date' => $row->input_date,
               'last_update' => $row->last_update
            );
         }
      }
      return array('list' => $list, 'total' => $this->get_total_tipe_paket($search));
   }

   # get info paket
   public function getInfoPaket($tipePaketID)
   {
      $this->db->select('id, paket_type_name')
         ->from('mst_paket_type')
         ->where('id', $tipePaketID);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return['id'] = $row->id;
            $return['paket_type_name'] = $row->paket_type_name;
         }
      }
      return $return;
   }

   # get paket type
   public function getPaketType()
   {
      $this->db->select('id, paket_type_name')
         ->from('mst_paket_type');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[] = array('id' => $row->id, 'paket_type_name' => $row->paket_type_name);
         }
      }
      return $return;
   }

   # get fasilitas
   public function getFasilitasPaket()
   {
      $this->db->select('id, facilities_name')
         ->from('mst_facilities');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->facilities_name;
         }
      }
      return $return;
   }

   # get kota
   public function getkota()
   {
      $this->db->select('id, city_name')
         ->from('mst_city');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->city_name;
         }
      }
      return $return;
   }

   # get airline
   public function getAirLines()
   {
      $this->db->select('id, airlines_name')
         ->from('mst_airlines');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->airlines_name;
         }
      }
      return $return;
   }

   # get hotel
   public function getHotel()
   {
      $this->db->select('id, hotel_name')
         ->from('mst_hotel');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->hotel_name;
         }
      }
      return $return;
   }

   # get muthawif
   public function getMuthawif()
   {
      $this->db->select('m.id, p.fullname')
         ->from('muthawif AS m')
         ->join('personal AS p', 'm.personal_id=p.personal_id', 'inner');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->fullname;
         }
      }
      return $return;
   }

   # get bandara
   public function getBandara()
   {
      $this->db->select('ma.id, ma.airport_name, c.city_name, c.city_code')
         ->from('mst_airport AS ma')
         ->join('mst_city AS c', 'ma.city_id=c.id', 'inner');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] =  $row->airport_name . ' ( ' . $row->city_name . ' - ' . $row->city_code . ' )';
         }
      }
      return $return;
   }

   public function getDataPaket($id)
   {
      $this->db->select('kode, photo, paket_name, description, departure_date, return_date,
                         departure_from, mahram_fee, jamaah_quota, city_visited, airlines,
                         hotel, facilities, show_homepage, airport_departure, airport_destination,
                         departure_time, time_arrival')
         ->from('paket')
         ->where('id', $id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $src = FCPATH . 'image/paket/' . $row->photo;
            $photo = 'default.png';
            if (file_exists($src)) {
               $photo = $row->photo;
            }
            $feedBack['photo'] = $photo;
            $feedBack['kode_paket'] = $row->kode;
            $feedBack['paket_name'] = $row->paket_name;
            $feedBack['description'] = $row->description;
            $feedBack['departure_date'] = $row->departure_date;
            $feedBack['return_date'] = $row->return_date;
            $feedBack['departure_from'] = $row->departure_from;
            $feedBack['mahram_fee'] = 'Rp. ' . number_format($row->mahram_fee);
            $feedBack['jamaah_quota'] = $row->jamaah_quota;
            $feedBack['city_visited'] =  $row->city_visited != '' ? unserialize($row->city_visited) : '';
            $feedBack['airlines'] = $row->airlines != '' ? unserialize($row->airlines) : '';
            $feedBack['hotel'] = $row->hotel != '' ? unserialize($row->hotel) : '';
            $feedBack['facilities'] = $row->facilities != '' ? unserialize($row->facilities) : '';
            $feedBack['show_homepage'] = $row->show_homepage == 'tampilkan' ? '1' : 0;
            $feedBack['airport_departure'] = $row->airport_departure;
            $feedBack['airport_destination'] = $row->airport_destination;
            $expDT = explode(" ", $row->departure_time);
            $feedBack['departure_time'] = $expDT[0] . 'T' . $expDT[1];
            $expTA = explode(" ", $row->time_arrival);
            $feedBack['time_arrival'] = $expTA[0] . 'T' . $expTA[1];
         }
      }

      // get price list
      $this->db->select('paket_type_id, price')
         ->from('paket_price')
         ->where('paket_id', $id);
      $q = $this->db->get();
      $paketPrice = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $paketPrice[$row->paket_type_id] = 'Rp. ' . number_format($row->price);
         }
      }
      $feedBack['paket_price'] = $paketPrice;

      // get muthawif
      $this->db->select('muthawif_id')
         ->from('paket_muthawif')
         ->where('paket_id', $id);
      $q = $this->db->get();
      $muthawif = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $muthawif[] = $row->muthawif_id;
         }
      }
      $feedBack['muthawif'] = $muthawif;

      // get Itinerary
      $this->db->select('activity_date, activity_title, description')
         ->from('paket_itinerary')
         ->where('paket_id', $id);
      $q = $this->db->get();
      $itinerary = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $exp = explode(" ", $row->activity_date);
            $itinerary[] = array(
               'activity_date' => $exp[0] . 'T' . $exp[1],
               'activity_title' => $row->activity_title,
               'description' => $row->description
            );
         }
      }
      $feedBack['itinerary'] = $itinerary;

      return $feedBack;
   }

   function checkPaketExist($id)
   {
      $this->db->select('id, paket_name, photo')
         ->from('paket')
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         return array('success' => TRUE, 'paket_name' => $row->paket_name, 'photo' => $row->photo);
      } else {
         return array('success' => FALSE, 'paket_name' => 'Not Found');
      }
   }

   function getPrice($id)
   {
      $this->db->select('price')
         ->from('paket_price')
         ->where('paket_id', $id);
      $q = $this->db->get();
      $listPrice = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $listPrice[] = $row->price;
         }
      }

      $min = min($listPrice);
      $max = max($listPrice);

      $prices = '<ul class="pl-0 list" style="list-style-type: none;">';
      if ($min == $max) {
         $prices .= '<li>Rp. ' . $this->text_ops->shortIDCurrency($max) . '</li>';
      } else {
         $prices .= '<li>Rp. ' . $this->text_ops->shortIDCurrency($min) . '</li>
                     <li>Rp. ' . $this->text_ops->shortIDCurrency($max) . '</li>';
      }
      $prices .= '</ul>';

      return $prices;
   }

   function getPaketBeranda()
   {
      $this->db->select('p.id, p.kode, p.paket_name, p.departure_date, p.duration_trip, p.photo,
                        (SELECT COUNT(jamaah_id)
                           FROM paket_transaction_jamaah AS ptj
                           INNER JOIN  paket_transaction AS pt ON ptj.paket_transaction_id = pt.id
                           WHERE pt.paket_id = p.id) AS totalJamaah')
         ->from('paket AS p')
         ->where('departure_date >= NOW()');
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[] = array(
               'id' => $row->id,
               'kode' => $row->kode,
               'paket_name' => $row->paket_name,
               'departure_date' => $this->date_ops->change_date_t4($row->departure_date),
               'duration_trip' => $row->duration_trip,
               'price' => $this->getPrice($row->id),
               'photo' => $row->photo,
               'totalJamaah' => $row->totalJamaah
            );
         }
      }
      return $return;
   }

   // paket total transaksi paket
   function get_total_transaksi_paket($search, $paket_id)
   {
      $this->db->select('pt.id')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.paket_name', $search)
            ->or_like('pt.no_register', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   // paket transaksi paket
   function get_index_transaksi_paket($limit = 6, $start = 0, $search = '', $paket_id)
   {
      $this->db->select('pt.id, pt.no_register, p.paket_name, ptype.paket_type_name, pt.total_paket_price, pt.total_mahram_fee,
                         pt.payment_methode, p.departure_date, pt.diskon,
                         (SELECT price FROM paket_price WHERE paket_id=pt.paket_id AND paket_type_id=pt.paket_type_id) AS harga')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('mst_paket_type AS ptype', 'pt.paket_type_id=ptype.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('p.paket_name', $search)
            ->or_like('pt.no_register', $search)
            ->group_end();
      }
      $this->db->order_by('id', 'desc')->limit($limit, $start);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $sudahbayar = $this->getSudahBayar($row->id, $row->payment_methode);
            $sisa = $row->total_paket_price - $sudahbayar;
            $list[] = array(
               'id' => $row->id,
               'nomor_register' => $row->no_register,
               'paket_name' => $row->paket_name,
               'paket_type_name' => $row->paket_type_name,
               'total_paket_price' => 'Rp.' . number_format($row->total_paket_price),
               'harga' => 'Rp.' . number_format($row->harga),
               'metode_pembayaran' => $row->payment_methode == 0 ? 'Cash' : 'Cicilan',
               'sudah_dibayar' => 'Rp.' . number_format($sudahbayar),
               'sisa' => 'Rp.' . number_format($sisa),
               'fee_mahram' => 'Rp.' . number_format($row->total_mahram_fee),
               'departure_date' => $this->date_ops->change_date_t4($row->departure_date),
               'diskon' => 'Rp.' . number_format($row->diskon),
               'jamaah' => $this->getJamaahInPaket($row->id)
            );
         }
      }
      return array('list' => $list, 'total' => $this->get_total_paket($search));
   }

   function getSudahBayar($paket_transaction_id, $payment_methode)
   {
      if ($payment_methode == 0) {
         $this->db->select('paid, ket')
            ->from('paket_transaction_history')
            ->where('paket_transaction_id', $paket_transaction_id);
         $q = $this->db->get();
         $bayar = 0;
         $refund = 0;
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
               if ($row->ket == 'cash') {
                  $bayar = $bayar + $row->paid;
               } elseif ($row->ket == 'refund') {
                  $refund = $refund + $row->paid;
               }
            }
         }
         return $bayar - $refund;
      } else {
         $this->db->select('paid, ket')
            ->from('paket_transaction_installement_history')
            ->where('paket_transaction_id', $paket_transaction_id);
         $q = $this->db->get();
         $bayar = 0;
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
               $bayar = $bayar + $row->paid;
            }
         }
         return $bayar;
      }
   }

   function getJamaahInPaket($paket_transaction_id)
   {
      $this->db->select('p.fullname, p.identity_number')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('ptj.paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $list = '<ul class="pl-3 list">';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list .= '<li>' . $row->fullname . ' <br>(No ID: ' . $row->identity_number . ')</li>';
         }
      }
      return $list;
   }

   function getJamaahNotInPaket($paket_id)
   {
      // get jamaah in this paket
      $this->db->select('ptj.jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pt.batal_berangkat', '0');
      $q = $this->db->get();
      $jamaahPaket = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $jamaahPaket[] = $row->jamaah_id;
         }
      }

      // get jamaah not in paket
      $this->db->select('j.id, p.fullname, p.identity_number')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner');
      if (count($jamaahPaket) > 0) {
         $this->db->where_not_in('j.id', $jamaahPaket);
      }
      $q = $this->db->get();
      $return = array(0 => 'Pilih Jamaah');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->fullname . ' (' . $row->identity_number . ')';
         }
      }

      return $return;
   }

   // get agen
   function getAgen()
   {
      $this->db->select('a.id, p.fullname, p.identity_number')
         ->from('agen AS a')
         ->join('personal AS p', 'a.personal_id=p.personal_id', 'inner');
      $q = $this->db->get();
      $return = array(0 => 'Pilih Agen');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->id] = $row->fullname . ' (' . $row->identity_number . ')';
         }
      }
      return $return;
   }


   # get list price paket
   public function getPriceListPaketByPaketID($paketID)
   {
      $this->db->select('pp.paket_type_id, pp.price, mpt.paket_type_name')
         ->from('paket_price AS pp')
         ->join('mst_paket_type AS mpt', 'pp.paket_type_id=mpt.id', 'inner')
         ->where('pp.paket_id', $paketID);
      $q = $this->db->get();
      $return = array(0 => 'Pilih Tipe Paket');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[$row->paket_type_id] = $row->paket_type_name . ' (Rp. ' . number_format($row->price) . ')';
         }
      }
      return $return;
   }

   // check paket price
   function checkPricePaket($paket_id, $paket_type_id)
   {
      $this->db->select('paket_id')
         ->from('paket_price')
         ->where('paket_id', $paket_id)
         ->where('paket_type_id', $paket_type_id);
      $q = $this->db->get();
      return  $q->num_rows();
   }

   // get price
   function getPriceTransaksiPaket($paket_id, $paket_type_id)
   {
      $this->db->select('price')
         ->from('paket_price')
         ->where('paket_id', $paket_id)
         ->where('paket_type_id', $paket_type_id);
      $q = $this->db->get();
      $harga = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $harga = $row->price;
         }
      }
      return $harga;
   }

   function getBiayaMahram($paket_id)
   {
      $this->db->select('mahram_fee')
         ->from('paket')
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

   // get info need mahram
   function getInfoNeedMahram($jamaah_id)
   {
      $this->db->select('gender, birth_date')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('id', $jamaah_id);
      $q = $this->db->get();
      $return = false;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
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

   // get paket name only
   function getPaketName($paket_id)
   {
      $this->db->select('paket_name')
         ->from('paket')
         ->where('id', $paket_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $paket_name = $q->row()->paket_name;
         return $paket_name;
      }
      return 'Nama paket tidak ditemukan.';
   }

   // get personal info
   function getPersonalData()
   {
      $this->db->select('personal_id, fullname, identity_number')
         ->from('personal');
      $q = $this->db->get();
      $list = array(0 => 'Pilih Penyetor');
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $deposit = $this->getDepositInfo($row->personal_id);
            $list[$row->personal_id] = $row->fullname . ' (' . $row->identity_number . '); Deposit : Rp.' . number_format($deposit) . ' ';
         }
      }
      return $list;
   }

   function getDepositInfo($personal_id)
   {
      $this->db->select('debet, kredit')
         ->from('deposit_transaction')
         ->where('personal_id', $personal_id);
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $debet = $debet + $row->debet;
            $kredit = $kredit + $row->kredit;
         }
      }
      return $debet - $kredit;
   }

   function checkNoRegisterExist($no_register, $paket_transaction_id = 0)
   {
      $this->db->select('id')
         ->from('paket_transaction')
         ->where('no_register', $no_register);
      if ($paket_transaction_id != 0) {
         $this->db->where('id != ' . $paket_transaction_id);
      }
      $q = $this->db->get();
      return $q->num_rows();
   }

   function checkPaketTypePaket($paket_id, $paket_type_id)
   {
      $this->db->select('paket_type_id')
         ->from('paket_price')
         ->where('paket_id', $paket_id)
         ->where('paket_type_id', $paket_type_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return  FALSE;
      }
   }

   //
   function checkAgenExist($agenID)
   {
      $this->db->select('id')
         ->from('agen')
         ->where('id', $agenID);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return  FALSE;
      }
   }

   // price
   function getPaketPricePerType($paket_id, $paket_type_id)
   {
      $this->db->select('price')
         ->from('paket_price')
         ->where('paket_id', $paket_id)
         ->where('paket_type_id', $paket_type_id);
      $q = $this->db->get();
      $price = 0;
      if ($q->num_rows() > 0) {
         $price = $q->row()->price;
      }
      return $price;
   }

   function checkNoInvoiceExist($invoice, $paket_transaction_id = 0)
   {
      $this->db->select('id')
         ->from('paket_transaction_history')
         ->where('invoice', $invoice);
      if ($paket_transaction_id != 0) {
         $this->db->where('id != ' . $paket_transaction_id);
      }
      $q = $this->db->get();
      return $q->num_rows();
   }

   function getPenyetorName($personal_id)
   {
      $this->db->select('fullname, no_hp, address')
         ->from('personal')
         ->where('personal_id', $personal_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $array['fullname'] = $row->fullname;
         $array['no_hp'] = $row->no_hp;
         $array['address'] = $row->address;
      }
      return $array;
   }

   function getNamaCodePaket($paket_id)
   {
      $this->db->select('kode, paket_name')
         ->from('paket')
         ->where('id', $paket_id);
      $q = $this->db->get();
      // $kode = 'NotFound';
      $array = array();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $array['kode'] = $row->kode;
         $array['nama'] = $row->paket_name;
      }
      return $array;
   }

   function getMetodePembayaran($paket_transaction_id)
   {
      $this->db->select('payment_methode')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id);
      $q = $this->db->get();
      $payment_methode = '';
      if ($q->num_rows() > 0) {
         $row = $q->row();
         $payment_methode = $row->payment_methode;
      }
      return $payment_methode;
   }

   function checkDepositPembayaran($pembayaran, $penyetor)
   {
      $this->db->select('debet, kredit')
         ->from('deposit_transaction')
         ->where('personal_id', $penyetor);
      $q = $this->db->get();
      $debet = 0;
      $kredit = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $debet = $debet + $row->debet;
            $kredit = $kredit + $row->kredit;
         }
      }

      $deposit = $debet - $kredit;
      if ($deposit < 0) {
         $deposit = 0;
      }

      if ($pembayaran <= $deposit) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   // get info transaksi cicilan
   function get_info_transaksi_cicilan($paket_transaction_id)
   {
      $this->db->select('no_register, payment_methode')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array['no_register'] = $row->no_register;
            $array['metode_pembayaran'] = $row->payment_methode;
         }
      }
      return $array;
   }

   function get_total_jamaah_paket($search, $paket_id)
   {
      $this->db->select('ptj.jamaah_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # list item
   function listItemHandover($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('item_name')
         ->from('handover_item')
         ->where('paket_transaction_id', $paket_transaction_id)
         ->where('jamaah_id', $jamaah_id)
         ->where('status', 'diambil');
      $list = '';
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list .= '<li>' . $row->item_name . '</li>';
         }
      } else {
         $list .= '<li>Barang Jamaah Tidak Ditemukan</li>';
      }
      return $list;
   }

   function get_index_jamaah_paket($limit = 6, $start = 0, $search = '', $paket_id)
   {
      $this->db->select('pt.no_register, ptj.paket_transaction_id, ptj.jamaah_id, per.fullname, per.identity_number,
                         p.paket_name, mpt.paket_type_name, pt.payment_methode,
                           (SELECT price
                              FROM paket_price
                              WHERE paket_id=pt.paket_id
                                 AND paket_type_id=pt.paket_type_id) AS harga,
                           (SELECT person.fullname FROM mahram AS mah
                              INNER JOIN jamaah AS jam ON mah.mahram_id=jam.id
                              INNER JOIN personal AS person ON jam.personal_id=person.personal_id
                              WHERE mah.jamaah_id=ptj.jamaah_id) AS mahrams')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
         ->join('personal AS per', 'j.personal_id=per.personal_id', 'inner')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.paket_id', $paket_id)
         ->where('pt.batal_berangkat', '0');
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('per.fullname', $search)
            ->or_like('per.identity_number', $search)
            ->group_end();
      }
      $this->db->order_by('j.id', 'desc')->limit($limit, $start);
      $r    = $this->db->get();
      $list = array();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $row) {
            $list[] = array(
               'jamaah_id' => $row->jamaah_id,
               'no_register' => $row->no_register,
               'paket_transaction_id' => $row->paket_transaction_id,
               'metode_pembayaran' => $row->payment_methode,
               'fullname' => $row->fullname,
               'identity_number' => $row->identity_number,
               'paket_name' => $row->paket_name,
               'paket_type_name' => $row->paket_type_name,
               'harga' => 'Rp.' . number_format($row->harga),
               'handover_item' => $this->listItemHandover($row->paket_transaction_id, $row->jamaah_id),
               'handover_facility'  => $this->listItemFasilitas($row->paket_transaction_id, $row->jamaah_id),
               'mahram' => $row->mahrams == NULL ? 'Tidak ada mahram' : $row->mahrams
            );
         }
      }
      return array('list' => $list, 'total' => $this->get_total_jamaah_paket($search, $paket_id));
   }

   function listItemFasilitas($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('m.facilities_name')
         ->from('handover_facilities AS hf')
         ->join('mst_facilities AS m', 'hf.facilities_id=m.id', 'inner')
         ->where('hf.paket_transaction_id', $paket_transaction_id)
         ->where('hf.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      $list = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list .= '<li>' . $row->facilities_name . '</li>';
         }
      } else {
         $list .= '<li>Fasilitas Tidak Ditemukan.</li>';
      }
      return $list;
   }

   // get info pembayaran cicilan
   function getInfoPembayaranCicilan($paket_transaction_id)
   {
      $this->db->select('invoice, paid, ket, receiver, deposit_name, last_update')
         ->from('paket_transaction_installement_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      $total_bayar = 0;
      $total_bayar_not_dp = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array(
               'invoice' => $row->invoice,
               'paid' => 'Rp. ' . number_format($row->paid),
               'penerima' => $row->receiver,
               'ket' => $row->ket,
               'penyetor' => $row->deposit_name,
               'tanggal_transaksi' => $row->last_update
            );
            $total_bayar = $total_bayar + $row->paid;
            if ($row->ket != 'dp') {
               $total_bayar_not_dp = $total_bayar_not_dp + $row->paid;
            }
         }
      }

      $this->db->select('total_paket_price')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id);
      $total_price = 0;
      $r = $this->db->get();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $rows) {
            $total_price = $total_price + $rows->total_paket_price;
         }
      }

      $riwayat_angsuran = array();
      // riwayat Angsuran
      if ($total_bayar_not_dp > 0) {
         $this->db->select('term, amount, duedate')
            ->from('paket_installment_scheme')
            ->where('paket_transaction_id', $paket_transaction_id);
         $w = $this->db->get();
         $sisa = $total_bayar_not_dp;
         if ($w->num_rows() > 0) {
            foreach ($w->result() as $roww) {
               $bayar = $sisa;
               $sisa = $sisa - $roww->amount;
               if ($sisa <= 0) {
                  $riwayat_angsuran[] = array(
                     'term' => $roww->term,
                     'bayar' => 'Rp. ' . number_format($bayar),
                     'sisa' => 'Rp. ' . number_format(abs($sisa)),
                     'ket' => 'Pembayaran angsuran ' . $roww->term
                  );
                  break;
               } else {
                  $riwayat_angsuran[] = array(
                     'term' => $roww->term,
                     'bayar' => 'Rp. ' . number_format($roww->amount),
                     'sisa' => 'Rp. 0',
                     'ket' => 'Pembayaran angsuran ' . $roww->term
                  );
               }
            }
         }
      }

      $sisa = $total_price - $total_bayar;
      return array(
         'list' => $array,
         'total_harga' => 'Rp. ' . number_format($total_price),
         'total_bayar' => 'Rp. ' . number_format($total_bayar),
         'riwayat_angsuran' => $riwayat_angsuran,
         'sisa' => 'Rp. ' . number_format($sisa),
         'invoice' => $this->text_ops->get_invoice_transaksi_paket_cicilan()
      );
   }

   // get no register
   function getNoRegister($paket_transaction_id)
   {
      $this->db->select('no_register')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id);
      $q = $this->db->get();
      $no_register = 'Tidak ditemukan.';
      if ($q->num_rows() > 0) {
         $no_register = $q->row()->no_register;
      }
      return $no_register;
   }

   // check invoice cicilan
   function checkInvoiceCicilan($invoice)
   {
      $this->db->select('id')
         ->from('paket_transaction_installement_history')
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return FALSE;
      } else {
         return TRUE;
      }
   }

   function getLastInfoKwitansiCicilan($paket_transaction_id)
   {
      $this->db->select('ptih.invoice, pt.no_register')
         ->from('paket_transaction_installement_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->where('ptih.paket_transaction_id', $paket_transaction_id)
         ->order_by('ptih.input_date', 'asc');
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['invoice'] = $row->invoice;
            $feedBack['no_register'] = $row->no_register;
         }
      }
      return $feedBack;
   }

   // get info transaction paket
   function getInfoTransaksiPaket($paket_id, $paket_transaction_id)
   {
      $this->db->select('no_register, payment_methode')
         ->from('paket_transaction')
         ->where('paket_id', $paket_id)
         ->where('id', $paket_transaction_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $row = $q->row();
         return array('no_register' => $row->no_register, 'payment_methode' => $row->payment_methode);
      }
   }

   // get info transaksi paket cash
   function getInfoPembayaranCash($paket_transaction_id)
   {

      // get history transaksi
      $this->db->select('invoice, paid, ket, receiver, deposit_name, source, last_update')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      $total_bayar = 0;
      $total_bayar_not_dp = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array(
               'invoice' => $row->invoice,
               'paid' => 'Rp. ' . number_format($row->paid),
               'penerima' => $row->receiver,
               'ket' => $row->ket,
               'sumber_biaya' => $row->source,
               'penyetor' => $row->deposit_name,
               'tanggal_transaksi' => $row->last_update
            );
            $total_bayar = $total_bayar + $row->paid;
         }
      }

      // get total price
      $this->db->select('total_paket_price')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id);
      $total_price = 0;
      $r = $this->db->get();
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $rows) {
            $total_price = $total_price + $rows->total_paket_price;
         }
      }

      // feedback
      return array(
         'list' => $array,
         'total_harga' => 'Rp. ' . number_format($total_price),
         'total_bayar' => 'Rp. ' . number_format($total_bayar),
         'sisa' => 'Rp. ' . number_format($total_price - $total_bayar),
         'invoice' => $this->text_ops->get_invoice_transaksi_paket_cash()
      );
   }

   function getRiwayatTransactionCash($paket_transaction_id)
   {
      // get history transaksi
      $this->db->select('invoice, paid, ket, receiver, deposit_name, source, last_update')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $array = array();
      $total_bayar = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $array[] = array(
               'invoice' => $row->invoice,
               'paid' => 'Rp. ' . number_format($row->paid),
               'penerima' => $row->receiver,
               'ket' => $row->ket,
               'sumber_biaya' => $row->source,
               'penyetor' => $row->deposit_name,
               'tanggal_transaksi' => $row->last_update
            );
            if ($row->ket == 'cash') {
               $total_bayar = $total_bayar + $row->paid;
            } elseif ($row->ket == 'refund') {
               $total_bayar = $total_bayar - $row->paid;
            }
         }
      }
      return array('list' => $array, 'total_bayar' => $total_bayar);
   }

   // check invoice cash
   function checkInvoiceCash($invoice)
   {
      $this->db->select('id')
         ->from('paket_transaction_history')
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return FALSE;
      } else {
         return TRUE;
      }
   }

   // get last info kwitansi cash
   function getLastInfoKwitansiCash($paket_transaction_id)
   {
      $this->db->select('ptih.invoice, pt.no_register')
         ->from('paket_transaction_history AS ptih')
         ->join('paket_transaction AS pt', 'ptih.paket_transaction_id=pt.id', 'inner')
         ->where('ptih.paket_transaction_id', $paket_transaction_id)
         ->order_by('ptih.input_date', 'asc');
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['invoice'] = $row->invoice;
            $feedBack['no_register'] = $row->no_register;
         }
      }
      return $feedBack;
   }

   // check personal exist
   function checkPersonalExist($personal_id)
   {
      $this->db->select('personal_id')
         ->from('personal')
         ->where('personal_id', $personal_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get jumlah pembayaran
   function getJumlahPembayaran($paket_transaction_id)
   {
      $this->db->select('paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q  = $this->db->get();
      $bayar = 0;
      $refund = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $bayar = $bayar + $row->paid;
            } elseif ($row->ket == 'refund') {
               $refund = $refund + $row->paid;
            }
         }
      }
      return array('bayar' => $bayar, 'refund' => $refund);
   }

   function getSkemaCicilan($paket_transaction_id)
   {
      $this->db->select('term, amount, duedate')
         ->from('paket_installment_scheme')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $list = array();
      $total_amount = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[$row->term] = array(
               'term' => $row->term,
               'amount' => 'Rp. ' . number_format($row->amount),
               'duedate' => $row->duedate
            );
            $total_amount = $total_amount + $row->amount;
         }
      }

      $this->db->select('down_payment, total_paket_price')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id);
      $r = $this->db->get();
      $total_cicilan = 0;
      if ($r->num_rows() > 0) {
         foreach ($r->result() as $ror) {
            $total_cicilan = $ror->total_paket_price - $ror->down_payment;
         }
      }
      return array(
         'listSkemaCicilan' => $list,
         'totalCicilan' => $total_cicilan,
         'totalAmount' => $total_amount
      );
   }

   function getTotalCicilan($paket_transaction_id)
   {
      $this->db->select('total_paket_price, down_payment')
         ->from('paket_transaction')
         ->where('id', $paket_transaction_id);
      $q = $this->db->get();
      $total_cicilan = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $total_cicilan = $row->total_paket_price - $row->down_payment;
         }
      }
      return $total_cicilan;
   }

   # get info handover barang
   function getInfoHandOverBarang($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('id, item_name, status, date_taken, date_returned,
                        giver_handover,
                        p.fullname AS receiver_handover,
                        p2.fullname AS giver_returned,
                        receiver_returned ')
         ->from('handover_item AS h')
         ->join('personal AS p', 'h.receiver_handover=p.personal_id', 'left')
         ->join('personal AS p2', 'h.giver_returned=p2.personal_id', 'left')
         ->where('h.paket_transaction_id', $paket_transaction_id)
         ->where('h.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      $list_barang = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_barang[$row->id] = array(
               'id' => $row->id,
               'item_name' => $row->item_name,
               'status' => $row->status,
               'giver_handover' => $row->giver_handover,
               'receiver_handover' => $row->receiver_handover,
               'giver_returned' => $row->giver_returned != null ? $row->giver_returned : '-',
               'receiver_returned' => $row->receiver_returned != '' ? $row->receiver_returned : '-',
               'date_taken' => $row->date_taken,
               'date_returned' => $row->date_returned
            );
         }
      }
      return $list_barang;
   }

   function checkIfJamaahExist($jamaah_id)
   {
      $this->db->select('id')
         ->from('jamaah')
         ->where('id', $jamaah_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   # @return true if exist
   function checkInvoiceHandover($invoice)
   {
      $this->db->select('invoice_handover')
         ->from('handover_item')
         ->where('invoice_handover', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # return true if exist
   function checkInvoiceReturned($invoice)
   {
      $this->db->select('invoice_returned')
         ->from('handover_item')
         ->where('invoice_returned', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function getInfoHandOverFasilitas($paket_transaction_id, $jamaah_id)
   {
      #
      $this->db->select('hf.id, hf.invoice, hf.facilities_id, m.facilities_name, hf.receiver_name, hf.receiver_identity, hf.date_transaction, p.fullname')
         ->from('handover_facilities AS hf')
         ->join('mst_facilities AS m', 'hf.facilities_id=m.id', 'inner')
         ->join('base_users AS u', 'hf.officer=u.user_id', 'inner')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner')
         ->where('hf.paket_transaction_id', $paket_transaction_id)
         ->where('hf.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      $list_id = array();
      $list_fasilitas = array();
      $list_barang = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_id[] = $row->id;
            $list_barang[] = array(
               'id' => $row->id,
               'invoice' => $row->invoice,
               'facilities_id' => $row->facilities_id,
               'facilities_name' => $row->facilities_name,
               'petugas' => $row->fullname,
               'receiver_name' => $row->receiver_name,
               'receiver_identity' => $row->receiver_identity,
               'date_transaction' => $row->date_transaction
            );
         }
      }
      # get fasilitas
      $this->db->select('id, facilities_name')
         ->from('mst_facilities');
      if (count($list_id) > 0) {
         $this->db->where_not_in('id', $list_id);
      }

      $q = $this->db->get();
      $list_fasilitas = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list_fasilitas[] = array('id' => $row->id, 'name' => $row->facilities_name);
         }
      }
      return array('list_barang' => $list_barang, 'list_fasilitas' => $list_fasilitas);
   }

   function checkInvoiceFasilitas($invoice)
   {
      $this->db->select('invoice')
         ->from('handover_facilities')
         ->where('invoice', $invoice);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function getPaketNotThis($paket_id, $jamaah_id)
   {

      # paket jamaah
      $this->db->select('pt.paket_id')
         ->from('paket_transaction_jamaah AS ptj')
         ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
         ->where('ptj.jamaah_id', $jamaah_id);
      $q = $this->db->get();
      $listPaketJamaah = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if (!in_array($row->paket_id, $listPaketJamaah)) {
               $listPaketJamaah[] = $row->paket_id;
            }
         }
      }

      # paket
      $this->db->select('id, paket_name')
         ->from('paket')
         ->where(' id != "' . $paket_id . '"')
         ->where('departure_date >= NOW()')
         ->where_not_in('id', $listPaketJamaah);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->id, 'paket_name' => $row->paket_name);
         }
      }
      return $list;
   }

   function getInfoJamaah($jamaah_id)
   {
      $this->db->select('j.id, p.fullname')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['id'] = $row->id;
            $feedBack['fullname'] = $row->fullname;
         }
      }
      return $feedBack;
   }

   function getInfoPaketSekarang($paket_transaction_id)
   {
      $this->db->select('p.paket_name, pt.price_per_pax, pt.total_paket_price')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $transaksiPembayaran = $this->getHistoryPaketTransaction($paket_transaction_id, $row->total_paket_price);
            $feedBack['paket_sekarang'] = $row->paket_name;
            $feedBack['total_harga_paket_sekarang'] = 'Rp. ' . number_format($row->total_paket_price);
            $feedBack['harga_per_paket_sekarang'] = 'Rp. ' . number_format($row->price_per_pax);
            $feedBack['biaya_yang_sudah_dibayar_sekarang'] = 'Rp. ' . number_format($transaksiPembayaran['sudahBayar']);
            $feedBack['sisa_pembayaran_sekarang'] = 'Rp. ' . number_format($transaksiPembayaran['sisaBayar']);
         }
      }
      return $feedBack;
   }

   function getTransaksiPaketSudahBayar($paket_transaction_id)
   {
      $this->db->select('paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      $bayar = 0;
      $refund = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $bayar = $bayar + $row->paid;
            }
            if ($row->ket == 'refund') {
               $refund = $refund + $row->paid;
            }
         }
      }
      $sudahBayar = $bayar - $refund;
      return $sudahBayar;
   }

   function getHistoryPaketTransaction($paket_transaction_id, $total_pembayaran)
   {
      $sudahBayar = $this->getTransaksiPaketSudahBayar($paket_transaction_id);
      return array('sudahBayar' => $sudahBayar, 'sisaBayar' => $total_pembayaran - $sudahBayar);
   }

   function getInfoTipePaket($paket_id)
   {
      $this->db->select('pp.paket_type_id, pp.price, mp.paket_type_name')
         ->from('paket_price AS pp')
         ->join('mst_paket_type AS mp', 'pp.paket_type_id=mp.id', 'inner')
         ->where('pp.paket_id', $paket_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[] = array('id' => $row->paket_type_id, 'paket_type_name' => $row->paket_type_name, 'price' => $row->price);
         }
      }
      return $feedBack;
   }

   function checkTipePaketPrice($paket_id, $paket_type_id)
   {
      $this->db->select('paket_id')
         ->from('paket_price')
         ->where('paket_id', $paket_id)
         ->where('paket_type_id', $paket_type_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function getPriceByPaketTipePaket($paket_id, $tipe_paket_id, $biaya_yang_dipindah, $paket_transaction_id_now, $refund = 0, $jamaah_id)
   {
      $this->db->select('pp.price, p.mahram_fee')
         ->from('paket_price AS pp')
         ->join('paket AS p', 'pp.paket_id=p.id', 'inner')
         ->where('pp.paket_id', $paket_id)
         ->where('pp.paket_type_id', $tipe_paket_id);
      $q = $this->db->get();
      $price = 0;
      $mahram_fee = 0;
      $biaya_mahram = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $price = $row->price;
            $mahram_fee = $row->mahram_fee;
         }
      }

      $sisa_pembayaran = ($price + $refund) - $biaya_yang_dipindah;
      if ($this->model_paket->getInfoNeedMahram($jamaah_id)) {
         $sisa_pembayaran = $sisa_pembayaran + $mahram_fee;
         $biaya_mahram = $mahram_fee;
      }
      $pembayaran_berlebih = $biaya_yang_dipindah - ($price + $refund);

      return array(
         'harga_paket_tujuan' => $price,
         'sisa_pembayaran' =>  $sisa_pembayaran < 0 ? 0 : $sisa_pembayaran,
         'pembayaran_berlebih' => $pembayaran_berlebih < 0 ? 0 : $pembayaran_berlebih,
         'biaya_mahram' => $biaya_mahram
      );
   }

   // check jamaah if in paket
   public function checkJamaahInPaket($jamaah_id, $paket_transaction_id)
   {
      $this->db->select('jamaah_id')
         ->from('paket_transaction_jamaah')
         ->where('paket_transaction_id', $paket_transaction_id)
         ->where('jamaah_id', $jamaah_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function checkTotalJamaahNotIn($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('jamaah_id')
         ->from('paket_transaction_jamaah')
         ->where('paket_transaction_id', $paket_transaction_id)
         ->where('jamaah_id != "' . $jamaah_id . '" ');
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get info paket
   function getInfoPaketAsal($paket_transaction_id, $jamaah_id)
   {
      $this->db->select('p.id AS paket_id, p.kode, p.paket_name, p.mahram_fee, pt.paket_type_id, pt.no_register,
                         pt.price_per_pax, mpt.paket_type_name')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
         ->where('pt.id', $paket_transaction_id);
      $q = $this->db->get();
      $feedBack = array();
      $mahram_fee = 0;
      $price_per_pax = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['kode'] = $row->kode;
            $feedBack['paket_id'] = $row->paket_id;
            $feedBack['paket_transaction_id'] = $paket_transaction_id;
            $feedBack['paket_name'] = $row->paket_name;
            $feedBack['no_register'] = $row->no_register;
            $feedBack['price_per_pax'] = $row->price_per_pax;
            $feedBack['paket_type_name'] = $row->paket_type_name;
            $feedBack['paket_type_id'] = $row->paket_type_id;
            $mahram_fee = $row->mahram_fee;
            $price_per_pax = $row->price_per_pax;
         }
      }
      // kalkulasi paket lama
      $this->db->select('jamaah_id')
         ->from('paket_transaction_jamaah')
         ->where('paket_transaction_id', $paket_transaction_id)
         ->where('jamaah_id != "' . $jamaah_id . '"');
      $q = $this->db->get();
      $num = $q->num_rows();
      if ($num > 0) {
         $feedBack['kalkulasiPaketLama'] = true;
         $jamaah_needMahram = 0;
         foreach ($q->result() as $rows) {
            $feedBackMahram = $this->model_paket->getInfoNeedMahram($rows->jamaah_id);
            if ($feedBackMahram === TRUE) {
               $jamaah_needMahram++;
            }
         }
         # total jamaah yang membutuhkan mahram
         $totalBiayaMahram = $mahram_fee * $jamaah_needMahram;
         $totalPaketPrice = ($num * $price_per_pax) + $totalBiayaMahram;

         $feedBack['total_mahram_fee'] = $totalBiayaMahram;
         $feedBack['total_paket_price'] = $totalPaketPrice;
      } else {
         $feedBack['kalkulasiPaketLama'] = false;
      }

      return $feedBack;
   }

   function getInfoJamaahPindahPaket($jamaah_id)
   {
      $this->db->select('p.fullname, p.address, p.no_hp')
         ->from('jamaah AS j')
         ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
         ->where('j.id', $jamaah_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['fullname'] = $row->fullname;
            $feedBack['address'] = $row->address;
            $feedBack['no_hp'] = $row->no_hp;
            $feedBack['jamaah_id'] = $jamaah_id;
            $feedBack['needMahram'] = $this->getInfoNeedMahram($jamaah_id);
         }
      }
      return $feedBack;
   }

   function getInfoPaketTujuan($paket_id, $tipe_aksi, $tipe_noreg, $jamaah_id)
   {
      if ($tipe_aksi == 0) {
         $no_register = $this->text_ops->get_no_register();
         $this->db->select('p.id AS paket_id, pc.price, p.kode, p.paket_name, p.mahram_fee, mpt.paket_type_name')
            ->from('paket_price AS pc')
            ->join('paket AS p', 'pc.paket_id=p.id', 'inner')
            ->join('mst_paket_type AS mpt', 'pc.paket_type_id=mpt.id', 'inner')
            ->where('pc.paket_id', $paket_id)
            ->where('pc.paket_type_id', $tipe_noreg);
      } else {
         $no_register = $tipe_noreg;
         $this->db->select('p.id AS paket_id, p.kode, p.paket_name, p.mahram_fee, mpt.paket_type_name,
                            pt.id AS paket_transaction_id, pt.paket_type_id, pt.price_per_pax,
                            pt.total_mahram_fee, pt.total_paket_price')
            ->from('paket_transaction AS pt')
            ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
            ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
            ->where('pt.no_register', $tipe_noreg);
      }
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack['kode'] = $row->kode;
            $feedBack['paket_id'] = $row->paket_id;
            $feedBack['paket_name'] = $row->paket_name;
            $feedBack['no_register'] = $no_register;
            $feedBack['mahram_fee'] = $row->mahram_fee;
            if ($tipe_aksi == 0) {
               $feedBack['price_per_pax'] = $row->price;
            } else {
               $feedBack['price_per_pax'] = $row->price_per_pax;
            }

            $feedBack['paket_type_name'] = $row->paket_type_name;

            if ($tipe_aksi == 1) {
               $feedBack['updatePaketTujuan'] = true;
               $total_paket_price = $row->total_paket_price + $row->price_per_pax;
               $total_mahram_fee = $row->total_mahram_fee;
               if ($this->getInfoNeedMahram($jamaah_id) === TRUE) {
                  $total_mahram_fee = $total_mahram_fee + $row->mahram_fee;
               }
               $feedBack['paket_transaction_id'] = $row->paket_transaction_id;
               $feedBack['total_mahram_fee'] = $total_mahram_fee;
               $feedBack['total_paket_price'] = $total_paket_price;
            } else {
               $feedBack['updatePaketTujuan'] = false;
            }

            if ($tipe_aksi == 0) {
               $feedBack['paket_type_id'] = $tipe_noreg;
            } else {
               $feedBack['paket_type_id'] = $row->paket_type_id;
            }
         }
      }
      return $feedBack;
   }

   // info get info no register
   function getInfoNoRegister($paket_id)
   {
      $this->db->select('id, no_register, price_per_pax')
         ->from('paket_transaction')
         ->where('paket_id', $paket_id);
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $feedBack[] = array('id' => $row->id, 'no_register' => $row->no_register, 'price' => $row->price_per_pax);
         }
      }
      return $feedBack;
   }

   function generatedKodePaket()
   {
      $my_this = &get_instance();
      $feedBack = false;
      $rand = '';
      do {
         $rand = random_num(5);
         $q = $my_this->db->select('kode')
            ->from('paket')
            ->where('kode', $rand)
            ->get();
         if ($q->num_rows() == 0) {
            $feedBack = true;
         }
      } while ($feedBack == false);
      return $rand;
   }


   function ckKodePaket($kode_paket)
   {
      $this->db->select('kode')
         ->from('paket')
         ->where('kode', $kode_paket);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function checkNoRegisterPaketPrice($paket_id, $no_register)
   {
      $this->db->select('paket_id')
         ->from('paket_transaction')
         ->where('paket_id', $paket_id)
         ->where('no_register', $no_register);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   function getPriceByPaketNoRegister($paket_id, $no_register, $biaya_yang_dipindah, $paket_transaction_id_now, $refund = 0, $jamaah_id)
   {
      // get info
      $this->db->select('pt.id, pt.total_paket_price, pt.price_per_pax, p.mahram_fee')
         ->from('paket_transaction AS pt')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('pt.no_register', $no_register);
      $q = $this->db->get();
      $total_paket_price = 0;
      $price = 0;
      $mahram_fee = 0;
      $biaya_mahram = 0;
      $paket_transaction_id = 0;
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $paket_transaction_id = $row->id;
            $total_paket_price = $row->total_paket_price;
            $price = $row->price_per_pax;
            $mahram_fee = $row->mahram_fee;
         }
      }

      $sudahBayar = 0;
      $this->db->select('paid, ket')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $refundPT = 0;
         $pindahPT = 0;
         $cashPT = 0;
         foreach ($q->result() as $row) {
            if ($row->ket == 'cash') {
               $cashPT = $cashPT + $row->paid;
            } elseif ($row->ket == 'refund') {
               $refundPT = $refundPT + $row->paid;
            } elseif ($row->ket == 'pindah_paket') {
               $pindahPT = $pindahPT + $row->paid;
            }
         }
         $sudahBayar = $cashPT - ($refundPT + $pindahPT);
      }

      if ($this->model_paket->getInfoNeedMahram($jamaah_id)) {
         $biaya_mahram = $mahram_fee;
      }

      $totalBiayaJamaah = $price + $biaya_mahram;
      $totalPembayaranSekarang = $total_paket_price + $totalBiayaJamaah + $refund;
      $sisa_pembayaran = $totalPembayaranSekarang  - ($sudahBayar + $biaya_yang_dipindah);
      $pembayaran_berlebih = $sudahBayar + $biaya_yang_dipindah - $totalPembayaranSekarang;

      return array(
         'harga_paket_tujuan' => $price,
         'sisa_pembayaran' =>  $sisa_pembayaran < 0 ? 0 : $sisa_pembayaran,
         'pembayaran_berlebih' => $pembayaran_berlebih < 0 ? 0 : $pembayaran_berlebih,
         'biaya_mahram' => $biaya_mahram
      );
   }

   # check tipe paket or no register if exist
   function checkTipeNoRegisterTujuan($paket_id, $tipe_aksi, $tipe_no_register)
   {
      if ($tipe_aksi == 0) {
         $this->db->select('price')
            ->from('paket_price')
            ->where('paket_id', $paket_id)
            ->where('paket_type_id', $tipe_no_register);
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            return TRUE;
         } else {
            return FALSE;
         }
      } elseif ($tipe_aksi == 1) {
         $this->db->select('id')
            ->from('paket_transaction')
            ->where('paket_id', $paket_id)
            ->where('no_register', $tipe_no_register);
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            return TRUE;
         } else {
            return FALSE;
         }
      } else {
         return FALSE;
      }
   }

   function get_tanda_tangan()
   {
      $this->db->select('p.fullname, u.user_id, g.nama_group')
         ->from('base_users AS u')
         ->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
         ->join('personal AS p', 'u.personal_id=p.personal_id', 'inner');
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $list[] = array('id' => $row->user_id, 'fullname' => $row->fullname, 'jabatan' => $row->nama_group);
         }
      }
      return  $list;
   }

   function checkTandaTangan($user_id)
   {
      $this->db->select('user_id')
         ->from('base_users')
         ->where('user_id', $user_id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return TRUE;
      } else {
         return FALSE;
      }
   }

   # nama group
   function getTandaTanganName($user_id)
   {
      $this->db->select('nama_group')
         ->from('base_users AS u')
         ->join('base_groups AS g', 'u.group_id=g.group_id', 'inner')
         ->where('u.user_id', $user_id);
      $q = $this->db->get();
      $nama_group = '';
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $nama_group = $row->nama_group;
         }
      }
      return $nama_group;
   }
}
