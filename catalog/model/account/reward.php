<?php
class ModelAccountReward extends Model {
	public function getRewards($data = array()) {
		/*
		$sql = "SELECT * FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$this->customer->getId() . "'";
		$sort_data = array(
			'points',
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
		$collection="mongo_customer_reward";
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
			'points',
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

	public function getTotalRewards() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		//return $query->row['total'];
		$collection="mongo_customer_reward";$customer_reward_data = array();
		$where=array('customer_id'=>(int)$this->customer->getId());
		$customer_reward_data=$this->mongodb->gettotal($collection,$where);
		return $customer_reward_data;
	}

	public function getTotalPoints() {
		/*$query = $this->db->query("SELECT SUM(points) AS total FROM `" . DB_PREFIX . "customer_reward` WHERE customer_id = '" . (int)$this->customer->getId() . "' GROUP BY customer_id");
		if ($query->num_rows) {
			return $query->row['total'];
		} else {
			return 0;
		}*/
		$collection="mongo_customer_reward";
		$match=array('$match'=> array('customer_id'=>(int)$this->customer->getId()));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$points')));		
		$customer_reward_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $customer_reward_data;
	}
}