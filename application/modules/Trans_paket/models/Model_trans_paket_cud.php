<?php

/**
 *  -----------------------
 *	Model trans paket cud
 *	Created by Muammar Kadafi
 *  -----------------------
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Model_trans_paket_cud extends CI_Model
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

   # update data visa
   function update_data_visa( $paket_transaction_id, $data ) {
      $this->db->trans_start();
      # update process
      $this->db->where('id', $paket_transaction_id)
               ->where('company_id', $this->company_id)
               ->update('paket_transaction', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menambahkan update data info visa dengan paket transaction id : ' . $paket_transaction_id ;
      }
      return $this->status;
   }

   function add_update_jamaah($dataParam, $dataPersonal, $dataJamaah, $dataMahram)
   {
      foreach ($dataParam as $key => $value) {
         // define jamaah id
         if ($key == 'jamaah_id') {
            $jamaah_id = $value;
         }
         // define personal id
         if ($key == 'personal_id') {
            $personal_id = $value;
         }
      }

      $this->db->trans_start();
      // input personal data
      if (isset($personal_id)) {
         // update personal data
         $dataPersonal['last_update'] = date('Y-m-d');
         $this->db->where('personal_id', $personal_id);
         $this->db->where('company_id', $this->company_id);
         $update = $this->db->update('personal', $dataPersonal);
      } else {
         $dataPersonal['last_update'] = date('Y-m-d');
         $dataPersonal['input_date'] = date('Y-m-d');
         // insert personal data
         $insert = $this->db->insert('personal', $dataPersonal);
         $personal_id = $this->db->insert_id();
      }

      // input data jamaah
      $dataJamaah['personal_id'] = $personal_id;
      if (isset($jamaah_id)) {
         $dataJamaah['last_update'] = date('Y-m-d H:i:s');
         // update jamaah
         $this->db->where('id', $jamaah_id);
         $this->db->where('company_id', $this->company_id);
         $update = $this->db->update('jamaah', $dataJamaah);
         // delete mahram
         $this->db->where('jamaah_id', $jamaah_id);
         $this->db->where('company_id', $this->company_id);
         $delete = $this->db->delete('mahram');
         // # delete mahram
         // $this->db->where('mahram_id', $jamaah_id );
         // $delete = $this->db->delete('mahram');
      } else {
         $dataJamaah['input_date'] = date('Y-m-d H:i:s');
         $dataJamaah['last_update'] = date('Y-m-d H:i:s');
         // insert
         $insert = $this->db->insert('jamaah', $dataJamaah);
         $jamaah_id = $this->db->insert_id();
      }

      if (count($dataMahram) > 0) {
         foreach ($dataMahram['mahram_id'] as $key => $value) {
            $dataMahramInput = array();
            $dataMahramInput['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
            $dataMahramInput['jamaah_id'] = $jamaah_id;
            $dataMahramInput['mahram_id'] = $value;
            $dataMahramInput['status'] = $dataMahram['status'][$key];
            $dataMahramInput['input_date'] = date('Y-m-d');
            $dataMahramInput['last_update'] = date('Y-m-d');
            // insert data mahram
            $insert = $this->db->insert('mahram', $dataMahramInput);
         }
      }

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menambahkan data jamaah dengan info jamaah (Jamaah ID : ' . $jamaah_id . ', Nama Jamaah : ' . $dataPersonal['fullname'] . ', Nomor Identitas : ' . $dataPersonal['identity_number'] . ')';
      }
      return $this->status;
   }

   # delete jamaah
   function delete_jamaah($id, $fullname)
   {
      $this->db->trans_start();
      // paket_transaction_jamaah
      $this->db->where('jamaah_id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('paket_transaction_jamaah');
      // delete jamaah id
      $this->db->where('id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('jamaah');
      // delete mahram_id
      $this->db->where('jamaah_id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('mahram');
      // delete mahram_id
      $this->db->where('mahram_id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('mahram');
      // handover item
      $this->db->where('jamaah_id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('handover_item');
      // handover falities
      $this->db->where('jamaah_id', $id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('handover_facilities');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menghapus data jamaah ID : ' . $id . ' dengan nama jamaah ' . $fullname . ' ';
      }
      return $this->status;
   }

   # delete photo
   function delete_photo($personal_id)
   {
      $this->db->trans_start();
      $data = array();
      $data['photo'] = '';
      $this->db->where('personal_id', $personal_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->update('personal', $data);

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menghapus data photo dengan personal id : ' . $personal_id;
      }
      return $this->status;
   }

   # insert transaction process
   function insert_transaction_process($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert paket transaction
      $this->db->insert('paket_transaction', $data['paket_transaction']);
      $paket_transaction_id = $this->db->insert_id();
      # insert paket transaction jamaah
      $data['paket_transaction_jamaah']['paket_transaction_id'] = $paket_transaction_id;
      $this->db->insert('paket_transaction_jamaah', $data['paket_transaction_jamaah']);
      # paket transaction history
      $data['paket_transaction_history']['paket_transaction_id'] = $paket_transaction_id;
      $this->db->insert('paket_transaction_history', $data['paket_transaction_history']);
      #  update pool
      if (isset($data['pool'])) {
         $data['pool']['paket_transaction_id'] = $paket_transaction_id;
         # update pool
         $this->db->where('id', $data['pool_id'])
            ->where('company_id', $this->company_id)
            ->update('pool', $data['pool']);
         # insert deposit
         if (isset($data['deposit_transaction'])) {
            $deposit_transaction = $data['deposit_transaction'];
            foreach ($deposit_transaction as $key => $value) {
               $value['paket_transaction_id'] = $paket_transaction_id;
               $this->db->insert('deposit_transaction', $value);
            }
         }
         # insert handover facilities
         if (isset($data['handover_facilities'])) {
            $handover_facilities = $data['handover_facilities'];
            foreach ($handover_facilities as $key => $value) {
               $value['paket_transaction_id'] = $paket_transaction_id;
               $this->db->insert('handover_facilities', $value);
            }
         }
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menambahkan data transaksi paket dengan nomor Register : ' . $data['paket_transaction']['no_register'] . ' dengan nama kode paket' . $data['info_paket']['kode'] . ' dan nama paket :' . $data['info_paket']['paket_name'] . ' ';
      }
      return $this->status;
   }

   function checkingDepositPersonalExist($paket_transaction_id)
   {
      $this->db->select('paid, source_id')
         ->from('paket_transaction_history AS pth')
         ->join('paket_transaction AS pt', 'pth.paket_transaction_id=pt.id', 'inner')
         ->join('paket AS p', 'pt.paket_id=p.id', 'inner')
         ->where('paket_transaction_id', $paket_transaction_id)
         ->where('p.company_id', $this->company_id)
         ->where('source', 'deposit');
      $q = $this->db->get();
      $feedBack = array();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {
            if (array_key_exists($row->source_id, $feedBack)) {
               $feedBack[$row->source_id] = $feedBack[$row->source_id] + $row->paid;
            } else {
               $feedBack[$row->source_id] = $row->paid;
            }
         }
      }
      return $feedBack;
   }

   # delete transaksi paket
   function deleteTransaksiPaket($paket_id, $paket_transaction_id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();

      # checking pool data in array
      if( isset($data['pool']) ){
         // insert pool data
         $this->db->insert('pool', $data['pool']);
         $data['pool_deposit_transaction']['pool_id'] = $this->db->insert_id();   
      }

      # insert deposit transaction
      $this->db->insert('deposit_transaction', $data['deposit_transaction']);
      $data['pool_deposit_transaction']['deposit_transaction_id'] = $this->db->insert_id();  

      # insert pool deposit transction
      $this->db->insert('pool_deposit_transaction', $data['pool_deposit_transaction']);

      // # update pool
      // $this->db->where('paket_transaction_id', $paket_transaction_id)
      //       ->where('company_id', $this->company_id)
      //       ->update('pool', array('active' => 'active'));

      // # delete deposit transaction
      // $this->db->where('paket_transaction_id', $paket_transaction_id)
      //    ->where('company_id', $this->company_id)
      //    ->delete('deposit_transaction');


         

      # delete handover facilities
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->where('company_id', $this->company_id)
         ->delete('handover_facilities');   

      # delete paket transaction history
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->where('company_id', $this->company_id)
         ->delete('paket_transaction_history');

      # delete paket transaction jamaah
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->where('company_id', $this->company_id)
         ->delete('paket_transaction_jamaah');

      # delete paket_transaction_installement_history
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->where('company_id', $this->company_id)
         ->delete('paket_transaction_installement_history');

      # delete paket_installment_scheme
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->where('company_id', $this->company_id)
         ->delete('paket_installment_scheme');

      # delete paket transaction paket
      $this->db->where('paket_id', $paket_id)
         ->where('id', $paket_transaction_id)
         ->where('company_id', $this->company_id)
         ->delete('paket_transaction');

      # delete fee keagenan unpaid
      $this->db->select('fee_keagenan_id')
         ->from('paket_transaction')
         ->where('company_id', $this->company_id)
         ->where('id', $paket_transaction_id)
         ->where('fee_keagenan_id !=', '0');

      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         $fee_keagenan_id = $q->row()->fee_keagenan_id;
         # delete detail fee keagenan id
         $this->db->where('fee_keagenan_id', $fee_keagenan_id)
            ->where('company_id', $this->company_id)
            ->delete('detail_fee_keagenan');
         # delete fee keagenan
         $this->db->where('id', $fee_keagenan_id)
            ->where('company_id', $this->company_id)
            ->delete('fee_keagenan');
      }

      
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus data transaksi paket dengan nomor Register : ' . $data['no_register'] . '  ';
      }
      return $this->status;
   }


   // insert pembayaran cash
   function insertPembayaranCash($data, $dataDeposit = array())
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert deposit
      if (count($dataDeposit) > 0) {
         $this->db->insert('deposit_transaction', $dataDeposit);
      }
      # insert to paket transaction table
      $this->db->insert('paket_transaction_history', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menambahkan data transaksi paket Cash dengan nomor Invoice : ' . $data['invoice'] . '  ';
      }
      return $this->status;
   }


   # insert refund
   function insertRefund($paket_transaction_id, $data, $batalBerangkat)
   {

      # Starting Transaction
      $this->db->trans_start();

      # update paket transaction
      if ($batalBerangkat == 1) {
         $dataPaketTransaction['batal_berangkat'] = 1;
         $this->db->where('id', $paket_transaction_id);
         $this->db->where('company_id', $this->company_id);
         $this->db->update('paket_transaction', $dataPaketTransaction);
      }

      # insert paket transaction history
      $this->db->insert('paket_transaction_history', $data);

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Melakukan insert transaksi refund. ';
      }
      return $this->status;
   }

   # insert Handover Barang
   function insertHandOverBarang($dataInput, $item)
   {

      # Starting Transaction
      $this->db->trans_start();

      foreach ($item as $key => $value) {
         $data = array();
         $data['paket_transaction_id'] = $dataInput['paket_transaction_id'];
         $data['jamaah_id'] = $dataInput['jamaah_id'];
         $data['invoice_handover'] = $dataInput['invoice_handover'];
         $data['giver_handover'] = $dataInput['giver_handover'];
         $data['giver_handover_identity'] = $dataInput['giver_handover_identity'];
         $data['giver_handover_hp'] = $dataInput['giver_handover_hp'];
         $data['giver_handover_address'] = $dataInput['giver_handover_address'];
         $data['item_name'] = $value;
         $data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $data['status'] = 'diambil';
         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
            $data['receiver_handover'] = 0;
         } else {
            $data['receiver_handover'] = $this->session->userdata($this->config->item('apps_name'))['user_id'];
         }
         $data['date_taken'] = date('Y-m-d H:i:s');

         $this->db->insert('handover_item', $data);
      }

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan insert handover.';
      }
      return $this->status;
   }

   # return handover barang
   function returnHandOverBarang($item, $dataInput)
   {

      # Starting Transaction
      $this->db->trans_start();

      foreach ($item as $key => $value) {
         $data = array();
         $data['invoice_returned'] = $dataInput['invoice_returned'];
         $data['status'] = $dataInput['status'];
         $data['giver_returned'] = $dataInput['giver_returned'];
         $data['receiver_returned'] = $dataInput['receiver_returned'];
         $data['receiver_returned_identity'] = $dataInput['receiver_returned_identity'];
         $data['receiver_returned_hp'] = $dataInput['receiver_returned_hp'];
         $data['receiver_returned_address'] = $dataInput['receiver_returned_address'];
         $data['date_returned'] = $dataInput['date_returned'];
         // $data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];

         $this->db->where('id', $value);
         $this->db->where('company_id', $this->company_id);
         $this->db->update('handover_item', $data);
      }

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan mengembalikan barang jamaah.';
      }
      return $this->status;
   }


   function insertFasilitasJamaah($item, $dataInput)
   {

      # Starting Transaction
      $this->db->trans_start();

      foreach ($item as $key => $value) {
         $data = array();
         $data['facilities_id'] = $value;
         $data['paket_transaction_id'] = $dataInput['paket_transaction_id'];
         $data['invoice'] = $dataInput['invoice'];
         $data['jamaah_id'] = $dataInput['jamaah_id'];

         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
            $data['officer'] = 0;
         } else {
            $data['officer'] = $this->session->userdata($this->config->item('apps_name'))['user_id'];
         }

         $data['company_id']   = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $data['receiver_name'] = $dataInput['receiver_name'];
         $data['receiver_identity'] = $dataInput['receiver_identity'];
         $data['date_transaction'] = $dataInput['date_transaction'];

         $this->db->insert('handover_facilities', $data);
      }

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan mengembalikan barang jamaah.';
      }
      return $this->status;
   }

   function delete_handover_fasilitas($handover_facilities_id)
   {
      $this->db->trans_start();
      // handover_facilities
      $this->db->where('id', $handover_facilities_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('handover_facilities');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = ' Menghapus data handover facilities dengan id : ' . $handover_facilities_id;
      }
      return $this->status;
   }

   function addUpdatePaketPindah($dataInput, $dataPindahPaket)
   {

      # LAMA (Untuk transaksi paket lama)
      # BARU (Untuk transaksi paket baru)
      # REFUND (Untuk transaksi refund)

      # Starting Transaction
      $this->db->trans_start();

      # id pindah paket
      $pindahPaketId = '';

      if ($dataInput['infoJamaah']['needMahram']) {
         $mahramFee = $dataInput['infoPaketTujuan']['mahram_fee'];
      } else {
         $mahramFee = '0';
      }

      // insert data pindah paket to table pindah_paket
      $tb_PindahPaket = array();
      // $tb_PindahPaket['receiver'] = $this->session->userdata($this->config->item('apps_name'))['user_id'];
      $tb_PindahPaket['kode_paket_asal'] = $dataInput['infoPaketAsal']['kode'];
      $tb_PindahPaket['paket_asal'] = $dataInput['infoPaketAsal']['paket_name'];
      $tb_PindahPaket['tipe_paket_asal'] = $dataInput['infoPaketAsal']['paket_type_name'];
      $tb_PindahPaket['no_register_asal'] = $dataInput['infoPaketAsal']['no_register'];
      $tb_PindahPaket['harga_paket_asal'] = $dataInput['infoPaketAsal']['price_per_pax'];
      $tb_PindahPaket['jamaah_id'] = $dataInput['infoJamaah']['jamaah_id'];
      $tb_PindahPaket['nama_jamaah'] = $dataInput['infoJamaah']['fullname'];
      $tb_PindahPaket['kode_paket_tujuan'] = $dataInput['infoPaketTujuan']['kode'];
      $tb_PindahPaket['paket_tujuan'] = $dataInput['infoPaketTujuan']['paket_name'];
      $tb_PindahPaket['tipe_paket_tujuan'] = $dataInput['infoPaketTujuan']['paket_type_name'];
      $tb_PindahPaket['no_register_paket_tujuan'] = $dataInput['infoPaketTujuan']['no_register'];
      $tb_PindahPaket['harga_paket_tujuan'] = $dataInput['infoPaketTujuan']['price_per_pax'];
      $tb_PindahPaket['biaya_yang_dipindahkan'] = $dataInput['biaya_yang_dipindahkan'];
      $tb_PindahPaket['fee_mahram'] = $mahramFee;
      if (isset($dataPindahPaket['refund'])) {
         $tb_PindahPaket['refund'] = $dataPindahPaket['refund'];
         $tb_PindahPaket['invoice_refund'] = $dataPindahPaket['invoice_refund'];
      }
      $tb_PindahPaket['invoice_tujuan'] = $dataPindahPaket['invoice_tujuan'];
      $tb_PindahPaket['transaction_date'] = $dataPindahPaket['transaction_date'];
      $tb_PindahPaket['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
      # insert to pindah paket table
      $this->db->insert('pindah_paket', $tb_PindahPaket);

      $pindahPaketId = $this->db->insert_id();

      $paket_transaction_terbaru = 0;

      # filter by tipe aksi
      if ($dataInput['tipe_aksi'] == '0') {
         # Melakukan insert paket transaksi baru ke dalam tabel
         #~~ paket_transaction ~~
         $tb_PaketTransaction_BARU = array();
         $tb_PaketTransaction_BARU['no_register'] = $dataInput['infoPaketTujuan']['no_register'];
         $tb_PaketTransaction_BARU['paket_id'] = $dataInput['infoPaketTujuan']['paket_id'];
         $tb_PaketTransaction_BARU['paket_type_id'] = $dataInput['infoPaketTujuan']['paket_type_id'];
         $tb_PaketTransaction_BARU['payment_methode'] = '0';
         $tb_PaketTransaction_BARU['total_mahram_fee'] = $mahramFee;
         $tb_PaketTransaction_BARU['total_paket_price'] = $dataInput['infoPaketTujuan']['price_per_pax'] + $mahramFee;
         $tb_PaketTransaction_BARU['price_per_pax'] = $dataInput['infoPaketTujuan']['price_per_pax'];
         $tb_PaketTransaction_BARU['batal_berangkat'] = '0';
         $tb_PaketTransaction_BARU['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $tb_PaketTransaction_BARU['input_date'] = date('Y-m-d H:i:s');
         $tb_PaketTransaction_BARU['last_update'] = date('Y-m-d H:i:s');
         # insert process paket_transaction
         $this->db->insert('paket_transaction', $tb_PaketTransaction_BARU);
         $paket_transaction_id_BARU = $this->db->insert_id();

         $paket_transaction_id_tujuan = $paket_transaction_id_BARU;

         # Melakukan update paket_transaction_id
         # dari paket_transaction_id lama ke paket_transaction_id yang baru ke dalam table
         #~~ paket_transaction_jamaah
         $tb_PaketTransactionJamaah_BARU = array();
         $tb_PaketTransactionJamaah_BARU['paket_transaction_id'] = $paket_transaction_id_BARU;
         $tb_PaketTransactionJamaah_BARU['leader'] = '1';
         # update process paket_transaction_jamaah
         $this->db->where('jamaah_id', $dataInput['infoJamaah']['jamaah_id'])
            ->where('paket_transaction_id', $dataInput['paket_transaction_id'])
            ->where('company_id', $this->company_id)
            ->update('paket_transaction_jamaah', $tb_PaketTransactionJamaah_BARU);

         # insert paket transaction history paket transaksi baru
         $tb_PaketTransactionHistory_BARU = array();
         $tb_PaketTransactionHistory_BARU['paket_transaction_id'] = $paket_transaction_id_BARU;
         $tb_PaketTransactionHistory_BARU['invoice'] = $dataPindahPaket['invoice_tujuan'];
         $tb_PaketTransactionHistory_BARU['paid'] = $dataInput['biaya_yang_dipindahkan'];
         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
            $tb_PaketTransactionHistory_BARU['receiver'] = 'Administrator';
         } else {
            $tb_PaketTransactionHistory_BARU['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }
         $tb_PaketTransactionHistory_BARU['ket'] = 'cash';
         $tb_PaketTransactionHistory_BARU['source'] = 'tunai';
         $tb_PaketTransactionHistory_BARU['deposit_name'] = $dataInput['infoJamaah']['fullname'];
         $tb_PaketTransactionHistory_BARU['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
         $tb_PaketTransactionHistory_BARU['deposit_address'] = $dataInput['infoJamaah']['address'];
         $tb_PaketTransactionHistory_BARU['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $tb_PaketTransactionHistory_BARU['input_date'] = date('Y-m-d H:i:s');
         $tb_PaketTransactionHistory_BARU['last_update'] = date('Y-m-d H:i:s');
         # insert process
         $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_BARU);

         // checking refund
         if (isset($dataPindahPaket['refund'])) {
            $tb_PaketTransactionHistory_REFUND = array();
            $tb_PaketTransactionHistory_REFUND['paket_transaction_id'] = $paket_transaction_id_BARU;
            $tb_PaketTransactionHistory_REFUND['invoice'] = $dataPindahPaket['invoice_refund'];
            $tb_PaketTransactionHistory_REFUND['paid'] = $dataPindahPaket['refund'];
            if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
               $tb_PaketTransactionHistory_REFUND['receiver'] = 'administrator';
            } else {
               $tb_PaketTransactionHistory_REFUND['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            $tb_PaketTransactionHistory_REFUND['ket'] = 'refund';
            $tb_PaketTransactionHistory_REFUND['source'] = 'tunai';
            $tb_PaketTransactionHistory_REFUND['deposit_name'] = $dataInput['infoJamaah']['fullname'];
            $tb_PaketTransactionHistory_REFUND['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
            $tb_PaketTransactionHistory_REFUND['deposit_address'] = $dataInput['infoJamaah']['address'];
            $tb_PaketTransactionHistory_REFUND['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
            $tb_PaketTransactionHistory_REFUND['input_date'] = date('Y-m-d H:i:s');
            $tb_PaketTransactionHistory_REFUND['last_update'] = date('Y-m-d H:i:s');
            # insert process
            $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_REFUND);
         }

         if ($dataInput['infoPaketAsal']['kalkulasiPaketLama'] == true) {
            # Melakukan update pada paket lama ke tabel
            #~~ paket_transaction
            $tb_PaketTransaction_LAMA = array();
            $tb_PaketTransaction_LAMA['total_mahram_fee'] = $dataInput['infoPaketAsal']['total_mahram_fee'];
            $tb_PaketTransaction_LAMA['total_paket_price'] = $dataInput['infoPaketAsal']['total_paket_price'];
            $tb_PaketTransaction_LAMA['last_update'] = date('Y-m-d H:i:s');
            # update process
            $this->db->where('id', $dataInput['infoPaketAsal']['paket_transaction_id'])
               ->where('company_id', $this->company_id)
               ->update('paket_transaction', $tb_PaketTransaction_LAMA);

            # insert info pindah paket dengan paket_transaction_id paket lama ke tabel
            #~~ paket_transaction_history
            $tb_PaketTransactionHistory_LAMA['paket_transaction_id'] = $dataInput['paket_transaction_id'];
            $tb_PaketTransactionHistory_LAMA['invoice'] = $this->text_ops->get_invoice_transaksi_paket();
            $tb_PaketTransactionHistory_LAMA['paid'] = $dataInput['biaya_yang_dipindahkan'];
            if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
               $tb_PaketTransactionHistory_LAMA['receiver'] = 'administrator';
            } else {
               $tb_PaketTransactionHistory_LAMA['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            $tb_PaketTransactionHistory_LAMA['ket'] = 'pindah_paket';
            $tb_PaketTransactionHistory_LAMA['source'] = 'tunai';
            $tb_PaketTransactionHistory_LAMA['source_id'] = '';
            $tb_PaketTransactionHistory_LAMA['deposit_name'] = $dataInput['infoJamaah']['fullname'];
            $tb_PaketTransactionHistory_LAMA['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
            $tb_PaketTransactionHistory_LAMA['deposit_address'] = $dataInput['infoJamaah']['address'];
            $tb_PaketTransactionHistory_LAMA['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
            $tb_PaketTransactionHistory_LAMA['input_date'] = date('Y-m-d H:i:s');
            $tb_PaketTransactionHistory_LAMA['last_update'] = date('Y-m-d H:i:s');
            # insert process
            $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_LAMA);
         } else {
            // delete paket_transaction lama by paket_transaction_id
            $this->db->where('id', $dataInput['paket_transaction_id'])
               ->where('company_id', $this->company_id)
               ->delete('paket_transaction');

            # delete data paket_transaction_history lama by paket_transaction_id
            $this->db->where('paket_transaction_id', $dataInput['paket_transaction_id'])
               ->where('company_id', $this->company_id)
               ->delete('paket_transaction_history');
         }
      } else {

         # kalkulasi ulang total_mahram_fee dan total_paket_price paket tujuan
         $tb_PaketTransaction_TUJUAN = array();
         $tb_PaketTransaction_TUJUAN['total_mahram_fee'] = $dataInput['infoPaketTujuan']['total_mahram_fee'];
         $tb_PaketTransaction_TUJUAN['total_paket_price'] = $dataInput['infoPaketTujuan']['total_paket_price'];
         $tb_PaketTransaction_TUJUAN['last_update'] = date('Y-m-d H:i:s');
         # update process paket_transaction
         $this->db->where('id', $dataInput['infoPaketTujuan']['paket_transaction_id'])
            ->where('company_id', $this->company_id)
            ->update('paket_transaction', $tb_PaketTransaction_TUJUAN);

         $paket_transaction_id_tujuan = $dataInput['infoPaketTujuan']['paket_transaction_id'];

         # update paket_transaksi_id pada tabel paket_transaction_jamaah dengan paket_transaction_id baru
         $tb_PaketTransactionJamaah_BARU = array();
         $tb_PaketTransactionJamaah_BARU['paket_transaction_id'] = $dataInput['infoPaketTujuan']['paket_transaction_id'];
         # update process paket_transaction_jamaah
         $this->db->where('jamaah_id', $dataInput['infoJamaah']['jamaah_id'])
            ->where('paket_transaction_id', $dataInput['paket_transaction_id'])
            ->where('company_id', $this->company_id)
            ->update('paket_transaction_jamaah', $tb_PaketTransactionJamaah_BARU);

         # insert paket transaction history paket transaksi baru
         $tb_PaketTransactionHistory_BARU = array();
         $tb_PaketTransactionHistory_BARU['paket_transaction_id'] = $dataInput['infoPaketTujuan']['paket_transaction_id'];
         $tb_PaketTransactionHistory_BARU['invoice'] = $dataPindahPaket['invoice_tujuan'];
         $tb_PaketTransactionHistory_BARU['paid'] = $dataInput['biaya_yang_dipindahkan'];
         if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
            $tb_PaketTransactionHistory_BARU['receiver'] = 'administrator';
         } else {
            $tb_PaketTransactionHistory_BARU['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         }
         $tb_PaketTransactionHistory_BARU['ket'] = 'cash';
         $tb_PaketTransactionHistory_BARU['source'] = 'tunai';
         $tb_PaketTransactionHistory_BARU['deposit_name'] = $dataInput['infoJamaah']['fullname'];
         $tb_PaketTransactionHistory_BARU['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
         $tb_PaketTransactionHistory_BARU['deposit_address'] = $dataInput['infoJamaah']['address'];
         $tb_PaketTransactionHistory_BARU['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $tb_PaketTransactionHistory_BARU['input_date'] = date('Y-m-d H:i:s');
         $tb_PaketTransactionHistory_BARU['last_update'] = date('Y-m-d H:i:s');
         # insert process
         $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_BARU);

         // checking refund
         if (isset($dataPindahPaket['refund'])) {
            $tb_PaketTransactionHistory_REFUND = array();
            $tb_PaketTransactionHistory_REFUND['paket_transaction_id'] = $paket_transaction_id_tujuan;
            $tb_PaketTransactionHistory_REFUND['invoice'] = $dataPindahPaket['invoice_refund'];
            $tb_PaketTransactionHistory_REFUND['paid'] = $dataPindahPaket['refund'];
            if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
               $tb_PaketTransactionHistory_REFUND['receiver'] = 'administrator';
            } else {
               $tb_PaketTransactionHistory_REFUND['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            $tb_PaketTransactionHistory_REFUND['ket'] = 'refund';
            $tb_PaketTransactionHistory_REFUND['source'] = 'tunai';
            $tb_PaketTransactionHistory_REFUND['deposit_name'] = $dataInput['infoJamaah']['fullname'];
            $tb_PaketTransactionHistory_REFUND['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
            $tb_PaketTransactionHistory_REFUND['deposit_address'] = $dataInput['infoJamaah']['address'];
            $tb_PaketTransactionHistory_REFUND['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
            $tb_PaketTransactionHistory_REFUND['input_date'] = date('Y-m-d H:i:s');
            $tb_PaketTransactionHistory_REFUND['last_update'] = date('Y-m-d H:i:s');
            # insert process
            $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_REFUND);
         }

         # filter kalkulasi paket lama
         // kalkulasiPaketLama
         if ($dataInput['infoPaketAsal']['kalkulasiPaketLama'] == true) {
            # Melakukan update pada paket lama ke tabel
            #~~ paket_transaction
            $tb_PaketTransaction_LAMA = array();
            $tb_PaketTransaction_LAMA['total_mahram_fee'] = $dataInput['infoPaketAsal']['total_mahram_fee'];
            $tb_PaketTransaction_LAMA['total_paket_price'] = $dataInput['infoPaketAsal']['total_paket_price'];
            $tb_PaketTransaction_LAMA['last_update'] = date('Y-m-d H:i:s');
            # update process
            $this->db->where('id', $dataInput['infoPaketAsal']['paket_transaction_id'])
               ->where('company_id', $this->company_id)
               ->update('paket_transaction', $tb_PaketTransaction_LAMA);

            # insert info pindah paket dengan paket_transaction_id paket lama ke tabel
            #~~ paket_transaction_history
            $tb_PaketTransactionHistory_LAMA['paket_transaction_id'] = $dataInput['paket_transaction_id'];
            $tb_PaketTransactionHistory_LAMA['invoice'] = $this->text_ops->get_invoice_transaksi_paket();
            $tb_PaketTransactionHistory_LAMA['paid'] = $dataInput['biaya_yang_dipindahkan'];
            if ($this->session->userdata($this->config->item('apps_name'))['level_akun'] == 'administrator') {
               $tb_PaketTransactionHistory_LAMA['receiver'] = 'administrator';
            } else {
               $tb_PaketTransactionHistory_LAMA['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            }
            $tb_PaketTransactionHistory_LAMA['ket'] = 'pindah_paket';
            $tb_PaketTransactionHistory_LAMA['source'] = 'tunai';
            $tb_PaketTransactionHistory_LAMA['source_id'] = '';
            $tb_PaketTransactionHistory_LAMA['deposit_name'] = $dataInput['infoJamaah']['fullname'];
            $tb_PaketTransactionHistory_LAMA['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
            $tb_PaketTransactionHistory_LAMA['deposit_address'] = $dataInput['infoJamaah']['address'];
            $tb_PaketTransactionHistory_LAMA['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
            $tb_PaketTransactionHistory_LAMA['input_date'] = date('Y-m-d H:i:s');
            $tb_PaketTransactionHistory_LAMA['last_update'] = date('Y-m-d H:i:s');
            # insert process
            $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_LAMA);
         } else {
            # delete paket_transaction lama by paket_transaction_id
            $this->db->where('id', $dataInput['paket_transaction_id'])
               ->where('company_id', $this->company_id)
               ->delete('paket_transaction');
            # delete data paket_transaction_history lama by paket_transaction_id
            $this->db->where('paket_transaction_id', $dataInput['paket_transaction_id'])
               ->where('company_id', $this->company_id)
               ->delete('paket_transaction_history');
         }
      }

      // update handover item
      $dataHandoverItem = array();
      $dataHandoverItem['paket_transaction_id'] = $paket_transaction_id_tujuan;
      $this->db->where('paket_transaction_id', $dataInput['infoPaketAsal']['paket_transaction_id'])
         ->where('company_id', $this->company_id)
         ->update('handover_item', $dataHandoverItem);

      // update handover facilities
      $dataHandoverFacilities = array();
      $dataHandoverFacilities['paket_transaction_id'] = $paket_transaction_id_tujuan;
      $this->db->where('paket_transaction_id', $dataInput['infoPaketAsal']['paket_transaction_id'])
         ->where('company_id', $this->company_id)
         ->update('handover_facilities', $dataHandoverFacilities);

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan pemindahan paket jamaah dari paket ' . $tb_PindahPaket['paket_asal'] . ' dengan kode paket
                           ' . $tb_PindahPaket['kode_paket_asal'] . ' dan No Regitrasi ' . $tb_PindahPaket['no_register_asal'] . ' menuju paket ' . $tb_PindahPaket['paket_tujuan'] . '
                           dengan kode paket ' . $tb_PindahPaket['kode_paket_tujuan'] . ' dan Nomor Registrasi ' . $tb_PindahPaket['no_register_paket_tujuan'] . '.';
      }

      return array('status' => $this->status, 'pindahPaketId' => $pindahPaketId);
   }

   function insertPembayaranCicilan($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert to paket transaction table
      $this->db->insert('paket_transaction_installement_history', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menambahkan data transaksi paket dengan nomor Invoice : ' . $data['invoice'] . '  ';
      }
      return $this->status;
   }


   function insertSkemaCicilan($paket_transaction_id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # delete skema cicilan
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->where('company_id', $this->company_id)
         ->delete('paket_installment_scheme');
      # insert paket intallement scheme
      foreach ($data as $key => $value) {
         $dataInput = array();
         $dataInput['paket_transaction_id'] = $paket_transaction_id;
         $dataInput['term'] = $value['term'];
         $dataInput['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $dataInput['amount'] = $this->text_ops->hide_currency($value['amount']);
         $dataInput['duedate'] = $value['duedate'];
         $this->db->insert('paket_installment_scheme', $dataInput);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan update skema transaksi.';
      }
      return $this->status;
   }

   // update room table
   function update_rooms($id, $room_data, $jamaah_data)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update room data
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('rooms', $room_data);
      // delete room jamaah data
      $this->db->where('room_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('rooms_jamaah');
      foreach ($jamaah_data as $key => $value) {
         $data = array();
         $data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $data['room_id'] = $id;
         $data['jamaah_id'] = $value;
         // insert to database
         $this->db->insert('rooms_jamaah', $data);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan pembaharuan data kamar degan room id ' . $id . ' dan id penginapan ' . $room_data['hotel_id'] . ' dan paket id ' . $room_data['paket_id'] . '.';
      }
      return $this->status;
   }

   // insert rooms
   function insert_rooms($rooms_data, $jamaah_data)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update room data
      $this->db->insert('rooms', $rooms_data);
      $id = $this->db->insert_id();
      // delete room jamaah data
      foreach ($jamaah_data as $key => $value) {
         $data = array();
         $data['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $data['room_id'] = $id;
         $data['jamaah_id'] = $value;
         // insert to database
         $this->db->insert('rooms_jamaah', $data);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penambahan data kamar dengan room id ' . $id . ' dan paket id ' . $rooms_data['paket_id'] . '.';
      }
      return $this->status;
   }

   // delete kamar paket
   function delete_kamar_paket($room_id, $paket_id, $data)
   {
      $this->db->trans_start();
      // delete room jamaah
      $this->db->where('room_id', $room_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('rooms_jamaah');
      // delete rooms
      $this->db->where('id', $room_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('rooms');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus data kamar paket dengan kamar id : ' . $data['id'] . ', nama hotel : ' . $data['hotel_name'] . ', paket id : ' . $paket_id;
      }
      return $this->status;
   }

   function update_bus($id, $data, $jamaah)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update bus data
      $this->db->where('id', $id)
         ->where('company_id', $this->company_id)
         ->update('bus', $data);
      // delete bus jamaah data
      $this->db->where('bus_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('bus_jamaah');
      foreach ($jamaah as $key => $value) {
         $data_jamaah = array();
         $data_jamaah['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $data_jamaah['bus_id'] = $id;
         $data_jamaah['jamaah_id'] = $value;
         // insert to database
         $this->db->insert('bus_jamaah', $data_jamaah);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan pembaharuan data bus degan nomor bus ' . $data['bus_number'] . ' dan paket id ' . $data['paket_id'] . '.';
      }
      return $this->status;
   }

   # insert bus
   function insert_bus($data, $jamaah)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update bus data
      $this->db->insert('bus', $data);
      $id = $this->db->insert_id();
      // delete bus jamaah data
      foreach ($jamaah as $key => $value) {
         $data_jamaah = array();
         $data_jamaah['company_id'] = $this->session->userdata($this->config->item('apps_name'))['company_id'];
         $data_jamaah['bus_id'] = $id;
         $data_jamaah['jamaah_id'] = $value;
         // insert to database
         $this->db->insert('bus_jamaah', $data_jamaah);
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penambahan data bus dengan nomor bus ' . $data['bus_number'] . ' dan paket id ' . $data['paket_id'] . '.';
      }
      return $this->status;
   }

   // delete bus paket
   function delete_bus_paket($bus_id, $paket_id, $data)
   {
      $this->db->trans_start();
      // delete bus jamaah
      $this->db->where('bus_id', $bus_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('bus_jamaah');
      // delete bus
      $this->db->where('id', $bus_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('bus');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus data bus paket dengan bus id : ' . $data['id'] . ', nomor bus ' . $data['bus_number'] . ', paket id : ' . $paket_id;
      }
      return $this->status;
   }

   # update aktualisasi anggaran
   function update_aktualisasi_anggaran($aktualisasi_id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update aktualisasi anggaran data
      $this->db->where('id', $aktualisasi_id)
         ->where('company_id', $this->company_id)
         ->update('aktualisasi_kegiatan_paket', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan pembaharuan data aktualisasi anggaran dengan nomor aktualisasi id ' . $aktualisasi_id . ', paket id ' . $data['paket_id'] . '.';
      }
      return $this->status;
   }

   # insert aktualisasi anggaran
   function insert_aktualisasi_anggaran($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update bus data
      $this->db->insert('aktualisasi_kegiatan_paket', $data);
      $id = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penambahan data aktualisasi anggaran pakett dengan id aktualisasi ' . $id . ' dan paket id ' . $data['paket_id'] . '.';
      }
      return $this->status;
   }

   // delete aktualisasi anggaran
   function delete_aktualisasi_anggaran($aktualisasi_id)
   {
      $this->db->trans_start();
      // delete aktualisasi anggaran detail
      $this->db->where('aktualisasi_id', $aktualisasi_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('aktualisasi_kegiatan_paket_detail');
      // delete aktualisasi anggaran
      $this->db->where('id', $aktualisasi_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('aktualisasi_kegiatan_paket');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus data aktualisasi anggaran dengan aktualisasi id : ' . $aktualisasi_id;
      }
      return $this->status;
   }

   // update aktualisasi detail anggaran
   function update_aktualisasi_detail_anggaran($aktualisasi_detail_id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update aktualisasi anggaran data detail
      $this->db->where('id', $aktualisasi_detail_id)
         ->where('company_id', $this->company_id)
         ->update('aktualisasi_kegiatan_paket_detail', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan pembaharuan data aktualisasi anggaran detail dengan nomor aktualisasi detail id ' . $aktualisasi_detail_id . ', aktualisasi id ' . $data['aktualisasi_id'] . '.';
      }
      return $this->status;
   }

   // insert aktualisasi detail anggaran
   function insert_aktualisasi_detail_anggaran($data)
   {
      # Starting Transaction
      $this->db->trans_start();
      // update aktualisasi data detail
      $this->db->insert('aktualisasi_kegiatan_paket_detail', $data);
      $id = $this->db->insert_id();
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penambahan data aktualisasi anggaran detail paket dengan id aktualisasi detail ' . $id . ' dan aktulisasi id ' . $data['aktualisasi_id'] . '.';
      }
      return $this->status;
   }

   // delete aktualisasi anggaran detail
   function delete_aktualisasi_anggaran_detail($aktualisasi_detail_id)
   {
      $this->db->trans_start();
      // delete aktualisasi anggaran detail
      $this->db->where('id', $aktualisasi_detail_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('aktualisasi_kegiatan_paket_detail');
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus data aktualisasi anggaran dengan aktualisasi detail id : ' . $aktualisasi_detail_id;
      }
      return $this->status;
   }

   function close_paket($paket_id, $param)
   {
      # Starting Transaction
      $this->db->trans_start();
      // insert jurnal
      $data = array();
      $data[0]['ket']       = 'Pendapatan Paket ' . $param['info_paket']['paket_name'] . ' Dengan Kode Paket:  ' . $param['info_paket']['kode'];
      $data[0]['company_id']   = $this->company_id;
      $data[0]['akun_debet']   = $param['akun']['kas'];
      $data[0]['akun_kredit'] = $param['akun']['pendapatan_paket'];
      $data[0]['source'] = 'paketid:' . $paket_id;
      $data[0]['saldo'] = $param['saldo'];
      $data[0]['periode_id'] = $param['periode'];
      $data[0]['input_date']    = date('Y-m-d');
      $data[0]['last_update'] = date('Y-m-d');
      // hapus hutang tabungan
      $data[1]['ket'] = 'Pelunasan hutang tabungan jamaah pada paket ' .  $param['info_paket']['paket_name']  .' Dengan Kode Paket: '. $param['info_paket']['kode'];
      $data[1]['company_id']   = $this->company_id;
      $data[1]['akun_debet']   = '24000';
      $data[1]['akun_kredit'] = '11010';
      $data[1]['source'] = 'piutangpaketid:' . $paket_id;
      $data[1]['saldo'] = $param['info_paket']['total_harga_paket'];
      $data[1]['periode_id'] = $param['periode'];
      $data[1]['input_date']    = date('Y-m-d');
      $data[1]['last_update'] = date('Y-m-d');

      foreach ($data as $key => $value) {
         // insert DATA to jurnal
         $this->db->insert('jurnal', $value);
      }

      // update paket tutup_paket
      $this->db->where('id', $paket_id)
         ->where('company_id', $this->company_id)
         ->update('paket', array('tutup_paket' => 'tutup'));
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan penutupan paket dengan paket id ' . $paket_id . '.';
      }
      return $this->status;
   }

   function open_paket($paket_id)
   {
      # Starting Transaction
      $this->db->trans_start();
      // delete jurnal
      $this->db->where('source', 'paketid:' . $paket_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('jurnal');
      // delete jurnal piutang
      $this->db->where('source', 'piutangpaketid:' . $paket_id);
      $this->db->where('company_id', $this->company_id);
      $this->db->delete('jurnal');
      // update paket tutup_paket
      $this->db->where('id', $paket_id)
         ->where('company_id', $this->company_id)
         ->update('paket', array('tutup_paket' => 'buka'));
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Melakukan pembukaan paket dengan paket id ' . $paket_id . '.';
      }
      return $this->status;
   }

   # proses pembayaran fee keagenan
   function proses_pembayaran_fee_agen($id, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert fee keagenan payment
      $this->db->insert('fee_keagenan_payment', $data);
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Pembayaran fee keagenan dengan nomor invoice ' . $data['invoice'] . '.';
      }
      return $this->status;
   }

   # update fee keagenan
   public function update_fee_keagenan($paket_transaction_id, $nomor_register, $data)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert fee keagenan payment
      if( isset( $data['paket_transaction'] ) ) {
         $this->db->where('fee_keagenan_id', $data['paket_transaction']['fee_keagenan_id'])
                  ->where('company_id', $this->company_id)
                  ->delete('detail_fee_keagenan');
         $this->db->where('id', $data['paket_transaction']['fee_keagenan_id'])
                  ->where('company_id', $this->company_id)
                  ->delete('fee_keagenan');
         $this->db->where('id', $paket_transaction_id)
                  ->where('company_id', $this->company_id)
                  ->update('paket_transaction', array('fee_keagenan_id' => 0));            
      }
      # insert fee keagenan id
      $this->db->insert('fee_keagenan', $data['fee_keagenan']);
      # get fee keagenan
      $fee_keagenan_id = $this->db->insert_id();
      # insert detail fee keagenan id
      foreach ($data['detail_fee_keagenan'] as $key => $value) {
         $value['fee_keagenan_id'] = $fee_keagenan_id;
         # insert detail fee keagenan
         $this->db->insert('detail_fee_keagenan', $value);
      }
      # update paket transaction
       $this->db->where('id', $paket_transaction_id)
                ->where('company_id', $this->company_id)
                ->update('paket_transaction', array('fee_keagenan_id' => $fee_keagenan_id));       
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Update Fee Keagenan dengan nomor register transaksi ' . $nomor_register . '.';
      }
      return $this->status;
   }

   // update jamaah by member
   function update_jamaah_by_member($jamaah_id, $data){
      // start transaction
      $this->db->trans_start();
      // check key exist
      if ( array_key_exists( "personal",$data ) ) {
         // update photo in personal
         $this->db->where('personal_id', $data['jamaah']['personal_id'])
                  ->where('company_id', $this->company_id)
                  ->update('personal', $data['personal']);   
      }
      // update data jamaah
      $this->db->where('id', $id)
                ->where('company_id', $this->company_id)
                ->update('jamaah', $data);       
      // delete data mahram
      $this->db->where('jamaah_id', $id)
         ->where('company_id', $this->company_id)
         ->delete('mahram');
      // mahram
      if ( count( $data['mahram'] ) > 0) {
         foreach ($data['mahram'] as $key => $value) {
            // insert jamaah id in mahram table
            $value['jamaah_id'] = $jamaah_id;
            // insert data mahram
            $insert = $this->db->insert('mahram', $value);
         }
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Memperbaharui data jamaah dengan personal id ' . $data['jamaah']['personal_id']. ' dengan jamaah id '. $jamaah_id;
      }
      return $this->status;
   }

   // insert jamaah by member
   function insert_jamaah_by_member( $data ){
      // start transaction
      $this->db->trans_start();
      // check key exist
      if ( array_key_exists( "personal",$data ) ) {
         // update photo in personal
         $this->db->where('personal_id', $data['jamaah']['personal_id'])
                  ->where('company_id', $this->company_id)
                  ->update('personal', $data['personal']);   
      }
      // insert data jamaah
      $insert = $this->db->insert('jamaah', $data['jamaah']);
      // get jamaah id
      $jamaah_id = $this->db->insert_id();
      // mahram
      if ( count( $data['mahram'] ) > 0 ) {
         foreach ( $data['mahram'] as $key => $value ) {
            // insert jamaah id in mahram table
            $value['jamaah_id'] = $jamaah_id;
            // insert data mahram
            $insert = $this->db->insert('mahram', $value);
         }
      }
      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menambahkan jamaah baru dari personal id ' . $data['jamaah']['personal_id']. ' dengan jamaah id '. $jamaah_id;
      }
      return $this->status;
   }

   /* Write log master data*/
   public function __destruct()
   {
      if ($this->write_log == 1) {
         if ($this->status == true) {
            if ($this->error == 0) {
               $this->syslog->write_log($this->content);
            }
         }
      }
   }
}
