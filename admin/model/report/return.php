<?php
class ModelReportReturn extends Model {
	public function getReturns($data = array()) {
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_return";
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
			$collection="mongo_return";
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
		$collection="mongo_return";
		$where= array();
		if (!empty($data['filter_return_status_id'])) {
			$where= array('return_status_id'=>(int)$data['filter_return_status_id']);
		} else {
			$where= array('return_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$return_data=0;
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$return_data=$this->mongodb->gettotal($collection,$where);
			if ($return_data>0) {
				$date_result[]= array(
					'date_start' => $daterange_info['start'],
					'date_end'   => $daterange_info['end'],
					'returns'    => $return_data
				);
			}
		}
		return $date_result;
		/*
		$sql = "SELECT MIN(r.date_added) AS date_start, MAX(r.date_added) AS date_end, COUNT(r.return_id) AS `returns` FROM `" . DB_PREFIX . "return` r";
		if (!empty($data['filter_return_status_id'])) {
			$sql .= " WHERE r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		} else {
			$sql .= " WHERE r.return_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(r.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(r.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(r.date_added), MONTH(r.date_added), DAY(r.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(r.date_added), WEEK(r.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(r.date_added), MONTH(r.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(r.date_added)";
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

	public function getTotalReturns($data = array()) {
		
		$daterange = array();
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		if (!empty($data['filter_date_start'])) {
			$start=$data['filter_date_start'];
		} else {
			$collection="mongo_return";
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
			$collection="mongo_return";
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
		$collection="mongo_return";
		$where= array();
		if (!empty($data['filter_return_status_id'])) {
			$where= array('return_status_id'=>(int)$data['filter_return_status_id']);
		} else {
			$where= array('return_status_id'=>array('$gt'=>0));
		}
		foreach ($daterange as $daterange_info) {
			$return_data=0;
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($daterange_info['start'])), '$lte'=>new MongoDate(strtotime($daterange_info['end'])));
			$return_data=$this->mongodb->gettotal($collection,$where);
			$date_result+=$return_data;
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
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added), DAY(date_added)) AS total FROM `" . DB_PREFIX . "return`";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), WEEK(date_added)) AS total FROM `" . DB_PREFIX . "return`";
				break;
			case 'month':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added), MONTH(date_added)) AS total FROM `" . DB_PREFIX . "return`";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM `" . DB_PREFIX . "return`";
				break;
		}
		if (!empty($data['filter_return_status_id'])) {
			$sql .= " WHERE return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		} else {
			$sql .= " WHERE return_status_id > '0'";
		}
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}
		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
	}
}