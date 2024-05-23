<?php

/**
 *  -----------------------
 *	Iak library
 *	Created by Muammar Kadafi
 *  -----------------------
 */

defined('BASEPATH') or exit('No direct script access allowed');

class Iak
{

	private $production = true;
	private $username = '085262802141';
	private $key_production = '472643293c215b8ayS8p';
	private $key_dev = '8286432937d964cegRmg';
	private $iak_callback_key = '583hb13Z183799O014785R4nB4TW294k5N6U3K45C61u969JA0637840974602559090PX16m1147012ev131498327w21S05G8714c509Y365E973847t33297302688188276r9554405762z03DI57f72671q54jx06869p5506458l0o3801V62483732Y56936s';
	
	private $url_production = 'https://prepaid.iak.id/';
	private $url_dev = 'https://prepaid.iak.dev/';
	private $url_check_balance = 'api/check-balance';
	private $url_price_list = 'api/pricelist/';
	private $url_check_operator = 'api/check-operator';
	private $url_inquiry_pln = 'api/inquiry-pln';
	private $url_inquiry_ovo = 'api/inquiry-ovo';
	private $url_top_up = 'api/top-up';
	private $url_check_status = 'api/check-status';
	private $api_key;
	private $url;

	public function __construct()
	{
		$this->iak = &get_instance();

		if( $this->production == true ){
			$this->api_key = $this->key_production;
			$this->url = $this->url_production;
		} else {
			$this->api_key = $this->key_dev;
			$this->url = $this->url_dev;
		}
	}

	public function check_balance(){
		$url = $this->url . $this->url_check_balance;
		$body = json_encode(['username' => $this->username,
						     'sign' => $this->sign_md5_check_balance()]);
		return $this->requestBody($url, $body);
	}


	public function convert_product_code_amra_to_iak($product_code){
		$this->iak->db->select('piak.product_code')
				 ->from('ppob_iak_prabayar_product AS piak')
				 ->join('ppob_product_local_to_server_product AS local', 'piak.id=local.product_id_iak', 'inner')
				 ->join('ppob_prabayar_product AS pproduct', 'local.product_id=pproduct.id', 'inner')
				 ->where('pproduct.product_code', $product_code);
		$code = '';
		$q = $this->iak->db->get();
		if( $q->num_rows() > 0 ) {
			foreach ( $q->result() as $row ) {
				$code = $row->product_code;
			}
		}
		return $code;
	}

	public function topUp($ref_id, $amra_product_code, $costumer_id ) {
		$url = $this->url . $this->url_top_up;
		$body = json_encode(['username' => $this->username,
							 'customer_id' => $costumer_id,
							 'ref_id' => $ref_id, 
							 'product_code' => $this->convert_product_code_amra_to_iak($amra_product_code),
						     'sign' => $this->sign_md5_top_up($ref_id)]);
		return $this->requestBody($url, $body);
	}

	public function check_status_transaksi($ref_id){
		$url = $this->url . $this->url_check_status;
		$body = json_encode(['username' => $this->username,
							 'ref_id' => $ref_id, 
						     'sign' => $this->sign_md5_check_status($ref_id)]);
		return $this->requestBody($url, $body);
	}

	public function check_operator($costumer_id){
		$url = $this->url . $this->url_check_operator;
		$body = json_encode(['username' => $this->username,
							 'customer_id' => $costumer_id,
						     'sign' => $this->sign_md5_check_operator()]);
		return $this->requestBody($url, $body);
	}

	public function check_product_exist($product_code){
		$this->iak->db->select('id')
				->from('ppob_iak_prabayar_product')
				->where('product_code', $product_code);
		$q = $this->iak->db->get();
		return $q->num_rows();		
	}

	public function check_product_exist_in_server( $product_code ){
		$this->iak->db->select('ppp.product_code AS product_code_iak, ppo.operator, pip.type')
					  ->from('ppob_iak_prabayar_product AS ppp')
					  ->join('ppob_iak_prabayar_operator AS ppo', 'ppp.operator=ppo.id', 'inner')
					  ->join('ppob_iak_prabayar_type AS pip', 'ppo.type_id=pip.id')
					  ->join('ppob_product_local_to_server_product AS local', 'ppp.id=local.product_id_iak', 'inner')
					  ->join('ppob_prabayar_product AS product', 'local.product_id=product.id', 'inner')
					  ->where('product.product_code', $product_code);
		$q = $this->iak->db->get();
		$list = array();
		if ( $q->num_rows() > 0 ) {
			foreach ( $q->result() as $rows ) {
				$type = $rows->type;
				$operator = $rows->operator;
				$url = $this->url . $this->url_price_list  . $type . '/' . $operator ;
				$body = json_encode(['username' => $this->username,
						     		 'sign' => $this->sign_md5_price_list(), 
						     		 'status' => 'all']);
				// proses request ke iak
				$arr = $this->requestBody( $url, $body );
				if (isset($arr->data->pricelist)){
					foreach ($arr->data->pricelist as $key => $value) {
						if( $value->product_code == $rows->product_code_iak) {
							$list['product_price'] = $value->product_price;
							$list['status'] = $value->status;
						}
					}
				}
			}
		}
		return $list;
	}

