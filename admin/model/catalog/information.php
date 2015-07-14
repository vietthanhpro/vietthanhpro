<?php
class ModelCatalogInformation extends Model {
	public function addInformation($data) {
		$this->event->trigger('pre.admin.information.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "information SET sort_order = '" . (int)$data['sort_order'] . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', status = '" . (int)$data['status'] . "'");
		//$information_id = $this->db->getLastId();
		$collection="mongo_information";
		$information_id=1+(int)$this->mongodb->getlastid($collection,'information_id');
		$information_description= array();
		$information_to_store= array();

		foreach ($data['information_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			$information_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}

		if (isset($data['information_store'])) {
			foreach ($data['information_store'] as $store_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_store SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "'");
				$information_to_store[]= (int)$store_id;
			}
		}
		$newdocument=array('information_id'=>(int)$information_id, 'information_description'=>$information_description, 'information_to_store'=>$information_to_store, 'sort_order'=>(int)$data['sort_order'], 'bottom'=>(isset($data['bottom']) ? (int)$data['bottom'] : 0), 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['information_layout'])) {
			foreach ($data['information_layout'] as $store_id => $layout_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_layout SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
				$collection="mongo_information_to_layout";
				$newdocument=array('information_id'=>(int)$information_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['keyword'])) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			$collection="mongo_url_alias";
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'information_id=' . (int)$information_id, 'keyword'=>$data['keyword']);
			$this->mongodb->create($collection,$newdocument); 
		}

		$this->cache->delete('information');

		$this->event->trigger('post.admin.information.add', $information_id);

		return $information_id;
	}

	public function editInformation($information_id, $data) {
		$this->event->trigger('pre.admin.information.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "information SET sort_order = '" . (int)$data['sort_order'] . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', status = '" . (int)$data['status'] . "' WHERE information_id = '" . (int)$information_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");
		$collection="mongo_information";
		$information_description= array();
		$information_to_store= array();

		foreach ($data['information_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
			$information_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'title'=>$value['title'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");
		if (isset($data['information_store'])) {
			foreach ($data['information_store'] as $store_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_store SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "'");
				$information_to_store[]= (int)$store_id;
			}
		}
		$infoupdate=array('information_id'=>(int)$information_id, 'information_description'=>$information_description, 'information_to_store'=>$information_to_store, 'sort_order'=>(int)$data['sort_order'], 'bottom'=>(isset($data['bottom']) ? (int)$data['bottom'] : 0), 'status'=>(int)$data['status']);
		$where=array('information_id'=>(int)$information_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");
		$collection="mongo_information_to_layout";
		$where=array('information_id'=>(int)$information_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['information_layout'])) {
			foreach ($data['information_layout'] as $store_id => $layout_id) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "information_to_layout SET information_id = '" . (int)$information_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
				$newdocument=array('information_id'=>(int)$information_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		//$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'");
		$collection="mongo_url_alias";
		$where=array('query'=>'information_id='.(int)$information_id);
		$this->mongodb->delete($collection,$where); 

		if ($data['keyword']) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'information_id=' . (int)$information_id, 'keyword'=>$data['keyword']);
			$this->mongodb->create($collection,$newdocument); 
		}

		$this->cache->delete('information');

		$this->event->trigger('post.admin.information.edit', $information_id);
	}

	public function deleteInformation($information_id) {
		$this->event->trigger('pre.admin.information.delete', $information_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "information WHERE information_id = '" . (int)$information_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");
		$collection="mongo_information";
		$where=array('information_id'=>(int)$information_id);
		$this->mongodb->delete($collection,$where); 
		
		//$this->db->query("DELETE FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");
		$collection="mongo_information_to_layout";
		$where=array('information_id'=>(int)$information_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "'");
		$collection="mongo_url_alias";
		$where=array('query'=>'information_id='.(int)$information_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('information');

		$this->event->trigger('post.admin.information.delete', $information_id);
	}

	public function getInformation($information_id) {
		//$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'information_id=" . (int)$information_id . "') AS keyword FROM " . DB_PREFIX . "information WHERE information_id = '" . (int)$information_id . "'");
		//return $query->row;
		$information_info = array();
		$collection="mongo_information";
		$where=array('information_id'=>(int)$information_id);
		$information_info=$this->mongodb->getBy($collection,$where);
		if ($information_info) {
			$collection="mongo_url_alias";
			$where=array('query'=>'information_id='.(int)$information_id);
			$url_alias_info=$this->mongodb->getBy($collection,$where);
			if ($url_alias_info) {$information_info['keyword']=$url_alias_info['keyword'];}
			else {$information_info['keyword']='';}
		} 
		return $information_info;
	}

	public function getInformations($data = array()) {
		$collection="mongo_information";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			$sort_data = array(
				'id.title',
				'i.sort_order'
			);
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY id.title";
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
				'title',
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "title";
			}
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'information_description.'. (int)$this->config->get('config_language_id').'.title';
			}
			if ($orderby == 'title') $orderby = 'information_description.'. (int)$this->config->get('config_language_id').'.title';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$information_data = $this->cache->get('information');

			if (!$information_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");
				//$information_data = $query->rows;
				$where=array();
				$order=array('information_description.'. (int)$this->config->get('config_language_id').'.title'=> 1);
				$information_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('information', $information_data);
			}

			return $information_data;
		}
	}
/*
	public function getInformationDescriptions($information_id) {
		$information_description_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword']
			);
		}

		return $information_description_data;
	}

	public function getInformationStores($information_id) {
		$information_store_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_store WHERE information_id = '" . (int)$information_id . "'");

		foreach ($query->rows as $result) {
			$information_store_data[] = $result['store_id'];
		}

		return $information_store_data;
	}*/

	public function getInformationLayouts($information_id) {
		$information_layout_data = array();
		$collection="mongo_information_to_layout";
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "'");
		$where=array('information_id'=>(int)$information_id);
		$order=array();
		$information_data = $this->mongodb->getall($collection,$where, $order);

		//foreach ($query->rows as $result) {
		foreach ($information_data as $result) {
			$information_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $information_layout_data;
	}

	public function getTotalInformations() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information");
		//return $query->row['total'];
		$collection="mongo_information";
		$where=array();
		$information_data=$this->mongodb->gettotal($collection,$where);
		return $information_data;
	}

	public function getTotalInformationsByLayoutId($layout_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
		//return $query->row['total'];
		$collection="mongo_information_to_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$information_data=$this->mongodb->gettotal($collection,$where);
		return $information_data;
	}
}