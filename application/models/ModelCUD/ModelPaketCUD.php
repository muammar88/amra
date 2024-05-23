<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ModelPaketCUD extends CI_Model
{

   private $status;
   private $content;
   private $error;

   public function __construct()
   {
      parent::__construct();
      $this->error = 0;
   }

   # Update Tipe Paket
   public function updateTipePaket($data, $id)
   {
      $this->db->where('id', $id);
      $update = $this->db->update('mst_paket_type', $data);
      if (!$update) {
         return 1;
      } else {
         $this->error = 0;
         $this->content = 'Mengubah Tipe Paket Dengan Tipe Paket ID ' . $id . '';
         return 0;
      }
   }

   # Insert Tipe Paket
   public function insertTipePaket($data)
   {
      $insert = $this->db->insert('mst_paket_type', $data);
      $id = $this->db->insert_id();
      if (!$insert) {
         return 1;
      } else {
         $this->error = 0;
         $this->content = 'Menambahkan Tipe Paket Dengan Tipe Paket ID ' . $id . '';
         return 0;
      }
   }

   # Delete Tipe Paket
   public function deleteTipePaket($id)
   {
      $this->db->where('id', $id);
      $delete = $this->db->delete('mst_paket_type');
      if (!$delete) {
         return 1;
      } else {
         return 0;
      }
   }

   # update paket
   public function updatePaket($id, $data, $data_muthawif, $data_paket_price, $data_itinerary)
   {
      # Starting Transaction
      $this->db->trans_start();
      // delete price
      $this->db->where('paket_id', $id);
      $this->db->delete('paket_price');
      // delete muthawif
      $this->db->where('paket_id', $id);
      $this->db->delete('paket_muthawif');
      // delete itinerary
      $this->db->where('paket_id', $id);
      $this->db->delete('paket_itinerary');
      # Insert Paket Price
      foreach ($data_paket_price as $keyPrice => $valuePrice) {
         $dataPrice = array();
         $dataPrice['paket_id'] = $id;
         $dataPrice['paket_type_id'] = $valuePrice['tipe_paket_id'];
         $dataPrice['price'] = $this->text_ops->hide_currency($valuePrice['tipe_paket_price']);
         $dataPrice['input_date'] = date('Y-m-d H:i:s');
         $dataPrice['last_update'] = date('Y-m-d H:i:s');
         $insert = $this->db->insert('paket_price', $dataPrice);
      }
      # Insert Muthawif
      foreach ($data_muthawif as $keyMuthawif => $valueMuthawif) {
         $dataMuthawif = array();
         $dataMuthawif['paket_id'] = $id;
         $dataMuthawif['muthawif_id'] = $valueMuthawif;
         $insert = $this->db->insert('paket_muthawif', $dataMuthawif);
      }
      # Insert Itinerary
      foreach ($data_itinerary as $keyitinerary  => $valueitinerary) {
         $dataItinerary = array();
         $dataItinerary['paket_id'] = $id;
         $dataItinerary['activity_date'] = $valueitinerary['activity_date'];
         $dataItinerary['activity_title'] = $valueitinerary['activity_title'];
         $dataItinerary['description'] = $valueitinerary['description'];
         $dataItinerary['input_date'] = date('Y-m-d H:i:s');
         $dataItinerary['last_update'] = date('Y-m-d H:i:s');
         $insert = $this->db->insert('paket_itinerary', $dataItinerary);
      }

      // update paket info
      $this->db->where('id', $id);
      $this->db->update('paket', $data);
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
         $this->content = 'Mengubah data paket ID : ' . $id . ' dengan nama paket' . $data['paket_name'] . ' ';
      }
      return $this->status;
   }

   # inser paket
   public function insertPaket($data, $data_muthawif, $data_paket_price, $data_itinerary)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert paket
      $insert = $this->db->insert('paket', $data);
      $id = $this->db->insert_id();
      # Insert Paket Price
      foreach ($data_paket_price as $keyPrice => $valuePrice) {
         $dataPrice = array();
         $dataPrice['paket_id'] = $id;
         $dataPrice['paket_type_id'] = $valuePrice['tipe_paket_id'];
         $dataPrice['price'] = $this->text_ops->hide_currency($valuePrice['tipe_paket_price']);
         $dataPrice['input_date'] = date('Y-m-d H:i:s');
         $dataPrice['last_update'] = date('Y-m-d H:i:s');
         $insert = $this->db->insert('paket_price', $dataPrice);
      }
      # Insert Muthawif
      foreach ($data_muthawif as $keyMuthawif => $valueMuthawif) {
         if ($valueMuthawif != 0) {
            $dataMuthawif = array();
            $dataMuthawif['paket_id'] = $id;
            $dataMuthawif['muthawif_id'] = $valueMuthawif;
            $insert = $this->db->insert('paket_muthawif', $dataMuthawif);
         }
      }
      # Insert Itinerary
      foreach ($data_itinerary as $keyitinerary  => $valueitinerary) {
         $dataItinerary = array();
         $dataItinerary['paket_id'] = $id;
         $dataItinerary['activity_date'] = $valueitinerary['activity_date'];
         $dataItinerary['activity_title'] = $valueitinerary['activity_title'];
         $dataItinerary['description'] = $valueitinerary['description'];
         $dataItinerary['input_date'] = date('Y-m-d H:i:s');
         $dataItinerary['last_update'] = date('Y-m-d H:i:s');
         $insert = $this->db->insert('paket_itinerary', $dataItinerary);
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
         $this->content = 'Menambahkan data paket ID : ' . $id . ' dengan nama paket' . $data['paket_name'] . ' ';
      }
      return $this->status;
   }

   # delete paket
   function deletePaket($id, $paket_name, $photo)
   {

      $this->db->trans_start();

      #  paket muthawif
      $this->db->where('paket_id', $id);
      $delete = $this->db->delete('paket_muthawif');

      # paket price
      $this->db->where('paket_id', $id);
      $delete = $this->db->delete('paket_price');

      # paket itinerary
      $this->db->where('paket_id', $id);
      $delete = $this->db->delete('paket_itinerary');

      # get paket transaction
      $this->db->select('id')
         ->from('paket_transaction')
         ->where('paket_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {

            // delete paket transaction history
            $this->db->where('paket_transaction_id', $row->id);
            $deletePTH = $this->db->delete('paket_transaction_history');

            // delete paket transaction history member
            $this->db->where('paket_transaction_id', $row->id);
            $deletePTHM = $this->db->delete('paket_transaction_history_member_request');

            // delete paket transaction jamaah_id
            $this->db->where('paket_transaction_id', $row->id);
            $deletePTJ = $this->db->delete('paket_transaction_jamaah');

            // delete paket transaction
            $this->db->where('id', $row->id);
            $deletePT = $this->db->delete('paket_transaction');
         }
      }

      // get paket transaction
      $this->db->select('id')
         ->from('paket_transaction_member_request')
         ->where('paket_id', $id);
      $q = $this->db->get();
      if ($q->num_rows() > 0) {
         foreach ($q->result() as $row) {

            // delete paket transaction history member
            $this->db->where('paket_transaction_member_request_id', $row->id);
            $deletePTHM = $this->db->delete('paket_transaction_history_member_request');

            # paket transaction member request
            $this->db->where('id', $id);
            $deletePTMR = $this->db->delete('paket_transaction_member_request');
         }
      }

      # delete paket
      $this->db->where('id', $id);
      $delete = $this->db->delete('paket');

      # Transaction Complete
      $this->db->trans_complete();
      # Filter Status
      if ($this->db->trans_status() === FALSE) {
         # Something Went Wsrong.
         $this->db->trans_rollback();
         $this->status = FALSE;
         $this->error = 1;
      } else {
         // delete photo
         $src = FCPATH . 'image/paket/' . $photo;
         if (file_exists($src)) {
            unlink($src);
         }
         # Transaction Commit
         $this->db->trans_commit();
         $this->status = TRUE;
         $this->content = 'Menghapus data paket ID : ' . $id . ' dengan nama paket' . $paket_name . ' ';
      }
      return $this->status;
   }

   function updateTransactionProcess($paket_transaction_id, $dataTransaction, $dataHistory, $dataJamaah, $dataDeposit)
   {
      // update

   }

   function insertTransactionProcess($dataTransaction, $dataHistory, $dataJamaah, $dataDeposit, $infoPaket)
   {
      # Starting Transaction
      $this->db->trans_start();
      # insert to paket transaction table
      $dataTransaction['input_date'] = date('Y-m-d H:i:s');
      $this->db->insert('paket_transaction', $dataTransaction);
      $paket_transaction_id = $this->db->insert_id();

      # masalah disini
      # insert installement shceme
      if ($dataTransaction['payment_methode'] == 1) {

         if ($dataHistory['ket'] == 'dp') {
            $pinjam = $dataTransaction['total_paket_price'] - $dataHistory['paid'];
            $amount =  $pinjam / $dataTransaction['tenor'];
         } else {
            $amount = $dataTransaction['total_paket_price'] / $dataTransaction['tenor'];
         }

         $amount = ceil($amount);
         if (substr($amount, -3) > 499) {
            $amount = round($amount, -3);
         } else {
            $amount = round($amount, -3) + 1000;
         }

         $dueDate = $dataTransaction['start_date'];

         $totalPinjam = $dataTransaction['total_paket_price'] - $dataTransaction['down_payment'];
         $sisaTotalPinjam = $totalPinjam;
         for ($i = 1; $i <= $dataTransaction['tenor']; $i++) {
            if ($i != 1) {
               $dueDate = date('Y-m-d', strtotime('+' . ($i - 1) . ' month', strtotime($dataTransaction['start_date'])));
            }
            $dataScheme = array();
            $dataScheme['paket_transaction_id'] = $paket_transaction_id;
            $dataScheme['term'] = $i;
            if ($sisaTotalPinjam < $amount) {
               $dataScheme['amount'] = $sisaTotalPinjam;
            } else {
               $dataScheme['amount'] = $amount;
            }
            $dataScheme['duedate'] = $dueDate;
            $this->db->insert('paket_installment_scheme', $dataScheme);
            $sisaTotalPinjam = $sisaTotalPinjam - $amount;
         }
      }

      # insert to paket transaction jamaah
      $jamaah = $dataJamaah['jamaah'];
      $leader = $dataJamaah['leader'];
      foreach ($jamaah  as $keyDataJamaah => $valueDataJamaah) {
         $data_jamaah = array();
         $data_jamaah['paket_transaction_id'] = $paket_transaction_id;
         $data_jamaah['jamaah_id'] = $valueDataJamaah;
         if ($valueDataJamaah == $leader) {
            $data_jamaah['leader'] = '1';
         } else {
            $data_jamaah['leader'] = '0';
         }
         $this->db->insert('paket_transaction_jamaah', $data_jamaah);
      }
      # insert transaction history
      # if metode pembayaran 0 OR cash \insert to paket transaction history
      if (count($dataHistory) > 0) {
         if ($this->input->post('metode_pembayaran') == 0) {
            $dataHistory['paket_transaction_id'] = $paket_transaction_id;
            $dataHistory['input_date'] = date('Y-m-d H:i:s');
            $this->db->insert('paket_transaction_history', $dataHistory);
            # insert deposit
            if ($this->input->post('sumber_biaya') == 1 and count($dataDeposit) > 0) {
               $this->db->insert('deposit_transaction', $dataDeposit);
            }
         } elseif ($this->input->post('metode_pembayaran') == 1) {
            $dataHistory['paket_transaction_id'] = $paket_transaction_id;
            $dataHistory['input_date'] = date('Y-m-d H:i:s');
            $this->db->insert('paket_transaction_installement_history', $dataHistory);
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
         $this->content = 'Menambahkan data transaksi paket dengan nomor Register : ' . $dataTransaction['no_register'] . ' dengan nama kode paket' . $infoPaket['kode'] . ' dan nama paket :' . $infoPaket['nama_paket'] . ' ';
      }
      return $this->status;
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

   function checkingDepositPersonalExist($paket_transaction_id)
   {
      $this->db->select('paid, source_id')
         ->from('paket_transaction_history')
         ->where('paket_transaction_id', $paket_transaction_id)
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

   function deleteTransaksiPaket($paket_id, $paket_transaction_id, $data)
   {

      $feedBack = $this->checkingDepositPersonalExist($paket_transaction_id);

      # Starting Transaction
      $this->db->trans_start();

      # check penggunaan deposit
      if (count($feedBack) > 0) {
         foreach ($feedBack as $key => $value) {
            $dataDeposit = array();
            $dataDeposit['personal_id'] = $key;
            $dataDeposit['debet'] = $value;
            $dataDeposit['status_deposit'] = 'deposit';
            $dataDeposit['info'] = 'Pengembalian Deposit Member karena proses delete transaksi';
            $dataDeposit['approver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            $dataDeposit['input_date'] = date('Y-m-d H:i:s');
            $dataDeposit['last_update'] = date('Y-m-d H:i:s');
            $this->db->insert('deposit_transaction', $dataDeposit);
         }
      }

      // delete paket transaction history
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->delete('paket_transaction_history');

      // delete paket transaction jamaah
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->delete('paket_transaction_jamaah');

      // delete paket_transaction_installement_history
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->delete('paket_transaction_installement_history');

      // delete paket_installment_scheme
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->delete('paket_installment_scheme');

      // delete paket transaction paket
      $this->db->where('paket_id', $paket_id)
         ->where('id', $paket_transaction_id)
         ->delete('paket_transaction');

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

   function insertSkemaCicilan($paket_transaction_id, $data)
   {

      # Starting Transaction
      $this->db->trans_start();

      # delete skema cicilan
      $this->db->where('paket_transaction_id', $paket_transaction_id)
         ->delete('paket_installment_scheme');

      # insert paket intallement scheme
      foreach ($data as $key => $value) {
         $dataInput = array();
         $dataInput['paket_transaction_id'] = $paket_transaction_id;
         $dataInput['term'] = $value['term'];
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

   # insert refund
   function insertRefund($paket_transaction_id, $data, $batalBerangkat)
   {

      # Starting Transaction
      $this->db->trans_start();

      # update paket transaction
      if ($batalBerangkat == 1) {
         $dataPaketTransaction['batal_berangkat'] = 1;
         $this->db->where('id', $paket_transaction_id);
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
         $this->content = 'Melakukan insert transaksi refund.';
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
         $data['status'] = 'diambil';
         $data['receiver_handover'] = $this->session->userdata($this->config->item('apps_name'))['user_id'];
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

         $this->db->where('id', $value);
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
         $data['officer'] = $this->session->userdata($this->config->item('apps_name'))['user_id'];
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

   public function addUpdatePaketPindah($dataInput, $dataPindahPaket)
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
      # insert to pindah paket table
      $this->db->insert('pindah_paket', $tb_PindahPaket);

      $pindahPaketId = $this->db->insert_id();

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
         $tb_PaketTransaction_BARU['total_paket_price'] = $dataInput['infoPaketTujuan']['price_per_pax'];
         $tb_PaketTransaction_BARU['price_per_pax'] = $dataInput['infoPaketTujuan']['price_per_pax'] + $mahramFee;
         $tb_PaketTransaction_BARU['batal_berangkat'] = '0';
         $tb_PaketTransaction_BARU['input_date'] = date('Y-m-d H:i:s');
         $tb_PaketTransaction_BARU['last_update'] = date('Y-m-d H:i:s');
         # insert process paket_transaction
         $this->db->insert('paket_transaction', $tb_PaketTransaction_BARU);
         $paket_transaction_id_BARU = $this->db->insert_id();

         # Melakukan update paket_transaction_id
         # dari paket_transaction_id lama ke paket_transaction_id yang baru ke dalam table
         #~~ paket_transaction_jamaah
         $tb_PaketTransactionJamaah_BARU = array();
         $tb_PaketTransactionJamaah_BARU['paket_transaction_id'] = $paket_transaction_id_BARU;
         $tb_PaketTransactionJamaah_BARU['leader'] = '1';
         # update process paket_transaction_jamaah
         $this->db->where('jamaah_id', $dataInput['infoJamaah']['jamaah_id'])
            ->where('paket_transaction_id', $dataInput['paket_transaction_id'])
            ->update('paket_transaction_jamaah', $tb_PaketTransactionJamaah_BARU);

         # insert paket transaction history paket transaksi baru
         $tb_PaketTransactionHistory_BARU = array();
         $tb_PaketTransactionHistory_BARU['paket_transaction_id'] = $paket_transaction_id_BARU;
         $tb_PaketTransactionHistory_BARU['invoice'] = $dataPindahPaket['invoice_tujuan'];
         $tb_PaketTransactionHistory_BARU['paid'] = $dataInput['biaya_yang_dipindahkan'];
         $tb_PaketTransactionHistory_BARU['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         $tb_PaketTransactionHistory_BARU['ket'] = 'cash';
         $tb_PaketTransactionHistory_BARU['source'] = 'tunai';
         $tb_PaketTransactionHistory_BARU['deposit_name'] = $dataInput['infoJamaah']['fullname'];
         $tb_PaketTransactionHistory_BARU['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
         $tb_PaketTransactionHistory_BARU['deposit_address'] = $dataInput['infoJamaah']['address'];
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
            $tb_PaketTransactionHistory_REFUND['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            $tb_PaketTransactionHistory_REFUND['ket'] = 'refund';
            $tb_PaketTransactionHistory_REFUND['source'] = 'tunai';
            $tb_PaketTransactionHistory_REFUND['deposit_name'] = $dataInput['infoJamaah']['fullname'];
            $tb_PaketTransactionHistory_REFUND['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
            $tb_PaketTransactionHistory_REFUND['deposit_address'] = $dataInput['infoJamaah']['address'];
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
            $this->db->where('id', $dataInput['infoPaketAsal']['paket_id'])
               ->update('paket_transaction', $tb_PaketTransaction_LAMA);

            # insert info pindah paket dengan paket_transaction_id paket lama ke tabel
            #~~ paket_transaction_history
            $tb_PaketTransactionHistory_LAMA['paket_transaction_id'] = $dataInput['paket_transaction_id'];
            $tb_PaketTransactionHistory_LAMA['invoice'] = $this->text_ops->get_invoice_transaksi_paket();
            $tb_PaketTransactionHistory_LAMA['paid'] = $dataInput['biaya_yang_dipindahkan'];
            $tb_PaketTransactionHistory_LAMA['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            $tb_PaketTransactionHistory_LAMA['ket'] = 'pindah_paket';
            $tb_PaketTransactionHistory_LAMA['source'] = 'tunai';
            $tb_PaketTransactionHistory_LAMA['source_id'] = '';
            $tb_PaketTransactionHistory_LAMA['deposit_name'] = $dataInput['infoJamaah']['fullname'];
            $tb_PaketTransactionHistory_LAMA['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
            $tb_PaketTransactionHistory_LAMA['deposit_address'] = $dataInput['infoJamaah']['address'];
            $tb_PaketTransactionHistory_LAMA['input_date'] = date('Y-m-d H:i:s');
            $tb_PaketTransactionHistory_LAMA['last_update'] = date('Y-m-d H:i:s');
            # insert process
            $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_LAMA);
         } else {
            // delete paket_transaction lama by paket_transaction_id
            $this->db->where('id', $dataInput['paket_transaction_id'])
               ->delete('paket_transaction');

            # delete data paket_transaction_history lama by paket_transaction_id
            $this->db->where('paket_transaction_id', $dataInput['paket_transaction_id'])
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
            ->update('paket_transaction', $tb_PaketTransaction_TUJUAN);

         # update paket_transaksi_id pada tabel paket_transaction_jamaah dengan paket_transaction_id baru
         $tb_PaketTransactionJamaah_BARU = array();
         $tb_PaketTransactionJamaah_BARU['paket_transaction_id'] = $dataInput['infoPaketTujuan']['paket_transaction_id'];
         # update process paket_transaction_jamaah
         $this->db->where('jamaah_id', $dataInput['infoJamaah']['jamaah_id'])
            ->where('paket_transaction_id', $dataInput['paket_transaction_id'])
            ->update('paket_transaction_jamaah', $tb_PaketTransactionJamaah_BARU);

         # insert paket transaction history paket transaksi baru
         $tb_PaketTransactionHistory_BARU = array();
         $tb_PaketTransactionHistory_BARU['paket_transaction_id'] = $dataInput['infoPaketTujuan']['paket_transaction_id'];
         $tb_PaketTransactionHistory_BARU['invoice'] = $dataPindahPaket['invoice_tujuan'];
         $tb_PaketTransactionHistory_BARU['paid'] = $dataInput['biaya_yang_dipindahkan'];
         $tb_PaketTransactionHistory_BARU['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
         $tb_PaketTransactionHistory_BARU['ket'] = 'cash';
         $tb_PaketTransactionHistory_BARU['source'] = 'tunai';
         $tb_PaketTransactionHistory_BARU['deposit_name'] = $dataInput['infoJamaah']['fullname'];
         $tb_PaketTransactionHistory_BARU['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
         $tb_PaketTransactionHistory_BARU['deposit_address'] = $dataInput['infoJamaah']['address'];
         $tb_PaketTransactionHistory_BARU['input_date'] = date('Y-m-d H:i:s');
         $tb_PaketTransactionHistory_BARU['last_update'] = date('Y-m-d H:i:s');
         # insert process
         $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_BARU);

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
               ->update('paket_transaction', $tb_PaketTransaction_LAMA);

            # insert info pindah paket dengan paket_transaction_id paket lama ke tabel
            #~~ paket_transaction_history
            $tb_PaketTransactionHistory_LAMA['paket_transaction_id'] = $dataInput['paket_transaction_id'];
            $tb_PaketTransactionHistory_LAMA['invoice'] = $this->text_ops->get_invoice_transaksi_paket();
            $tb_PaketTransactionHistory_LAMA['paid'] = $dataInput['biaya_yang_dipindahkan'];
            $tb_PaketTransactionHistory_LAMA['receiver'] = $this->session->userdata($this->config->item('apps_name'))['fullname'];
            $tb_PaketTransactionHistory_LAMA['ket'] = 'pindah_paket';
            $tb_PaketTransactionHistory_LAMA['source'] = 'tunai';
            $tb_PaketTransactionHistory_LAMA['source_id'] = '';
            $tb_PaketTransactionHistory_LAMA['deposit_name'] = $dataInput['infoJamaah']['fullname'];
            $tb_PaketTransactionHistory_LAMA['deposit_phone'] = $dataInput['infoJamaah']['no_hp'];
            $tb_PaketTransactionHistory_LAMA['deposit_address'] = $dataInput['infoJamaah']['address'];
            $tb_PaketTransactionHistory_LAMA['input_date'] = date('Y-m-d H:i:s');
            $tb_PaketTransactionHistory_LAMA['last_update'] = date('Y-m-d H:i:s');
            # insert process
            $this->db->insert('paket_transaction_history', $tb_PaketTransactionHistory_LAMA);
         } else {
            # delete paket_transaction lama by paket_transaction_id
            $this->db->where('id', $dataInput['paket_transaction_id'])
               ->delete('paket_transaction');
            # delete data paket_transaction_history lama by paket_transaction_id
            $this->db->where('paket_transaction_id', $dataInput['paket_transaction_id'])
               ->delete('paket_transaction_history');
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
         $this->content = 'Melakukan pemindahan paket jamaah dari paket ' . $tb_PindahPaket['paket_asal'] . ' dengan kode paket
                           ' . $tb_PindahPaket['kode_paket_asal'] . ' dan No Regitrasi ' . $tb_PindahPaket['no_register_asal'] . ' menuju paket ' . $tb_PindahPaket['paket_tujuan'] . '
                           dengan kode paket ' . $tb_PindahPaket['kode_paket_tujuan'] . ' dan Nomor Registrasi ' . $tb_PindahPaket['no_register_paket_tujuan'] . '.';
      }

      return array('status' => $this->status, 'pindahPaketId' => $pindahPaketId);
   }

   // if tipe aksi == 0

   // buat paket transaksi baru
   // kalkulasi ulang semua total paket pada paket baru

   // update paket_transaction_id pada tabel paket transaction jamaah menjadi paket_transaction_id yang terbaru
   // jika biaya berlebih maka lakukan refund
   // jika masih ada jamaah di transaction lama
   // Maka updat paket_transaction_jamaah dengan mengurangi jumlah jamah di dalam paket transaksi
   // kalkulasi ulang total harga paket transaksi lama
   // else
   // update paket transaction id pada tabel paket_transaction_history menjadi paket_transaction_id terbaru
   // else
   // kalkulasi ulang total_mahram_fee dan total_paket_price paket tujuan
   // update paket_transaksi_id pada tabel paket_transaction_jamaah dengan paket_transaction_id baru
   // tambahkan biaya yang dipindahkan dari paket transaksi history lama ke dalam paket transaksi history baru

   // jika masih ada jamaah di transaksi yang lama
   // updat paket_transaction_jamaah dengan mengurangi jumlah jamah di dalam paket transaksi
   // kalkulasi ulang total harga paket transaksi lama
   // else
   # delete paket_transaction lama by paket_transaction_id
   # delete data paket_transaction_history lama by paket_transaction_id



   /* Write log master data*/
   public function __destruct()
   {
      if ($this->status == true) {
         if ($this->error == 0) {
            $this->syslog->write_log($this->content);
         }
      }
   }
}
