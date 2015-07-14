<?php
class ModelCatalogAttribute extends Model {
	public function addAttribute($data) {
		$this->event->trigger('pre.admin.attribute.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "', sort_order = '" . (int)$data['sort_order'] . "'");
		//$attribute_id = $this->db->getLastId();
		$collection="mongo_attribute";
		$attribute_id=1+(int)$this->mongodb->getlastid($collection,'attribute_id');
		$option_description= array();

		foreach ($data['attribute_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");	
			$attribute_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$newdocument=array('attribute_id'=>(int)$attribute_id, 'attribute_description'=>$attribute_description, 'sort_order'=>(int)$data['sort_order']);
		$this->mongodb->create($collection,$newdocument); 

		$this->event->trigger('post.admin.attribute.add', $attribute_id);

		return $attribute_id;
	}

	public function editAttribute($attribute_id, $data) {
		$this->event->trigger('pre.admin.attribute.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE attribute_id = '" . (int)$attribute_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");
		$collection="mongo_attribute";
		$attribute_description= array();

		foreach ($data['attribute_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			$attribute_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('attribute_id'=>(int)$attribute_id, 'attribute_description'=>$attribute_description, 'sort_order'=>(int)$data['sort_order']);
		$where=array('attribute_id'=>(int)$attribute_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.admin.attribute.edit', $attribute_id);
	}

	public function deleteAttribute($attribute_id) {
		$this->event->trigger('pre.admin.attribute.delete', $attribute_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");
		$collection="mongo_attribute";
		$where=array('attribute_id'=>(int)$attribute_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.attribute.delete', $attribute_id);
	}

	public function getAttribute($attribute_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE a.attribute_id = '" . (int)$attribute_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
		$collection="mongo_attribute";
		$where=array('attribute_id'=>(int)$attribute_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getAttributes($data = array()) {
		$collection="mongo_attribute";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['attribute_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_attribute_group_id'])) {
			$where['attribute_group_id']=(int)$data['filter_attribute_group_id'];
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
			'attribute_group_id',
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
			$orderby = 'attribute_description.'. (int)$this->config->get('config_language_id').'.name';
		}
		if ($orderby == 'name') $orderby = 'attribute_description.'. (int)$this->config->get('config_language_id').'.name';	

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		}  //print_r($where); die();
		return $this->mongodb->get($collection,$where, $order, $start, $limit);
		/*
		$sql = "SELECT *, (SELECT agd.name FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_attribute_group_id'])) {
			$sql .= " AND a.attribute_group_id = '" . $this->db->escape($data['filter_attribute_group_id']) . "'";
		}
		$sort_data = array(
			'ad.name',
			'attribute_group',
			'a.sort_order'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY attribute_group, ad.name";
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
	public function getAttributeDescriptions($attribute_id) {
		$attribute_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");

		foreach ($query->rows as $result) {
			$attribute_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $attribute_data;
	}*/

	public function getTotalAttributes() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute");
		//return $query->row['total'];
		$collection="mongo_attribute";
		$where=array();
		$attribute_data=$this->mongodb->gettotal($collection,$where);
		return $attribute_data;
	}

	public function getTotalAttributesByAttributeGroupId($attribute_group_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "attribute WHERE attribute_group_id = '" . (int)$attribute_group_id . "'");
		//return $query->row['total'];
		$collection="mongo_attribute";
		$where=array('attribute_group_id'=>(int)$attribute_group_id);
		$attribute_data=$this->mongodb->gettotal($collection,$where);
		return $attribute_data;
	}
}