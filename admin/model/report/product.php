<?php
class ModelReportProduct extends Model {
	public function getProductsViewed($data = array()) {
		$product_data = array();
		$collection="mongo_product";
		$where=array('viewed' => array('$gt'=>0));
		$order=array('viewed'=>-1);
		//$sql = "SELECT pd.name, p.model, p.viewed FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.viewed > 0 ORDER BY p.viewed DESC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		$product_data = $this->mongodb->getlimit($collection,$where, $order, (int)$data['start'], (int)$data['limit']);
		return $product_data;
		//$query = $this->db->query($sql);
		//return $query->rows;
	}
	
    public function getTotalProductViews() {
		//$query = $this->db->query("SELECT SUM(viewed) AS total FROM " . DB_PREFIX . "product");	
		//return $query->row['total'];
		$collection="mongo_product";
		$product_query_data= array();
		$keys = array();
		$initial = array("sumviewed" => 0);
		$reduce = 'function (obj, prev) {prev.sumviewed = prev.sumviewed + obj.viewed - 0;}';
		$condition = array('condition' => array());
		$product_query_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
		if (isset($product_query_data[0]['sumviewed'])) {return $product_query_data[0]['sumviewed'];}
		else {return 0;}
	}

	public function getTotalProductsViewed() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE viewed > 0");
		//return $query->row['total'];
		$collection="mongo_product";
		$where=array('viewed'=>array('$gt'=>0));
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function reset() {
		//$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = '0'"); 
		$collection="mongo_product";
		$infoupdate=array('viewed'=>0);
		$where=array();
		$this->mongodb->update($collection,$infoupdate,$where, 1);
	}

	public function getPurchased($data = array()) {
		$order_id_array = array();		
		$collection="mongo_order";
		$where=array();
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
			$order_id_array[] = $order_query_data_info['order_id'];		
		} 
		$order_product_list= array();
		$product_id_array=array();
		$quantity_product_array=array();
		$total_product_array=array();	
		$collection="mongo_order_product";
		$where=array('order_id'=>array('$in'=>$order_id_array));
		$order=array();
		$order_product_list = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_product_list as $order_product_list_info) {
			$total_tt=((float)$order_product_list_info['total'] + (float)$order_product_list_info['tax']) * (float)$order_product_list_info['quantity'];
			$quantity_tt=(float)$order_product_list_info['quantity'];
			$product_id_array[]=(int)$order_product_list_info['product_id'];
			if (isset($quantity_product_array[(int)$order_product_list_info['product_id']])) 
				$quantity_product_array[(int)$order_product_list_info['product_id']]+=$quantity_tt;
			else 
				$quantity_product_array[(int)$order_product_list_info['product_id']]=$quantity_tt;
			if (isset($total_product_array[(int)$order_product_list_info['product_id']])) 
				$total_product_array[(int)$order_product_list_info['product_id']]+=$total_tt;	
			else 
				$total_product_array[(int)$order_product_list_info['product_id']]=$total_tt;	
			
		}
		$product_id_array=array_unique($product_id_array);
		$data_result= array();
		$this->load->model('catalog/product');
		foreach ($product_id_array as $product_id_array_id) {
			$product_info= array();
			$product_info=$this->model_catalog_product->getProduct((int)$product_id_array_id);
			$data_result[]= array(
				'product_id'=>(int)$product_id_array_id,
				'name'=>$product_info['product_description'][(int)$this->config->get('config_language_id')]['name'],
				'model'=>$product_info['model'],
				'quantity'=>$quantity_product_array[(int)$product_id_array_id],
				'total'=>$total_product_array[(int)$product_id_array_id],
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
		$sql = "SELECT op.name, op.model, SUM(op.quantity) AS quantity, SUM((op.total + op.tax) * op.quantity) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";
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
		$sql .= " GROUP BY op.product_id ORDER BY total DESC";
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}*/
		//$query = $this->db->query($sql);
		//return $query->rows;
	}

	public function getTotalPurchased($data) {
		/*
		$sql = "SELECT COUNT(DISTINCT op.product_id) AS total FROM `" . DB_PREFIX . "order_product` op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id)";

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

		//$query = $this->db->query($sql);

		return $query->row['total'];*/
		
		$order_id_array = array();		
		$collection="mongo_order";
		$where=array();
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
			$order_id_array[] = $order_query_data_info['order_id'];		
		} 
		$order_product_list= array();
		$product_id_array=array();
		$quantity_product_array=array();
		$total_product_array=array();	
		$collection="mongo_order_product";
		$where=array('order_id'=>array('$in'=>$order_id_array));
		$order=array();
		$order_product_list = $this->mongodb->getall($collection,$where, $order);
		foreach ($order_product_list as $order_product_list_info) {
			$product_id_array[]=(int)$order_product_list_info['product_id'];	
		}
		$product_id_array=array_unique($product_id_array);
		return count($product_id_array);
	}
}