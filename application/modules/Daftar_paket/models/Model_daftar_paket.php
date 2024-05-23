<?php

/**
 *  -----------------------
 *	Model daftar paket
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_daftar_paket extends CI_Model
{
   private $company_id;
   private $status;
   private $content;

   public function __construct()
   {
      parent::__construct();
      $this->company_id = $this->session->userdata($this->config->item('apps_name'))['company_id'];
   }

   function get_provider_visa(){
      $this->db->select('id, nama_provider')
         ->from('mst_provider')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $return[0] = array('id' => 0, 'name' => '-- Pilih Provider Visa --');
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $return[] = array('id' => $rows->id, 'name' => $rows->nama_provider);
         }
      }
      return $return;
   }

   # check provider visa id
   function check_provider_visa_id( $id ) {
      $this->db->select('id')
         ->from('mst_provider')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   function get_asuransi(){
      $this->db->select('id, nama_asuransi')
         ->from('mst_asuransi')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $return[0] = array('id' => 0, 'name' => '-- Pilih Asuransi --');
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS $rows ) {
            $return[] = array('id' => $rows->id, 'name' => $rows->nama_asuransi);
         }
      }
      return $return;
   }

   # check asuransi id
   function check_asuransi_id( $id ) {
      $this->db->select('id')
         ->from('mst_asuransi')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if( $q->num_rows() > 0 ){
         return true;
      }else{
         return false;
      }
   }

   # get total daftar paket
   function get_total_daftar_paket($search, $status)
   {
      $this->db->select('id')
         ->from('paket')
         ->where('company_id', $this->company_id);
      if ($status != 'semua') {
         if ($status == 'belum_berangkat') {
            $this->db->where('departure_date > NOW()');
         } elseif ($status == 'sudah_berangkat') {
            $this->db->where('departure_date <= NOW()');
         }
      }
      if ($search != '' or $search != null or !empty($search)) {
         $this->db->group_start()
            ->like('kode', $search)
            ->or_like('paket_name', $search)
            ->group_end();
      }
      $r    = $this->db->get();
      return $r->num_rows();
   }

   # get index daftar paket
   function get_index_daftar_paket($limit = 6, $start = 0, $search = '', $status)
   {
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
         ->where('company_id', $this->company_id);
      if ($status != 'semua') {
         if ($status == 'belum_berangkat') {
            $this->db->where('departure_date > NOW()');
         } elseif ($status == 'sudah_berangkat') {
            $this->db->where('departure_date <= NOW()');
         }
      }
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
            //echo $rows->kode."<br>";
            $list[] = array(
               'id' => $rows->id,
               'kode' => $rows->kode,
               'status_keberangkatan' => ($rows->departure_date <= date('Y-m-d') ? '<div class="mt-3" style="color:red;font-weight:bold;">SUDAH BERANGKAT</div>' : ''),
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

   # paket type
   function get_paket_type()
   {
      $this->db->select('id, paket_type_name')
         ->from('mst_paket_type')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[] = array('id' => $row->id, 'name' => $row->paket_type_name);
         }
      }
      return $return;
   }

   # get fasilitas paket
   function get_fasilitas_paket()
   {
      $this->db->select('id, facilities_name')
         ->from('mst_facilities')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $return[] = array('id' => $rows->id, 'name' => $rows->facilities_name);
         }
      }
      return $return;
   }

   # kota kunjungan
   function get_kota()
   {
      $this->db->select('id, city_name, city_code')
         ->from('mst_city')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $return[] = array('id' => $rows->id, 'name' => $rows->city_name . ' (' . $rows->city_code . ')');
         }
      }
      return $return;
   }

   # get airlines
   function get_airlines()
   {
      $this->db->select('id, airlines_name')
         ->from('mst_airlines')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $return[] = array('id' => $rows->id, 'name' => $rows->airlines_name);
         }
      }
      return $return;
   }

   # get hotel
   function get_hotel()
   {
      $this->db->select('id, hotel_name')
         ->from('mst_hotel')
         ->where('company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $return[] = array('id' => $rows->id, 'name' => $rows->hotel_name);
         }
      }
      return $return;
   }

   # get muthawif
   function get_muthawif()
   {
      $this->db->select('m.id, p.fullname')
         ->from('muthawif AS m')
         ->join('personal AS p', 'm.personal_id=p.personal_id', 'inner')
         ->where('m.company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[] = array('id' => $row->id, 'name' => $row->fullname);
         }
      }
      return $return;
   }

   # get beranda
   function get_bandara()
   {
      $this->db->select('ma.id, ma.airport_name, c.city_name, c.city_code')
         ->from('mst_airport AS ma')
         ->join('mst_city AS c', 'ma.city_id=c.id', 'inner')
         ->where('ma.company_id', $this->company_id);
      $q = $this->db->get();
      $return = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            $return[] = array(
               'id' => $row->id,
               'name' => $row->airport_name . ' (Kota:' . $row->city_name . ' -- ' . $row->city_code . ')'
            );
         }
      }
      return $return;
   }

   function check_paket_id_exist($id)
   {
      $this->db->select('id')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   function get_list_tipe_paket_id()
   {
      $this->db->select('id')
         ->from('mst_paket_type')
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

   # get list muthawif
   function get_list_muthawif_id()
   {
      $this->db->select('id')
         ->from('muthawif')
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

   # get list airport
   function get_list_airport()
   {
      $this->db->select('id')
         ->from('mst_airport')
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

   # get list fasilitas
   function get_list_fasilitas()
   {
      $this->db->select('id')
         ->from('mst_facilities')
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

   # get list kota
   function get_list_kota()
   {
      $this->db->select('id')
         ->from('mst_city')
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

   # get list airlines
   function get_list_airlines()
   {
      $this->db->select('id')
         ->from('mst_airlines')
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

   # get list hotel
   function get_list_hotel()
   {
      $this->db->select('id')
         ->from('mst_hotel')
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

   # get photo name
   function get_photo_name($id)
   {
      $this->db->select('photo')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('id', $id);
      $q = $this->db->get();
      $photo_name = '';
      if ($q->num_rows() > 0) {
         $photo_name = $q->row()->photo;
      }
      return $photo_name;
   }

   # check kode paket is exist in database
   function check_kode_paket_is_exist($kode_paket)
   {
      $this->db->select('id')
         ->from('paket')
         ->where('company_id', $this->company_id)
         ->where('kode', $kode_paket);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         return true;
      } else {
         return false;
      }
   }

   # get paket price
   function get_info_edit_paket($id)
   {

      $this->db->select('p.id, p.jenis_kegiatan, p.kode, p.provider_id, p.asuransi_id, p.no_polis, 
                         p.tgl_input_polis, p.tgl_awal_polis, p.tgl_akhir_polis, p.photo, p.slug, p.paket_name, p.description,
                         p.departure_date, p.return_date, p.departure_from, p.duration_trip, p.mahram_fee,
                         p.jamaah_quota, p.city_visited, p.airlines, p.hotel, p.facilities, p.show_homepage,
                         p.airport_departure, p.airport_destination, p.departure_time, p.time_arrival, p.tutup_paket, p.input_date, p.last_update,
                         (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', pi.activity_date, pi.activity_title, pi.description ) SEPARATOR \';\')
                            FROM paket_itinerary AS pi
                            WHERE pi.company_id="' . $this->company_id . '"  AND
                            pi.paket_id=p.id) AS paket_itinerary,
                         (SELECT GROUP_CONCAT( muthawif_id SEPARATOR \';\')
                            FROM paket_muthawif AS pw
                            WHERE pw.company_id="' . $this->company_id . '"  AND
                            pw.paket_id=p.id) AS paket_muthawif,
                         (SELECT GROUP_CONCAT( CONCAT_WS(\'$\', pp.paket_type_id, pp.price ) SEPARATOR \';\')
                           FROM paket_price AS pp
                           WHERE pp.company_id="' . $this->company_id . '"  AND
                           pp.paket_id=p.id) AS paket_price')
         ->from('paket AS p')
         ->where('p.company_id', $this->company_id)
         ->where('p.id', $id);
      $q = $this->db->get();
      $list = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $paket_price = array();
            if ($rows->paket_price != '') {
               foreach (explode(';', $rows->paket_price) as $key => $value) {
                  $exp = explode('$', $value);
                  $paket_price[$exp[0]] = $exp[1];
               }
            }

            $paket_itinerary = array();
            if ($rows->paket_itinerary != '') {
               foreach (explode(';', $rows->paket_itinerary)  as $key => $value) {
                  $exp = explode('$', $value);
                  $paket_itinerary[] = array('activity_date' => $this->text_ops->change_to_date_time_local($exp[0]), 'activity_title' => $exp[1],  'description' => $exp[2]);
               }
            }

            $list['id'] = $rows->id;
            $list['jenis_kegiatan'] = $rows->jenis_kegiatan;
            $list['kode'] = $rows->kode;
            $list['photo'] = $rows->photo;
            $list['paket_name'] = $rows->paket_name;
            $list['description'] = $rows->description;
            $list['departure_date'] = $rows->departure_date;
            $list['return_date'] = $rows->return_date;
            $list['departure_from'] = $rows->departure_from;
            $list['mahram_fee'] = $rows->mahram_fee;
            $list['jamaah_quota'] = $rows->jamaah_quota;
            $list['city_visited'] = unserialize($rows->city_visited);
            $list['airlines'] = unserialize($rows->airlines);
            $list['hotel'] = unserialize($rows->hotel);
            $list['facilities'] = unserialize($rows->facilities);
            $list['show_homepage'] = ($rows->show_homepage == 'tampilkan' ? 1 : 0);
            $list['departure_time'] = $this->text_ops->change_to_date_time_local($rows->departure_time);
            $list['time_arrival'] = $this->text_ops->change_to_date_time_local($rows->time_arrival);
            $list['paket_price'] = $paket_price;
            $list['airport_departure'] = $rows->airport_departure;
            $list['airport_destination'] = $rows->airport_destination;
            $list['provider_id'] = $rows->provider_id;
            $list['asuransi_id'] = $rows->asuransi_id;
            $list['no_polis'] = $rows->no_polis;
            $list['tgl_input_polis'] = $rows->tgl_input_polis;
            $list['tgl_awal_polis'] = $rows->tgl_awal_polis;
            $list['tgl_akhir_polis'] = $rows->tgl_akhir_polis;
            $list['paket_muthawif'] = explode(';', $rows->paket_muthawif);
            $list['itinerary'] = $paket_itinerary;
            $list['tutup_paket'] = $rows->tutup_paket;
         }
      }
      return $list;
   }
}
