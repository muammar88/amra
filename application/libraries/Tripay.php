<?php

/**
 *  -----------------------
 *	Tripay library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Tripay
{
    private $production = true;
    private $url_sandbox = 'https://tripay.id/api-sandbox/v2/';
    private $url_production = 'https://tripay.id/api/v2/';
    private $api_key = '3SZA2ssdoqIzHJ39RNddeqDh9eO1OBMw';
    private $tripay_callback_key = '583hb13Z183799O014785R4nB4TW294sassadk5N6U3K45C61u969JA063784097460qwq2559021S05G8714c509Y365E9sas54405762z03DI57f72671q54jx06869p5506458l0o380sswreqweqevxk897213612eb1123k1vh';
    private $main_url = '';
    private $pin = '9089';
    private $no_hp_owner = '085262802141';

    public function __construct()
    {
        $this->tripay = &get_instance();
        $this->main_url = $this->production == true ? $this->url_production : $this->url_sandbox;
    }

    public function tripay_callback_key(){
        return $this->tripay_callback_key;
    }

    function get_harga_from_server($product_code){
        $req = $this->requestCekharga($product_code);
        $harga = 0;
        if ( isset( $req->success ) AND $req->success == 1  ) {
            if ( isset( $req->data->price ) ) {
                $harga = $req->data->price;
            }
        }
        return $harga;
    }

    function get_trx($ref_id){
        $this->tripay->db->select('trxid')
                 ->from('ppob_transaction_history')
                 ->where('transaction_code', $ref_id);
        $q = $this->tripay->db->get();
        $return = '';
        if( $q->num_rows() > 0 ){
            foreach ( $q->result() as $rows ) {
                $return = $rows->trxid;
            }
        }
        return $return;
    }

    public function check_status_transaksi($ref_id){
        $get_trx = $this->get_trx($ref_id);

        $url = $this->main_url . 'histori/transaksi/detail';
        $payload = array();
        $payload['trxid'] = $get_trx;
        $payload['api_trxid'] = $ref_id;

        // print_r($payload);

        // $payload = ['code' => $ref_id];
        // $url = $this->main_url . '/pembelian/produk/cek?'.http_build_query($payload);

        // $url = $this->main_url . 'transaksi/detail?'.http_build_query($payload);
        // return $this->requestBodyGet($url);

        return $this->requestDetail($url, $payload);
    }

        //     $payload = [
    //         'inquiry'       => 'I', //PLN untuk pembelian PLN Prabayar, atau I untuk produk lainnya,
    //         'code'          => 'AX5',
    //         'phone'         => '083856000000',
    //         'no_meter_pln'  => '123232132',
    //         'api_trxid'     => 'INV123',
    //         'pin'           => '1234',
    //     ];


    public function topUp($ref_id, $amra_product_code, $costumer_id) {
        $pln_status = $this->check_status_pln($amra_product_code); 
        $url = $this->main_url . 'transaksi/pembelian';
        $payload = array();
        if( $pln_status == 'PLN' ){
            $payload['inquiry'] = 'PLN';
            $payload['no_meter_pln'] = $costumer_id;
            $payload['phone'] = $this->no_hp_owner;
        }else{
            $payload['inquiry'] = 'I';
            $payload['phone'] = $costumer_id;
        }
        $payload['code'] = $this->convert_product_code_amra_to_tripay($amra_product_code);
        $payload['api_trxid'] = $ref_id;
        return $this->requestTopUp($url, $payload);
    }


    function check_status_pln($product_code){
        $this->tripay->db->select('ppp.id, ppc.category_code')
                 ->from('ppob_prabayar_product AS ppp')
                 ->join('ppob_prabayar_operator AS ppo', 'ppp.operator_id=ppo.id', 'inner')
                 ->join('ppob_prabayar_category AS ppc', 'ppo.category_id=ppc.id', 'inner')
                 ->where('ppp.product_code', $product_code);                 ;
        $q = $this->tripay->db->get();
        $status = 'I';
        if( $q->num_rows() > 0 ) {
            foreach ($q->result() as $rows) {
                if($rows->category_code == 'TL') {
                    $status = 'PLN';
                }
            }
        }
        return $status;
    }

    function convert_product_code_amra_to_tripay($product_code){
        $this->tripay->db->select('ptripay.product_code')
                 ->from('ppob_tripay_prabayar_product AS ptripay')
                 ->join('ppob_product_local_to_server_product AS local', 'ptripay.id=local.product_id_tripay', 'inner')
                 ->join('ppob_prabayar_product AS pproduct', 'local.product_id=pproduct.id', 'inner')
                 ->where('pproduct.product_code', $product_code);
        $code = '';
        $q = $this->tripay->db->get();
        if( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $row ) {
                $code = $row->product_code;
            }
        }
        return $code;
    }

    function check_balance(){
        $url = $this->main_url . 'ceksaldo';
        return $this->requestBodyGet($url);
    }

    function get_category(){
        $url = $this->main_url . 'pembelian/category';
        $category = $this->requestBodyGet($url);
         if (isset($category->data)){
            foreach ($category->data as $keyKategori => $valueKategori) {
                $this->tripay->db->select('id')
                         ->from('ppob_tripay_prabayar_kategori')
                         ->where('id', $valueKategori->id);
                $q = $this->tripay->db->get();
                if( $q->num_rows() > 0 ) {
                    // update categori tripay
                    $this->tripay->db->trans_start();
                    # update data ppob_tripay_prabayar_kategori
                    $this->tripay->db->where('id', $valueKategori->id)
                                     ->update('ppob_tripay_prabayar_kategori', 
                                                    array('category' => $valueKategori->product_name, 
                                                          'type' => $valueKategori->type, 
                                                          'status' => $valueKategori->status == 1 ? 'tersedia'  : 'tidak tersedia'));
                    # Transaction Complete
                    $this->tripay->db->trans_complete();
                    # Filter Status
                    if ($this->tripay->db->trans_status() === FALSE) {
                        # Something Went Wrong.
                        $this->tripay->db->trans_rollback();
                    } else {
                        # Transaction Commit
                        $this->tripay->db->trans_commit();
                    }
                }else{
                    // insert new categori
                    $this->tripay->db->trans_start();
                    # insert 
                    $this->tripay->db->insert('ppob_tripay_prabayar_kategori', 
                                                    array('id' => $valueKategori->id,
                                                          'category' => $valueKategori->product_name, 
                                                          'type' => $valueKategori->type, 
                                                          'status' => $valueKategori->status == 1 ? 'tersedia'  : 'tidak tersedia'));
                    # Transaction Complete
                    $this->tripay->db->trans_complete();
                    # Filter Status
                    if ($this->tripay->db->trans_status() === FALSE) {
                        # Something Went Wsrong.
                        $this->tripay->db->trans_rollback();
                    } else {
                        # Transaction Commit
                        $this->tripay->db->trans_commit();
                    }
                }
            }
         }
    }

    function get_operator(){
        $url_operator = $this->main_url . 'pembelian/operator';
        $operator = $this->requestBodyGet($url_operator);
        // print_r($operator);
        if (isset($operator->data)){
            foreach ($operator->data as $keyOperator => $valueOperator) {
                $this->tripay->db->select('id')
                         ->from('ppob_tripay_prabayar_operator')
                         ->where('id', $valueOperator->id)
                         ->where('category_id', $valueOperator->pembeliankategori_id);
                $q = $this->tripay->db->get();
                if( $q->num_rows() > 0 ) {
                    // update categori tripay
                    $this->tripay->db->trans_start();
                    # update data ppob_tripay_prabayar_operator
                    $this->tripay->db->where('id', $valueOperator->id)
                                     ->update('ppob_tripay_prabayar_operator', 
                                                    array('operator_code' => $valueOperator->product_id, 
                                                          'operator' => $valueOperator->product_name, 
                                                          'category_id' => $valueOperator->pembeliankategori_id,
                                                          'status' => $valueOperator->status == 1 ? 'tersedia'  : 'tidak tersedia'));
                    # Transaction Complete
                    $this->tripay->db->trans_complete();
                    # Filter Status
                    if ($this->tripay->db->trans_status() === FALSE) {
                        # Something Went Wrong.
                        $this->tripay->db->trans_rollback();
                    } else {
                        # Transaction Commit
                        $this->tripay->db->trans_commit();
                    }
                }else{
                    // insert new operator
                    $this->tripay->db->trans_start();
                    # insert 
                    $this->tripay->db->insert('ppob_tripay_prabayar_operator', 
                                                    array('id' => $valueOperator->id,
                                                          'operator_code' => $valueOperator->product_id, 
                                                          'operator' => $valueOperator->product_name, 
                                                          'category_id' => $valueOperator->pembeliankategori_id,
                                                          'status' => $valueOperator->status == 1 ? 'tersedia'  : 'tidak tersedia'));
                    # Transaction Complete
                    $this->tripay->db->trans_complete();
                    # Filter Status
                    if ($this->tripay->db->trans_status() === FALSE) {
                        # Something Went Wsrong.
                        $this->tripay->db->trans_rollback();
                    } else {
                        # Transaction Commit
                        $this->tripay->db->trans_commit();
                    }
                }

            }
        }
    }

    function check_product_exist_in_server($product_code){
        $this->tripay->db->select('ptp.product_code')
                 ->from('ppob_tripay_prabayar_product AS ptp')
                 ->join('ppob_product_local_to_server_product AS local', 'ptp.id=local.product_id_tripay', 'inner')
                 ->join('ppob_prabayar_product AS product', 'local.product_id=product.id', 'inner')
                 ->where('product.product_code', $product_code);
        $q = $this->tripay->db->get();
        $list = array();
        if ( $q->num_rows() > 0 ) {
            foreach ( $q->result() as $rows ) {
                $url_product = $this->main_url . 'pembelian/produk';
                $product = $this->requestBodyGet($url_product);
                if ( isset($product->data) ) {
                    foreach ($product->data as $key => $value) {
                        if( $rows->product_code == $value->code ) { 
                            $list['product_price'] = $value->price;
                            $list['status'] = $value->status == 1 ? 'active' : 'non active';
                        }
                    }
                }
            }
        }         
        return $list;
    }

    function get_product(){
        $url_product = $this->main_url . 'pembelian/produk';
        $product = $this->requestBodyGet($url_product);
         // print_r($product);
        if (isset($product->data)){
            foreach ($product->data as $key => $value) {
                $this->tripay->db->select('id')
                                 ->from('ppob_tripay_prabayar_product')
                                 ->where('id', $value->id)
                                 ->where('operator_id', $value->pembelianoperator_id);
                $q = $this->tripay->db->get();
                if( $q->num_rows() > 0 ) {
                    // update product
                    $this->tripay->db->trans_start();
                    # update data ppob_tripay_prabayar_product
                    $this->tripay->db->where('id', $value->id)
                                     ->update('ppob_tripay_prabayar_product', 
                                                    array('product_code' => $value->code, 
                                                          'operator_id' => $value->pembelianoperator_id, 
                                                          'product_name' => $value->product_name,
                                                          'price' => $value->price,
                                                          'description' => $value->desc,
                                                          'status' => $value->status == 1 ? 'tersedia'  : 'tidak tersedia'));
                    # Transaction Complete
                    $this->tripay->db->trans_complete();
                    # Filter Status
                    if ($this->tripay->db->trans_status() === FALSE) {
                        # Something Went Wrong.
                        $this->tripay->db->trans_rollback();
                    } else {
                        # Transaction Commit
                        $this->tripay->db->trans_commit();
                    }
                }else{
                    // insert new product
                    $this->tripay->db->trans_start();
                    # insert 
                    $this->tripay->db->insert('ppob_tripay_prabayar_product', 
                                                    array('id' => $value->id,
                                                          'product_code' => $value->code, 
                                                          'operator_id' => $value->pembelianoperator_id, 
                                                          'product_name' => $value->product_name,
                                                          'price' => $value->price,
                                                          'description' => $value->desc,
                                                          'status' => $value->status == 1 ? 'tersedia'  : 'tidak tersedia'));
                    # Transaction Complete
                    $this->tripay->db->trans_complete();
                    # Filter Status
                    if ($this->tripay->db->trans_status() === FALSE) {
                        # Something Went Wsrong.
                        $this->tripay->db->trans_rollback();
                    } else {
                        # Transaction Commit
                        $this->tripay->db->trans_commit();
                    }

                }              
            }
        }
    }

    // function requestTopUp2(){
    //     $apiKey = 'api_key_anda';

    //     $payload = [
    //         'inquiry'       => 'I', //PLN untuk pembelian PLN Prabayar, atau I untuk produk lainnya,
    //         'code'          => 'AX5',
    //         'phone'         => '083856000000',
    //         'no_meter_pln'  => '123232132',
    //         'api_trxid'     => 'INV123',
    //         'pin'           => '1234',
    //     ];

    //     $url = 'https://tripay.id/api/v2/transaksi/pembelian';

    //     $header = array(
    //         'Accept: application/json',
    //         'Authorization: Bearer  '.$apiKey,
    //     );

    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    //     curl_setopt($ch, CURLOPT_URL, $url);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    //     curl_setopt($ch, CURLOPT_HEADER, false);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //     curl_setopt($ch, CURLOPT_POST, 1);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

    //     $response = curl_exec($ch);
    //     $err      = curl_error($ch);

    //     curl_close($ch);

    //     echo !empty($err) ? $err : $response;
    // }

    public function requestTopUp( $url, $payload){

        $payload['pin'] = $this->pin;
        
        $header = array(
            'Accept: application/json',
            'Authorization: Bearer '. $this->api_key,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return json_decode($response);
        }
    }


    function requestDetail( $url, $payload ){
        $header = array(
            'Accept: application/json',
            'Authorization: Bearer '.$this->api_key,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return json_decode($response);
        }
    }

    public function requestBodyGet($url){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $this->api_key
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return json_decode($response);
        }
    }

    public function requestCekharga($product_code){

        $payload = [
            'code' => $product_code
        ];

        $url = $this->main_url.'pembelian/produk/cek?'.http_build_query($payload);

        $header = array(
        'Accept: application/json',
        'Authorization: Bearer ' . $this->api_key, 
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $response = curl_exec($ch);
        $err      = curl_error($ch);

        curl_close($ch);

         if ($err) {
          return "cURL Error #:" . $err;
        } else {
          return json_decode($response);
        }
    }

}