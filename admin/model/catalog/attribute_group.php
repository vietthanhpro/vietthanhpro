<?php
class ModelCatalogAttributeGroup extends Model {
	public function addAttributeGroup($data) {
		$this->event->trigger('pre.admin.attribute_group.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group SET sort_order = '" . (int)$data['sort_order'] . "'");
		//$attribute_group_id = $this->db->getLastId();
		$collection="mongo_attribute_group";
		$attribute_group_id=1+(int)$this->mongodb->getlastid($collection,'attribute_group_id');
		$attribute_group_description= array();

		foreach ($data['attribute_group_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '" . (int)$attribute_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$attribute_group_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$newdocument=array('attribute_group_id'=>(int)$attribute_group_id, 'attribute_group_description'=>$attribute_group_description, 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$newdocument); 

		$this->event->trigger('post.admin.attribute_group.add', $attribute_group_id);

		return $attribute_group_id;
	}

	public function editAttributeGroup($attribute_group_id, $data) {
		$this->event->trigger('pre.admin.attribute_group.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "attribute_group SET sort_order = '" . (int)$data['sort_order'] . "' WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		$collection="mongo_attribute_group";
		$attribute_group_description= array();

		foreach ($data['attribute_group_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '" . (int)$attribute_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$attribute_group_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('attribute_group_id'=>(int)$attribute_group_id, 'attribute_group_description'=>$attribute_group_description, 'sort_order'=>(int)$data['sort_order']);
		$where=array('attribute_group_id'=>(int)$attribute_group_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.admin.attribute_group.edit', $attribute_group_id);
	}

	public function deleteAttributeGroup($attribute_group_id) {
		$this->event->trigger('pre.admin.attribute_group.delete', $attribute_group_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_group WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		$collection="mongo_attribute_group";
		$where=array('attribute_group_id'=>(int)$attribute_group_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.attribute_group.delete', $attribute_group_id);
	}

	public function getAttributeGroup($attribute_group_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		//return $query->row;
		$collection="mongo_attribute_group";
		$where=array('attribute_group_id'=>(int)$attribute_group_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getAttributeGroups($data = array()) {
		$collection="mongo_attribute_group";
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
				$orderby = 'attribute_group_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'attribute_group_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$attribute_group_data = $this->cache->get('attribute_group');
			if (!$attribute_group_data) {
				$where=array();
				$order=array('attribute_group_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
				$attribute_group_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('attribute_group', $attribute_group_data);
			}
			return $attribute_group_data;
		}
		/*
		$sql = "SELECT * FROM " . DB_PREFIX . "attribute_group ag LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE agd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$sort_data = array(
			'name',
			'sort_order'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY agd.name";
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
	}
	/*
	public function getAttributeGroupDescriptions($attribute_group_id) {
		$attribute_group_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_group_description WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");

		foreach ($query->rows as $result) {
			$attribute_group_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $attribute_group_data;
	}*/

	public function getTotalAttributeGroups() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute_group");
		//return $query->row['total'];
		$collection="mongo_attribute_group";
		$where=array();
		$attribute_group_data=$this->mongodb->gettotal($collection,$where);
		return $attribute_group_data;
	}
}