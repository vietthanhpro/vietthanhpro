<?php
class ModelCatalogReview extends Model {
	public function addReview($data) {
		$this->event->trigger('pre.admin.review.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['author']) . "', product_id = '" . (int)$data['product_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
		//$review_id = $this->db->getLastId();
		$collection="mongo_address";
		$review_id=1+(int)$this->mongodb->getlastid($collection,'review_id');
		$where=array('review_id'=>(int)$review_id, 'customer_id'=>0, 'author'=>$data['author'], 'product_id'=>(int)$data['product_id'], 'text'=>strip_tags($data['text']), 'rating'=>(int)$data['rating'], 'status'=>(int)$data['status'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('product');
		$this->event->trigger('post.admin.review.add', $review_id);
		return $review_id;
	}

	public function editReview($review_id, $data) {
		$this->event->trigger('pre.admin.review.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['author']) . "', product_id = '" . (int)$data['product_id'] . "', text = '" . $this->db->escape(strip_tags($data['text'])) . "', rating = '" . (int)$data['rating'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE review_id = '" . (int)$review_id . "'");
		$collection="mongo_review";
		$infoupdate=array('author'=>$data['author'], 'product_id'=>(int)$data['product_id'], 'text'=>strip_tags($data['text']), 'rating'=>(int)$data['rating'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('review_id'=>(int)$review_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('product');

		$this->event->trigger('post.admin.review.edit', $review_id);
	}

	public function deleteReview($review_id) {
		$this->event->trigger('pre.admin.review.delete', $review_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE review_id = '" . (int)$review_id . "'");
		$collection="mongo_review";
		$where=array('review_id'=>(int)$review_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('product');

		$this->event->trigger('post.admin.review.delete', $review_id);
	}

	public function getProduct($product_id) {
		$product_info = array();
		$collection="mongo_product";
		$where=array('product_id'=>(int)$product_id);
		$product_info=$this->mongodb->getBy($collection,$where);
		return $product_info;
	}

	public function getReview($review_id) {
		//$query = $this->db->query("SELECT DISTINCT *, (SELECT pd.name FROM " . DB_PREFIX . "product_description pd WHERE pd.product_id = r.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS product FROM " . DB_PREFIX . "review r WHERE r.review_id = '" . (int)$review_id . "'");
		//return $query->row;
		$collection="mongo_review";
		$review_info = array();
		$where=array('review_id'=>(int)$review_id);
		$review_info= $this->mongodb->getBy($collection,$where);
		if ($review_info) {
			$product_info=$this->getProduct($review_info['product_id']);
			if ($product_info) {
				$review_info['product']=$product_info['product_description'][(int)$this->config->get('config_language_id')]['name'];
			} else {
				$review_info['product']='';
			}
		}
		return $review_info;
	}

	public function getReviews($data = array()) {
		$collection="mongo_product";$product_query_data= array();$product_id_array= array();
		$where = array();
		if (!empty($data['filter_product'])) {
			$where['product_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_product'].'/');
		}
		$order = array();
		$product_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($product_query_data as $product_query_data_info) {
			$product_id_array[]=$product_query_data_info['product_id'];
		}
		/*
		$sql = "SELECT r.review_id, pd.name, r.author, r.rating, r.status, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
		}
		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		$sort_data = array(
			'pd.name',
			'r.author',
			'r.rating',
			'r.status',
			'r.date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.date_added";
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
		$review_data = array();
		$collection="mongo_review";
		$where=array();
		if (!empty($data['filter_product'])) {
			$where['product_id']=array('$in'=>$product_id_array);
		}
		if (!empty($data['filter_author'])) {
			$where['author']=new MongoRegex('/^'.$data['filter_author'].'/');
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where['status']=(int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}/////
		$order=array();
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
			'product_id',
			'author',
			'rating',
			'status',
			'date_added'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'date_added';
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} 
		$review_data = $this->mongodb->get($collection,$where, $order, $start, $limit);
		return $review_data;
		
	}

	public function getTotalReviews($data = array()) {
		$collection="mongo_product";$product_query_data= array();$product_id_array= array();
		$where = array();
		if (!empty($data['filter_product'])) {
			$where['product_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_product'].'/');
		}
		$order = array();
		$product_query_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($product_query_data as $product_query_data_info) {
			$product_id_array[]=$product_query_data_info['product_id'];
		}
		/*
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product_description pd ON (r.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_product'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
		}
		if (!empty($data['filter_author'])) {
			$sql .= " AND r.author LIKE '" . $this->db->escape($data['filter_author']) . "%'";
		}
		if (!empty($data['filter_status'])) {
			$sql .= " AND r.status = '" . (int)$data['filter_status'] . "'";
		}
		if (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];*/
		$collection="mongo_review";
		$where=array();
		if (!empty($data['filter_product'])) {
			$where['product_id']=array('$in'=>$product_id_array);
		}
		if (!empty($data['filter_author'])) {
			$where['author']=new MongoRegex('/^'.$data['filter_author'].'/');
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$where['status']=(int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		$review_total=$this->mongodb->gettotal($collection,$where);
		return $review_total;
	}

	public function getTotalReviewsAwaitingApproval() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review WHERE status = '0'");
		//return $query->row['total'];
		$collection="mongo_review";
		$where=array('status'=>0);
		$review_total=$this->mongodb->gettotal($collection,$where);
		return $review_total;
	}
}