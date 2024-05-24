<?php

/**
 *  -----------------------
 *	Model download
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_download extends CI_Model
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

   function total_deposit_tabungan_umrah($pool_id)
   {
      $this->db->select("d.debet, d.kredit, pd.transaction_status")
         ->from('pool_deposit_transaction AS pd')
         ->join('pool AS p', 'pd.pool_id=p.id', 'inner')
         ->join('deposit_transaction AS d', 'pd.deposit_transaction_id=d.id', 'inner')
         ->where('pd.pool_id', $pool_id)
         ->where('pd.company_id', $this->company_id);
      $q = $this->db->get();
      $total = 0;
      $refund = 0;
      if( $q->num_rows() > 0 ) {
         foreach( $q->result() AS  $rows ) {
            if( $rows->transaction_status == 'cash' ) {
               $total = $total + $rows->debet;
            }
            if( $rows->transaction_status == 'refund' ) {
                $refund = $refund + $rows->kredit;
            }
         }
      }
      return $total - $refund;
   }

   function model_download_absensi_kamar($sesi){
      $this->db->distinct()
               ->select('h.city_id, c.city_name')
               ->from('rooms_jamaah AS rj')
               ->join('rooms AS r', 'rj.room_id=r.id', 'inner')
               ->join('mst_hotel AS h', 'r.hotel_id=h.id', 'inner')
               ->join('mst_city AS c', 'h.city_id=c.id', 'inner')
               ->where('rj.company_id', $this->company_id)
               ->where('r.paket_id', $sesi['paket_id']);
         $q = $this->db->get();
         $city = array();
         if( $q->num_rows() > 0 ) {
            foreach( $q->result() AS $rows ){
               $city[] = array('city_id' => $rows->city_id, 'city_name' => $rows->city_name);
            }
         }
         $html = '<tr>
                     <td rowspan="2" style="text-align:center;"><center>NO</center></td>
                     <td rowspan="2"><center>FULLNAME</center></td>
                     <td rowspan="2"><center>SEX</center></td>
                     <td rowspan="2"><center>TYPE</center></td>
                     <td colspan="'.count($city).'"><center>NO KAMAR</center></td>
                  </tr>';
         $html .= '<tr>';
         foreach ($city as $key => $value) {
            $html .= '<td><center>'.$value['city_name'].'</center></td>';
         }         
         $html .= '</tr>';

         $this->db->select('id, room_type')
                  ->from('rooms')
                  ->where('paket_id', $sesi['paket_id']);
         $q = $this->db->get();
         if( $q->num_rows() > 0 ) {
            $num = 1;
            $firts = 0;
            foreach ($q->result() as $rows) {
               $this->db->select('p.fullname, mpt.paket_type_name')
                  ->from('rooms_jamaah AS rj')
                  ->join('paket_transaction_jamaah AS ptj', 'rj.jamaah_id=ptj.jamaah_id', 'inner')
                  ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
                  ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
                  ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
                  ->join('mst_paket_type AS mpt', 'pt.paket_type_id=mpt.id', 'inner')
                  ->where('rj.room_id', $rows->id);
               $r = $this->db->get();
               if( $r->num_rows() > 0) {
                  // $no
                  foreach ($r->result() as $rowr ) {
                     $html .= '<tr>
                                  <td>'.($num != $firts ? $num : '' ).'</td>
                                  <td>'.$rowr->fullname.'</td>
                                  <td>'.$rows->room_type.'</td>
                                  <td>'.$rowr->paket_type_name.'</td>';
                        foreach ($city as $key => $value) {
                           $html .= '<td></td>';
                        }         
                     $html .= '</tr>';
                     $firts = $num;
                  }

               }
               $num++;   
            }
         }         
         return $html ;
   }

   function model_download_manifest_tabungan_umrah($sesi)
   {
      $this->db->select('po.id, p.identity_number, p.fullname, p.gender, p.birth_place, p.birth_date, j.title, 
                        j.father_name, p.address, 
                        v.name AS kelurahan, d.name AS kecamatan, r.name AS kabupaten_kota, prov.name AS provinsi,
                        pkjr.nama_pekerjaan, j.pasport_name,
                        j.kewarganegaraan, j.jenis_identitas, j.status_nikah, 
                        j.telephone, p.nomor_whatsapp, p.email, mp.nama_pendidikan, j.passport_number, 
                        j.passport_dateissue, j.validity_period, j.passport_place')
      ->from('pool AS po')
      ->join('jamaah AS j', 'po.jamaah_id=j.id', 'inner')
      ->join('reg_villages AS v', 'j.kelurahan_id=v.id', 'left')
      ->join('reg_districts AS d', 'v.district_id=d.id', 'left')
      ->join('reg_regencies AS r', 'd.regency_id=r.id', 'left')
      ->join('reg_provinces AS prov', 'r.province_id=prov.id', 'left')
      ->join('mst_pekerjaan AS pkjr', 'j.pekerjaan_id=pkjr.id', 'left')
      ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
      ->join('mst_pendidikan AS mp', 'j.last_education=mp.id_pendidikan', 'left')
      ->where('po.company_id', $this->company_id);
      
      if( isset($sesi['filter']) AND isset ( $sesi['filter']['filterTransaksi'] ) ) {
         $this->db->where('po.active', $sesi['filter']['filterTransaksi'] == 'belum' ? 'active' : 'non_active');
      }

      if( isset( $sesi['filter'] ) AND isset ( $sesi['filter']['search'] ) 
            AND $sesi['filter']['search'] != '' OR $sesi['filter']['search'] != null 
            OR !empty($sesi['filter']['search'])) {
         $this->db->group_start()
            ->like('p.fullname', $sesi['filter']['search'])
            ->or_like('p.identity_number', $sesi['filter']['search'])
            ->group_end();
      }
   
      $html = '<tr>
                  <th>NO</th>
                  <th>TITLE</th>
                  <th>NAMA</th>
                  <th>NAMA AYAH</th>
                  <th>JENIS IDENTITAS</th>
                  <th>NO IDENTITAS</th>
                  <th>NAMA PASPOR</th>
                  <th>NO PASPOR</th>
                  <th>TANGGAL DIKELUARKAN PASPOR</th>
                  <th>KOTA PASPOR</th>
                  <th>TEMPAT LAHIR</th>
                  <th>TANGGAL LAHIR</th>
                  <th>ALAMAT</th>
                  <th>PROVINSI</th>
                  <th>KABUPATEN</th>
                  <th>KECAMATAN</th>
                  <th>KELURAHAN</th>
                  <th>NO TELEPON</th>
                  <th>NO HP</th>
                  <th>KEWARGANEGARAAN</th>
                  <th>STATUS PERNIKAHAN</th>
                  <th>PENDIDIKAN</th>
                  <th>PEKERJAAN</th>
                  <th>PROVIDER VISA</th>
                  <th>NO VISA</th>
                  <th>TANGGAL BERLAKU VISA</th>
                  <th>TANGGAL AKHIR VISA</th>
                  <th>ASURANSI</th>
                  <th>NO POLIS</th>
                  <th>TANGGAL INPUT POLIS</th>
                  <th>TANGGAL AWAL POLIS</th>
                  <th>TANGGAL AKHIR POLIS</th>
                  <th>TOTAL TABUNGAN UMRAH</th>
               </tr>';

      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         $n = 1;
         foreach( $q->result()  AS $rows ) {
            $umur = $this->date_ops->get_umur($rows->birth_date);
            if($umur < 2 ){
               $kat_umur = 'Bayi';
            }elseif ($umur >= 2 AND $umur < 12) {
               $kat_umur = 'Anak';
            }else{
               $kat_umur = 'Dewasa';
            }
            $total = $this->total_deposit_tabungan_umrah($rows->id) ;
            //ketegori usia
            $html .= '<tr>
                        <td>'.$n.'</td>
                        <td>'.$rows->title.'</td>
                        <td>'.$rows->fullname.'</td>
                        <td>'.$rows->father_name.'</td>
                        <td>'.$rows->jenis_identitas.'</td>
                        <td>'.$rows->identity_number.'</td>
                        <td>'.$rows->pasport_name.'</td>
                        <td>'.$rows->passport_number.'</td>
                        <td>'.$rows->passport_dateissue.'</td>
                        <td>'.$rows->passport_place.'</td>
                        <td>'.$rows->birth_place.'</td>
                        <td>'.$rows->birth_date.'</td>
                        <td>'.$rows->address.'</td>
                        <td>'.$rows->provinsi.'</td>
                        <td>'.$rows->kabupaten_kota.'</td>
                        <td>'.$rows->kecamatan.'</td>
                        <td>'.$rows->kelurahan.'</td>
                        <td>'.$rows->telephone.'</td>
                        <td>'.$rows->nomor_whatsapp.'</td>
                        <td>'.$rows->kewarganegaraan.'</td>
                        <td>'.$rows->status_nikah.'</td>
                        <td>'.$rows->nama_pendidikan.'</td>
                        <td>'.$rows->nama_pekerjaan.'</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>'.$total.'</td>
                     </tr>';
            $n++;
         }
      }else{
         $html .= '<tr><td colspan="33"></td></tr>';
      }

      return  $html;
   }  

   function model_download_manifest($sesi)
   {
      $this->db->select('p.identity_number, p.fullname, p.gender, p.birth_place, p.birth_date, j.status_nikah, j.title, j.father_name, 
                         j.jenis_identitas, p.address, j.kewarganegaraan, pt.no_visa, pt.tgl_berlaku_visa, pt.tgl_akhir_visa,
                         j.telephone, p.nomor_whatsapp, p.email, mp.nama_pendidikan, j.passport_number, j.pasport_name,
                         v.name AS kelurahan, d.name AS kecamatan, rr.name AS kabupaten_kota, prov.name AS provinsi,
                         pkjr.nama_pekerjaan, as.nama_asuransi, pkt.no_polis, pkt.tgl_input_polis, pkt.tgl_awal_polis, pkt.tgl_akhir_polis,
                         mpov.nama_provider, j.passport_dateissue, j.validity_period, j.passport_place')
      ->from('paket_transaction_jamaah AS ptj')
      ->join('paket_transaction AS pt', 'ptj.paket_transaction_id=pt.id', 'inner')
      ->join('paket AS pkt', 'pt.paket_id=pkt.id', 'inner')
      ->join('mst_asuransi AS as', 'pkt.asuransi_id=as.id', 'left')
      ->join('mst_provider AS mpov', 'pkt.provider_id=mpov.id', 'left')
      ->join('jamaah AS j', 'ptj.jamaah_id=j.id', 'inner')
      ->join('reg_villages AS v', 'j.kelurahan_id=v.id', 'left')
      ->join('reg_districts AS d', 'v.district_id=d.id', 'left')
      ->join('reg_regencies AS rr', 'd.regency_id=rr.id', 'left')
      ->join('reg_provinces AS prov', 'rr.province_id=prov.id', 'left')
      ->join('mst_pekerjaan AS pkjr', 'j.pekerjaan_id=pkjr.id', 'left')
      ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
      ->join('rooms_jamaah AS rj', 'ptj.jamaah_id=rj.jamaah_id', 'left' )
      ->join('rooms AS r', 'rj.room_id=r.id', 'left')
      ->join('mst_pendidikan AS mp', 'j.last_education=mp.id_pendidikan', 'left')
      ->where('pkt.id', $sesi['paket_id'])
      ->where('ptj.company_id', $this->company_id);

      $html = '<tr>
                  <th>NO</th>
                  <th>TITLE</th>
                  <th>NAMA</th>
                  <th>NAMA AYAH</th>
                  <th>JENIS IDENTITAS</th>
                  <th>NO IDENTITAS</th>
                  <th>NAMA PASPOR</th>
                  <th>NO PASPOR</th>
                  <th>TANGGAL DIKELUARKAN PASPOR</th>
                  <th>TANGGAL EXPIRED PASPOR</th>
                  <th>KOTA PASPOR</th>
                  <th>TEMPAT LAHIR</th>
                  <th>TANGGAL LAHIR</th>
                  <th>ALAMAT</th>
                  <th>PROVINSI</th>
                  <th>KABUPATEN</th>
                  <th>KECAMATAN</th>
                  <th>KELURAHAN</th>
                  <th>NO TELEPON</th>
                  <th>NO HP</th>
                  <th>KEWARGANEGARAAN</th>
                  <th>STATUS PERNIKAHAN</th>
                  <th>PENDIDIKAN</th>
                  <th>PEKERJAAN</th>
                  <th>PROVIDER VISA</th>
                  <th>NO VISA</th>
                  <th>TANGGAL BERLAKU VISA</th>
                  <th>TANGGAL AKHIR VISA</th>
                  <th>ASURANSI</th>
                  <th>NO POLIS</th>
                  <th>TANGGAL INPUT POLIS</th>
                  <th>TANGGAL AWAL POLIS</th>
                  <th>TANGGAL AKHIR POLIS</th>
               </tr>';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         $n = 1;
         foreach( $q->result() AS $rows ){
            $umur = $this->date_ops->get_umur($rows->birth_date);
            if($umur < 2 ){
               $kat_umur = 'Bayi';
            }elseif ($umur >= 2 AND $umur < 12) {
               $kat_umur = 'Anak';
            }else{
               $kat_umur = 'Dewasa';
            }
            $html .= '<tr>
                        <td>'.$n.'</td>
                        <td>'.$rows->title.'</td>
                        <td>'.$rows->fullname.'</td>
                        <td>'.$rows->father_name.'</td>
                        <td>'.$rows->jenis_identitas.'</td>
                        <td>"'.$rows->identity_number.'"</td>
                        <td>'.$rows->pasport_name.'</td>
                        <td>'.$rows->passport_number.'</td>
                        <td>'.$rows->passport_dateissue.'</td>
                        <td>'.$rows->validity_period.'</td>
                        <td>'.$rows->passport_place.'</td>
                        <td>'.$rows->birth_place.'</td>
                        <td>'.$rows->birth_date.'</td>
                        <td>'.$rows->address.'</td>
                        <td>'.$rows->provinsi.'</td>
                        <td>'.$rows->kabupaten_kota.'</td>
                        <td>'.$rows->kecamatan.'</td>
                        <td>'.$rows->kelurahan.'</td>
                        <td>'.$rows->telephone.'</td>
                        <td>'.$rows->nomor_whatsapp.'</td>
                        <td>'.$rows->kewarganegaraan.'</td>
                        <td>'.$rows->status_nikah.'</td>
                        <td>'.$rows->nama_pendidikan.'</td>
                        <td>'.$rows->nama_pekerjaan.'</td>
                        <td>'.$rows->nama_provider.'</td>
                        <td>'.$rows->no_visa.'</td>
                        <td>'.$rows->tgl_berlaku_visa.'</td>
                        <td>'.$rows->tgl_akhir_visa.'</td>
                        <td>'.$rows->nama_asuransi.'</td>
                        <td>'.$rows->no_polis.'</td>
                        <td>'.$rows->tgl_input_polis.'</td>
                        <td>'.$rows->tgl_awal_polis.'</td>
                        <td>'.$rows->tgl_akhir_polis.'</td>
                     </tr>';
            $n++;
         }
      }else{
         $html .= '<tr><td colspan="32"></td></tr>';
      }
      return  $html;
   }

   # download buku besar
   function model_download_buku_besar($sesi)
   {
      # param
      $param = array('periode' =>  $sesi['periode'], 'akun' =>  $sesi['akun']);
      # Serial Number
      $this->db->select('ap.sn')
         ->from('akun_secondary AS as')
         ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
         ->where('as.company_id', $this->company_id)
         ->where('as.nomor_akun_secondary', $param['akun']);
      $q = $this->db->get();
      $sn = '';
      if ($q->num_rows() > 0) {
         $sn = $q->row()->sn;
      }
      # get saldo awal
      $this->db->select('s.saldo')
         ->from('saldo AS s')
         ->join('akun_secondary AS as', 's.akun_secondary_id=as.id', 'inner')
         ->where('s.company_id', $this->company_id)
         ->where('s.periode', $param['periode'])
         ->where('as.nomor_akun_secondary', $param['akun']);
      $q = $this->db->get();
      $saldo = 0;
      if ($q->num_rows() > 0) {
         $saldo = $q->row()->saldo;
      }
      # jurnal
      $this->db->select('id, ref, ket, akun_kredit, akun_debet, saldo, last_update')
         ->from('jurnal')
         ->where('company_id', $this->company_id);
      if (count($param) > 0) {
         $this->db->where('periode_id', $param['periode']);
         if (isset($param['akun']) and $param['akun'] != 0) {
            $this->db->group_start()
               ->where('akun_debet', $param['akun'])
               ->or_where('akun_kredit', $param['akun'])
               ->group_end();
         }
      }
      $this->db->order_by('id', 'desc');
      $q = $this->db->get();
      $html = '<tr>
                  <th>Tanggal Transaksi</th>
                  <th>Ref</th>
                  <th>Ket</th>
                  <th>Debet</th>
                  <th>Kredit</th>
                  <th>Saldo</th>
               </tr>';
      $total_debet = ($sn == "D" ? $saldo : 0);
      $total_kredit = ($sn == "K" ? $saldo : 0);
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $akun_kredit = 0;
            if ($param['akun'] == $rows->akun_kredit) {
               $akun_kredit = $rows->saldo;
            }
            $akun_debet = 0;
            if ($param['akun'] == $rows->akun_debet) {
               $akun_debet =  $rows->saldo;
            }
            if ($sn == 'D') {
               if ($param['akun'] == $rows->akun_kredit) {
                  $total_kredit = $total_kredit + $rows->saldo;
                  $saldo = $saldo - $rows->saldo;
               } elseif ($param['akun'] == $rows->akun_debet) {
                  $total_debet = $total_debet + $rows->saldo;
                  $saldo = $saldo + $rows->saldo;
               }
            } elseif ($sn == 'K') {
               if ($param['akun'] == $rows->akun_kredit) {
                  $saldo = $saldo + $rows->saldo;
               } elseif ($param['akun'] == $rows->akun_debet) {
                  $saldo = $saldo - $rows->saldo;
               }
            }
            $html .= '<tr>
                        <td>' . $rows->last_update . '</td>
                        <td>' . strip_tags($rows->ref) . '</td>
                        <td>' . $rows->ket . '</td>
                        <td>' . $akun_debet . '</td>
                        <td>' . $akun_kredit . '</td>
                        <td>' . $saldo . '</td>
                     </tr>';
         }
      }
      return  $html;
   }

   # get download neraca lajur
   function model_download_neraca_lajur($sesi)
   {
      # param
      $param = array('periode' =>  $sesi['periode']);
      # get periode name
      $periode_name = $this->periode_name($param['periode']);
      # periode id
      $salwo_awal = array();
      $this->db->select('akun_secondary_id, saldo')
         ->from('saldo')
         ->where('company_id', $this->company_id)
         ->where('periode', $param['periode']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      # jurnal
      $this->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $this->company_id)
         ->where('periode_id', $param['periode']);
      $q = $this->db->get();
      $akun_debet = array();
      $akun_kredit = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (isset($akun_debet[$rows->akun_debet])) {
               $akun_debet[$rows->akun_debet] = $akun_debet[$rows->akun_debet] + $rows->saldo;
            } else {
               $akun_debet[$rows->akun_debet] = $rows->saldo;
            }
            if (isset($akun_kredit[$rows->akun_kredit])) {
               $akun_kredit[$rows->akun_kredit] = $akun_kredit[$rows->akun_kredit] + $rows->saldo;
            } else {
               $akun_kredit[$rows->akun_kredit] = $rows->saldo;
            }
         }
      }
      # Serial Number
      $this->db->select('ap.sn, ap.pos, as.id, as.nomor_akun_secondary, as.nama_akun_secondary')
         ->from('akun_secondary AS as')
         ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
         ->where('as.company_id', $this->company_id);
      $q = $this->db->get();
      $list = array();
      $total = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal_debet =  $rows->sn == "D" ? (!isset($saldo_awal[$rows->id]) ? 0 : $saldo_awal[$rows->id]) : 0;
            $saldo_awal_kredit = $rows->sn == "K" ? (!isset($saldo_awal[$rows->id]) ? 0 : $saldo_awal[$rows->id]) : 0;
            $penyesuaian_akun_debet = isset($akun_debet[$rows->nomor_akun_secondary]) ? $akun_debet[$rows->nomor_akun_secondary] : 0;
            $penyesuaian_akun_kredit = isset($akun_kredit[$rows->nomor_akun_secondary]) ? $akun_kredit[$rows->nomor_akun_secondary] : 0;
            if ($rows->pos == 'NRC') {
               $neraca_debet = $saldo_awal_debet + $penyesuaian_akun_debet;
               $neraca_kredit = $saldo_awal_kredit + $penyesuaian_akun_kredit;
               $laba_debet = 0;
               $laba_kredit = 0;
            } else {
               $neraca_debet = 0;
               $neraca_kredit = 0;
               $laba_debet = $saldo_awal_debet + $penyesuaian_akun_debet;
               $laba_kredit = $saldo_awal_kredit + $penyesuaian_akun_kredit;
            }
            # total
            $total['saldo_awal_debet'] = isset($total['saldo_awal_debet']) ? ($total['saldo_awal_debet'] + $saldo_awal_debet) : $saldo_awal_debet;
            $total['saldo_awal_kredit'] = isset($total['saldo_awal_kredit']) ? ($total['saldo_awal_kredit'] + $saldo_awal_kredit) : $saldo_awal_kredit;
            $total['penyesuaian_akun_debet'] = isset($total['penyesuaian_akun_debet']) ? ($total['penyesuaian_akun_debet'] + $penyesuaian_akun_debet) : $penyesuaian_akun_debet;
            $total['penyesuaian_akun_kredit'] = isset($total['penyesuaian_akun_kredit']) ? ($total['penyesuaian_akun_kredit'] + $penyesuaian_akun_kredit) : $penyesuaian_akun_kredit;
            $total['saldo_disesuaikan_debet'] = isset($total['saldo_disesuaikan_debet']) ? ($total['saldo_disesuaikan_debet'] + ($saldo_awal_debet + $penyesuaian_akun_debet)) : ($saldo_awal_debet + $penyesuaian_akun_debet);
            $total['saldo_disesuaikan_kredit'] = isset($total['saldo_disesuaikan_kredit']) ? ($total['saldo_disesuaikan_kredit'] + ($saldo_awal_kredit + $penyesuaian_akun_kredit)) : ($saldo_awal_kredit + $penyesuaian_akun_kredit);
            $total['neraca_debet'] = isset($total['neraca_debet']) ? ($total['neraca_debet'] + $neraca_debet) : $neraca_debet;
            $total['neraca_kredit'] = isset($total['neraca_kredit']) ? ($total['neraca_kredit'] + $neraca_kredit) : $neraca_kredit;
            $total['laba_debet'] = isset($total['laba_debet']) ? ($total['laba_debet'] + $laba_debet) : $laba_debet;
            $total['laba_kredit'] = isset($total['laba_kredit']) ? ($total['laba_kredit'] + $laba_kredit) : $laba_kredit;
            # list
            $list[] = array(
               'sn' => $rows->sn,
               'nomor_akun_secondary' => $rows->nomor_akun_secondary,
               'nama_akun_secondary' => $rows->nama_akun_secondary,
               'saldo_awal_debet' => $saldo_awal_debet,
               'saldo_awal_kredit' => $saldo_awal_kredit,
               'penyesuaian_akun_debet' => $penyesuaian_akun_debet,
               'penyesuaian_akun_kredit' => $penyesuaian_akun_kredit,
               'saldo_disesuaikan_debet' => ($saldo_awal_debet + $penyesuaian_akun_debet),
               'saldo_disesuaikan_kredit' => ($saldo_awal_kredit + $penyesuaian_akun_kredit),
               'neraca_debet' => $neraca_debet,
               'neraca_kredit' => $neraca_kredit,
               'laba_debet' => $laba_debet,
               'laba_kredit' => $laba_kredit
            );
         }
      }
      $html = '<tr>
                  <th colspan="12">NERACA LAJUR PERIODE ' . $periode_name . ' </th>
               </tr>
               <tr>
                  <th style="width:7%;" rowspan="2">Kode Akun</th>
                  <th style="width:23%;" rowspan="2">Nama Akun</th>
                  <th style="width:14%;" colspan="2">Saldo Awal</th>
                  <th style="width:14%;" colspan="2">Penyesuaian</th>
                  <th style="width:14%;" colspan="2">Saldo Disesuaikan</th>
                  <th style="width:14%;" colspan="2">Neraca</th>
                  <th style="width:14%;" colspan="2">Laba Rugi</th>
               </tr>
               <tr>
                  <th style="width:7%;">Debet</th>
                  <th style="width:7%;">Kredit</th>
                  <th style="width:7%;">Debet</th>
                  <th style="width:7%;">Kredit</th>
                  <th style="width:7%;">Debet</th>
                  <th style="width:7%;">Kredit</th>
                  <th style="width:7%;">Debet</th>
                  <th style="width:7%;">Kredit</th>
                  <th style="width:7%;">Debet</th>
                  <th style="width:7%;">Kredit</th>
               </tr>';
      foreach ($list as $key => $value) {
         $html .= '<tr>
                     <td>' . $value['nomor_akun_secondary'] . '</td>
                     <td>' . $value['nama_akun_secondary'] . '</td>
                     <td>' . $value['saldo_awal_debet'] . '</td>
                     <td>' . $value['saldo_awal_kredit'] . '</td>
                     <td>' . $value['penyesuaian_akun_debet'] . '</td>
                     <td>' . $value['penyesuaian_akun_kredit'] . '</td>
                     <td>' . $value['saldo_disesuaikan_debet'] . '</td>
                     <td>' . $value['saldo_disesuaikan_kredit'] . '</td>
                     <td>' . $value['neraca_debet'] . '</td>
                     <td>' . $value['neraca_kredit'] . '</td>
                     <td>' . $value['laba_debet'] . '</td>
                     <td>' . $value['laba_kredit'] . '</td>
                   </tr>';
      }


      $a_debet = ($total['laba_kredit'] -  $total['laba_debet']) > 0 ? 0 : $total['laba_kredit'] -  $total['laba_debet'];
      $a_kredit = ($total['laba_kredit'] - $total['laba_debet']) > 0 ?  $total['laba_kredit'] - $total['laba_debet'] : 0;

      $html .= '<tr>
                  <td colspan="2" class="text-right"><b>TOTAL</b></td>
                  <td><b>' . $total['saldo_awal_debet'] . '</b></td>
                  <td><b>' . $total['saldo_awal_kredit'] . '</b></td>
                  <td><b>' . $total['penyesuaian_akun_debet'] . '</b></td>
                  <td><b>' . $total['penyesuaian_akun_kredit'] . '</b></td>
                  <td><b>' . $total['saldo_disesuaikan_debet'] . '</b></td>
                  <td><b>' . $total['saldo_disesuaikan_kredit'] . '</b></td>
                  <td><b>' . $total['neraca_debet'] . '</b></td>
                  <td><b>' . $total['neraca_kredit'] . '</b></td>
                  <td><b>' . $total['laba_debet'] . '</b></td>
                  <td><b>' . $total['laba_kredit'] . '</b></td>
               </tr>
               <tr>
                  <td colspan="7" class="text-right"></td>
                  <td><b>' . ($a_kredit > 0 ? 'LABA' : 'RUGI') . '</b></td>
                  <td><b>' . number_format($a_debet) . '</b></td>
                  <td><b>' . number_format($a_kredit) . '</b></td>
                  <td><b>' . number_format($a_debet) . '</b></td>
                  <td><b>' . number_format($a_kredit) . '</b></td>
               </tr>
               <tr>
                  <td colspan="7"></td>
                  <td><b>NRC</b></td>
                  <td><b>' . ($total['neraca_debet'] + $a_debet) . '</b></td>
                  <td><b>' . ($total['neraca_kredit'] + $a_kredit) . '</b></td>
                  <td></td>
                  <td></td>
               </tr>';
      return $html;
   }

   function periode_name($periode_id)
   {
      if( $periode_id != 0 ) {
         $this->db->select('nama_periode')
            ->from('jurnal_periode')
            ->where('company_id', $this->company_id)
            ->where('id', $periode_id);
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               $periode_name = $rows->nama_periode;
            }
         }
      }else{
         $periode_name = 'Periode Sekarang';
      }
      
      return $periode_name;
   }

   # download laba rugi
   function model_download_laba_rugi($param)
   {
      $periode_name = $this->periode_name($param['periode']);
      # akun primary
      $akun_primary = array(4, 5, 6);
      $salwo_awal = array();
      $this->db->select('akun_secondary_id, saldo')
         ->from('saldo')
         ->where('company_id', $this->company_id)
         ->where('periode', $param['periode']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      # jurnal
      $this->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $this->company_id)
         ->where('periode_id', $param['periode']);
      $q = $this->db->get();
      $akun_debet = array();
      $akun_kredit = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (isset($akun_debet[$rows->akun_debet])) {
               $akun_debet[$rows->akun_debet] = $akun_debet[$rows->akun_debet] + $rows->saldo;
            } else {
               $akun_debet[$rows->akun_debet] = $rows->saldo;
            }
            if (isset($akun_kredit[$rows->akun_kredit])) {
               $akun_kredit[$rows->akun_kredit] = $akun_kredit[$rows->akun_kredit] + $rows->saldo;
            } else {
               $akun_kredit[$rows->akun_kredit] = $rows->saldo;
            }
         }
      }
      # list
      $list = array();
      foreach ($akun_primary as $key => $value) {
         $this->db->select('as.id, as.nomor_akun_secondary, as.nama_akun_secondary, ap.sn')
            ->from('akun_secondary AS as')
            ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
            ->where('as.company_id', $this->company_id)
            ->where('as.akun_primary_id', $value)
            ->order_by('as.nomor_akun_secondary', 'asc');
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               # get saldo
               $saldo = 0;
               if (isset($saldo_awal[$rows->id])) {
                  $saldo = $saldo + $saldo_awal[$rows->id];
               }

               if ($rows->sn == 'D') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_kredit[$rows->nomor_akun_secondary];
                  }
               } elseif ($rows->sn == 'K') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_kredit[$rows->nomor_akun_secondary];
                  }
               }

               $list[$value][] = array(
                  'nomor_akun' => $rows->nomor_akun_secondary,
                  'nama_akun_secondary' => $rows->nama_akun_secondary,
                  'saldo' => $saldo
               );
            }
         }
      }

      $pendapatan = $list[4];
      $penjualan = $list[5];
      $pengeluaran = $list[6];
      # PENDAPATAN
      $html = '<tr><th colspan="3"><center><b>LABA RUGI PERIODE ' . $periode_name . '</b></center></th></tr>';
      $html .= '<tr><td colspan="3" class="text-right"><b>PENDAPATAN</b></td></tr>';
      $total_pendapatan = 0;
      foreach ($pendapatan as $key => $value) {
         $html .= '<tr>
                     <td>' . $value['nomor_akun'] . '</td>
                     <td>' . $value['nama_akun_secondary'] . '</td>
                     <td>' . $value['saldo'] . '</td>
                   </tr>';
         $total_pendapatan = $total_pendapatan + $value['saldo'];
      }
      $html .= '<tr><td colspan="2" class="text-right"><b>SUBTOTAL PENDAPATAN</b></td><td class="text-right"><b>' . $total_pendapatan . '</b></td></tr>';
      # PENJUALAN
      $html .= '<tr><td colspan="3"></td></tr>';
      $html .= '<tr><td colspan="3" class="text-right"><b>PENJUALAN</b></td></tr>';
      $total_penjualan = 0;
      foreach ($penjualan as $key => $value) {
         $html .= '<tr>
                     <td>' . $value['nomor_akun'] . '</td>
                     <td>' . $value['nama_akun_secondary'] . '</td>
                     <td>' . $value['saldo'] . '</td>
                   </tr>';
         $total_penjualan = $total_penjualan + $value['saldo'];
      }
      $html .= '<tr><td colspan="2" class="text-right"><b>SUBTOTAL PENJUALAN</b></td><td class="text-right"><b>' . $total_penjualan . '</b></td></tr>';
      $html .= '<tr><td colspan="2" class="text-right"><b>LABA KOTOR</b></td><td class="text-right"><b>' . ($total_pendapatan - $total_penjualan) . '</b></td></tr>';
      # PENGELUARAN
      $html .= '<tr><td colspan="3"></td></tr>';
      $html .= '<tr><td colspan="3" class="text-right"><b>PENGELUARAN</b></td></tr>';
      $total_pengeluaran = 0;
      foreach ($pengeluaran as $key => $value) {
         $html .= '<tr>
                    <td>' . $value['nomor_akun'] . '</td>
                    <td>' . $value['nama_akun_secondary'] . '</td>
                    <td>' . $value['saldo'] . '</td>
                  </tr>';
         $total_pengeluaran = $total_pengeluaran + $value['saldo'];
      }
      $html .= '<tr><td colspan="2" class="text-right"><b>SUBTOTAL PENGELUARAN</b></td><td class="text-right"><b>' . $total_pengeluaran . '</b></td></tr>';
      $html .= '<tr><td colspan="2" class="text-right"><b>LABA BERSIH</b></td><td class="text-right"><b>' . ($total_pendapatan - $total_penjualan - $total_pengeluaran) . '</b></td></tr>';

      return $html;
   }

   #  model download neraca
   function model_download_neraca($param)
   {
      # akun primary
      $akun_primary = array(1, 2, 3);
      # get saldo awal
      $salwo_awal = array();
      $this->db->select('akun_secondary_id, saldo')
         ->from('saldo')
         ->where('company_id', $this->company_id)
         ->where('periode', $param['periode']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      # jurnal
      $this->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $this->company_id)
         ->where('periode_id', $param['periode']);
      $q = $this->db->get();
      $akun_debet = array();
      $akun_kredit = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (isset($akun_debet[$rows->akun_debet])) {
               $akun_debet[$rows->akun_debet] = $akun_debet[$rows->akun_debet] + $rows->saldo;
            } else {
               $akun_debet[$rows->akun_debet] = $rows->saldo;
            }
            if (isset($akun_kredit[$rows->akun_kredit])) {
               $akun_kredit[$rows->akun_kredit] = $akun_kredit[$rows->akun_kredit] + $rows->saldo;
            } else {
               $akun_kredit[$rows->akun_kredit] = $rows->saldo;
            }
         }
      }
      # list
      $list = array();
      foreach ($akun_primary as $key => $value) {
         $this->db->select('as.id, as.nomor_akun_secondary, as.nama_akun_secondary, ap.sn')
            ->from('akun_secondary AS as')
            ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
            ->where('as.company_id', $this->company_id)
            ->where('as.akun_primary_id', $value)
            ->order_by('as.nomor_akun_secondary', 'asc');
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               # get saldo
               $saldo = 0;
               if (isset($saldo_awal[$rows->id])) {
                  $saldo = $saldo + $saldo_awal[$rows->id];
               }
               if ($rows->sn == 'D') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_kredit[$rows->nomor_akun_secondary];
                  }
               } elseif ($rows->sn == 'K') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_kredit[$rows->nomor_akun_secondary];
                  }
               }
               if ($rows->nomor_akun_secondary == '33000') {
                  $saldo = $saldo + $this->_iktisar_laba_rugi($param);
               }
               $list[$value][] = array(
                  'nomor_akun' => $rows->nomor_akun_secondary,
                  'nama_akun_secondary' => $rows->nama_akun_secondary,
                  'saldo' => $saldo
               );
            }
         }
      }
      # get periode name
      $periode_name = $this->periode_name($param['periode']);
      $asset = $list[1];
      $kewajiban = $list[2];
      $ekuitas = $list[3];
      $total_asset = 0;
      # get periode
      $html  = '<tr><th colspan="3"><center><b>LAPORAN NERACA PERIODE ' . $periode_name . '</b></center></th></tr>';
      #  aktiva
      $html .= '<tr><td colspan="3" class="text-right"><b>AKTIVA</b></td></tr>';
      # asset
      $html .= '<tr><td colspan="3" class="text-right"><b>Asset</b></td></tr>';
      foreach ($asset as $key => $value) {
         $html .= '<tr><td>' . $value['nomor_akun'] . '</td><td>' . $value['nama_akun_secondary'] . '</td><td>' . $value['saldo'] . '</td></tr>';
         $total_asset = $total_asset + $value['saldo'];
      }
      $html .= '<tr><td colspan="2" class="text-right"><b>SUBTOTAL ASSET</b></td><td class="text-right"><b>' . $total_asset . '</b></td></tr>';
      $html .= '<tr><td colspan="3" class="text-right"></td></tr>';
      $html .= '<tr><td colspan="2" class="text-right"><b>TOTAL AKTIVA</b></td><td class="text-right"><b>' . $total_asset . '</b></td></tr>';
      #  passiva
      $html .= '<tr><td colspan="3" class="text-right"></td></tr>';
      $html .= '<tr><td colspan="3" class="text-right"><b>PASSIVA</b></td></tr>';
      # kewajiban
      $total_kewajiban = 0;
      $html .= '<tr><td colspan="3" class="text-right"><b>Kewajiban</b></td></tr>';
      foreach ($kewajiban as $key => $value) {
         $html .= '<tr><td>' . $value['nomor_akun'] . '</td><td>' . $value['nama_akun_secondary'] . '</td><td>' . $value['saldo'] . '</td></tr>';
         $total_kewajiban = $total_kewajiban + $value['saldo'];
      }
      $html .= '<tr><td colspan="2" class="text-right"><b>SUBTOTAL KEWAJIBAN</b></td><td class="text-right"><b>' . $total_kewajiban . '</b></td></tr>';
      # ekuitas
      $total_ekuitas = 0;
      $html .= '<tr><td colspan="3" class="text-right"><b>Ekuitas</b></td></tr>';
      foreach ($ekuitas as $key => $value) {
         $html .= '<tr><td>' . $value['nomor_akun'] . '</td><td>' . $value['nama_akun_secondary'] . '</td><td>' . $value['saldo'] . '</td></tr>';
         $total_ekuitas = $total_ekuitas + $value['saldo'];
      }
      $html .= '<tr><td colspan="2" class="text-right"><b>SUBTOTAL EKUITAS</b></td><td class="text-right"><b>' . $total_ekuitas . '</b></td></tr>';
      $html .= '<tr><td colspan="3" class="text-right"></td></tr>';
      $html .= '<tr><td colspan="2" class="text-right"><b>TOTAL PASSIVA</b></td><td class="text-right"><b>' . ($total_kewajiban + $total_ekuitas) . '</b></td></tr>';
      return $html;
   }

   # iktisar laba rugi
   function _iktisar_laba_rugi($param)
   {
      # akun primary
      $akun_primary = array(1, 2, 3);
      $salwo_awal = array();
      $this->db->select('akun_secondary_id, saldo')
         ->from('saldo')
         ->where('company_id', $this->company_id)
         ->where('periode', $param['periode']);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            $saldo_awal[$rows->akun_secondary_id] = $rows->saldo;
         }
      }
      # jurnal
      $this->db->select('akun_debet, akun_kredit, saldo')
         ->from('jurnal')
         ->where('company_id', $this->company_id)
         ->where('periode_id', $param['periode']);
      $q = $this->db->get();
      $akun_debet = array();
      $akun_kredit = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $rows) {
            if (isset($akun_debet[$rows->akun_debet])) {
               $akun_debet[$rows->akun_debet] = $akun_debet[$rows->akun_debet] + $rows->saldo;
            } else {
               $akun_debet[$rows->akun_debet] = $rows->saldo;
            }
            if (isset($akun_kredit[$rows->akun_kredit])) {
               $akun_kredit[$rows->akun_kredit] = $akun_kredit[$rows->akun_kredit] + $rows->saldo;
            } else {
               $akun_kredit[$rows->akun_kredit] = $rows->saldo;
            }
         }
      }
      # list
      $total = array();
      foreach ($akun_primary as $key => $value) {
         $this->db->select('as.id, as.nomor_akun_secondary, as.nama_akun_secondary, ap.sn')
            ->from('akun_secondary AS as')
            ->join('akun_primary AS ap', 'as.akun_primary_id=ap.id', 'inner')
            ->where('as.company_id', $this->company_id)
            ->where('as.akun_primary_id', $value)
            ->order_by('as.nomor_akun_secondary', 'asc');
         $q = $this->db->get();
         if ($q->num_rows() > 0) {
            foreach ($q->result() as $rows) {
               # get saldo
               $saldo = 0;
               if (isset($saldo_awal[$rows->id])) {
                  $saldo = $saldo + $saldo_awal[$rows->id];
               }
               if ($rows->sn == 'D') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_kredit[$rows->nomor_akun_secondary];
                  }
               } elseif ($rows->sn == 'K') {
                  # akun debet
                  if (isset($akun_debet[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo - $akun_debet[$rows->nomor_akun_secondary];
                  }
                  # akun kredit
                  if (isset($akun_kredit[$rows->nomor_akun_secondary])) {
                     $saldo = $saldo + $akun_kredit[$rows->nomor_akun_secondary];
                  }
               }

               if (isset($total[$value])) {
                  $total[$value] = $total[$value] + $saldo;
               } else {
                  $total[$value] = $saldo;
               }
            }
         }
      }
      return ($total[1] - $total[2] - $total[3]);
   }

   // model download excel daftar agen
   function model_download_excel_daftar_agen(){
      $html = '<tr>
                  <th>#</th>
                  <th><center>NAMA</center></th>
                  <th><center>NOMOR IDENTITAS</center></th>
                  <th><center>NAMA JAMAAH</center></th>
                  <th><center>FEE</center></th>
                  <th><center>SUDAH BAYAR</center></th>
                  <th><center>STATUS FEE</center></th>
               </tr>';
      $this->db->select('a.id, per.fullname, per.identity_number')
         ->from('agen AS a')
         ->join('personal AS per', 'a.personal_id=per.personal_id', 'inner')
         ->where('a.company_id', $this->company_id);
      $q = $this->db->get();
      $no = 1;
      if( $q->num_rows() > 0 ) {
         foreach ( $q->result() as $rows ) {
            $this->db->select('p.fullname, dfk.fee, dfk.sudah_bayar, dfk.status_fee')
               ->from('detail_fee_keagenan AS dfk')
               ->join('fee_keagenan AS fk', 'dfk.fee_keagenan_id=fk.id', 'inner')
               ->join('personal AS p', 'fk.personal_id=p.personal_id', 'inner')
               ->where('dfk.agen_id', $rows->id)
               ->order_by('dfk.input_date', 'desc');
            $r = $this->db->get();
            if( $r->num_rows() > 0 ) {
               foreach ( $r->result() as $rowr ) {
                  $html .= '<tr>
                              <td><center>'.$no.'</center></td>
                              <td>'.$rows->fullname.'</td>
                              <td><center>'.$rows->identity_number.'</center></td>
                              <td>'.$rowr->fullname.'</td>
                              <td><center>'.$rowr->fee.'</center></td>
                              <td><center>'.$rowr->sudah_bayar.'</center></td>
                              <td><center>'.$rowr->status_fee.'</center></td>
                           </tr>';
                  $no++;         
               }
            }   
         }
      }
      return $html;
   }

   // model download all jamaah to excel
   function model_download_all_jamaah_to_excel(){
      $this->db->select('p.identity_number, p.fullname, p.gender, p.birth_place, p.birth_date, j.status_nikah, j.title, j.father_name, 
                         j.jenis_identitas, p.address, j.kewarganegaraan, j.pasport_name,
                         j.telephone, p.nomor_whatsapp, p.email, mp.nama_pendidikan, j.passport_number, 
                         pkjr.nama_pekerjaan, j.passport_dateissue, j.validity_period, j.passport_place')
               ->from('jamaah AS j')
               ->join('mst_pekerjaan AS pkjr', 'j.pekerjaan_id=pkjr.id', 'left')
               ->join('personal AS p', 'j.personal_id=p.personal_id', 'inner')
               ->join('mst_pendidikan AS mp', 'j.last_education=mp.id_pendidikan', 'left')
               ->where('j.company_id', $this->company_id);

      $html = '<tr>
                  <th>NO</th>
                  <th>TITLE</th>
                  <th>NAMA</th>
                  <th>NAMA AYAH</th>
                  <th>JENIS IDENTITAS</th>
                  <th>NO IDENTITAS</th>
                  <th>NAMA PASPOR</th>
                  <th>NO PASPOR</th>
                  <th>TANGGAL DIKELUARKAN PASPOR</th>
                  <th>TANGGAL EXPIRED PASPOR</th>
                  <th>KOTA PASPOR</th>
                  <th>TEMPAT LAHIR</th>
                  <th>TANGGAL LAHIR</th>
                  <th>ALAMAT</th>
                  <th>NO TELEPON</th>
                  <th>NO HP</th>
                  <th>KEWARGANEGARAAN</th>
                  <th>STATUS PERNIKAHAN</th>
                  <th>PENDIDIKAN</th>
                  <th>PEKERJAAN</th>
               </tr>';
      $q = $this->db->get();
      if( $q->num_rows() > 0 ) {
         $n = 1;
         foreach( $q->result() AS $rows ){
            $umur = $this->date_ops->get_umur($rows->birth_date);
            if($umur < 2 ){
               $kat_umur = 'Bayi';
            }elseif ($umur >= 2 AND $umur < 12) {
               $kat_umur = 'Anak';
            }else{
               $kat_umur = 'Dewasa';
            }
            $html .= '<tr>
                        <td>'.$n.'</td>
                        <td>'.$rows->title.'</td>
                        <td>'.$rows->fullname.'</td>
                        <td>'.$rows->father_name.'</td>
                        <td>'.$rows->jenis_identitas.'</td>
                        <td>"'.$rows->identity_number.'"</td>
                        <td>'.$rows->pasport_name.'</td>
                        <td>'.$rows->passport_number.'</td>
                        <td>'.$rows->passport_dateissue.'</td>
                        <td>'.$rows->validity_period.'</td>
                        <td>'.$rows->passport_place.'</td>
                        <td>'.$rows->birth_place.'</td>
                        <td>'.$rows->birth_date.'</td>
                        <td>'.$rows->address.'</td>
                        <td>'.$rows->telephone.'</td>
                        <td>'.$rows->nomor_whatsapp.'</td>
                        <td>'.$rows->kewarganegaraan.'</td>
                        <td>'.$rows->status_nikah.'</td>
                        <td>'.$rows->nama_pendidikan.'</td>
                        <td>'.$rows->nama_pekerjaan.'</td>
                     </tr>';
            $n++;
         }
      }else{
         $html .= '<tr><td colspan="32"></td></tr>';
      }
      return  $html;
   }

   function model_download_excel_info_saldo_member() {

       $html = '<tr>
                  <th>NAMA</th>
                  <th>TOTAL DEPOSIT</th>
                  <th>TOTAL TABUNGAN</th>
               </tr>';


      return $html;

   }
}
