<?php
class ModelCatalogProduct extends Model {
	public function updateViewed($product_id) {
		//$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int)$product_id . "'");
		$collection="mongo_product";
		$where=array('product_id'=>(int)$product_id);
		$info= array('$inc'=> array('viewed'=>1));
		$this->mongodb->incelement($collection,$where, $info); 
	}
	
	public function getStockStatus($stock_status_id) {
		$collection="mongo_stock_status";
		$where=array('language_id'=>(int)$this->config->get('config_language_id'), 'stock_status_id'=>(int)$stock_status_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getManufacturer($manufacturer_id) {
		$manufacturer_info = array();
		$collection="mongo_manufacturer";
		$where=array('manufacturer_id'=>(int)$manufacturer_id);
		$manufacturer_info=$this->mongodb->getBy($collection,$where);
		if ($manufacturer_info) {
			return $manufacturer_info['name'];
		} else {
			return '';
		}
	}

	public function getProductDiscount($product_id) {
		$product_discount_list = array();
		$collection="mongo_product_discount";
		$where=array('product_id'=>(int)$product_id,'customer_group_id'=>(int)$this->config->get('config_customer_group_id'), 'quantity'=>1, 'date_start'=>array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_end'=>array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
		$order=array('priority'=>1,'price'=>1);
		$product_discount_list=$this->mongodb->getall($collection,$where,$order);
		if ($product_discount_list) {
			$product_discount_list_info=array_shift($product_discount_list);
			return $product_discount_list_info['price'];
		} else {
			return 0;
		}
	}

	public function getProductSpecial($product_id) {
		$collection="mongo_product_special";
		$where=array('product_id'=>(int)$product_id,'customer_group_id'=>(int)$this->config->get('config_customer_group_id'), 'date_start'=>array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_end'=>array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
		$order=array('priority'=>1,'price'=>1);
		$product_special_list=$this->mongodb->getall($collection,$where,$order); 
		
		if ($product_special_list) { 
			foreach ($product_special_list as $product_special_list) {
				$price_result=$product_special_list['price'];
			}
			return $price_result;
		} else {
			return 0;
		}
	}

	public function getProductReward($product_id) {
		$collection="mongo_product_reward";
		$where=array('product_id'=>(int)$product_id,'customer_group_id'=>(int)$this->config->get('config_customer_group_id'));
		$product_reward_list=$this->mongodb->getBy($collection,$where);
		if ($product_reward_list) {
			return $product_reward_list['points'];
		} else {
			return 0;
		}
	}

	public function getRatingProduct($product_id) {
		$collection="mongo_review";
		
		$match=array('$match'=> array('product_id'=>(int)$product_id));
		$group=array('$group'=> array('_id'=>'','ketqua'=>array('$avg' => '$rating')));		
		$product_review_total=$this->mongodb->getaggregate($collection, $match, $group);
		
		if ($product_review_total) {
			return $product_review_total;
		} else {
			return 0;
		}
	}

	public function getTotalReviewProduct($product_id) {
		$collection="mongo_review";
		$where=array('product_id'=>(int)$product_id, 'status'=>1);
		return $this->mongodb->gettotal($collection,$where);
	}

	public function getProduct($product_id) {
		$product_info = array();
		$product_data = $this->cache->get('product_id_'.$product_id);
		if (!$product_data) {
			$product_data = array();
			$collection="mongo_product";
			$where=array('product_id'=>(int)$product_id);
			$product_info=$this->mongodb->getBy($collection,$where);
			if ($product_info) {
				$stock_status_info=$this->getStockStatus($product_info['stock_status_id']);
				$manufacturer_info=$this->getManufacturer($product_info['manufacturer_id']);
				$product_discount_info=$this->getProductDiscount($product_id);
				$product_special_info=$this->getProductSpecial($product_id);
				$product_reward_info=$this->getProductReward($product_id);
				$product_rating_info=$this->getRatingProduct($product_id);
				$product_total_review_info=$this->getTotalReviewProduct($product_id);
				$product_data = array(
					'product_id'       => $product_info['product_id'],
					'name'             => $product_info['product_description'][(int)$this->config->get('config_language_id')]['name'],
					'description'      => $product_info['product_description'][(int)$this->config->get('config_language_id')]['description'],
					'meta_title'       => $product_info['product_description'][(int)$this->config->get('config_language_id')]['meta_title'],
					'meta_description' => $product_info['product_description'][(int)$this->config->get('config_language_id')]['meta_description'],
					'meta_keyword'     => $product_info['product_description'][(int)$this->config->get('config_language_id')]['meta_keyword'],
					'tag'              => $product_info['product_tag'],
					'product_image'    => $product_info['product_image'],
					'model'            => $product_info['model'],
					'sku'              => $product_info['sku'],
					'upc'              => $product_info['upc'],
					'ean'              => $product_info['ean'],
					'jan'              => $product_info['jan'],
					'isbn'             => $product_info['isbn'],
					'mpn'              => $product_info['mpn'],
					'location'         => $product_info['location'],
					'quantity'         => $product_info['quantity'],
					'stock_status'     => $stock_status_info,
					'image'            => $product_info['image'],
					'manufacturer_id'  => $product_info['manufacturer_id'],
					'manufacturer'     => $manufacturer_info,
					'price'            => ($product_discount_info ? $product_discount_info : $product_info['price']),
					'special'          => $product_special_info,
					'reward'           => $product_reward_info,
					'points'           => $product_info['points'],
					'tax_class_id'     => $product_info['tax_class_id'],
					'date_available'   => $product_info['date_available'],
					'weight'           => $product_info['weight'],
					'weight_class_id'  => $product_info['weight_class_id'],
					'length'           => $product_info['length'],
					'width'            => $product_info['width'],
					'height'           => $product_info['height'],
					'length_class_id'  => $product_info['length_class_id'],
					'subtract'         => $product_info['subtract'],
					'product_download'         => $product_info['product_download'],
					'rating'           => round($product_rating_info),
					'reviews'          => $product_total_review_info ? $product_total_review_info : 0,
					'minimum'          => $product_info['minimum'],
					'sort_order'       => $product_info['sort_order'],
					'status'           => $product_info['status'],
					'date_added'       => $product_info['date_added'],
					'date_modified'    => $product_info['date_modified'],
					'product_to_store' => $product_info['product_to_store'],
					'viewed'           => $product_info['viewed']
				);
				//print_r($product_data); die();
				$this->cache->set('product_id_'.$product_id, $product_data);
				return $product_data;
			} else {
				return false;
			}
		} else {
			return $product_data;
		}
		/*$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'ean'              => $query->row['ean'],
				'jan'              => $query->row['jan'],
				'isbn'             => $query->row['isbn'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
				'viewed'           => $query->row['viewed']
			);
		} else {
			return false;
		}*/
	}

	public function getProducts($data = array()) {
		/*
		$sql = "SELECT p.product_id, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special";
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}
			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
			if (!empty($data['filter_filter'])) {
				$implode = array();
				$filters = explode(',', $data['filter_filter']);
				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}
				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";
			if (!empty($data['filter_name'])) {
				$implode = array();
				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));
				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}
				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}
			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape($data['filter_tag']) . "%'";
			}
			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}
			$sql .= ")";
		}
		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}
		$sql .= " GROUP BY p.product_id";
		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} elseif ($data['sort'] == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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
		$product_data = array();
		//$query = $this->db->query($sql);
		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}*/		
		$product_data = array();
		$collection="mongo_product";
		$where=array();
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$where['product_category']=(int)$data['filter_sub_category'];
			} else {
				$where['product_category']=(int)$data['filter_category_id'];
			}
			if (!empty($data['filter_filter'])) {
				$filter_id_array = array();
				$filters = explode(',', $data['filter_filter']);
				foreach ($filters as $filter_id) {
					$filter_id_array[] = (int)$filter_id;
				}
				$where['product_filter']=array('$in'=>$filter_id_array);
			}
		}	
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			if (!empty($data['filter_name'])) {
				$where['$or']['product_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/'.$data['filter_name'].'/');
				$where['$or']['model']=utf8_strtolower($data['filter_name']);
				$where['$or']['sku']=utf8_strtolower($data['filter_name']);
				$where['$or']['upc']=utf8_strtolower($data['filter_name']);
				$where['$or']['ean']=utf8_strtolower($data['filter_name']);
				$where['$or']['jan']=utf8_strtolower($data['filter_name']);
				$where['$or']['isbn']=utf8_strtolower($data['filter_name']);
				$where['$or']['mpn']=utf8_strtolower($data['filter_name']);
				if (!empty($data['filter_description'])) {
					if (!empty($data['filter_tag'])) {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.description'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.tag'=>new MongoRegex('/'.$data['filter_tag'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					} else {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.description'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					}
				} else {
					if (!empty($data['filter_tag'])) {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.tag'=>new MongoRegex('/'.$data['filter_tag'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					} else {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					}
				}
			} else {
				$where['product_description.'. (int)$this->config->get('config_language_id').'.tag']=new MongoRegex('/'.$data['filter_tag'].'/');
			}
		}
		if (!empty($data['filter_manufacturer_id'])) {
			$where['manufacturer_id']=(int)$data['filter_manufacturer_id'];
		}
		$order=array(); $product_list_data = array(); //$product_list = array();
		$product_list_data = $this->mongodb->getall($collection,$where, $order); //print_r($product_list_data); echo 'fdfd'; die();
		foreach ($product_list_data as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}
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
			'model',
			'quantity',
			'price',
			'rating',
			'sort_order',
			'date_added'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'sort_order';
		}	

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order = 0;
		} else {
			$order= 1;
		} //echo count($product_data); die();
		$product_data = $this->mongodb->sapxepthemphantu($product_data,$orderby, $order);
		$product_data = array_slice($product_data, (int)$start,(int)$limit); 
		//$product_data = $this->mongodb->get($collection,$where, $order, $start, $limit);
		return $product_data;
	}

	public function getLatestProducts($limit) {
		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);

		if (!$product_data) {
			//$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);
		$collection='mongo_product';
		$fields=array('product_id'=>1) ;
		$where=array('status'=>1, 'date_available'=> array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))),'product_to_store'=>(int)$this->config->get('config_store_id'));
		$order=array('date_added' => -1);
		$product_query_data = $this->mongodb->getelement($collection,$where, $fields, $order, 0, (int)$limit);

		//foreach ($query->rows as $result) {
		foreach ($product_query_data as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) {
		$product_data = array();

		//$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed DESC, p.date_added DESC LIMIT " . (int)$limit);
		$collection='mongo_product';
		$fields=array('product_id'=>1) ;
		$where=array('status'=>1, 'date_available'=> array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))),'product_to_store'=>(int)$this->config->get('config_store_id'));
		$order=array('viewed'=> -1, 'date_available' => -1);
		$product_query_data = $this->mongodb->getelement($collection,$where, $fields, $order, 0, (int)$limit);

		//foreach ($query->rows as $result) {
		foreach ($product_query_data as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getBestSellerProducts($limit) {
		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
		$product_data = array();
		if (!$product_data) {
			$product_data = array();
			$order_id_array= array();
			$product_id_array= array();
			//			
			$order_list_data= array();
			$collection="mongo_order";
			$where=array('order_status_id'=>array('$gt'=>0));
			$order=array();
			$order_list_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($order_list_data as $order_list_data_info) {
				$order_id_array[]= $order_list_data_info['order_id'];
			}
			//			
			$product_list_data= array();
			$collection="mongo_product";
			$where=array('status'=>1, 'date_available'=>array('$lte'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'product_to_store'=> (int)$this->config->get('config_store_id'));
			$product_list_data=$this->mongodb->getall($collection,$where, $order);
			foreach ($product_list_data as $product_list_data_info) {
				$product_id_array[]= $product_list_data_info['product_id'];
			}
			//
			$collection='mongo_order_product';
			$order_product_query_data= array();
			$keys = array('product_id'=>1);
			$initial = array("sumquantity" => 0);
			$reduce = 'function (obj, prev) {prev.sumquantity = prev.sumquantity + obj.quantity - 0;}';
			$condition = array('condition' => array('product_id' =>array('$in'=>$product_id_array), 'order_id'=>array('$in'=>$order_id_array)));
			$order_product_query_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
			$order_product_query_data=$this->mongodb->sapxepthemphantu($order_product_query_data, 'sumquantity', 0);
			$order_product_query_data = array_slice($order_product_query_data, 0,(int)$limit);
			//
			//$query = $this->db->query("SELECT op.product_id, SUM(op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);
			//foreach ($query->rows as $result) {
			foreach ($order_product_query_data as $result) {
				$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
			}

			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getProductAttributes($product_id) {
		//print_r($this->getPopularProducts(5)); die();
		$product_attribute_group_data = array();
		$product_attribute_group_data_result= array();
		$product_attribute_text_data_result= array();
		$collection='mongo_product_attribute';
		$product_attribute_query_data= array();
		$attribute_id_data = array();
		$keys = array('attribute_id'=>1, 'text'=>1);
		$initial = array("count" => 0);
		$reduce = 'function (obj, prev) {}';
		$condition = array('condition' => array('product_id' =>(int)$product_id, 'language_id'=>(int)$this->config->get('config_language_id')));
		$product_attribute_query_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
		//print_r($product_attribute_query_data); die();
		foreach ($product_attribute_query_data as $product_attribute_query_list_info) {
			$attribute_id_data[]= (int)$product_attribute_query_list_info['attribute_id'];
			$product_attribute_text_data_result[(int)$product_attribute_query_list_info['attribute_id']]=  $product_attribute_query_list_info['text'];
		}
		//----------------------------
		$collection='mongo_attribute';
		$product_attribute_group_query_data= array();
		$attribute_group_id_data = array();
		$keys = array('attribute_group_id'=>1);
		$initial = array("count" => 0);
		$reduce = 'function (obj, prev) {}';
		$condition = array('condition' => array('attribute_id' => array('$in'=>$attribute_id_data)));
		$product_attribute_group_query_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
		foreach ($product_attribute_group_query_data as $product_attribute_group_query_list_info) {
			$attribute_group_id_data[]= (int)$product_attribute_group_query_list_info['attribute_group_id'];
		} 
		//----------------------------
		$collection='mongo_attribute_group';
		$where=array('attribute_group_id' => array('$in'=>$attribute_group_id_data));
		$order=array('sort_order'=>1, 'attribute_group_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
		$attribute_group_data_result = $this->mongodb->getall($collection,$where, $order);
			$collection='mongo_attribute';
			$attribute_data_result = array();
		foreach ($attribute_group_data_result as $attribute_group_data_result_info) {
			$where=array('attribute_group_id' => $attribute_group_data_result_info['attribute_group_id'], 'attribute_id' => array('$in'=>$attribute_id_data));
			$order=array('sort_order'=>1, 'attribute_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
			$attribute_data_result = $this->mongodb->getall($collection,$where, $order);
			$product_attribute_data_result = array();
			foreach ($attribute_data_result as $attribute_data_result_info) {
				$product_attribute_data_result[]= array(
					'attribute_id' => $attribute_data_result_info['attribute_id'],
					'name'         => $attribute_data_result_info['attribute_description'][(int)$this->config->get('config_language_id')]['name'],
					'text'         => $product_attribute_text_data_result[(int)$attribute_data_result_info['attribute_id']]
				);
			}
			$product_attribute_group_data_result[]= array(
				'attribute_group_id' => $attribute_group_data_result_info['attribute_group_id'],
				'name'               => $attribute_group_data_result_info['attribute_group_description'][(int)$this->config->get('config_language_id')]['name'],
				'attribute'          => $product_attribute_data_result
			);
		}
		return $product_attribute_group_data_result;
		//----------------------------
		/*$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");
		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			//$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);
		}

		return $product_attribute_group_data;*/
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();
		$collection='mongo_product_option';
		$product_option_query_data= array();
		//$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");	
		$where=array('product_id'=>(int)$product_id);
		$order=array();
		$product_option_query_data = $this->mongodb->getall($collection,$where, $order);
			
		//foreach ($product_option_query->rows as $product_option) {
		foreach ($product_option_query_data as $product_option) {
			$product_option_value_data = array();
			//$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value WHERE product_option_id = '" . (int)$product_option['product_option_id'] . "'");
			$collection='mongo_product_option_value';
			$product_option_value_query_data= array();	
			$where=array('product_option_id'=>(int)$product_option['product_option_id']);
			$order=array();
			$product_option_value_query_data = $this->mongodb->getall($collection,$where, $order);
		
			//foreach ($product_option_value_query->rows as $product_option_value) {
			foreach ($product_option_value_query_data as $product_option_value) {
				$product_option_value_info=$this->getOptionValue($product_option_value['option_value_id']);
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'image'                   => $product_option_value_info['image'],
					'name'                   => $product_option_value_info['option_value_description'][(int)$this->config->get('config_language_id')]['name'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			} 
			$product_option_description=$this->getOption($product_option['option_id']);
			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option_description['option_description'][$this->config->get('config_language_id')]['name'],
				'type'                 => $product_option_description['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}
		return $product_option_data;
	}
	
	public function getOption($option_id) {
		$collection="mongo_option";
		$where=array('option_id'=>(int)$option_id);
		return $this->mongodb->getBy($collection,$where);
	}
	
	public function getOptionValue($option_value_id) {
		$collection="mongo_option_value";
		$where=array('option_value_id'=>(int)$option_value_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getProductDiscounts($product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");
		//return $query->rows;
		$collection="mongo_product_discount";
		$product_discount_data = array();
		$where=array('product_id'=>(int)$product_id, 'customer_group_id'=>(int)$this->config->get('config_customer_group_id') ,'quantity'=>array('$gt'=>1), 'date_start'=>array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_end'=>array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
		$order = array('quantity'=>1, 'priority'=>1, 'price'=>1);
		$product_discount_data = $this->mongodb->getall($collection,$where,$order);
		return $product_discount_data;
	}
/*
	public function getProductImages($product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}*/

	public function getProductRelated($product_id) {
		$product_data = array();
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
		$collection="mongo_product_related";
		$where=array('product_id'=>(int)$product_id);
		$order = array();
		$product_related_data = $this->mongodb->getall($collection,$where,$order);

		//foreach ($query->rows as $result) {
		foreach ($product_related_data as $result) {
			$product_info=$this->getProduct($result['related_id']);
			if (($product_info['status']==1) && (in_array((int)$this->config->get('config_store_id'),$product_info['product_to_store']))) 
			$product_data[$result['related_id']] = $product_info;
		} 
		return $product_data;
	}

	public function getProductLayoutId($product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");
		$collection="mongo_product_to_layout";
		$where=array('product_id'=>(int)$product_id, 'store_id'=>(int)$this->config->get('config_store_id'));
		$product_to_layout_info=$this->mongodb->getBy($collection,$where);

		//if ($query->num_rows) {
		if ($product_to_layout_info) {
			return $product_to_layout_info['layout_id'];
		} else {
			return 0;
		}
	}

	public function getCategories($product_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		//return $query->rows;
		$category_data= array();
		$product_info=$this->getProduct($product_id);
		$category_list=$product_info['product_category'];
		foreach ($category_list as $category_list_info) {
			$category_data[]= array (
				'category_id'=>$category_list_info
			);
		}
		return $category_data;
	}

	public function getTotalProducts($data = array()) {
		/*
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " FROM " . DB_PREFIX . "category_path cp LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)";
			} else {
				$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id) LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)";
			} else {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";
			}
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "'";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}

			if (!empty($data['filter_filter'])) {
				$implode = array();

				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$implode[] = (int)$filter_id;
				}

				$sql .= " AND pf.filter_id IN (" . implode(',', $implode) . ")";
			}
		}

		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

				foreach ($words as $word) {
					$implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}

				if (!empty($data['filter_description'])) {
					$sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
				}
			}

			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
			}

			if (!empty($data['filter_name'])) {
				$sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
				$sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
		}

		//$query = $this->db->query($sql);

		return $query->row['total'];*/
		$product_data = array();
		$collection="mongo_product";
		$where=array();
		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$where['product_category']=(int)$data['filter_sub_category'];
			} else {
				$where['product_category']=(int)$data['filter_category_id'];
			}
			if (!empty($data['filter_filter'])) {
				$filter_id_array = array();
				$filters = explode(',', $data['filter_filter']);
				foreach ($filters as $filter_id) {
					$filter_id_array[] = (int)$filter_id;
				}
				$where['product_filter']=array('$in'=>$filter_id_array);
			}
		}		
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			if (!empty($data['filter_name'])) {
				$where['$or']['product_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/'.$data['filter_name'].'/');
				$where['$or']['model']=utf8_strtolower($data['filter_name']);
				$where['$or']['sku']=utf8_strtolower($data['filter_name']);
				$where['$or']['upc']=utf8_strtolower($data['filter_name']);
				$where['$or']['ean']=utf8_strtolower($data['filter_name']);
				$where['$or']['jan']=utf8_strtolower($data['filter_name']);
				$where['$or']['isbn']=utf8_strtolower($data['filter_name']);
				$where['$or']['mpn']=utf8_strtolower($data['filter_name']);
				if (!empty($data['filter_description'])) {
					if (!empty($data['filter_tag'])) {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.description'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.tag'=>new MongoRegex('/'.$data['filter_tag'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					} else {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.description'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					}
				} else {
					if (!empty($data['filter_tag'])) {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('product_description.'. (int)$this->config->get('config_language_id').'.tag'=>new MongoRegex('/'.$data['filter_tag'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					} else {
						$where['$or'] = array(
							array('product_description.'. (int)$this->config->get('config_language_id').'.name'=>new MongoRegex('/'.$data['filter_name'].'/')), 
							array('model'=>utf8_strtolower($data['filter_name'])), 
							array('sku'=>utf8_strtolower($data['filter_name'])), 
							array('upc'=>utf8_strtolower($data['filter_name'])), 
							array('ean'=>utf8_strtolower($data['filter_name'])), 
							array('jan'=>utf8_strtolower($data['filter_name'])), 
							array('isbn'=>utf8_strtolower($data['filter_name'])), 
							array('mpn'=>utf8_strtolower($data['filter_name']))
						);
					}
				}
			} else {
				$where['product_description.'. (int)$this->config->get('config_language_id').'.tag']=new MongoRegex('/'.$data['filter_tag'].'/');
			}
		}
		if (!empty($data['filter_manufacturer_id'])) {
			$where['manufacturer_id']=(int)$data['filter_manufacturer_id'];
		}		
		$product_data = $this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getProfiles($product_id) {
		//return $this->db->query("SELECT `pd`.* FROM `" . DB_PREFIX . "product_recurring` `pp` JOIN `" . DB_PREFIX . "recurring_description` `pd` ON `pd`.`language_id` = " . (int)$this->config->get('config_language_id') . " AND `pd`.`recurring_id` = `pp`.`recurring_id` JOIN `" . DB_PREFIX . "recurring` `p` ON `p`.`recurring_id` = `pd`.`recurring_id` WHERE `product_id` = " . (int)$product_id . " AND `status` = 1 AND `customer_group_id` = " . (int)$this->config->get('config_customer_group_id') . " ORDER BY `sort_order` ASC")->rows;		
		$recurring_data = array();
		$collection="mongo_product_recurring";
		$where=array('product_id'=>(int)$product_id);
		$order = array();
		$product_recurring_data = $this->mongodb->getall($collection,$where,$order);
		$i=0;
		foreach ($product_recurring_data as $result) {
			$collection="mongo_recurring";
			$where=array('recurring_id'=>(int)$result['recurring_id']);
			$recurring_data[$i]=$this->mongodb->getBy($collection,$where);
			$recurring_data[$i]['product_id']=$product_id;
			$recurring_data[$i]['customer_group_id']=$result['customer_group_id'];
			$i++;
		}
		return $recurring_data;
	}

	public function getProfile($product_id, $recurring_id) {
		//return $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` `p` JOIN `" . DB_PREFIX . "product_recurring` `pp` ON `pp`.`recurring_id` = `p`.`recurring_id` AND `pp`.`product_id` = " . (int)$product_id . " WHERE `pp`.`recurring_id` = " . (int)$recurring_id . " AND `status` = 1 AND `pp`.`customer_group_id` = " . (int)$this->config->get('config_customer_group_id'))->row;
		$recurring_data = array();
		$collection="mongo_product_recurring";
		$where=array('product_id'=>(int)$product_id, 'recurring_id'=>(int)$recurring_id);
		$product_recurring_data = $this->mongodb->getBy($collection,$where);
		
		$collection="mongo_recurring";
		$where=array('recurring_id'=>(int)$product_recurring_data['recurring_id']);
		$recurring_data=$this->mongodb->getBy($collection,$where);
		$recurring_data['product_id']=$product_id;
		$recurring_data['customer_group_id']=$product_recurring_data['customer_group_id'];
		return $recurring_data;
	}

	public function getTotalProductSpecials() {
		//$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");
		$collection="mongo_product_special";$product_special_data = array();$product_id_array = array();
		$where=array('customer_group_id'=>(int)$this->config->get('config_customer_group_id'), 'date_start'=> array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_end'=> array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
		$order=array();
		$product_special_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($product_special_data as $product_special_info) {
			$product_id_array[] = $product_special_info['product_id'];
		}
		$collection="mongo_product";
		$where=array('product_id'=> array('$in'=>$product_id_array), 'status'=>1, 'date_available'=> array('$lte'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'product_to_store'=>(int)$this->config->get('config_store_id'));
		$product_total=$this->mongodb->gettotal($collection,$where);
		return $download_total;
		/*
		if (isset($query->row['total'])) {
			return $query->row['total'];
		} else {
			return 0;
		}*/
	}

	public function getProductSpecials($data = array()) {
		//$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";
		$product_data = array();
		$collection="mongo_product_special";$product_special_data = array();$product_id_array = array();$product_query_list= array();
		$where=array('customer_group_id'=>(int)$this->config->get('config_customer_group_id'), 'date_start'=> array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_end'=> array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
		$order=array();
		$product_special_data = $this->mongodb->getall($collection,$where, $order);
		foreach ($product_special_data as $product_special_info) {
			$product_id_array[] = $product_special_info['product_id'];
		}
		$collection="mongo_product";
		$where=array('product_id'=> array('$in'=>$product_id_array), 'status'=>1, 'date_available'=> array('$lte'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'product_to_store'=>(int)$this->config->get('config_store_id'));
		$product_query_list=$this->mongodb->getall($collection,$where, $order);
		$sort_data = array(
			'name',
			'model',
			'price',
			'rating',
			'sort_order'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$element = $data['sort'];
		} else {
			$element = "sort_order";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order = 0;
		} else {
			$order = 1;
		}
		$product_query_list=$this->mongodb->sapxepthemphantu($product_query_list, $element, $order);
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
		}
		$product_data_result=array_slice($product_query_list, (int)$data['start'], (int)$data['limit']);
		foreach ($product_data_result as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}
		return $product_data;
		/*
		$sort_data = array(
			'pd.name',
			'p.model',
			'ps.price',
			'rating',
			'p.sort_order'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
			} else {
				$sql .= " ORDER BY " . $data['sort'];
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
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

		$product_data = array();

		//$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;*/
	}
}
