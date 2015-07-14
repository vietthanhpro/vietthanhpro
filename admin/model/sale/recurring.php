<?php
class ModelSaleRecurring extends Model {
	/*
	public function getTotalRecurrings($data) {
		$sql = "SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "order_recurring` `or` JOIN `" . DB_PREFIX . "order` o USING(order_id) WHERE 1 = 1";
		if (!empty($data['filter_order_recurring_id'])) {
			$sql .= " AND or.order_recurring_id = " . (int)$data['filter_order_recurring_id'];
		}
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND or.order_id = " . (int)$data['filter_order_id'];
		}
		if (!empty($data['filter_payment_reference'])) {
			$sql .= " AND or.reference LIKE '" . $this->db->escape($data['filter_reference']) . "%'";
		}
		if (!empty($data['filter_customer'])) {
			$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}
		if (!empty($data['filter_status'])) {
			$sql .= " AND or.status = " . (int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(or.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];
	}*/

	public function getRecurrings($data) {
		$order_id_data = array();
		if (isset($data['filter_customer'])) {
			$collection="mongo_order";
			$where['firstname']=new MongoRegex('/'.$data['filter_customer'].'/');
			$order=array();
			$order_id_list = $this->mongodb->getall($collection,$where, $order);
			foreach ($order_id_list as $order_id_list_info) {
				$order_id_data[]= (int)$order_id_list_info['order_id'];
			}
		}
		//$sql = "SELECT `or`.order_recurring_id, `or`.order_id, `or`.reference, `or`.`status`, `or`.`date_added`, CONCAT(`o`.`firstname`, ' ', `o`.`lastname`) AS `customer` FROM `" . DB_PREFIX . "order_recurring` `or` JOIN `" . DB_PREFIX . "order` `o` USING(`order_id`) WHERE 1 = 1 ";
		$order_recurring_data = array();
		$collection="mongo_order_recurring";
		$where=array();
		if (!empty($data['filter_order_recurring_id'])) {
			//$sql .= " AND or.order_recurring_id = " . (int)$data['filter_order_recurring_id'];
			$where['order_recurring_id']=(int)$data['filter_order_recurring_id'];
		}
		if (!empty($data['filter_order_id'])) {
			//$sql .= " AND or.order_id = " . (int)$data['filter_order_id'];
			$where['order_id']=(int)$data['filter_order_id'];
		}
		if (!empty($data['filter_reference'])) {
			//$sql .= " AND or.reference LIKE '" . $this->db->escape($data['filter_reference']) . "%'";
			$where['reference']=new MongoRegex('/^'.$data['filter_reference'].'/');
		}
		if (!empty($data['filter_customer'])) {
			//$sql .= " AND CONCAT(o.firstname, ' ', o.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
			$where['order_id']=array('$in'=>$order_id_data);
		}
		if (!empty($data['filter_status'])) {
			//$sql .= " AND or.status = " . (int)$data['filter_status'];
			$where['status']=(int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			//$sql .= " AND DATE(or.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		$sort_data = array(
			'order_recurring_id',
			'order_id',
			'reference',
			//'customer',
			'status',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			//$sql .= " ORDER BY " . $data['sort'];
			$orderby = $data['sort'];
		} else {
			//$sql .= " ORDER BY order_recurring_id";
			$orderby = 'order_recurring_id';
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			//$sql .= " DESC";
			$order[$orderby] = -1;
		} else {
			//$sql .= " ASC";
			$order[$orderby] = 1;
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {$data['start'] = 0;}
			if ($data['limit'] < 1) {$data['limit'] = 20;}
			//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			$start=(int)$data['start'];
			$limit=(int)$data['limit'];
		}
		$recurrings = array();
		$recurring_data_list = $this->mongodb->get($collection,$where, $order, $start, $limit);
		$results = $recurring_data_list['results'];
		//$results = $this->db->query($sql)->rows;
		$this->load->model('sale/order');
		$recurrings = array();
		foreach ($results as $result) {
			$order_info=array();
			$order_info=$this->model_sale_order->getOrder($result['order_id']);
			$recurrings[] = array(
				'order_recurring_id' => $result['order_recurring_id'],
				'order_id'           => $result['order_id'],
				'reference'          => $result['reference'],
				'customer'           => trim($order_info['firstname'].' '.$order_info['lastname']),
				'status'             => $this->getStatus($result['status']),
				'date_added'         => $result['date_added']
			);
		}
		$data_result=array();
		$data_result['results']=$recurrings;
		$data_result['count']=$recurring_data_list['count'];
		return $data_result;
		//return $recurrings;
	}

	public function getRecurring($order_recurring_id) {
		$recurring = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_recurring WHERE order_recurring_id = " . (int)$order_recurring_id);
		$collection="mongo_order_recurring";
		$where=array('order_recurring_id'=>(int)$order_recurring_id);
		$query_data = $this->mongodb->getBy($collection,$where);

		//if ($query->num_rows) {
		if ($query_data) {
			$recurring = array(/*
				'order_recurring_id'    => $query->row['order_recurring_id'],
				'order_id'              => $query->row['order_id'],
				'reference'             => $query->row['reference'],
				'recurring_id'          => $query->row['recurring_id'],
				'recurring_name'        => $query->row['recurring_name'],
				'recurring_description' => $query->row['recurring_description'],
				'product_name'          => $query->row['product_name'],
				'product_quantity'      => $query->row['product_quantity'],
				'status_id'             => $query->row['status'],
				'status'                => $this->getStatus($query->row['status'])*/
				'order_recurring_id'    => $query_data['order_recurring_id'],
				'order_id'              => $query_data['order_id'],
				'reference'             => $query_data['reference'],
				'recurring_id'          => $query_data['recurring_id'],
				'recurring_name'        => $query_data['recurring_name'],
				'recurring_description' => $query_data['recurring_description'],
				'product_name'          => $query_data['product_name'],
				'product_quantity'      => $query_data['product_quantity'],
				'status_id'             => $query_data['status'],
				'status'                => $this->getStatus($query_data['status'])
			);
		}

		return $recurring;
	}

	public function getRecurringTransactions($order_recurring_id) {
		$transactions = array();

		//$query = $this->db->query("SELECT amount, type, date_added FROM " . DB_PREFIX . "order_recurring_transaction WHERE order_recurring_id = " . (int)$order_recurring_id . " ORDER BY date_added DESC")->rows;	
		$collection="mongo_order_recurring_transaction";	
		$where=array('order_recurring_id'=>(int)$order_recurring_id);
		$order=array('date_added'=> -1);
		$query_data = $this->mongodb->getall($collection,$where, $order);

		//foreach ($query->rows as $result) {
		foreach ($query_data as $result) {
			switch ($result['type']) {
				case 0:
					$type = $this->language->get('text_transaction_date_added');
					break;
				case 1:
					$type = $this->language->get('text_transaction_payment');
					break;
				case 2:
					$type = $this->language->get('text_transaction_outstanding_payment');
					break;
				case 3:
					$type = $this->language->get('text_transaction_skipped');
					break;
				case 4:
					$type = $this->language->get('text_transaction_failed');
					break;
				case 5:
					$type = $this->language->get('text_transaction_cancelled');
					break;
				case 6:
					$type = $this->language->get('text_transaction_suspended');
					break;
				case 7:
					$type = $this->language->get('text_transaction_suspended_failed');
					break;
				case 8:
					$type = $this->language->get('text_transaction_outstanding_failed');
					break;
				case 9:
					$type = $this->language->get('text_transaction_expired');
					break;
				default:
					$type = '';
					break;
			}

			$transactions[] = array(
				'date_added' => $result['date_added'],
				'amount'     => $result['amount'],
				'type'       => $type
			);
		}

		return $transactions;
	}

	private function getStatus($status) {
		switch ($status) {
			case 1:
				$result = $this->language->get('text_status_inactive');
				break;
			case 2:
				$result = $this->language->get('text_status_active');
				break;
			case 3:
				$result = $this->language->get('text_status_suspended');
				break;
			case 4:
				$result = $this->language->get('text_status_cancelled');
				break;
			case 5:
				$result = $this->language->get('text_status_expired');
				break;
			case 6:
				$result = $this->language->get('text_status_pending');
				break;
			default:
				$result = '';
				break;
		}

		return $result;
	}
}