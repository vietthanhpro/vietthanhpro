<?php
class ModelReportSale extends Model {
	// Sales
	public function getTotalSales($data = array()) { 
		/*$sql = "SELECT SUM(total) AS total FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0'";
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
		//find(array("date" => array('$gte' => $startOfDay, '$lte' => $endOfDay)));
		$collection="mongo_order";
		if (!empty($data['filter_date_added'])) {
			$start = new MongoDate(strtotime($data['filter_date_added']." 00:00:00"));
			$end = new MongoDate(strtotime($data['filter_date_added']." 23:59:59"));
			$match=array('$match'=> array('order_status_id'=>array('$gt'=>0), "date_added" => array('$gt' => $start, '$lte' => $end)));
		} else {
			$match=array('$match'=> array('order_status_id'=>array('$gt'=>0)));
		}
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$total')));		
		$order_data=$this->mongodb->getaggregate($collection, $match, $group);
		return $order_data;
	}
		
	// Map
	public function getTotalOrdersByCountry() {
		//$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, c.iso_code_2 FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "country` c ON (o.payment_country_id = c.country_id) WHERE o.order_status_id > '0' GROUP BY o.payment_country_id");
		//return $query->rows;
		$collection="mongo_country"; $country_data = array();
		$where=array();
		$order=array();
		$country_list=$this->mongodb->getall($collection,$where, $order);
		foreach ($country_list as $country_list_info) {
			$collection="mongo_order";
			$country_total= 0;
			$where=array('payment_country_id'=>$country_list_info['country_id']);
			$country_total=$this->mongodb->gettotal($collection,$where);
			//
			$match=array('$match'=> array('payment_country_id'=>$country_list_info['country_id']));
			$group=array('$group'=> array('_id'=>'','ketqua'=>array('$sum' => '$total')));		
			$country_amount=$this->mongodb->getaggregate($collection, $match, $group);
		
			$country_data[] = array(
				'iso_code_2'=>$country_list_info['iso_code_2'],
				'total'=>$country_total,
				'amount'=>$country_amount,
			);
		}
		return $country_data;
	}
		
	// Orders
	public function getTotalOrdersByDay() {
		//$implode = array();
		$order_status_id_array= array();
		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			//$implode[] = "'" . (int)$order_status_id . "'";
			$order_status_id_array[]=(int)$order_status_id;
		}
		$order_data = array();
		$collection="mongo_order";

		for ($i = 0; $i < 24; $i++) {
			$date = date('Y-m-d');
			if ($i<10) {$k='0'.(string)$i;}
			else {$k=(string)$i;}
			//
			$start = new MongoDate(strtotime($date." ".$k.":00:00"));
			$end = new MongoDate(strtotime($date." ".$k.":59:59"));
			$where=array('order_status_id'=>array('$in'=>$order_status_id_array), "date_added" => array('$gte' => $start, '$lte' => $end));
			$order_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$order_data[$i] = array(
				'hour'  => $i,
				//'total' => 0,
				'total' => $order_data_total
			);
		}/*				
		//$query = $this->db->query("SELECT COUNT(*) AS total, HOUR(date_added) AS hour FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND DATE(date_added) = DATE(NOW()) GROUP BY HOUR(date_added) ORDER BY date_added ASC");
		foreach ($query->rows as $result) {
			$order_data[$result['hour']] = array(
				'hour'  => $result['hour'],
				'total' => $result['total']
			);
		}*/
		return $order_data;
	}

