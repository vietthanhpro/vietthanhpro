<?php
class ModelAccountRecurring extends Model {
	private $recurring_status = array(
		0 => 'Inactive',
		1 => 'Active',
		2 => 'Suspended',
		3 => 'Cancelled',
		4 => 'Expired / Complete'
	);

	private $transaction_type = array(
		0 => 'Created',
		1 => 'Payment',
		2 => 'Outstanding payment',
		3 => 'Payment skipped',
		4 => 'Payment failed',
		5 => 'Cancelled',
		6 => 'Suspended',
		7 => 'Suspended from failed payment',
		8 => 'Outstanding payment failed',
		9 => 'Expired'
	);

	public function getProfile($id) {
		//$result = $this->db->query("SELECT `or`.*,`o`.`payment_method`,`o`.`payment_code`,`o`.`currency_code` FROM `" . DB_PREFIX . "order_recurring` `or` LEFT JOIN `" . DB_PREFIX . "order` `o` ON `or`.`order_id` = `o`.`order_id` WHERE `or`.`order_recurring_id` = '" . (int)$id . "' AND `o`.`customer_id` = '" . (int)$this->customer->getId() . "' LIMIT 1");		
		$order_id_array = array();
		$collection="mongo_order";
		$where=array('customer_id'=>(int)$this->customer->getId());
		$order=array();
		$order_id_array_list=$this->mongodb->getall($collection,$where,$order);	
		foreach ($order_id_array_list as $order_id_array_list_info) {
			$order_id_array[] = $order_id_array_list_info['order_id'];
		}
		//
		$order_recurring_info = array();
		$collection="mongo_order_recurring";
		$where=array('order_recurring_id'=>(int)$id, 'order_id'=>array('$in'=>$order_id_array));
		$order_recurring_info=$this->mongodb->getBy($collection,$where);
		if ($order_recurring_info) {
			$order_info = array();
			$collection="mongo_order";
			$where=array('order_id'=>(int)$order_recurring_info['order_id'], 'customer_id'=>(int)$this->customer->getId());
			$order_info=$this->mongodb->getBy($collection,$where);
			if ($order_info) {
				$order_recurring_info['payment_method']=$order_info['payment_method'];
				$order_recurring_info['payment_code']=$order_info['payment_code'];
				$order_recurring_info['currency_code']=$order_info['currency_code'];
			} else {
				$order_recurring_info['payment_method']='';
				$order_recurring_info['payment_code']='';
				$order_recurring_info['currency_code']='';
			}
			return $order_recurring_info;
		} else {
			return false;
		}/*
		if ($result->num_rows > 0) {
			$recurring = $result->row;
			return $recurring;
		} else {
			return false;
		}*/
	}

	public function getProfileByRef($ref) {
		//$recurring = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_recurring` WHERE `reference` = '" . $this->db->escape($ref) . "' LIMIT 1");
		$order_recurring_info = array();
		$collection="mongo_order_recurring";
		$where=array('reference'=>$ref);
		$order_recurring_info=$this->mongodb->getBy($collection,$where);
		return $order_recurring_info;
		/*
		if ($recurring->num_rows > 0) {
			return $recurring->row;
		} else {
			return false;
		}*/
	}

	public function getProfileTransactions($id) {
		$recurring = $this->getProfile($id);
		//$results = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_recurring_transaction` WHERE `order_recurring_id` = '" . (int)$id . "'");
		$order_recurring_transaction_data = array();
		$collection="mongo_order_recurring_transaction";
		$where=array('order_recurring_id'=>(int)$id);
		$order=array();
		$order_recurring_transaction_data=$this->mongodb->getall($collection,$where,$order);		
		//if ($results->num_rows > 0) {
		if ($order_recurring_transaction_data) {
			$transactions = array();
			//foreach ($results->rows as $transaction) {
			foreach ($order_recurring_transaction_data as $transaction) {
				$transaction['amount'] = $this->currency->format($transaction['amount'], $recurring['currency_code'], 1);
				$transactions[] = $transaction;
			}
			return $transactions;
		} else {
			return false;
		}
	}

	public function getAllProfiles($start = 0, $limit = 20) {
		if ($start < 0) {$start = 0;}
		if ($limit < 1) {$limit = 1;}
		//$result = $this->db->query("SELECT `or`.*,`o`.`payment_method`,`o`.`currency_id`,`o`.`currency_value` FROM `" . DB_PREFIX . "order_recurring` `or` LEFT JOIN `" . DB_PREFIX . "order` `o` ON `or`.`order_id` = `o`.`order_id` WHERE `o`.`customer_id` = '" . (int)$this->customer->getId() . "' ORDER BY `o`.`order_id` DESC LIMIT " . (int)$start . "," . (int)$limit);
		$ketquatrave=array();$order_recurring_query_data=array();
		$collection="mongo_order_recurring";
		$where=array('customer_id'=>(int)$this->customer->getId());
		$order=array('return_id'=>-1);
		$order_recurring_query_data= $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		/*
		if ($result->num_rows > 0) {
			$recurrings = array();
			foreach ($result->rows as $recurring) {
				$recurrings[] = $recurring;
			}
			return $recurrings;
		} else {
			return false;
		}*/
	}

	public function getTotalRecurring() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_recurring` `or` LEFT JOIN `" . DB_PREFIX . "order` `o` ON `or`.`order_id` = `o`.`order_id` WHERE `o`.`customer_id` = '" . (int)$this->customer->getId() . "'");
		//return $query->row['total'];	
		$order_id_array = array();
		$collection="mongo_order";
		$where=array('customer_id'=>(int)$this->customer->getId());
		$order=array();
		$order_id_array_list=$this->mongodb->getall($collection,$where,$order);	
		foreach ($order_id_array_list as $order_id_array_list_info) {
			$order_id_array[] = $order_id_array_list_info['order_id'];
		}
		//
		$order_recurring_info = array();
		$collection="mongo_order_recurring";
		$where=array('order_id'=>array('$in'=>$order_id_array));
		$order_recurring_info=$this->mongodb->gettotal($collection,$where);
		return $order_recurring_info;
	}
}
