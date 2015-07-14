<?php
class ModelMarketingCoupon extends Model {
	public function addCoupon($data) {
		$this->event->trigger('pre.admin.coupon.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', discount = '" . (float)$data['discount'] . "', type = '" . $this->db->escape($data['type']) . "', total = '" . (float)$data['total'] . "', logged = '" . (int)$data['logged'] . "', shipping = '" . (int)$data['shipping'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', uses_total = '" . (int)$data['uses_total'] . "', uses_customer = '" . (int)$data['uses_customer'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
		//$coupon_id = $this->db->getLastId();
		$collection="mongo_coupon";
		$coupon_id=1+(int)$this->mongodb->getlastid($collection,'coupon_id');
		$newdocument=array('coupon_id'=>(int)$coupon_id, 'name'=>$data['name'], 'code'=>$data['code'], 'discount'=>(float)$data['discount'], 'type'=>$data['type'], 'total'=>(float)$data['total'], 'logged'=>(int)$data['logged'], 'shipping'=>(int)$data['shipping'], 'date_start'=>new MongoDate(strtotime($data['date_start'])), 'date_end'=>new MongoDate(strtotime($data['date_end'])), 'uses_total'=>(int)$data['uses_total'], 'uses_customer'=>(int)$data['uses_customer'], 'status'=>(int)$data['status'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['coupon_product'])) {
				$collection="mongo_coupon_product";
			foreach ($data['coupon_product'] as $product_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET coupon_id = '" . (int)$coupon_id . "', product_id = '" . (int)$product_id . "'");
				$coupon_product_id=1+(int)$this->mongodb->getlastid($collection,'coupon_product_id');
				$newdocument=array('coupon_product_id'=>(int)$coupon_product_id, 'coupon_id'=>(int)$coupon_id, 'product_id'=>(int)$product_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['coupon_category'])) {
				$collection="mongo_coupon_category";
			foreach ($data['coupon_category'] as $category_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_category SET coupon_id = '" . (int)$coupon_id . "', category_id = '" . (int)$category_id . "'");
				$newdocument=array('coupon_id'=>(int)$coupon_id, 'category_id'=>(int)$category_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$this->event->trigger('post.admin.coupon.add', $coupon_id);

		return $coupon_id;
	}

	public function editCoupon($coupon_id, $data) {
		$this->event->trigger('pre.admin.coupon.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', discount = '" . (float)$data['discount'] . "', type = '" . $this->db->escape($data['type']) . "', total = '" . (float)$data['total'] . "', logged = '" . (int)$data['logged'] . "', shipping = '" . (int)$data['shipping'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', uses_total = '" . (int)$data['uses_total'] . "', uses_customer = '" . (int)$data['uses_customer'] . "', status = '" . (int)$data['status'] . "' WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon";
		$infoupdate=array('coupon_id'=>(int)$coupon_id, 'name'=>$data['name'], 'code'=>$data['code'], 'discount'=>(float)$data['discount'], 'type'=>$data['type'], 'total'=>(float)$data['total'], 'logged'=>(int)$data['logged'], 'shipping'=>(int)$data['shipping'], 'date_start'=>new MongoDate(strtotime($data['date_start'])), 'date_end'=>new MongoDate(strtotime($data['date_end'])), 'uses_total'=>(int)$data['uses_total'], 'uses_customer'=>(int)$data['uses_customer'], 'status'=>(int)$data['status']);
		$where=array('coupon_id'=>(int)$coupon_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon_product";
		$where=array('coupon_id'=>(int)$coupon_id);
		$this->mongodb->delete($collection,$where); 
		
		if (isset($data['coupon_product'])) {
			foreach ($data['coupon_product'] as $product_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_product SET coupon_id = '" . (int)$coupon_id . "', product_id = '" . (int)$product_id . "'");
				$coupon_product_id=1+(int)$this->mongodb->getlastid($collection,'coupon_product_id');
				$newdocument=array('coupon_product_id'=>(int)$coupon_product_id, 'coupon_id'=>(int)$coupon_id, 'product_id'=>(int)$product_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon_category";
		$where=array('coupon_id'=>(int)$coupon_id);
		$this->mongodb->delete($collection,$where); 
		
		if (isset($data['coupon_category'])) {
			foreach ($data['coupon_category'] as $category_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "coupon_category SET coupon_id = '" . (int)$coupon_id . "', category_id = '" . (int)$category_id . "'");
				$newdocument=array('coupon_id'=>(int)$coupon_id, 'category_id'=>(int)$category_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$this->event->trigger('post.admin.coupon.edit', $coupon_id);
	}

	public function deleteCoupon($coupon_id) {
		$this->event->trigger('pre.admin.coupon.delete', $coupon_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "coupon WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon";
		$where=array('coupon_id'=>(int)$coupon_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon_product";
		$where=array('coupon_id'=>(int)$coupon_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon_category";
		$where=array('coupon_id'=>(int)$coupon_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon_history";
		$where=array('coupon_id'=>(int)$coupon_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.coupon.delete', $coupon_id);
	}

	public function getCoupon($coupon_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon WHERE coupon_id = '" . (int)$coupon_id . "'");
		//return $query->row;
		$coupon_info = array();
		$collection="mongo_coupon";
		$where=array('coupon_id'=>(int)$coupon_id);
		$coupon_info=$this->mongodb->getBy($collection,$where);
		return $coupon_info;
	}

	public function getCouponByCode($code) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "coupon WHERE code = '" . $this->db->escape($code) . "'");
		//return $query->row;
		$coupon_info = array();
		$collection="mongo_coupon";
		$where=array('code'=>$code);
		$coupon_info=$this->mongodb->getBy($collection,$where);
		return $coupon_info;
	}

	public function getCoupons($data = array()) {/*
		$sql = "SELECT coupon_id, name, code, discount, date_start, date_end, status FROM " . DB_PREFIX . "coupon";
		$sort_data = array(
			'name',
			'code',
			'discount',
			'date_start',
			'date_end',
			'status'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}
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
		$collection="mongo_coupon";
		$where = array();
		$order = array();
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}	
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$start=$data['start'];
			$limit=$data['limit'];
		} else {
			$start=0;
			$limit=0;
		}	
		$sort_data = array(
			'name',
			'code',
			'discount',
			'date_start',
			'date_end',
			'status'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'name';
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} 
		return $this->mongodb->get($collection,$where, $order, $start, $limit);
	}

	public function getCouponProducts($coupon_id) {
		$coupon_product_data = array();
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon_product";$query_data= array();
		$where = array('coupon_id'=>(int)$coupon_id);
		$order = array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		//foreach ($query->rows as $result) {
		foreach ($query_data as $result) {
			$coupon_product_data[] = $result['product_id'];
		}
		return $coupon_product_data;
	}

	public function getCouponCategories($coupon_id) {
		$coupon_category_data = array();
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
		$collection="mongo_coupon_category";$query_data= array();
		$where = array('coupon_id'=>(int)$coupon_id);
		$order = array();
		$query_data = $this->mongodb->getall($collection,$where, $order);
		//foreach ($query->rows as $result) {
		foreach ($query_data as $result) {
			$coupon_category_data[] = $result['category_id'];
		}
		return $coupon_category_data;
	}

	public function getTotalCoupons() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon");
		//return $query->row['total'];
		$collection="mongo_coupon";$coupon_data= array();
		$where=array();
		$coupon_data=$this->mongodb->gettotal($collection,$where);
		return $coupon_data;
	}

	public function getCouponHistories($coupon_id, $start = 0, $limit = 10) {
		$collection="mongo_coupon_history";
		$coupon_history_query_data = array();
		$coupon_history_query_list = array();
		if ($start < 0) {
			$start = 0;
		}
		if ($limit < 1) {
			$limit = 10;
		}
		$where = array('coupon_id'=>(int)$coupon_id);
		$order = array('date_added'=>1);
		$coupon_history_query_list = $this->mongodb->getlimit($collection,$where, $order, $start, $limit); 
		//$query = $this->db->query("SELECT ch.order_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, ch.amount, ch.date_added FROM " . DB_PREFIX . "coupon_history ch LEFT JOIN " . DB_PREFIX . "customer c ON (ch.customer_id = c.customer_id) WHERE ch.coupon_id = '" . (int)$coupon_id . "' ORDER BY ch.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
		//return $query->rows;
		$this->load->model('sale/customer');
		foreach ($coupon_history_query_list as $coupon_history_query_list_info) {
			$customer_info=array();
			$customer_info=$this->model_sale_customer->getCustomer((int)$coupon_history_query_list_info['customer_id']);
			$coupon_history_query_data[] = array(
				'order_id'=>$coupon_history_query_list_info['order_id'],
				'customer'=>$customer_info['firstname'].' '.$customer_info['lastname'],
				'amount'=>$coupon_history_query_list_info['amount'],
				'date_added'=>$coupon_history_query_list_info['date_added'],
			);
		}
		return $coupon_history_query_data;
	}

	public function getTotalCouponHistories($coupon_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon_history WHERE coupon_id = '" . (int)$coupon_id . "'");
		//return $query->row['total'];
		$collection="mongo_coupon_history";$coupon_history_data= array();
		$where=array('coupon_id'=>(int)$coupon_id);
		$coupon_history_data=$this->mongodb->gettotal($collection,$where);
		return $coupon_history_data;
	}
}