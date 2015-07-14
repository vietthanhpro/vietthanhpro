<?php
class ModelAffiliateTransaction extends Model {
	public function getTransactions($data = array()) {
		//$sql = "SELECT * FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "'";
		$collection="mongo_affiliate_transaction";$affiliate_transaction_query_data= array();
		$where=array();
		$order=array();
		$sort_data = array(
			'amount',
			'description',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			//$sql .= " ORDER BY " . $data['sort'];
			$orderby = $data['sort'];
		} else {
			//$sql .= " ORDER BY date_added";
			$orderby = 'date_added';
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			//$sql .= " DESC";
			$order[$orderby] = -1;
		} else {
			//$sql .= " ASC";
			$order[$orderby] = 1;
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			$start=(int)$data['start'];
			$limit=(int)$data['limit'];
		}
		//$query = $this->db->query($sql);
		//return $query->rows;
		$affiliate_transaction_query_data = $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		return $affiliate_transaction_query_data;
	}

	public function getTotalTransactions() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "'");
		//return $query->row['total'];
		$affiliate_transaction_data= array();
		$collection="mongo_affiliate_transaction";
		$where=array('affiliate_id'=>(int)$this->affiliate->getId());
		$affiliate_transaction_data=$this->mongodb->gettotal($collection,$where);
		return $affiliate_transaction_data;
	}

	public function getBalance() {
		//$query = $this->db->query("SELECT SUM(amount) AS total FROM `" . DB_PREFIX . "affiliate_transaction` WHERE affiliate_id = '" . (int)$this->affiliate->getId() . "' GROUP BY affiliate_id");
		$collection="mongo_affiliate_transaction";
		$match=array('$match'=> array('affiliate_id'=>(int)$this->affiliate->getId()));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$amount')));		
		$affiliate_transaction_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $affiliate_transaction_data;
		/*
		if ($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}*/
	}
}