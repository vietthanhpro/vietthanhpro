<?php
class ModelReportCustomer extends Model {
	public function getTotalCustomersByDay() {
		$customer_data = array();
		$collection="mongo_customer";
		for ($i = 0; $i < 24; $i++) {
			$date = date('Y-m-d');
			if ($i<10) {$k='0'.(string)$i;}
			else {$k=(string)$i;}
			//
			$start = new MongoDate(strtotime($date." ".$k.":00:00"));
			$end = new MongoDate(strtotime($date." ".$k.":59:59"));
			$where=array("date_added" => array('$gte' => $start, '$lte' => $end));
			$customer_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$customer_data[$i] = array(
				'hour'  => $i,
				//'total' => 0,
				'total' => $customer_data_total
			);
		}/*
		//$query = $this->db->query("SELECT COUNT(*) AS total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");

		foreach ($query->rows as $result) {
			$customer_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}*/

		return $customer_data;
	}

	public function getTotalCustomersByWeek() {
		$customer_data = array();
		$collection="mongo_customer";
		$date_start = strtotime('-' . date('w') . ' days');

		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));
			//
			$start = new MongoDate(strtotime($date." 00:00:00"));
			$end = new MongoDate(strtotime($date." 23:59:59"));
			$where=array("date_added" => array('$gte' => $start, '$lte' => $end));
			$customer_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$customer_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				//'total' => 0,
				'total' => $customer_data_total
			);
		}
		/*
		//$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}*/

		return $customer_data;
	}

	public function getTotalCustomersByMonth() {
		$customer_data = array();
		$collection="mongo_customer";
		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;
			//
			$start = new MongoDate(strtotime($date." 00:00:00"));
			$end = new MongoDate(strtotime($date." 23:59:59"));
			$where=array("date_added" => array('$gte' => $start, '$lte' => $end));
			$customer_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$customer_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				//'total' => 0,
				'total' => $customer_data_total
			);
		}
		/*
		//$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");

		foreach ($query->rows as $result) {
			$customer_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}*/

		return $customer_data;
	}

	public function getTotalCustomersByYear() {
		$customer_data = array();
		$collection="mongo_customer";
		$year_cur=date('Y');
		for ($i = 1; $i <= 12; $i++) {
			//
			if ($i<10) {$k='0'.(string)$i;}
			else {$k=(string)$i;}
			$end_number = cal_days_in_month(CAL_GREGORIAN, $i, $year_cur);
			$start = new MongoDate(strtotime($year_cur . '-' . $k . '-1' . " 00:00:00"));
			$end = new MongoDate(strtotime($year_cur . '-' . $k . '-'.$end_number . " 23:59:59"));
			$where=array("date_added" => array('$gte' => $start, '$lte' => $end));
			$customer_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$customer_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				//'total' => 0,
				'total' => $customer_data_total
			);
		}
		/*
		//$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "customer` WHERE YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");
		foreach ($query->rows as $result) {
			$customer_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}*/
		return $customer_data;
	}
	
	public function getOrders($data = array()) {
		$customer_id_array = array();	
		$order_id_array = array();		
		$total_order_array = array();
		$collection="mongo_order";
		$where=array();
		$where['customer_id']= array('$gt'=>0); 
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
		$order=array();
		$order_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_query_data as $order_query_data_info) {
			$customer_id_array[]=(int)$order_query_data_info['customer_id'];
			$order_id_array[(int)$order_query_data_info['customer_id']][]=(int)$order_query_data_info['order_id'];
			if (isset($total_order_array[(int)$order_query_data_info['customer_id']])) 
				$total_order_array[(int)$order_query_data_info['customer_id']]+=(float)$order_query_data_info['total'];	
			else 
				$total_order_array[(int)$order_query_data_info['customer_id']]=(float)$order_query_data_info['total'];	
		}
		$customer_id_array=array_unique($customer_id_array);
		$data_result= array();
		$this->load->model('sale/customer');
		$this->load->model('sale/customer_group');
		$collection="mongo_order_product";
		foreach ($customer_id_array as $customer_id_array_id) {
			$customer_info= array();
			$customer_info=$this->model_sale_customer->getCustomer((int)$customer_id_array_id);
			$customer_group_info= array();
			$customer_group_info=$this->model_sale_customer_group->getCustomerGroup((int)$customer_info['customer_group_id']);
			$order_id_array[(int)$customer_id_array_id]=array_unique($order_id_array[(int)$customer_id_array_id]);
			//
			$quantity_order_data=0;
			$match=array('$match'=> array('order_id'=>array('$in'=>$order_id_array[(int)$customer_id_array_id])));
			$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$quantity')));		
			$quantity_order_data=$this->mongodb->getaggregate($collection, $match, $group);
			$data_result[]= array(
				'customer_id'=>(int)$customer_id_array_id,
				'customer'=>trim($customer_info['firstname'].' '.$customer_info['lastname']),
				'email'=>$customer_info['email'],
				'customer_group'=>$customer_group_info['customer_group_description'][(int)$this->config->get('config_language_id')]['name'],
				'status'=>$customer_info['status'],
				'orders'=>count($order_id_array[(int)$customer_id_array_id]),
				'products'=>$quantity_order_data,
				'total'=>$total_order_array[(int)$customer_id_array_id],
			);
		}
		$data_result=$this->mongodb->sapxepthemphantu($data_result, 'total', 0);
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$data_result = array_slice($data_result, (int)$data['start'],(int)$data['limit']);
		return $data_result;
		/*
		$sql = "SELECT c.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, COUNT(o.order_id) AS orders, SUM(op.quantity) AS products, SUM(o.total) AS `total` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_product` op ON (o.order_id = op.order_id)LEFT JOIN `" . DB_PREFIX . "customer` c ON (o.customer_id = c.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE o.customer_id > 0 AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		$sql .= " GROUP BY o.customer_id ORDER BY total DESC";
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

	public function getTotalOrders($data = array()) {
		$customer_id_array = array();	$order_query_data = array();
		$collection="mongo_order";
		$where=array();
		$where['customer_id']= array('$gt'=>0); 
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
		$order=array();
		$order_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_query_data as $order_query_data_info) {
			$customer_id_array[]=(int)$order_query_data_info['customer_id'];	
		}
		$customer_id_array=array_unique($customer_id_array);
		return count($customer_id_array);
		/*
		$sql = "SELECT COUNT(DISTINCT o.customer_id) AS total FROM `" . DB_PREFIX . "order` o WHERE o.customer_id > '0'";
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
	}
		
	public function getRewardPoints($data = array()) {		
		$collection="mongo_customer_reward";
		$customer_id_array= array();
		$points_reward_array= array();
		$orders_reward_array= array();
		$customer_reward_query_data= array();
		$where = array();
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
		$order = array();
		$customer_reward_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($customer_reward_query_data as $customer_reward_query_data_info) {
			$customer_id_array[]=(int)$customer_reward_query_data_info['customer_id'];
			$orders_reward_array[(int)$customer_reward_query_data_info['customer_id']][]=$customer_reward_query_data_info['order_id'];	
			if (isset($points_reward_array[(int)$customer_reward_query_data_info['customer_id']])) 
				$points_reward_array[(int)$customer_reward_query_data_info['customer_id']]+=$customer_reward_query_data_info['points'];
			else 
				$points_reward_array[(int)$customer_reward_query_data_info['customer_id']]=$customer_reward_query_data_info['points'];
		}
		$customer_id_array=array_unique($customer_id_array);
		$data_result= array();
		$this->load->model('sale/customer');
		$this->load->model('sale/customer_group');
		$collection="mongo_order";
		foreach ($customer_id_array as $customer_id_array_id) {
			$customer_info= array();
			$customer_info=$this->model_sale_customer->getCustomer((int)$customer_id_array_id);
			$customer_group_info= array();
			$customer_group_info=$this->model_sale_customer_group->getCustomerGroup((int)$customer_info['customer_group_id']);
			$orders_reward_array[(int)$customer_id_array_id]=array_unique($orders_reward_array[(int)$customer_id_array_id]);
			//
			$total_reward_data=0;
			$match=array('$match'=> array('order_id'=>array('$in'=>$orders_reward_array[(int)$customer_id_array_id])));
			$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$total')));		
			$total_reward_data=$this->mongodb->getaggregate($collection, $match, $group);
			$data_result[]= array(
				'customer_id'=>(int)$customer_id_array_id,
				'customer'=>trim($customer_info['firstname'].' '.$customer_info['lastname']),
				'email'=>$customer_info['email'],
				'customer_group'=>$customer_group_info['customer_group_description'][(int)$this->config->get('config_language_id')]['name'],
				'status'=>$customer_info['status'],
				'points'=>$points_reward_array[(int)$customer_id_array_id],
				'orders'=>count($orders_reward_array[(int)$customer_id_array_id]),
				'total'=>$total_reward_data,
			);
		}
		$data_result=$this->mongodb->sapxepthemphantu($data_result, 'points', 0);
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$data_result = array_slice($data_result, (int)$data['start'],(int)$data['limit']);
		return $data_result;
		/*
		$sql = "SELECT cr.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, SUM(cr.points) AS points, COUNT(o.order_id) AS orders, SUM(o.total) AS total FROM " . DB_PREFIX . "customer_reward cr LEFT JOIN `" . DB_PREFIX . "customer` c ON (cr.customer_id = c.customer_id) LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) LEFT JOIN `" . DB_PREFIX . "order` o ON (cr.order_id = o.order_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(cr.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(cr.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		$sql .= " GROUP BY cr.customer_id ORDER BY points DESC";
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

	public function getTotalRewardPoints($data = array()) {	
		$collection="mongo_customer_reward";
		$customer_id_array= array();
		$where = array();
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
		$order = array();
		$customer_reward_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($customer_reward_query_data as $customer_reward_query_data_info) {
			$customer_id_array[]=(int)$customer_reward_query_data_info['customer_id'];
		}
		$customer_id_array=array_unique($customer_id_array);
		return count($customer_id_array);
		/*
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_reward`";
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

	public function getCredit($data = array()) {
		$collection="mongo_customer_transaction";
		$customer_transaction_query_data= array();
		$where = array();
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
		$product_query_data= array();
		$keys = array('customer_id'=>1);
		$initial = array("sumamount" => 0);
		$reduce = 'function (obj, prev) {prev.sumamount = prev.sumamount + obj.amount - 0;}';
		$condition = array('condition' => $where);
		$customer_transaction_query_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
		$this->load->model('sale/customer');
		$this->load->model('sale/customer_group');
		$data_query_result= array();
		foreach ($customer_transaction_query_data as $customer_transaction_query_data_info) {
			$customer_info= array();
			$customer_info=$this->model_sale_customer->getCustomer((int)$customer_transaction_query_data_info['customer_id']);
			$customer_group_info= array();
			$customer_group_info=$this->model_sale_customer_group->getCustomerGroup((int)$customer_info['customer_group_id']);
			$data_query_result[]= array(
				'customer_id'=>$customer_transaction_query_data_info['customer_id'],
				'customer'=>trim($customer_info['firstname'].' '.$customer_info['lastname']),
				'email'=>$customer_info['email'],
				'customer_group'=>$customer_group_info['customer_group_description'][(int)$this->config->get('config_language_id')]['name'],
				'status'=>$customer_info['status'],
				'total'=>$customer_transaction_query_data_info['sumamount']
			);
		}
		return $data_query_result;
		/*
		$sql = "SELECT ct.customer_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, c.email, cgd.name AS customer_group, c.status, SUM(ct.amount) AS total FROM `" . DB_PREFIX . "customer_transaction` ct LEFT JOIN `" . DB_PREFIX . "customer` c ON (ct.customer_id = c.customer_id) LEFT JOIN `" . DB_PREFIX . "customer_group_description` cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(ct.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(ct.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		$sql .= " GROUP BY ct.customer_id ORDER BY total DESC";
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

	public function getTotalCredit($data = array()) {/*
		$sql = "SELECT COUNT(DISTINCT customer_id) AS total FROM `" . DB_PREFIX . "customer_transaction`";
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
		$collection="mongo_customer_transaction";
		$customer_id_array=array();
		$where = array();
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
		$order = array();
		$customer_data=$this->mongodb->getall($collection,$where, $order);
		foreach ($customer_data as $customer_data_info) {
			$customer_id_array[]=$customer_data_info['customer_id'];
		}
		$customer_id_array=array_unique($customer_id_array);
		return count($customer_id_array);
	}

	public function getCustomersOnline($data = array()) {
		$where = array();
		$order= array();
		if (!empty($data['filter_customer'])) {
			$customer_id_array=array();
			$collection="mongo_customer";
			$where1=array('fullname'=>$data['filter_customer']);
			$order1=array();
			$customer_data=$this->mongodb->getall($collection,$where1, $order1);
			foreach ($customer_data as $customer_data_info) {
				$customer_id_array[]=$customer_data_info['customer_id'];
			}
			if ($customer_id_array) {$where['customer_id']= array('$in'=>$customer_id_array);}
		}
		$collection="mongo_customer_online";
		$customer_online_data= 0;
		if (!empty($data['filter_ip'])) {
			$where['ip']=new MongoRegex('/^'.$data['filter_ip'].'/');
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
		$sql = "SELECT co.ip, co.customer_id, co.url, co.referer, co.date_added FROM " . DB_PREFIX . "customer_online co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";
		$implode = array();
		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}
		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sql .= " ORDER BY co.date_added DESC";
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

	public function getTotalCustomersOnline($data = array()) {
		$where = array();
		$order= array();
		if (!empty($data['filter_customer'])) {
			$customer_id_array=array();
			$collection="mongo_customer";
			$where1=array('fullname'=>$data['filter_customer']);
			$order1=array();
			$customer_data=$this->mongodb->getall($collection,$where1, $order1);
			foreach ($customer_data as $customer_data_info) {
				$customer_id_array[]=$customer_data_info['customer_id'];
			}
			if ($customer_id_array) {$where['customer_id']= array('$in'=>$customer_id_array);}
		}
		$collection="mongo_customer_online";
		$customer_online_data= 0;
		if (!empty($data['filter_ip'])) {
			$where['ip']=new MongoRegex('/^'.$data['filter_ip'].'/');
		}
		$customer_online_data=$this->mongodb->gettotal($collection,$where);
		return $customer_online_data;
		/*
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_online` co LEFT JOIN " . DB_PREFIX . "customer c ON (co.customer_id = c.customer_id)";
		$implode = array();
		if (!empty($data['filter_ip'])) {
			$implode[] = "co.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}
		if (!empty($data['filter_customer'])) {
			$implode[] = "co.customer_id > 0 AND CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
	}

	public function getCustomerActivities($data = array()) {	
		$customer_id_array = array();		
		$collection="mongo_customer";
		$where=array();
		if (!empty($data['filter_customer'])) {
			$where['fullname']=new MongoRegex('/^'.$data['filter_customer'].'/');
		}
		$order=array();
		$customer_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($customer_query_data as $customer_query_data_info) {
			$customer_id_array[] = $customer_query_data_info['customer_id'];	
		} 	
		$collection="mongo_customer_activity";
		$where=array();
		$where['customer_id']=array('$in'=>$customer_id_array);
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
		$customer_query_result = $this->mongodb->getlimit($collection,$where, $order, (int)$data['start'], (int)$data['limit']);
		return $customer_query_result;
		/*
		$sql = "SELECT ca.activity_id, ca.customer_id, ca.key, ca.data, ca.ip, ca.date_added FROM " . DB_PREFIX . "customer_activity ca LEFT JOIN " . DB_PREFIX . "customer c ON (ca.customer_id = c.customer_id)";
		$implode = array();
		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}
		if (!empty($data['filter_ip'])) {
			$implode[] = "ca.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sql .= " ORDER BY ca.date_added DESC";
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

	public function getTotalCustomerActivities($data = array()) {
		$customer_id_array = array();		
		$collection="mongo_customer";
		$where=array();
		if (!empty($data['filter_customer'])) {
			$where['fullname']=new MongoRegex('/^'.$data['filter_customer'].'/');
		}
		$order=array();
		$customer_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($customer_query_data as $customer_query_data_info) {
			$customer_id_array[] = $customer_query_data_info['customer_id'];	
		} 	
		$collection="mongo_customer_activity";
		$where=array();
		$where['customer_id']=array('$in'=>$customer_id_array);
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
		$customer_query_total = $this->mongodb->gettotal($collection,$where);
		return $customer_query_total;
		/*
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer_activity` ca LEFT JOIN " . DB_PREFIX . "customer c ON (ca.customer_id = c.customer_id)";
		$implode = array();
		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "'";
		}
		if (!empty($data['filter_ip'])) {
			$implode[] = "ca.ip LIKE '" . $this->db->escape($data['filter_ip']) . "'";
		}
		if (!empty($data['filter_date_start'])) {
			$implode[] = "DATE(ca.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$implode[] = "DATE(ca.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
	}
}