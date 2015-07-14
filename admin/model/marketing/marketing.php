<?php
class ModelMarketingMarketing extends Model {
	public function addMarketing($data) {
		$this->event->trigger('pre.admin.marketing.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "marketing SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', code = '" . $this->db->escape($data['code']) . "', date_added = NOW()");
		//$marketing_id = $this->db->getLastId();
		
		$collection="mongo_marketing";
		$marketing_id=1+(int)$this->mongodb->getlastid($collection,'marketing_id');
		$newdocument=array('marketing_id'=>(int)$marketing_id, 'name'=>$data['name'], 'description'=>$data['description'], 'code'=>$data['code'], 'clicks'=>0, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		$this->event->trigger('post.admin.marketing.add', $marketing_id);

		return $marketing_id;
	}

	public function editMarketing($marketing_id, $data) {
		$this->event->trigger('pre.admin.marketing.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "marketing SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', code = '" . $this->db->escape($data['code']) . "' WHERE marketing_id = '" . (int)$marketing_id . "'");
		$collection="mongo_marketing";
		$newdocument=array('name'=>$data['name'], 'description'=>$data['description'], 'code'=>$data['code']);
		$where=array('marketing_id'=>(int)$marketing_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.admin.marketing.edit', $marketing_id);
	}

	public function deleteMarketing($marketing_id) {
		$this->event->trigger('pre.admin.marketing.delete', $marketing_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "marketing WHERE marketing_id = '" . (int)$marketing_id . "'");
		$collection="mongo_marketing";
		$where=array('marketing_id'=>(int)$marketing_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.marketing.delete', $marketing_id);
	}

	public function getMarketing($marketing_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "marketing WHERE marketing_id = '" . (int)$marketing_id . "'");
		//return $query->row;
		$collection="mongo_marketing";
		$marketing_info = array();
		$where=array('marketing_id'=>(int)$marketing_id);
		$marketing_info=$this->mongodb->getBy($collection,$where);
		return $marketing_info;
	}

	public function getMarketings($data = array()) {
		//$implode = array();
		$order_status_id_array=array();
		$order_statuses = $this->config->get('config_complete_status');
		foreach ($order_statuses as $order_status_id) {
			//$implode[] = "o.order_status_id = '" . (int)$order_status_id . "'";
			$order_status_id_array[]=(int)$order_status_id;
		}
		$collection="mongo_marketing";$marketing_query_data= array();
		$where=array();
		$order=array();
		//$sql = "SELECT *, (SELECT COUNT(*) FROM `" . DB_PREFIX . "order` o WHERE (" . implode(" OR ", $implode) . ") AND o.marketing_id = m.marketing_id) AS orders FROM " . DB_PREFIX . "marketing m";
		//$implode = array();
		if (!empty($data['filter_name'])) {
			//$implode[] = "m.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			$where['name']=new MongoRegex('/'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_code'])) {
			//$implode[] = "m.code = '" . $this->db->escape($data['filter_code']) . "'";
			$where['code']=$data['filter_code'];
		}
		if (!empty($data['filter_date_added'])) {
			//$implode[] = "DATE(m.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		//if ($implode) {
			//$sql .= " WHERE " . implode(" AND ", $implode);
		//}
		$sort_data = array(
			'name',
			'code',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			//$sql .= " ORDER BY " . $data['sort'];
			$orderby = $data['sort'];
		} else {
			//$sql .= " ORDER BY name";
			$orderby = 'name';
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			//$sql .= " DESC";
			$order[$orderby] = -1;
		} else {
			//$sql .= " ASC";
			$order[$orderby] = 1;
		}
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}
			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}
			//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			$start=(int)$data['start'];
			$limit=(int)$data['limit'];
		}
		$marketing_query_data = $this->mongodb->getlimit($collection,$where, $order, $start, $limit); 
		$marketing_data_list= array();
		//(SELECT COUNT(*) FROM `" . DB_PREFIX . "order` o WHERE (" . implode(" OR ", $implode) . ") AND o.marketing_id = m.marketing_id) AS orders
		$collection="mongo_order"; 
		foreach ($marketing_query_data as $marketing_query_data_info) {
				$where=array('marketing_id'=>(int)$marketing_query_data_info['marketing_id'],'order_status_id'=>array('$in'=>$order_status_id_array));
				$order_total=$this->mongodb->gettotal($collection,$where); //print_r($where); die();
			$marketing_data_list[]= array(
				'marketing_id' => $marketing_query_data_info['marketing_id'],
				'name'         => $marketing_query_data_info['name'],
				'code'         => $marketing_query_data_info['code'],
				'clicks'       => $marketing_query_data_info['clicks'],
				//'orders'       => $marketing_query_data_info['orders'],
				'orders'   => $order_total,
				'date_added'   => $marketing_query_data_info['date_added'],
			);
		}
		return $marketing_data_list;
		//$query = $this->db->query($sql);
		//return $query->rows;
	}

	public function getTotalMarketings($data = array()) {
		//$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "marketing";
		//$implode = array();	
		$collection="mongo_marketing";	
		$where=array();
		if (!empty($data['filter_name'])) {
			//$implode[] = "name LIKE '" . $this->db->escape($data['filter_name']) . "'";
			$where['name']=new MongoRegex('/'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_code'])) {
			//$implode[] = "code = '" . $this->db->escape($data['filter_code']) . "'";
			$where['code']=$data['filter_code'];
		}
		if (!empty($data['filter_date_added'])) {
			//$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		//if ($implode) {
			//$sql .= " WHERE " . implode(" AND ", $implode);
		//}
		$marketing_total=$this->mongodb->gettotal($collection,$where);
		return $marketing_total;
		//$query = $this->db->query($sql);
		//return $query->row['total'];
	}
}
