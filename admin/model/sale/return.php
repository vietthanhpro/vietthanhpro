<?php
class ModelSaleReturn extends Model {
	public function addReturn($data) {
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', return_status_id = '" . (int)$data['return_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");
		$collection="mongo_return";
		$return_id=1+(int)$this->mongodb->getlastid($collection,'return_id');
		$newdocument=array('return_id'=>(int)$return_id, 'order_id'=>(int)$data['order_id'], 'product_id'=>(int)$data['product_id'], 'customer_id'=>(int)$data['customer_id'], 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'product'=>$data['product'], 'model'=>$data['model'],'quantity'=>(int)$data['quantity'], 'opened'=>(int)$data['opened'],'return_reason_id'=>(int)$data['return_reason_id'], 'return_action_id'=>(int)$data['return_action_id'], 'return_status_id'=>(int)$data['return_status_id'], 'comment'=>(int)$data['comment'], 'date_ordered'=>new MongoDate(strtotime($data['date_ordered'])),'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))),'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 
	}

	public function editReturn($return_id, $data) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
		$collection="mongo_return";
		$infoupdate=array('order_id'=>(int)$data['order_id'], 'product_id'=>(int)$data['product_id'], 'customer_id'=>(int)$data['customer_id'], 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'product'=>$data['product'], 'model'=>$data['model'],'quantity'=>(int)$data['quantity'], 'opened'=>(int)$data['opened'],'return_reason_id'=>(int)$data['return_reason_id'], 'return_action_id'=>(int)$data['return_action_id'], 'return_status_id'=>(int)$data['return_status_id'], 'comment'=>(int)$data['comment'], 'date_ordered'=>new MongoDate(strtotime($data['date_ordered'])), 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('return_id'=>(int)$return_id);
		$this->mongodb->update($collection,$infoupdate,$where);
	}