	public function get_price_product_list(){
		$this->iak->db->select('pipt.type, pipo.operator, pipo.id')
				->from('ppob_iak_prabayar_operator AS pipo')
				 ->join('ppob_iak_prabayar_type AS pipt', 'pipo.type_id=pipt.id', 'inner')
				 ->order_by('pipt.id', 'DESC');
		$q = $this->iak->db->get();
		if( $q->num_rows() > 0 ) {
			foreach ( $q->result() as $rows ) {
				$type = $rows->type;
				$operator = $rows->operator;
				$operator_id = $rows->id;
				$url = $this->url . $this->url_price_list  . $type . '/' . $operator ;
				$body = json_encode(['username' => $this->username,
						     		 'sign' => $this->sign_md5_price_list(), 
						     		 'status' => 'all']);
				$arr = $this->requestBody( $url, $body );
				if (isset($arr->data->pricelist)){
					foreach ($arr->data->pricelist as $key => $value) {
						// echo "<br>===========<br>";
						// print_r($value);
						// echo "<br>===========<br>";
						$data = array();
						if( $this->check_product_exist($value->product_code) > 0 ){
							# update process
							$data['status'] = ($value->status == 'active' ? 'active' : 'inactive');
							$data['product_price'] = $value->product_price;
							$data['updatedAt'] = date('Y-m-d');
							# Update process || Starting Transaction
							$this->iak->db->trans_start();
							# update data ppob_iak_prabayar_product
							$this->iak->db->where('product_code', $value->product_code)
									 ->update('ppob_iak_prabayar_product', $data);
							# Transaction Complete
							$this->iak->db->trans_complete();
							# Filter Status
							if ($this->iak->db->trans_status() === FALSE) {
								# Something Went Wrong.
								$this->iak->db->trans_rollback();
							} else {
								# Transaction Commit
								$this->iak->db->trans_commit();
							}
						}else{
							# insert process 
							$data['product_code'] = $value->product_code;
							$data['product_name'] = $value->product_description . ' ' . $value->product_nominal ;
							$data['product_price'] = $value->product_price;
							$data['status'] = ($value->status == 'active' ? 'active' : 'inactive');
							$data['operator'] = $operator_id;
							$data['application_markup'] = 0;
							$data['createdAt'] = date('Y-m-d');
							$data['updatedAt'] = date('Y-m-d');
							# insert process || Starting Transaction
						    $this->iak->db->trans_start();
						    # insert 
						    $this->iak->db->insert('ppob_iak_prabayar_product', $data);
						    # Transaction Complete
						    $this->iak->db->trans_complete();
						    # Filter Status
						    if ($this->iak->db->trans_status() === FALSE) {
						        # Something Went Wsrong.
						        $this->iak->db->trans_rollback();
						    } else {
						        # Transaction Commit
						        $this->iak->db->trans_commit();
						    }
						}
					}
				}
			}
		}		 
	}
	
	public function sign_md5_inquiry_pln($costumer_id){
		return md5($this->username . $this->api_key . $costumer_id);
	}

	public function sign_md5_inquiry_ovo($costumer_id){
		return $this->md5_inquiry_pln($costumer_id);
	}

	public function sign_md5_check_balance(){
		return md5($this->username . $this->api_key . 'bl');
	}

	public function sign_md5_price_list(){
		return md5($this->username . $this->api_key . 'pl');
	}

	public function sign_md5_check_operator(){
		return md5($this->username . $this->api_key . 'op');	
	}

	public function sign_md5_top_up($ref_id){
		return md5($this->username . $this->api_key . $ref_id);	
	}

	public function sign_md5_check_status($ref_id){
		return $this->sign_md5_top_up($ref_id);	
	}

	public function iak_callback_key(){
		return $this->iak_callback_key;
	}

	public function requestBody($url, $postBody){
		$curl = curl_init();
		curl_setopt_array($curl, [
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => $postBody,
		  CURLOPT_HTTPHEADER => [
		    "Content-Type: application/json"
		  ],
		]);
		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return json_decode($response);
		}
	}
}