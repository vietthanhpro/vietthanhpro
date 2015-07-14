<?php
class ModelAccountTransaction extends Model {
	public function getTransactions($data = array()) {/*
		$sql = "SELECT * FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$this->customer->getId() . "'";
		$sort_data = array(
			'amount',
			'description',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		//$query = $this->db->query($sql);
		return $query->rows;*/
		$collection="mongo_customer_transaction";
		$where=array('customer_id'=> (int)$this->customer->getId());
		$order=array();
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}	
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$start=$data['start'];
			$limit=$data['limit'];
		} else {
			$start=0;
			$limit=0;
		}	
		$sort_data = array(
			'amount',
			'description',
			'date_added'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = "date_added";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} 
		return $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
	}

	public function getTotalTransactions() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		//return $query->row['total'];
		$collection="mongo_customer_transaction";$customer_transaction_data = array();
		$where=array('customer_id'=>(int)$this->customer->getId());
		$customer_transaction_data=$this->mongodb->gettotal($collection,$where);
		return $customer_transaction_data;
	}

	public function getTotalAmount() {/*
		//$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "customer_transaction` WHERE customer_id = '" . (int)$this->customer->getId() . "' GROUP BY customer_id");
		if ($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}*/
		$collection="mongo_customer_transaction";
		$match=array('$match'=> array('customer_id'=>(int)$this->customer->getId()));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$amount')));		
		$customer_transaction_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $customer_transaction_data;
	}
}