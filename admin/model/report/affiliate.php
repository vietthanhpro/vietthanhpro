<?php
class ModelReportAffiliate extends Model {
	public function getCommission($data = array()) {	
		$affiliate_id_array = array();		
		$orders_affiliate_array = array();
		$commission_affiliate_array = array();
		$collection="mongo_affiliate_transaction";
		$where=array();
		
		if (!empty($data['filter_date_start'])) {
			if (!empty($data['filter_date_end'])) {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
				//$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
			} else {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')));
			}
		} elseif (!empty($data['filter_date_end'])) {
			$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
		}
		$order=array();
		$affiliate_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($affiliate_query_data as $affiliate_query_data_info) {
			$commission_tt=(float)$affiliate_query_data_info['amount'];
			$affiliate_id_array[] = $affiliate_query_data_info['affiliate_id'];
			$orders_affiliate_array[(int)$affiliate_query_data_info['affiliate_id']][] = $affiliate_query_data_info['order_id'];
			if (isset($commission_affiliate_array[(int)$affiliate_query_data_info['affiliate_id']])) 
				$commission_affiliate_array[(int)$affiliate_query_data_info['affiliate_id']]+=$commission_tt;	
			else 
				$commission_affiliate_array[(int)$affiliate_query_data_info['affiliate_id']]=$commission_tt;		
		} 
		$affiliate_id_array=array_unique($affiliate_id_array);
		$data_result= array();
		$this->load->model('marketing/affiliate');
		$collection="mongo_order";
		foreach ($affiliate_id_array as $affiliate_id_array_id) {
			$affiliate_info= array();
			$total_affiliate_data=0;
			$affiliate_info=$this->model_marketing_affiliate->getAffiliate((int)$affiliate_id_array_id);
			$match=array('$match'=> array('order_id'=>array('$in'=>$orders_affiliate_array[(int)$affiliate_id_array_id])));
			$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$total')));		
			$total_affiliate_data=$this->mongodb->getaggregate($collection, $match, $group);
			///
			$data_result[]= array(
				'affiliate_id'=>(int)$affiliate_id_array_id,
				'affiliate'=>trim($affiliate_info['firstname'].' '.$affiliate_info['firstname']),
				'email'=>$affiliate_info['email'],
				'status'=>$affiliate_info['status'],
				'commission'=>$commission_affiliate_array[(int)$affiliate_id_array_id],
				'orders'=>count($orders_affiliate_array[(int)$affiliate_id_array_id]),
				'total'=>(float)$total_affiliate_data,
			);
		}
		$data_result=$this->mongodb->sapxepthemphantu($data_result, 'commission', 0);
		return $data_result;
		/*
		$sql = "SELECT at.affiliate_id, CONCAT(a.firstname, ' ', a.lastname) AS affiliate, a.email, a.status, SUM(at.amount) AS commission, COUNT(o.order_id) AS orders, SUM(o.total) AS total FROM " . DB_PREFIX . "affiliate_transaction at LEFT JOIN `" . DB_PREFIX . "affiliate` a ON (at.affiliate_id = a.affiliate_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (at.order_id = o.order_id)";
		$implode = array();
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(at.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(at.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sql .= " GROUP BY at.affiliate_id ORDER BY commission DESC";
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
	}

	public function getTotalCommission($data = array()) {		
		$affiliate_id_array = array();	
		$collection="mongo_affiliate_transaction";
		$where=array();
		
		if (!empty($data['filter_date_start'])) {
			if (!empty($data['filter_date_end'])) {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
				//$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
			} else {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')));
			}
		} elseif (!empty($data['filter_date_end'])) {
			$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
		}
		$order=array();
		$affiliate_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($affiliate_query_data as $affiliate_query_data_info) {
			$affiliate_id_array[] = $affiliate_query_data_info['affiliate_id'];	
		} 
		$affiliate_id_array=array_unique($affiliate_id_array);
		return count($affiliate_id_array);
		/*
		$sql = "SELECT COUNT(DISTINCT affiliate_id) AS total FROM `" . DB_PREFIX . "affiliate_transaction`";
		$implode = array();
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
	}

	public function getAffiliateActivities($data = array()) {		
		$affiliate_id_array = array();		
		$collection="mongo_affiliate";
		$where=array();
		if (!empty($data['filter_affiliate'])) {
			$where['firstname']=new MongoRegex('/^'.$data['filter_affiliate'].'/');
		}
		$order=array();
		$affiliate_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($affiliate_query_data as $affiliate_query_data_info) {
			$affiliate_id_array[] = $affiliate_query_data_info['affiliate_id'];	
		} 	
		$collection="mongo_affiliate_activity";
		$where=array();
		$where['affiliate_id']=array('$in'=>$affiliate_id_array);
		if (!empty($data['filter_ip'])) {
			$where['ip']=new MongoRegex('/^'.$data['filter_ip'].'/');
		}
		
		if (!empty($data['filter_date_start'])) {
			if (!empty($data['filter_date_end'])) {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
				//$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
			} else {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')));
			}
		} elseif (!empty($data['filter_date_end'])) {
			$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
		}
		$order=array('date_added'=>-1);
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$affiliate_query_result = $this->mongodb->getlimit($collection,$where, $order, (int)$data['start'], (int)$data['limit']);
		return $affiliate_query_result;
		/*
		$sql = "SELECT aa.activity_id, aa.affiliate_id, aa.key, aa.data, aa.ip, aa.date_added FROM " . DB_PREFIX . "affiliate_activity aa LEFT JOIN " . DB_PREFIX . "affiliate a ON (aa.affiliate_id = a.affiliate_id)";
		$implode = array();
		if (!empty($data['filter_affiliate'])) {
			$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_affiliate']) . "'";
		}
		if (!empty($data['filter_ip'])) {
			$implode[] = "aa.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(aa.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(aa.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sql .= " ORDER BY aa.date_added DESC";
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
	}

	public function getTotalAffiliateActivities($data = array()) {
		$affiliate_id_array = array();		
		$collection="mongo_affiliate";
		$where=array();
		if (!empty($data['filter_affiliate'])) {
			$where['firstname']=new MongoRegex('/^'.$data['filter_affiliate'].'/');
		}
		$order=array();
		$affiliate_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($affiliate_query_data as $affiliate_query_data_info) {
			$affiliate_id_array[] = $affiliate_query_data_info['affiliate_id'];	
		} 	
		$collection="mongo_affiliate_activity";
		$where=array();
		$where['affiliate_id']=array('$in'=>$affiliate_id_array);
		if (!empty($data['filter_ip'])) {
			$where['ip']=new MongoRegex('/^'.$data['filter_ip'].'/');
		}
		
		if (!empty($data['filter_date_start'])) {
			if (!empty($data['filter_date_end'])) {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
				//$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
			} else {
				$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_start'].' 00:00:01')));
			}
		} elseif (!empty($data['filter_date_end'])) {
			$where['date_added']=array('$lte'=>new MongoDate(strtotime($data['filter_date_end'].' 23:59:59')));
		}
		$affiliate_query_result = $this->mongodb->gettotal($collection,$where);
		return $affiliate_query_result;
		/*
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "affiliate_activity` aa LEFT JOIN " . DB_PREFIX . "affiliate a ON (aa.affiliate_id = a.affiliate_id)";
		$implode = array();
		if (!empty($data['filter_affiliate'])) {
			$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_affiliate']) . "'";
		}
		if (!empty($data['filter_ip'])) {
			$implode[] = "aa.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(aa.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(aa.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
	}
}