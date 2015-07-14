<?php
class ModelCatalogManufacturer extends Model {
	public function addManufacturer($data) {
		$this->event->trigger('pre.admin.manufacturer.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "'");
		//$manufacturer_id = $this->db->getLastId();
		$collection="mongo_manufacturer";
		$manufacturer_id=1+(int)$this->mongodb->getlastid($collection,'manufacturer_id');
		$manufacturer_store= array();

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
				$manufacturer_to_store[]= (int)$store_id;
			}
		}
		$newdocument=array('manufacturer_id'=>(int)$manufacturer_id, 'name'=>$data['name'], 'manufacturer_to_store'=>$manufacturer_to_store, 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['image'])) {
			//$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
			$infoupdate=array('image'=>$data['image']);
			$where=array('manufacturer_id'=>(int)$manufacturer_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		if (isset($data['keyword'])) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			$collection="mongo_url_alias";
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'manufacturer_id=' . (int)$manufacturer_id, 'keyword'=>$data['keyword']);
			$this->mongodb->create($collection,$newdocument); 
		}

		$this->cache->delete('manufacturer');

		$this->event->trigger('post.admin.manufacturer.add', $manufacturer_id);

		return $manufacturer_id;
	}

	public function editManufacturer($manufacturer_id, $data) {
		$this->event->trigger('pre.admin.manufacturer.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$collection="mongo_manufacturer";
		$manufacturer_store= array();

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
				$manufacturer_to_store[]= (int)$store_id;
			}
		}
		$infoupdate=array('manufacturer_id'=>(int)$manufacturer_id, 'name'=>$data['name'], 'manufacturer_to_store'=>$manufacturer_to_store, 'sort_order'=>(int)$data['sort_order']);
		$where=array('manufacturer_id'=>(int)$manufacturer_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		if (isset($data['image'])) {
			//$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
			$infoupdate=array('image'=>$data['image']);
			$where=array('manufacturer_id'=>(int)$manufacturer_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}
		
		$collection="mongo_url_alias";
		//$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");
		$where=array('query'=>'manufacturer_id='.(int)$manufacturer_id);
		$this->mongodb->delete($collection,$where); 

		if ($data['keyword']) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'manufacturer_id=' . (int)$manufacturer_id, 'keyword'=>$data['keyword']);
			$this->mongodb->create($collection,$newdocument); 
		}

		$this->cache->delete('manufacturer');

		$this->event->trigger('post.admin.manufacturer.edit');
	}

	public function deleteManufacturer($manufacturer_id) {
		$this->event->trigger('pre.admin.manufacturer.delete', $manufacturer_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$collection="mongo_manufacturer";
		$where=array('manufacturer_id'=>(int)$manufacturer_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "'");
		$collection="mongo_url_alias";
		$where=array('query'=>'manufacturer_id='.(int)$manufacturer_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('manufacturer');

		$this->event->trigger('post.admin.manufacturer.delete', $manufacturer_id);
	}

	public function getManufacturer($manufacturer_id) {
		//$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'manufacturer_id=" . (int)$manufacturer_id . "') AS keyword FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		//return $query->row;
		$manufacturer_info = array();
		$collection="mongo_manufacturer";
		$where=array('manufacturer_id'=>(int)$manufacturer_id);
		$manufacturer_info=$this->mongodb->getBy($collection,$where);
		if ($manufacturer_info) {
			$collection="mongo_url_alias";
			$where=array('query'=>'manufacturer_id='.(int)$manufacturer_id);
			$url_alias_info=$this->mongodb->getBy($collection,$where);
			if ($url_alias_info) {$manufacturer_info['keyword']=$url_alias_info['keyword'];}
			else {$manufacturer_info['keyword']='';}
		} 
		return $manufacturer_info;
	}

	public function getManufacturers($data = array()) {
		$collection="mongo_manufacturer";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";
	
			if (!empty($data['filter_name'])) {
				$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			}
	
			$sort_data = array(
				'name',
				'sort_order'
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
			$where=array();
			if (!empty($data['filter_name'])) {
				$where=array('name'=>new MongoRegex('/^'.$data['filter_name'].'/'));
			}
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
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$manufacturer_data = $this->cache->get('manufacturer');
			if (!$manufacturer_data) {
				$where=array();
				$order=array('name'=> 1);
				$manufacturer_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('manufacturer', $manufacturer_data);
			}
			return $manufacturer_data;
		}
	}
/*
	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}*/

	public function getTotalManufacturers() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer");
		//return $query->row['total'];
		$collection="mongo_manufacturer";
		$where=array();
		$manufacturer_data=$this->mongodb->gettotal($collection,$where);
		return $manufacturer_data;
	}
}