	public function getTotalOrdersByWeek() {
		//$implode = array();
		$order_status_id_array= array();
		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			//$implode[] = "'" . (int)$order_status_id . "'";
			$order_status_id_array[]=(int)$order_status_id;
		}				
		$order_data = array();
		$collection="mongo_order";
		$date_start = strtotime('-' . date('w') . ' days');
		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', $date_start + ($i * 86400));
			//
			$start = new MongoDate(strtotime($date." 00:00:00"));
			$end = new MongoDate(strtotime($date." 23:59:59"));
			$where=array('order_status_id'=>array('$in'=>$order_status_id_array), "date_added" => array('$gte' => $start, '$lte' => $end));
			$order_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$order_data[date('w', strtotime($date))] = array(
				'day'   => date('D', strtotime($date)),
				//'total' => 0,
				'total' => $order_data_total
			);
		}/*
		//$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND DATE(date_added) >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "') GROUP BY DAYNAME(date_added)");
		foreach ($query->rows as $result) {
			$order_data[date('w', strtotime($result['date_added']))] = array(
				'day'   => date('D', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}*/
		return $order_data;
	}

	public function getTotalOrdersByMonth() {
		//$implode = array();
		$order_status_id_array= array();
		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			//$implode[] = "'" . (int)$order_status_id . "'";
			$order_status_id_array[]=(int)$order_status_id;
		}				
		$order_data = array();
		$collection="mongo_order";
		for ($i = 1; $i <= date('t'); $i++) {
			$date = date('Y') . '-' . date('m') . '-' . $i;
			//
			$start = new MongoDate(strtotime($date." 00:00:00"));
			$end = new MongoDate(strtotime($date." 23:59:59"));
			$where=array('order_status_id'=>array('$in'=>$order_status_id_array), "date_added" => array('$gte' => $start, '$lte' => $end));
			$order_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$order_data[date('j', strtotime($date))] = array(
				'day'   => date('d', strtotime($date)),
				//'total' => 0,
				'total' => $order_data_total
			);
		}
		//$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND DATE(date_added) >= '" . $this->db->escape(date('Y') . '-' . date('m') . '-1') . "' GROUP BY DATE(date_added)");
		/*
		foreach ($query->rows as $result) {
			$order_data[date('j', strtotime($result['date_added']))] = array(
				'day'   => date('d', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}*/
		return $order_data;
	}

	public function getTotalOrdersByYear() {
		//$implode = array();
		$order_status_id_array= array();
		foreach ($this->config->get('config_complete_status') as $order_status_id) {
			//$implode[] = "'" . (int)$order_status_id . "'";
			$order_status_id_array[]=(int)$order_status_id;
		}								
		$order_data = array();
		$collection="mongo_order";
		$year_cur=date('Y');
		for ($i = 1; $i <= 12; $i++) {
			//
			if ($i<10) {$k='0'.(string)$i;}
			else {$k=(string)$i;}
			$end_number = cal_days_in_month(CAL_GREGORIAN, $i, $year_cur);
			$start = new MongoDate(strtotime($year_cur . '-' . $k . '-1' . " 00:00:00"));
			$end = new MongoDate(strtotime($year_cur . '-' . $k . '-'.$end_number . " 23:59:59"));
			$where=array('order_status_id'=>array('$in'=>$order_status_id_array), "date_added" => array('$gte' => $start, '$lte' => $end));
			$order_data_total=$this->mongodb->gettotal($collection,$where);
			//
			$order_data[$i] = array(
				'month' => date('M', mktime(0, 0, 0, $i)),
				//'total' => 0,
				'total' => $order_data_total
			);
		}
		/*
		//$query = $this->db->query("SELECT COUNT(*) AS total, date_added FROM `" . DB_PREFIX . "order` WHERE order_status_id IN(" . implode(",", $implode) . ") AND YEAR(date_added) = YEAR(NOW()) GROUP BY MONTH(date_added)");

		foreach ($query->rows as $result) {
			$order_data[date('n', strtotime($result['date_added']))] = array(
				'month' => date('M', strtotime($result['date_added'])),
				'total' => $result['total']
			);
		}*/
		return $order_data;
	}
	
	public function getOrders($data = array()) {			
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getlastid($collection,'date_added');
			if ($date_added) {
				$start='1970-01-01 00:00:01';
			} else {
				$date_added=(array)$date_added;
				$start=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		if (!empty($data['filter_date_end'])) {
			$end = $data['filter_date_end'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getfirstid($collection,'date_added');
			if ($date_added) {
				$end = date("Y-m-d").' 23:59:59'; 
			} else {
				$date_added=(array)$date_added;
				$end=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		switch($group) {
			case 'day';
				$daterange=$this->mongodb->date_start_end($start,$end);
				break;
			default:
			case 'week':
				$daterange=$this->mongodb->week_start_end($start,$end);
				break;
			case 'month':
				$daterange=$this->mongodb->month_start_end($start,$end);
				break;
			case 'year':
				$daterange=$this->mongodb->year_start_end($start,$end);
				break;
		}
		$date_result= array();
		$where= array();
		if (!empty($data['filter_order_status_id'])) {
			$where= array('order_status_id'=>(int)$data['filter_order_status_id']);
		} else {
			$where= array('order_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$order_id_array=array();
			$order_data=array();
			$collection="mongo_order";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$order = array();
			$order_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($order_data as $order_data_info) {
				$order_id_array[]=$order_data_info['order_id'];
			}
			$order_id_array=array_unique($order_id_array);
			if ($order_id_array) {
				$order_total_data= array();
				$collection="mongo_order_total";
				$keys = array();
				$initial = array("sumvalue" => 0);
				$reduce = 'function (obj, prev) {prev.sumvalue = prev.sumvalue + obj.value - 0;}';
				$condition = array('condition' => array('order_id'=>array('$in'=>$order_id_array),'code' => 'tax'));
				$order_total_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
				if (isset($order_total_data[0]['sumvalue'])) {$tax_result= $order_total_data[0]['sumvalue'];}
				else {$tax_result= 0;}
				//
				$order_product_data= array();
				$collection="mongo_order_product";
				$keys = array();
				$initial = array("sumquantity" => 0);
				$reduce = 'function (obj, prev) {prev.sumquantity = prev.sumquantity + obj.quantity - 0;}';
				$condition = array('condition' => array('order_id'=>array('$in'=>$order_id_array)));
				$order_product_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
				if (isset($order_product_data[0]['sumquantity'])) {$product_result= $order_product_data[0]['sumquantity'];}
				else {$product_result= 0;}
				//
				$order_data= array();
				$collection="mongo_order";
				$keys = array();
				$initial = array("sumtotal" => 0);
				$reduce = 'function (obj, prev) {prev.sumtotal = prev.sumtotal + obj.total - 0;}';
				$condition = array('condition' => array('order_id'=>array('$in'=>$order_id_array)));
				$order_product_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
				if (isset($order_data[0]['sumtotal'])) {$order_result= $order_data[0]['sumtotal'];}
				else {$order_result= 0;}
				//
				$date_result[]= array(
					'date_start' => $daterange_info['start'],
					'date_end'   => $daterange_info['end'],
					'orders'   => count($order_id_array),
					'products'   => $product_result,
					'tax'   => $tax_result,
					'total'   => $order_result,
				);
			}
		}
		return $date_result;
		/*
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, COUNT(*) AS `orders`, SUM((SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS products, SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id)) AS tax, SUM(o.total) AS `total` FROM `" . DB_PREFIX . "order` o";
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE o.order_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}
		$sql .= " ORDER BY o.date_added DESC";
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
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getlastid($collection,'date_added');
			if ($date_added) {
				$start='1970-01-01 00:00:01';
			} else {
				$date_added=(array)$date_added;
				$start=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		if (!empty($data['filter_date_end'])) {
			$end = $data['filter_date_end'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getfirstid($collection,'date_added');
			if ($date_added) {
				$end = date("Y-m-d").' 23:59:59'; 
			} else {
				$date_added=(array)$date_added;
				$end=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		switch($group) {
			case 'day';
				$daterange=$this->mongodb->date_start_end($start,$end);
				break;
			default:
			case 'week':
				$daterange=$this->mongodb->week_start_end($start,$end);
				break;
			case 'month':
				$daterange=$this->mongodb->month_start_end($start,$end);
				break;
			case 'year':
				$daterange=$this->mongodb->year_start_end($start,$end);
				break;
		}
		$date_result= 0;
		$where= array();
		if (!empty($data['filter_order_status_id'])) {
			$where= array('order_status_id'=>(int)$data['filter_order_status_id']);
		} else {
			$where= array('order_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$order_id_array=array();
			$order_data=array();
			$collection="mongo_order";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$order = array();
			$order_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($order_data as $order_data_info) {
				$order_id_array[]=$order_data_info['order_id'];
			}
			$order_id_array=array_unique($order_id_array);
			if ($order_id_array) {
				$date_result++;
			}
		}
		return $date_result;
		/*
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added), DAY(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), WEEK(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
		}
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];
		*/
	}

	public function getTaxes($data = array()) {			
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getlastid($collection,'date_added');
			if ($date_added) {
				$start='1970-01-01 00:00:01';
			} else {
				$date_added=(array)$date_added;
				$start=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		if (!empty($data['filter_date_end'])) {
			$end = $data['filter_date_end'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getfirstid($collection,'date_added');
			if ($date_added) {
				$end = date("Y-m-d").' 23:59:59'; 
			} else {
				$date_added=(array)$date_added;
				$end=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		switch($group) {
			case 'day';
				$daterange=$this->mongodb->date_start_end($start,$end);
				break;
			default:
			case 'week':
				$daterange=$this->mongodb->week_start_end($start,$end);
				break;
			case 'month':
				$daterange=$this->mongodb->month_start_end($start,$end);
				break;
			case 'year':
				$daterange=$this->mongodb->year_start_end($start,$end);
				break;
		}
		$date_result= array();
		$where= array();
		if (!empty($data['filter_order_status_id'])) {
			$where= array('order_status_id'=>(int)$data['filter_order_status_id']);
		} else {
			$where= array('order_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$order_id_array=array();
			$order_data=array();
			$collection="mongo_order";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$order = array();
			$order_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($order_data as $order_data_info) {
				$order_id_array[]=$order_data_info['order_id'];
			}
			if ($order_id_array) {
				$order_total_data= array();
				$collection="mongo_order_total";
				$keys = array('title'=>1);
				$initial = array("sumvalue" => 0,"countorder_id"=>0);
				$reduce = 'function (obj, prev) {prev.sumvalue = prev.sumvalue + obj.value - 0;if (obj.order_id != null) if (obj.order_id instanceof Array) prev.countorder_id += obj.order_id.length; else prev.countorder_id++;}';
				$condition = array('condition' => array('order_id'=>array('$in'=>$order_id_array),'code' => 'tax'));
				$order_total_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
				foreach ($order_total_data as $order_total_data_info) {
					$date_result[]= array(
						'date_start' => $daterange_info['start'],
						'date_end'   => $daterange_info['end'],
						'title'   => $order_total_data_info['title'],
						'total'   => $order_total_data_info['sumvalue'],
						'orders'   => $order_total_data_info['countorder_id'],
					);
				}
			}
		}
		return $date_result;
		/*
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'";
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
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
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
		return $query->rows;
		*/
	}

	public function getTotalTaxes($data = array()) {		
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getlastid($collection,'date_added');
			if ($date_added) {
				$start='1970-01-01 00:00:01';
			} else {
				$date_added=(array)$date_added;
				$start=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		if (!empty($data['filter_date_end'])) {
			$end = $data['filter_date_end'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getfirstid($collection,'date_added');
			if ($date_added) {
				$end = date("Y-m-d").' 23:59:59'; 
			} else {
				$date_added=(array)$date_added;
				$end=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		switch($group) {
			case 'day';
				$daterange=$this->mongodb->date_start_end($start,$end);
				break;
			default:
			case 'week':
				$daterange=$this->mongodb->week_start_end($start,$end);
				break;
			case 'month':
				$daterange=$this->mongodb->month_start_end($start,$end);
				break;
			case 'year':
				$daterange=$this->mongodb->year_start_end($start,$end);
				break;
		}
		$date_result= 0;
		$where= array();
		if (!empty($data['filter_order_status_id'])) {
			$where= array('order_status_id'=>(int)$data['filter_order_status_id']);
		} else {
			$where= array('order_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$order_id_array=array();
			$order_data=array();
			$collection="mongo_order";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$order = array();
			$order_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($order_data as $order_data_info) {
				$order_id_array[]=$order_data_info['order_id'];
			}
			if ($order_id_array) {
				$order_total_data= array();
				$collection="mongo_order_total";
				$keys = array('title'=>1);
				$initial = array("sumvalue" => 0,"countorder_id"=>0);
				$reduce = 'function (obj, prev) {prev.sumvalue = prev.sumvalue + obj.value - 0;if (obj.order_id != null) if (obj.order_id instanceof Array) prev.countorder_id += obj.order_id.length; else prev.countorder_id++;}';
				$condition = array('condition' => array('order_id'=>array('$in'=>$order_id_array),'code' => 'tax'));
				$order_total_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
				$date_result+=count($order_total_data);
			}
		}
		return $date_result;
		/*
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}
		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'tax'";
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

	public function getShipping($data = array()) {		
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getlastid($collection,'date_added');
			if ($date_added) {
				$start='1970-01-01 00:00:01';
			} else {
				$date_added=(array)$date_added;
				$start=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		if (!empty($data['filter_date_end'])) {
			$end = $data['filter_date_end'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getfirstid($collection,'date_added');
			if ($date_added) {
				$end = date("Y-m-d").' 23:59:59'; 
			} else {
				$date_added=(array)$date_added;
				$end=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		switch($group) {
			case 'day';
				$daterange=$this->mongodb->date_start_end($start,$end);
				break;
			default:
			case 'week':
				$daterange=$this->mongodb->week_start_end($start,$end);
				break;
			case 'month':
				$daterange=$this->mongodb->month_start_end($start,$end);
				break;
			case 'year':
				$daterange=$this->mongodb->year_start_end($start,$end);
				break;
		}
		$date_result= array();
		$where= array();
		if (!empty($data['filter_order_status_id'])) {
			$where= array('order_status_id'=>(int)$data['filter_order_status_id']);
		} else {
			$where= array('order_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$order_id_array=array();
			$order_data=array();
			$collection="mongo_order";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$order = array();
			$order_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($order_data as $order_data_info) {
				$order_id_array[]=$order_data_info['order_id'];
			}
			if ($order_id_array) {
				$order_total_data= array();
				$collection="mongo_order_total";
				$keys = array('title'=>1);
				$initial = array("sumvalue" => 0,"countorder_id"=>0);
				$reduce = 'function (obj, prev) {prev.sumvalue = prev.sumvalue + obj.value - 0;if (obj.order_id != null) if (obj.order_id instanceof Array) prev.countorder_id += obj.order_id.length; else prev.countorder_id++;}';
				$condition = array('condition' => array('order_id'=>array('$in'=>$order_id_array),'code' => 'shipping'));
				$order_total_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
				foreach ($order_total_data as $order_total_data_info) {
					$date_result[]= array(
						'date_start' => $daterange_info['start'],
						'date_end'   => $daterange_info['end'],
						'title'   => $order_total_data_info['title'],
						'total'   => $order_total_data_info['sumvalue'],
						'orders'   => $order_total_data_info['countorder_id'],
					);
				}
			}
		}
		return $date_result;
		/*
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'shipping'";
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
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added), ot.title";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;
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
	}

	public function getTotalShipping($data = array()) {		
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getlastid($collection,'date_added');
			if ($date_added) {
				$start='1970-01-01 00:00:01';
			} else {
				$date_added=(array)$date_added;
				$start=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		if (!empty($data['filter_date_end'])) {
			$end = $data['filter_date_end'];
		} else {
			$collection="mongo_order";
			$date_added=$this->mongodb->getfirstid($collection,'date_added');
			if ($date_added) {
				$end = date("Y-m-d").' 23:59:59'; 
			} else {
				$date_added=(array)$date_added;
				$end=date($this->language->get('date_format_short'),$date_added['sec']).' 00:00:01';
			}
		}
		switch($group) {
			case 'day';
				$daterange=$this->mongodb->date_start_end($start,$end);
				break;
			default:
			case 'week':
				$daterange=$this->mongodb->week_start_end($start,$end);
				break;
			case 'month':
				$daterange=$this->mongodb->month_start_end($start,$end);
				break;
			case 'year':
				$daterange=$this->mongodb->year_start_end($start,$end);
				break;
		}
		$date_result= 0;
		$where= array();
		if (!empty($data['filter_order_status_id'])) {
			$where= array('order_status_id'=>(int)$data['filter_order_status_id']);
		} else {
			$where= array('order_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$order_id_array=array();
			$order_data=array();
			$collection="mongo_order";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$order = array();
			$order_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($order_data as $order_data_info) {
				$order_id_array[]=$order_data_info['order_id'];
			}
			if ($order_id_array) {
				$order_total_data= array();
				$collection="mongo_order_total";
				$keys = array('title'=>1);
				$initial = array("sumvalue" => 0,"countorder_id"=>0);
				$reduce = 'function (obj, prev) {prev.sumvalue = prev.sumvalue + obj.value - 0;if (obj.order_id != null) if (obj.order_id instanceof Array) prev.countorder_id += obj.order_id.length; else prev.countorder_id++;}';
				$condition = array('condition' => array('order_id'=>array('$in'=>$order_id_array),'code' => 'shipping'));
				$order_total_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
				$date_result+=count($order_total_data);
			}
		}
		return $date_result;
		/*
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), WEEK(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), MONTH(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(o.date_added), ot.title) AS total FROM `" . DB_PREFIX . "order` o";
				break;
		}
		$sql .= " LEFT JOIN `" . DB_PREFIX . "order_total` ot ON (o.order_id = ot.order_id) WHERE ot.code = 'shipping'";
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND order_status_id > '0'";
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
}