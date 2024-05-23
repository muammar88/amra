<?php

/**
 *  -----------------------
 *	Migrations Controller
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Migrations extends CI_Controller
{

	private $company_code;
	private $company_id;


   /**
    * Construct
    */
   public function __construct()
   {
      parent::__construct();
      $this->load->model('Model_migrations', 'model_migrations');
      $this->auth_library->Is_not_login();
   }

   // function indexsasa122(){
   //    # data temp
   //    $data_temp = $this->model_migrations->get_data_deposit_transaction_temporary();
   //    # get data personal jamaah
   //    $get_data_personal_jamaah = $this->model_migrations->get_data_personal_jamaah();
   //    # personal id
   //    $personal_id = $data_temp['list_personal_uniq'];
	//
	// 	echo "<br>======<br>";
	// 	print_r($personal_id);
	// 	echo "<br>======<br>";
   //    $list_pool_id = array();
   //    foreach ($personal_id as $key => $value) {
   //       # pool
   //       $data = array();
   //       $data['company_id'] = $value['company_id'];
   //       $data['jamaah_id'] = $get_data_personal_jamaah[$value['personal_id']];
   //       $data['active'] = 'active';
   //       $data['input_date'] = date('Y-m-d H:i:s');
   //       $data['last_update'] = date('Y-m-d H:i:s');
   //       # insert to the pool
   //       $feedBack = $this->model_migrations->insert_pool( $data );
   //       if( $feedBack['status'] == true ) {
   //          $list_pool_id[$value['personal_id']] = $feedBack['pool_id'];
   //       }
   //    }
	// 	$list_company_id = array();
	// 	$list_pool_deposit_transaction = array();
   //    # insert deposit transaction
   //    foreach ($data_temp['list'] as $key => $value) {
	// 		$list_company_id[$value['personal_id']] = $value['company_id'];
	//
   //       $data = array();
   //       $data['nomor_transaction'] = $value['nomor_transaction'];
   //       $data['personal_id'] = $value['personal_id'];
   //       $data['company_id'] = $value['company_id'];
   //       $data['debet'] = $value['debet'];
   //       $data['kredit'] = $value['kredit'];
   //       $data['approver'] = $value['approver'];
   //       $data['transaction_requirement'] = $value['transaction_requirement'];
   //       $data['info'] = $value['info'];
   //       $data['input_date'] = date('Y-m-d H:i:s');
   //       $data['last_update'] = date('Y-m-d H:i:s');
   //       # insert to deposit transaction
   //       $feedBack = $this->model_migrations->insert_deposit_transaction($data);
	// 		if( $feedBack['status'] == true ){
	// 			$list_pool_deposit_transaction[$feedBack['deposit_transaction_id']] = $value['personal_id'];
	// 		}
   //    }
	//
   //    // $
   //    # pool deposit transaction
   //    $pool_deposit_transaction = array();
	// 	foreach ($list_pool_deposit_transaction as $key => $value) {
	// 		$data = array();
	// 		$data['company_id'] = $list_company_id[$value];
	// 		$data['pool_id'] = $list_pool_id[$value];
	// 		$data['deposit_transaction_id'] = $key;
	// 		# insert pool deposit transaction
	// 		$this->model_migrations->insert_pool_deposit_transaction($data);
	// 	}
	//
   // }

}
