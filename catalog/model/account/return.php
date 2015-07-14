<?php
class ModelAccountReturn extends Model {
	public function addReturn($data) {
		$this->event->trigger('pre.return.add', $data);

		//$this->db->query("INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', customer_id = '" . (int)$this->customer->getId() . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_status_id = '" . (int)$this->config->get('config_return_status_id') . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");
		//$return_id = $this->db->getLastId();
		$collection="mongo_return";
		$return_id=1+(int)$this->mongodb->getlastid($collection,'return_id');
		$newdocument=array('return_id'=>(int)$return_id, 'order_id'=>(int)$data['order_id'], 'product_id'=>(int)$data['product_id'], 'customer_id'=>(int)$this->customer->getId(), 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'email'=>$data['email'], 'telephone'=>$data['telephone'], 'product'=>$data['product'], 'model'=>$data['model'],'quantity'=>(int)$data['quantity'], 'opened'=>(int)$data['opened'],'return_reason_id'=>(int)$data['return_reason_id'], 'return_action_id'=>0, 'return_status_id'=>(int)$this->config->get('config_return_status_id'), 'comment'=>$data['comment'], 'date_ordered'=>new MongoDate(strtotime($data['date_ordered'])),'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))),'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		$this->event->trigger('post.return.add', $return_id);

		return $return_id;
	}

	public function getReturn($return_id) {
		//$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, r.email, r.telephone, r.product, r.model, r.quantity, r.opened, (SELECT rr.name FROM " . DB_PREFIX . "return_reason rr WHERE rr.return_reason_id = r.return_reason_id AND rr.language_id = '" . (int)$this->config->get('config_language_id') . "') AS reason, (SELECT ra.name FROM " . DB_PREFIX . "return_action ra WHERE ra.return_action_id = r.return_action_id AND ra.language_id = '" . (int)$this->config->get('config_language_id') . "') AS action, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status, r.comment, r.date_ordered, r.date_added, r.date_modified FROM `" . DB_PREFIX . "return` r WHERE return_id = '" . (int)$return_id . "' AND customer_id = '" . $this->customer->getId() . "'");
		//return $query->row;
		$return_info = array();
		$collection="mongo_return";
		$where=array('return_id'=>(int)$return_id, 'customer_id'=>(int)$this->customer->getId());
		$return_info=$this->mongodb->getBy($collection,$where);
		if ($return_info) {
			$return_reason_info = array();
			$collection="mongo_return_reason";
			$where=array('return_reason_id'=>(int)$return_info['return_reason_id'], 'language_id'=>(int)$this->config->get('config_language_id'));
			$return_reason_info=$this->mongodb->getBy($collection,$where);
			$return_info['reason']=$return_reason_info['name'];
			///
			$return_action_info = array();
			$collection="mongo_return_action";
			$where=array('return_action_id'=>(int)$return_info['return_action_id'], 'language_id'=>(int)$this->config->get('config_language_id'));
			$return_action_info=$this->mongodb->getBy($collection,$where);
			$return_info['action']=$return_action_info['name'];
			//
			$return_status_info = array();
			$collection="mongo_return_status";
			$where=array('return_status_id'=>(int)$return_info['return_status_id'], 'language_id'=>(int)$this->config->get('config_language_id'));
			$return_status_info=$this->mongodb->getBy($collection,$where);
			$return_info['status']=$return_status_info['name'];
		}
		return $return_info;
	}

	public function getReturns($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 20;
		}
		//$query = $this->db->query("SELECT r.return_id, r.order_id, r.firstname, r.lastname, rs.name as status, r.date_added FROM `" . DB_PREFIX . "return` r LEFT JOIN " . DB_PREFIX . "return_status rs ON (r.return_status_id = rs.return_status_id) WHERE r.customer_id = '" . $this->customer->getId() . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.return_id DESC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
		$ketquatrave=array();$return_query_data=array();
		$collection="mongo_return";
		$where=array('customer_id'=>(int)$this->customer->getId());
		$order=array('return_id'=>-1);
		$return_query_data= $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
			$collection="mongo_return_status";
		foreach ($return_query_data as $return_query_data_info) {
				$return_status_query_data = array();
				$where=array('return_status_id'=>(int)$return_history_query_list_info['return_status_id']);
				$return_status_query_data=$this->mongodb->getBy($collection,$where);
				if ($return_status_query_data) {$return_status_name=$return_status_query_data['name'];}
				else {$return_status_name='';}
			$ketquatrave[] = array(
				'return_id'=>$return_query_data_info['return_id'],
				'order_id'=>$return_query_data_info['order_id'],
				'firstname'=>$return_query_data_info['firstname'],
				'lastname'=>$return_query_data_info['lastname'],
				'date_added'=>$return_query_data_info['date_added'],
				'status'=>$return_status_name,
			);
		}
		return $ketquatrave;
	}

	public function getTotalReturns() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`WHERE customer_id = '" . $this->customer->getId() . "'");
		//return $query->row['total'];
		$return_info= array();
		$collection="mongo_return";
		$where=array('customer_id'=>(int)$this->customer->getId());
		$return_info=$this->mongodb->gettotal($collection,$where);
		return $return_info;
	}

	public function getReturnHistories($return_id) {
		//$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rh.notify = '1' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC");
		//return $query->rows;
		$return_history_data = array();$return_history_query_list = array();
		$collection="mongo_return_history";
		$where=array('return_id'=>(int)$return_id);
		$order=array();
		$return_history_query_list=$this->mongodb->getall($collection,$where,$order);
				$collection="mongo_return_status";
		foreach ($return_history_query_list as $return_history_query_list_info) {
				$return_status_query_data = array();
				$where=array('return_status_id'=>(int)$return_history_query_list_info['return_status_id']);
				$return_status_query_data=$this->mongodb->getBy($collection,$where);
				if ($return_status_query_data) {$return_status_name=$return_status_query_data['name'];}
				else {$return_status_name='';}
			$return_history_data[] = array(
				'date_added'=>$return_history_query_list_info['date_added'],
				'status'=>$return_status_name,
				'comment'=>$return_history_query_list_info['comment'],
				'notify'=>$return_history_query_list_info['notify'],
			);
		}
	}
}