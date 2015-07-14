<?php
class ModelReportCoupon extends Model {
	public function getCoupons($data = array()) {		
		$coupon_id_array = array();		
		$orders_coupon_array = array();
		$total_coupon_array = array();
		$collection="mongo_coupon_history";
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
		$coupon_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($coupon_query_data as $coupon_query_data_info) {
			$total_tt=(float)$coupon_query_data_info['amount'];
			///
			$coupon_id_array[] = $coupon_query_data_info['coupon_id'];	
			if (isset($orders_coupon_array[(int)$coupon_query_data_info['coupon_id']])) 
				$orders_coupon_array[(int)$coupon_query_data_info['coupon_id']]+=1;
			else 
				$orders_coupon_array[(int)$coupon_query_data_info['coupon_id']]=1;
			if (isset($total_coupon_array[(int)$coupon_query_data_info['coupon_id']])) 
				$total_coupon_array[(int)$coupon_query_data_info['coupon_id']]+=$total_tt;	
			else 
				$total_coupon_array[(int)$coupon_query_data_info['coupon_id']]=$total_tt;		
		} 
		$coupon_id_array=array_unique($coupon_id_array);
		$data_result= array();
		$this->load->model('marketing/coupon');
		foreach ($coupon_id_array as $coupon_id_array_id) {
			$coupon_info= array();
			$coupon_info=$this->model_marketing_coupon->getCoupon((int)$coupon_id_array_id);
			$data_result[]= array(
				'coupon_id'=>(int)$coupon_id_array_id,
				'name'=>$coupon_info['name'],
				'code'=>$coupon_info['code'],
				'orders'=>$orders_coupon_array[(int)$coupon_id_array_id],
				'total'=>$total_coupon_array[(int)$coupon_id_array_id],
			);
		}
		$data_result=$this->mongodb->sapxepthemphantu($data_result, 'total', 0);
		return $data_result;
		/*
		$sql = "SELECT ch.coupon_id, c.name, c.code, COUNT(DISTINCT ch.order_id) AS `orders`, SUM(ch.amount) AS total FROM `" . DB_PREFIX . "coupon_history` ch LEFT JOIN `" . DB_PREFIX . "coupon` c ON (ch.coupon_id = c.coupon_id)";
		$implode = array();
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ch.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ch.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sql .= " GROUP BY ch.coupon_id ORDER BY total DESC";
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

	public function getTotalCoupons($data = array()) {
		
		$coupon_id_array = array();	
		$collection="mongo_coupon_history";
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
		$coupon_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($coupon_query_data as $coupon_query_data_info) {
			$coupon_id_array[] = $coupon_query_data_info['coupon_id'];	
		} 
		$coupon_id_array=array_unique($coupon_id_array);
		return count($coupon_id_array);
		/*
		$sql = "SELECT COUNT(DISTINCT coupon_id) AS total FROM `" . DB_PREFIX . "coupon_history`";
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
}