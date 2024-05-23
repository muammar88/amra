<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *	Serpul Controller
 *	Created by Muammar Kadafi
 */
class Serpul extends CI_Controller
{

   public function __construct()
   {
      parent::__construct();
      # Load model ppob
     $this->load->model('Model_ppob', 'model_ppob');
      #load model ppob cud
      $this->load->model('Model_ppob_cud', 'model_ppob_cud');
      # set date timezone
      ini_set('date.timezone', 'Asia/Jakarta');
   }

   function _send($url){
      $curl = curl_init();
      curl_setopt_array($curl, array(
      CURLOPT_URL => $this->config->item('serpul_main_url').$url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
           'Accept: application/json',
           'Authorization: '. $this->config->item('serpul_api_key')
         ),
      ));
      $response = curl_exec($curl);
      curl_close($curl);
      return json_decode($response);
   }


   function getListProduct(){
      // $res = $this->_send('/prabayar/category');
      // if( $res->responseCode == 200 ){
      //    $category = $res->responseData;
      //    foreach ($category as $row) {
      //       $category_code = $row->product_id;
      //       # res operator
      //       $res_operator = $this->_send('/prabayar/operator?product_id='.$category_code);
      //       if( $res_operator->responseCode == 200 ){
      //          $operator = $res_operator->responseData;
      //          foreach ($operator as $row1) {
      //             $operator_code = $row1->product_id;
      //             $res_product = $this->_send('/prabayar/product?product_id='.$operator_code);
      //             if( $res_product->responseCode == 200 ){
      //                $product =  $res_product->responseData;

      //                // print_r($product);
      //                print("<pre>".print_r($product,true)."</pre>");
                     
      //             }
      //          }
      //       }
      //    }
      // }else{
      //    echo "Terjadi Error";
      // }

      $res = $this->_send('/pascabayar/category');
      if( $res->responseCode == 200 ) {
         $category = $res->responseData;
         foreach ($category as $row) {
            $res_product = $this->_send('/pascabayar/product?product_id='.$row->product_id);
            if( $res_product->responseCode == 200 ) {
               $product = $res_product->responseData;
               print("<pre>".print_r($product,true)."</pre>");
            }
         }
      }
   }

 



   // foreach ( $product as $row1 ) {
   //    // // $data_product = array();
   //    // // $data_product['category_id'] = $category_id ;
   //    // // $data_product['product_code'] = $row1->product_id;
   //    // // $data_product['product_name'] = $row1->product_name;
   //    // // $data_product['product_fee'] = $row1->product_fee;
   //    // // $data_product['application_markup'] = $markup;
   //    // // $data_product['status'] = $row1->status == 'ACTIVE' ? 'active' : 'inactive';
   //    // // $data_product['server'] = 'serpul';
   //    // // $data_product['created_at'] = date('Y-m-d H:i:s');
   //    // // $data_product['updated_at'] = date('Y-m-d H:i:s');
   //    // // // insert product pascabayar
   //    // // $this->model_ppob_cud->insert_product_pascabayar($data_product);
   //    // $data_product = array();
   //    // $data_product['status'] = $row1->status == 'ACTIVE' ? 'active' : 'inactive';
   //    // $data_product['product_fee'] = $row1->product_fee;
   //    // $data_product['application_markup'] = $markup;
   //    // $data_product['updated_at'] = date('Y-m-d H:i:s');
   //    // $this->model_ppob_cud->update_product_pascabayar($row1->product_id, $data_product);
   // }

   // function getAllProduct(){
   //    // UPDATE PRABAYAR -->> JANGAN DIGANGGU LAGI, SUDAH FIX
   //    $res = $this->_send('/prabayar/category');
   //    if( $res->responseCode == 200 ) {
   //       $markup = $this->model_ppob->get_markup_aplikasi();
   //       $category = $res->responseData;
   //       foreach ($category as $row) {
   //          $category_code = $row->product_id;
   //          $res_operator = $this->_send('/prabayar/operator?product_id='.$row->product_id);
   //          if( $res_operator->responseCode == 200 ) {
   //             $operator = $res_operator->responseData;
   //             foreach ($operator as $row1) {
   //                $operator_code = $row1->product_id;
   //                $res_product = $this->_send('/prabayar/product?product_id='.$row1->product_id);
   //                if( $res_product->responseCode == 200 ) {
   //                   $product =  $res_product->responseData;
   //                   foreach ($product as $row2) {
   //                      $this->model_ppob_cud->update_price(array('category_code' => $category_code, 
   //                                                                'operator_code' => $operator_code, 
   //                                                                'product_code' => $row2->product_id, 
   //                                                                'price' => $row2->product_price, 
   //                                                                'markup' => $markup,
   //                                                                'status' => ($row2->status == 'ACTIVE' ? 'active' : 'inactive')));
   //                   }
   //                }
   //             }
   //          }
   //       }
   //    }else{
   //       echo "Terjadi Error";
   //    }
   //    // https://api.serpul.co.id/pascabayar/category
   //    // UPDATE PASCA BAYAR
   //    $res = $this->_send('/pascabayar/category');
   //    if( $res->responseCode == 200 ) {
   //       $markup = $this->model_ppob->get_markup_aplikasi();
   //       $category = $res->responseData;
   //       foreach ($category as $row) {
   //          // $data_category = array();
   //          // $data_category['category_code'] = $row->product_id;
   //          // $data_category['category_name'] = $row->product_name;
   //          // $data_category['created_at'] = date('Y-m-d H:i:s');
   //          //  # insert category
   //          // $this->model_ppob_cud->insert_category_pascabayar( $data_category );
   //          // # get category id
   //          // $category_id = $this->model_ppob_cud->get_category_id_pascabayar();
   //          // res product
   //          $res_product = $this->_send('/pascabayar/product?product_id='.$row->product_id);
   //          if( $res_product->responseCode == 200 ) {
   //             $product = $res_product->responseData;
   //             foreach ( $product as $row1 ) {
   //                // $data_product = array();
   //                // $data_product['category_id'] = $category_id ;
   //                // $data_product['product_code'] = $row1->product_id;
   //                // $data_product['product_name'] = $row1->product_name;
   //                // $data_product['product_fee'] = $row1->product_fee;
   //                // $data_product['application_markup'] = $markup;
   //                // $data_product['status'] = $row1->status == 'ACTIVE' ? 'active' : 'inactive';
   //                // $data_product['server'] = 'serpul';
   //                // $data_product['created_at'] = date('Y-m-d H:i:s');
   //                // $data_product['updated_at'] = date('Y-m-d H:i:s');
   //                // // insert product pascabayar
   //                // $this->model_ppob_cud->insert_product_pascabayar($data_product);
   //                $data_product = array();
   //                $data_product['status'] = $row1->status == 'ACTIVE' ? 'active' : 'inactive';
   //                $data_product['product_fee'] = $row1->product_fee;
   //                $data_product['application_markup'] = $markup;
   //                $data_product['updated_at'] = date('Y-m-d H:i:s');
   //                $this->model_ppob_cud->update_product_pascabayar($row1->product_id, $data_product);
   //             }
   //          }
   //       }
   //    }
   // }
}