	public function deleteReturn($return_id) {
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "return` WHERE return_id = '" . (int)$return_id . "'");
		$collection="mongo_return";
		$where=array('return_id'=>(int)$return_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");
		$collection="mongo_return_history";
		$where=array('return_id'=>(int)$return_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getReturn($return_id) {
		//$query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'");
		//return $query->row;
		$return_info = array();
		$collection="mongo_return";
		$where=array('return_id'=>(int)$return_id);
		$return_info=$this->mongodb->getBy($collection,$where);
		if ($return_info) {
			$collection="mongo_customer";
			$where=array('customer_id'=>(int)$return_info['customer_id']);
			$customer_info=$this->mongodb->getBy($collection,$where);
			if ($customer_info) {$return_info['customer']=trim($return_info['firstname'].' '.$return_info['lastname']);}
			else {$return_info['customer']='';}
		} 
		return $return_info;
	}

	public function getReturns($data = array()) {/*
		$sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status FROM `" . DB_PREFIX . "return` r";
		$implode = array();
		if (!empty($data['filter_return_id'])) {
			$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		}
		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}
		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}
		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}
		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}
		if (!empty($data['filter_return_status_id'])) {
			$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sort_data = array(
			'r.return_id',
			'r.order_id',
			'customer',
			'r.product',
			'r.model',
			'status',
			'r.date_added',
			'r.date_modified'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.return_id";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {$data['start'] = 0;}
			if ($data['limit'] < 1) {$data['limit'] = 20;}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		//$query = $this->db->query($sql);
		return $query->rows;*/
		$collection="mongo_return";
		$where=array();
		if (!empty($data['filter_return_id'])) {
			$where['return_id']=(int)$data['filter_return_id'];
		}
		if (!empty($data['filter_order_id'])) {
			$where['order_id']=(int)$data['filter_order_id'];
		}
		if (!empty($data['filter_customer'])) {
			$where['firstname']=new MongoRegex('/^'.$data['filter_customer'].'/');
		}
		if (!empty($data['filter_product'])) {
			$where['product']=$data['filter_product'];
		}
		if (!empty($data['filter_model'])) {
			$where['model']=$data['filter_model'];
		}
		if (!empty($data['filter_return_status_id'])) {
			$where['return_status_id']=(int)$data['filter_return_status_id'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
		}
		$order=array();
		if (isset($data['start']) || isset($data['limit'])) {if ($data['start'] < 0) {$data['start'] = 0;}if ($data['limit'] < 1) {$data['limit'] = 20;}$start=$data['start'];$limit=$data['limit'];} else {$start=0;$limit=0;}
		$sort_data = array(
			'return_id',
			'order_id',
			'firstname',
			'product',
			'model',
			'return_status_id',
			'date_added',
			'date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'return_id';
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		}
		return $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
	}

	public function getTotalReturns($data = array()) {/*
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`r";
		$implode = array();
		if (!empty($data['filter_return_id'])) {
			$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		}
		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}
		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
		}
		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}
		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}
		if (!empty($data['filter_return_status_id'])) {
			$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
		$collection="mongo_return";
		$where=array();
		if (!empty($data['filter_return_id'])) {
			$where['return_id']=(int)$data['filter_return_id'];
		}
		if (!empty($data['filter_order_id'])) {
			$where['order_id']=(int)$data['filter_order_id'];
		}
		if (!empty($data['filter_customer'])) {
			$where['firstname']=new MongoRegex('/^'.$data['filter_customer'].'/');
		}
		if (!empty($data['filter_product'])) {
			$where['product']=$data['filter_product'];
		}
		if (!empty($data['filter_model'])) {
			$where['model']=$data['filter_model'];
		}
		if (!empty($data['filter_return_status_id'])) {
			$where['return_status_id']=(int)$data['filter_return_status_id'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
		}
		$return_data=$this->mongodb->gettotal($collection,$where);
		return $return_data;
	}

	public function getTotalReturnsByReturnStatusId($return_status_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_status_id = '" . (int)$return_status_id . "'");
		//return $query->row['total'];
		$collection="mongo_return";
		$where=array('return_status_id'=>(int)$return_status_id);
		$category_data=$this->mongodb->gettotal($collection,$where);
		return $category_data;
	}

	public function getTotalReturnsByReturnReasonId($return_reason_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_reason_id = '" . (int)$return_reason_id . "'");
		//return $query->row['total'];
		$collection="mongo_return";
		$where=array('return_reason_id'=>(int)$return_reason_id);
		$category_data=$this->mongodb->gettotal($collection,$where);
		return $category_data;
	}

	public function getTotalReturnsByReturnActionId($return_action_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_action_id = '" . (int)$return_action_id . "'");
		//return $query->row['total'];
		$collection="mongo_return";
		$where=array('return_action_id'=>(int)$return_action_id);
		$category_data=$this->mongodb->gettotal($collection,$where);
		return $category_data;
	}

	public function addReturnHistory($return_id, $data) {
		//$this->db->query("UPDATE `" . DB_PREFIX . "return` SET return_status_id = '" . (int)$data['return_status_id'] . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
		$collection="mongo_return";
		$infoupdate=array('return_status_id'=>(int)$data['return_status_id'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('return_id'=>(int)$return_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "return_history SET return_id = '" . (int)$return_id . "', return_status_id = '" . (int)$data['return_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");
		$collection="mongo_return_history";
		$return_history_id=1+(int)$this->mongodb->getlastid($collection,'return_history_id');
		$where=array('return_history_id'=>(int)$return_history_id, 'return_id'=>(int)$return_id, 'return_status_id'=>(int)$data['return_status_id'], 'notify'=>(isset($data['notify']) ? (int)$data['notify'] : 0), 'comment'=>strip_tags($data['comment']), 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 

		if ($data['notify']) {
			//$return_query = $this->db->query("SELECT *, rs.name AS status FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			$return_query_data = array();
			$collection="mongo_return";
			$where=array('return_id'=>(int)$return_id);
			$return_query_data = $this->mongodb->getBy($collection,$where);

			//if ($return_query->num_rows) {
			if ($return_query_data) {
				$collection="mongo_return_status";
				$return_status_query_data = array();
				$where=array('language_id'=>(int)$this->config->get('config_language_id'), 'return_status_id'=>(int)$return_query_data['return_status_id']);
				$return_status_query_data = $this->mongodb->getBy($collection,$where);
				if ($return_status_query_data) {
					$return_query_data['status']=$return_status_query_data['name'];
				} else {
					$return_query_data['status']='';
				}
				///////////
				$this->load->language('mail/return');

				$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'), $return_id);

				$message  = $this->language->get('text_return_id') . ' ' . $return_id . "\n";
				//$message .= $this->language->get('text_date_added') . ' ' . date($this->language->get('date_format_short'), strtotime($return_query->row['date_added'])) . "\n\n";
				$message .= $this->language->get('text_date_added') . ' ' . date($this->language->get('date_format_short'), strtotime($return_query_data['date_added'])) . "\n\n";
				$message .= $this->language->get('text_return_status') . "\n";
				//$message .= $return_query->row['status'] . "\n\n";
				$message .= $return_query_data['status'] . "\n\n";

				if ($data['comment']) {
					$message .= $this->language->get('text_comment') . "\n\n";
					$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
				}

				$message .= $this->language->get('text_footer');

				$mail = new Mail($this->config->get('config_mail'));
				//$mail->setTo($return_query->row['email']);
				$mail->setTo($return_query_data['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject($subject);
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	}

	public function getReturnHistories($return_id, $start = 0, $limit = 10) {
		if ($start < 0) {$start = 0;}
		if ($limit < 1) {$limit = 10;}
		$voucher_history_data = array();
		$collection="mongo_return_history";
		$where = array('return_id'=>(int)$return_id);
		$order = array('date_added'=>1);
		$return_history_query_data = $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		$this->load->model('localisation/return_status');
		foreach ($return_history_query_data as $return_history_data_info) {
			$return_status_info= array();
			$return_status_info=$this->model_sale_return_status->getReturnStatus($return_history_data_info['return_status_id']);
			$voucher_history_data[] = array(
				'notify'=>$return_history_data_info['notify'],
				'status'=>$return_status_info['name'],
				'comment'=>$return_history_data_info['comment'],
				'date_added'=>$return_history_data_info['date_added'],
			);
		}
		//$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
	}

	public function getTotalReturnHistories($return_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");
		//return $query->row['total'];
		$collection="mongo_return_history";
		$where=array('return_id'=>(int)$return_id);
		$category_data=$this->mongodb->gettotal($collection,$where);
		return $category_data;
	}

	public function getTotalReturnHistoriesByReturnStatusId($return_status_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_status_id = '" . (int)$return_status_id . "' GROUP BY return_id");
		//return $query->row['total'];
		$collection="mongo_return_history";
		$where=array('return_status_id'=>(int)$return_status_id);
		$category_data=$this->mongodb->gettotal($collection,$where);
		return $category_data;
	}
}