<?php
class ModelCatalogRecurring extends Model {
	public function addRecurring($data) {
		$this->event->trigger('pre.admin.recurring.add', $data);

		//$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring` SET `sort_order` = " . (int)$data['sort_order'] . ", `status` = " . (int)$data['status'] . ", `price` = " . (float)$data['price'] . ", `frequency` = '" . $this->db->escape($data['frequency']) . "', `duration` = " . (int)$data['duration'] . ", `cycle` = " . (int)$data['cycle'] . ", `trial_status` = " . (int)$data['trial_status'] . ", `trial_price` = " . (float)$data['trial_price'] . ", `trial_frequency` = '" . $this->db->escape($data['trial_frequency']) . "', `trial_duration` = " . (int)$data['trial_duration'] . ", `trial_cycle` = '" . (int)$data['trial_cycle'] . "'");
		//$recurring_id = $this->db->getLastId();
		$collection="mongo_recurring";
		$recurring_id=1+(int)$this->mongodb->getlastid($collection,'recurring_id');
		$recurring_description= array();

		foreach ($data['recurring_description'] as $language_id => $recurring_description) {
			//$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring_description` (`recurring_id`, `language_id`, `name`) VALUES (" . (int)$recurring_id . ", " . (int)$language_id . ", '" . $this->db->escape($recurring_description['name']) . "')");
			$recurring_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
			);
		}
		$newdocument=array('recurring_id'=>(int)$recurring_id, 'recurring_description'=>$recurring_description, 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status'], 'price'=>(float)$data['price'], 'frequency'=>$data['frequency'], 'duration'=>(int)$data['duration'], 'cycle'=>(int)$data['cycle'], 'trial_status'=>(int)$data['trial_status'], 'trial_price'=>(int)$data['trial_price'], 'trial_frequency'=>$data['trial_frequency'], 'trial_duration'=>(int)$data['trial_duration'], 'trial_cycle'=>(int)$data['trial_cycle']);
		$this->mongodb->create($collection,$newdocument); 

		$this->event->trigger('post.admin.recurring.add', $recurring_id);

		return $recurring_id;
	}

	public function editRecurring($recurring_id, $data) {
		$this->event->trigger('pre.admin.recurring.edit', $data);

		//$this->db->query("DELETE FROM `" . DB_PREFIX . "recurring_description` WHERE recurring_id = '" . (int)$recurring_id . "'");
		//$this->db->query("UPDATE `" . DB_PREFIX . "recurring` SET `price` = '" . (float)$data['price'] . "', `frequency` = '" . $this->db->escape($data['frequency']) . "', `duration` = '" . (int)$data['duration'] . "', `cycle` = '" . (int)$data['cycle'] . "', `sort_order` = '" . (int)$data['sort_order'] . "', `status` = '" . (int)$data['status'] . "', `trial_price` = '" . (float)$data['trial_price'] . "', `trial_frequency` = '" . $this->db->escape($data['trial_frequency']) . "', `trial_duration` = '" . (int)$data['trial_duration'] . "', `trial_cycle` = '" . (int)$data['trial_cycle'] . "', `trial_status` = '" . (int)$data['trial_status'] . "' WHERE recurring_id = '" . (int)$recurring_id . "'");
		$collection="mongo_recurring";
		$recurring_description= array();

		foreach ($data['recurring_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO `" . DB_PREFIX . "recurring_description` (`recurring_id`, `language_id`, `name`) VALUES (" . (int)$recurring_id . ", " . (int)$language_id . ", '" . $this->db->escape($recurring_description['name']) . "')");
			$recurring_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
			);
		}
		$infoupdate=array('recurring_id'=>(int)$recurring_id, 'recurring_description'=>$recurring_description, 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status'], 'price'=>(float)$data['price'], 'frequency'=>$data['frequency'], 'duration'=>(int)$data['duration'], 'cycle'=>(int)$data['cycle'], 'trial_status'=>(int)$data['trial_status'], 'trial_price'=>(int)$data['trial_price'], 'trial_frequency'=>$data['trial_frequency'], 'trial_duration'=>(int)$data['trial_duration'], 'trial_cycle'=>(int)$data['trial_cycle']);
		$where=array('recurring_id'=>(int)$recurring_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.admin.recurring.edit', $recurring_id);
	}

	public function copyRecurring($recurring_id) {
		$data = $this->getRecurring($recurring_id);
		$this->addRecurring($data);
	}

	public function deleteRecurring($recurring_id) {
		$this->event->trigger('pre.admin.recurring.delete', $recurring_id);

		//$this->db->query("DELETE FROM `" . DB_PREFIX . "recurring` WHERE recurring_id = " . (int)$recurring_id . "");
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "recurring_description` WHERE recurring_id = " . (int)$recurring_id . "");
		$collection="mongo_recurring";
		$where=array('recurring_id'=>(int)$recurring_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE recurring_id = " . (int)$recurring_id . "");
		$collection="mongo_product_recurring";
		$where=array('recurring_id'=>(int)$recurring_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("UPDATE `" . DB_PREFIX . "order_recurring` SET `recurring_id` = 0 WHERE `recurring_id` = " . (int)$recurring_id . "");
		$collection="mongo_order_recurring";
		$infoupdate=array('recurring_id'=>0);
		$where=array('recurring_id'=>(int)$recurring_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.admin.recurring.delete', $recurring_id);
	}

	public function getRecurring($recurring_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` WHERE recurring_id = '" . (int)$recurring_id . "'");
		//return $query->row;
		$recurring_info = array();
		$collection="mongo_recurring";
		$where=array('recurring_id'=>(int)$recurring_id);
		$recurring_info=$this->mongodb->getBy($collection,$where);
		return $recurring_info;
	}
	/*
	public function getRecurringDescription($recurring_id) {
		$recurring_description_data = array();
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring_description` WHERE `recurring_id` = '" . (int)$recurring_id . "'");
		foreach ($query->rows as $result) {
			$recurring_description_data[$result['language_id']] = array('name' => $result['name']);
		}
		return $recurring_description_data;
	}*/

	public function getRecurrings($data = array()) {/*
		$sql = "SELECT * FROM `" . DB_PREFIX . "recurring` r LEFT JOIN " . DB_PREFIX . "recurring_description rd ON (r.recurring_id = rd.recurring_id) WHERE rd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_name'])) {
			$sql .= " AND rd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		$sort_data = array(
			'rd.name',
			'r.sort_order'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY rd.name";
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
		$collection="mongo_recurring";
		if ($data) {
			$where=array();
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
				'name',
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "name";
			}
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'recurring_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'recurring_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$recurring_data = $this->cache->get('recurring');

			if (!$recurring_data) {
				$where=array();
				$order=array('recurring_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
				$recurring_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('recurring', $recurring_data);
			}

			return $recurring_data;
		}
	}

	public function getTotalRecurrings() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "recurring`");
		//return $query->row['total'];
		$collection="mongo_recurring";
		$where=array();
		$recurring_data=$this->mongodb->gettotal($collection,$where);
		return $recurring_data;
	}
}