// <?php
// defined('BASEPATH') or exit('No direct script access allowed');

// /**
//  * Serpul Controller
//  * Created by Muammar Kadafi
//  */
// class Serpul extends CI_Controller
// {

//    public function __construct()
//    {
//       parent::__construct();
//       # Load model ppob
//      // $this->load->model('Model_ppob', 'model_ppob');
//       #load model ppob cud
//       $this->load->model('Model_ppob_cud', 'model_ppob_cud');
//       # set date timezone
//       ini_set('date.timezone', 'Asia/Jakarta');
//    }


//    function _send($url){
//       $curl = curl_init();
//       curl_setopt_array($curl, array(
//       CURLOPT_URL => $this->config->item('serpul_main_url').$url,
//       CURLOPT_RETURNTRANSFER => true,
//       CURLOPT_ENCODING => '',
//       CURLOPT_MAXREDIRS => 10,
//       CURLOPT_TIMEOUT => 0,
//       CURLOPT_FOLLOWLOCATION => true,
//       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//       CURLOPT_CUSTOMREQUEST => 'GET',
//       CURLOPT_HTTPHEADER => array(
//            'Accept: application/json',
//            'Authorization: '. $this->config->item('serpul_api_key')
//          ),
//       ));
//       $response = curl_exec($curl);
//       curl_close($curl);
//       return json_decode($response);
//    }

