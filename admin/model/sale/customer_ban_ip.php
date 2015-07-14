<?php
class ModelSaleCustomerBanIp extends Model {
	public function addCustomerBanIp($data) {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "customer_ban_ip` SET `ip` = '" . $this->db->escape($data['ip']) . "'");
		$collection="mongo_customer_ban_ip";
		$customer_ban_ip_id=1+(int)$this->mongodb->getlastid($collection,'customer_ban_ip_id');
		$newdocument=array('customer_ban_ip_id'=>(int)$customer_ban_ip_id, 'ip'=>$ip);
		$this->mongodb->create($collection,$newdocument); 
	}

	public function editCustomerBanIp($customer_ban_ip_id, $data) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "customer_ban_ip` SET `ip` = '" . $this->db->escape($data['ip']) . "' WHERE customer_ban_ip_id = '" . (int)$customer_ban_ip_id . "'");
		$collection="mongo_customer_ban_ip";
		$infoupdate=array('ip'=>$ip);
		$where=array('customer_ban_ip_id'=>(int)$customer_ban_ip_id);
		$this->mongodb->update($collection,$infoupdate,$where);
	}

	public function deleteCustomerBanIp($customer_ban_ip_id) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_ban_ip` WHERE customer_ban_ip_id = '" . (int)$customer_ban_ip_id . "'");
		$collection="mongo_customer_ban_ip";
		$where=array('customer_ban_ip_id'=>(int)$customer_ban_ip_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getCustomerBanIp($customer_ban_ip_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ban_ip` WHERE customer_ban_ip_id = '" . (int)$customer_ban_ip_id . "'");
		//return $query->row;
		$collection="mongo_customer_ban_ip";
		$where=array('customer_ban_ip_id'=>(int)$customer_ban_ip_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getCustomerBanIps($data = array()) {
		/*
		$sql = "SELECT *, (SELECT COUNT(DISTINCT customer_id) FROM `" . DB_PREFIX . "customer_ip` ci WHERE ci.ip = cbi.ip) AS total FROM `" . DB_PREFIX . "customer_ban_ip` cbi";
		$sql .= " ORDER BY `ip`";
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
		$collection="mongo_customer_ban_ip";
		$where=array();$order=array();$data_result=array();
		if (isset($data['start']) || isset($data['limit'])){if ($data['start'] < 0) {$data['start'] = 0;}if ($data['limit'] < 1) {$data['limit'] = 20;}$start=$data['start'];$limit=$data['limit'];}else{$start=0;$limit=0;}	
		$orderby = 'ip';
		if (isset($data['order']) && ($data['order'] == 'DESC')) {$order[$orderby] = -1;} else {$order[$orderby]= 1;}
		$data_list=$this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		$collection="mongo_customer_ip";
		foreach ($data_list as $result) {
			$total_result=$this->getTotalCustomerIps($result['ip']);
					$data_result[] = array(
						'total' => $total_result,
						'customer_ban_ip_id'        => $result['customer_ban_ip_id'],
						'ip'      => $result['ip']
					);
		}
		return $data_result;
	}

	public function getTotalCustomerBanIps($data = array()) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ban_ip`");
		//return $query->row['total'];
		$collection="mongo_customer_ban_ip";
		$where=array();$customer_ban_ip_data= array();
		$customer_ban_ip_data=$this->mongodb->gettotal($collection,$where);
		return $customer_ban_ip_data;
	}

	public function getTotalCustomerIps($customer_ip) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_ban_ip`");
		//return $query->row['total'];
		$collection="mongo_customer_ip";
		$where=array('customer_ip'=>$customer_ip);$customer_ban_ip_data= array();
		$customer_ip_data=$this->mongodb->gettotal($collection,$where);
		return $customer_ip_data;
	}
}