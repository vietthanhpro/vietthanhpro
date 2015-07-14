<?php
class ModelReportMarketing extends Model {
	public function getMarketing($data = array()) {		
		$collection="mongo_marketing";
		$marketing_data = array();
		$where=array();
		$order=array('date_added'=>1);
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$marketing_query_result = $this->mongodb->getlimit($collection,$where, $order, (int)$data['start'], (int)$data['limit']);
		foreach ($marketing_query_result as $marketing_query_result_info) {
			$collection="mongo_order";
			$where=array();
			$where['marketing_id']=(int)$marketing_query_result_info['marketing_id'];
			if (!empty($data['filter_order_status_id'])) {
				$where['order_status_id']=(int)$data['filter_order_status_id'];
			} else {
				$where['order_status_id']=array('$gt'=>0);
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
			//			
			$total_query_data= array();
			$orders_query_data= 0;
			$keys = array('customer_id'=>1);
			$initial = array("sumtotal" => 0);
			$reduce = 'function (obj, prev) {prev.sumtotal = prev.sumtotal + obj.total - 0;}';
			$condition = array('condition' => $where);
			$order_query_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
			if (isset($total_query_data[0]['sumtotal'])) {$total_data= $total_query_data[0]['sumtotal'];}
			else {$total_data= 0;}
			$orders_query_data=$this->mongodb->gettotal($collection,$where);
			//
			$marketing_data[] = array(
				'marketing_id'=>$marketing_query_result_info['marketing_id'],
				'campaign'=>$marketing_query_result_info['name'],
				'code'=>$marketing_query_result_info['code'],
				'clicks'=>$marketing_query_result_info['clicks'],
				'orders'=>$orders_query_data,
				'total'=>$total_data,
			);
		}
		return $marketing_data;
		/*
		$sql = "SELECT m.marketing_id, m.name AS campaign, m.code, m.clicks AS clicks, (SELECT COUNT(DISTINCT order_id) FROM `" . DB_PREFIX . "order` o1 WHERE o1.marketing_id = m.marketing_id";
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o1.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o1.order_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o1.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o1.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		$sql .= ") AS `orders`, (SELECT SUM(total) FROM `" . DB_PREFIX . "order` o2 WHERE o2.marketing_id = m.marketing_id";
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o2.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o2.order_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o2.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o2.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		$sql .= " GROUP BY o2.marketing_id) AS `total` FROM `" . DB_PREFIX . "marketing` m ORDER BY m.date_added ASC";
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

	public function getTotalMarketing($data = array()) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "marketing`");
		//return $query->row['total'];
		$collection="mongo_marketing";
		$where=array();
		$marketing_data=$this->mongodb->gettotal($collection,$where);
		return $marketing_data;
	}
}