//    function getAllProduct(){
//       $res = $this->_send('/prabayar/category');
//       if( $res->responseCode == 200 ){
//          $category = $res->responseData;

//          foreach ($category as $row) {
//             $category_code = $row->product_id;
//             // $data_category = array();
//             // $data_category['category_code'] = $row->product_id;
//             // $data_category['category_name'] = $row->product_name;
//             // $data_category['created_at'] = date('Y-m-d H:i:s');
//             // # insert category
//             // $this->model_ppob_cud->insert_category($data_category);
//             // # get category id
//             // $category_id = $this->model_ppob_cud->get_category_id();
//             # res operator
//             $res_operator = $this->_send('/prabayar/operator?product_id='.$row->product_id);
//             if( $res_operator->responseCode == 200 ){
//                $operator = $res_operator->responseData;
//                foreach ($operator as $row1) {
//                   $operator_code = $row1->product_id;
//                   // $data_operator = array();
//                   // $data_operator['category_id'] = $category_id;
//                   // $data_operator['operator_code'] = $row1->product_id;
//                   // $data_operator['operator_name'] = $row1->product_name;
//                   // $data_operator['created_at'] = date('Y-m-d H:i:s');
//                    # insert operator
// //                   $this->model_ppob_cud->insert_operator($data_operator);
// //                   # get operator id
// //                   $operator_id = $this->model_ppob_cud->get_operator_id();
// //                   // filter prefix
// //                   if( $row1->prefix != '' ) {
// //                      $data_prefix = array();

// // //                      if (strpos($haystack, $needle) !== false) {
// // //     echo 'true';
// // // }
// //                      if (strpos($row1->prefix, ',') !== false ) { 
// //                          foreach (explode(',', $row1->prefix) as $key => $value) {
// //                            $data_prefix[] = array('operator_id' => $operator_id, 
// //                                                   'prefix' =>  $value, 
// //                                                   'created_at' => date('Y-m-d H:i:s'));
// //                          }
// //                      }else{
// //                         $data_prefix[] = array('operator_id' => $operator_id, 
// //                                                'prefix' =>  $row1->prefix, 
// //                                                'created_at' => date('Y-m-d H:i:s'));
// //                      }
// //                      # insert prefix
// //                      $this->model_ppob_cud->insert_prefix($data_prefix);
// //                   }
//                   $res_product = $this->_send('/prabayar/product?product_id='.$row1->product_id);
//                   if( $res_product->responseCode == 200 ){
//                      $product =  $res_product->responseData;
//                      foreach ($product as $row2) {
//                         $this->model_ppob_cud->update_price(array('category_code' => $category_code, 
//                                                                   'operator_code' => $operator_code, 
//                                                                   'product_code' => $row2->product_id, 
//                                                                   'price' => $row2->product_price));
//                         // print_r($row2);
//                         // $data_product = array();
//                         // $data_product['operator_id'] = $operator_id;
//                         // $data_product['product_code'] = $row2->product_id;
//                         // $data_product['product_name'] = $row2->product_name;
//                         // $data_product['price'] = $row2->product_price;
//                         // $data_product['application_markup'] = '0';
//                         // $data_product['status'] = $row2->status == 'ACTIVE' ? 'active' : 'inactive';
//                         // $data_product['server'] = 'serpul';
//                         // $data_product['created_at'] = date('Y-m-d H:i:s');
//                         // $data_product['updated_at'] = date('Y-m-d H:i:s');
//                         //  # insert prefix
//                         // $this->model_ppob_cud->insert_product($data_product);
//                      }
//                   }
//                }
//             }
//          }
//       }else{
//          echo "Terjadi Error";
//       }
//    }
// }