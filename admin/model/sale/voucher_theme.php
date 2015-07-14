<?php
class ModelSaleVoucherTheme extends Model {
	public function addVoucherTheme($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "voucher_theme SET image = '" . $this->db->escape($data['image']) . "'");
		//$voucher_theme_id = $this->db->getLastId();
		$collection="mongo_voucher_theme";
		$voucher_theme_id=1+(int)$this->mongodb->getlastid($collection,'voucher_theme_id');
		$voucher_theme_description= array();

		foreach ($data['voucher_theme_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "voucher_theme_description SET voucher_theme_id = '" . (int)$voucher_theme_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");			
			$voucher_theme_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$where=array('voucher_theme_id'=>(int)$voucher_theme_id, 'voucher_theme_description'=>$voucher_theme_description, 'image'=>$data['image']);
		$this->mongodb->create($collection,$where); 

		$this->cache->delete('voucher_theme');
	}

	public function editVoucherTheme($voucher_theme_id, $data) {
		//$this->db->query("UPDATE " . DB_PREFIX . "voucher_theme SET image = '" . $this->db->escape($data['image']) . "' WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "voucher_theme_description WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");
		$collection="mongo_voucher_theme";
		$voucher_theme_description= array();

		foreach ($data['voucher_theme_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "voucher_theme_description SET voucher_theme_id = '" . (int)$voucher_theme_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");			
			$voucher_theme_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('voucher_theme_id'=>(int)$voucher_theme_id, 'voucher_theme_description'=>$voucher_theme_description, 'image'=>$data['image']);
		$where=array('voucher_theme_id'=>(int)$voucher_theme_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('voucher_theme');
	}

	public function deleteVoucherTheme($voucher_theme_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "voucher_theme WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "voucher_theme_description WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");
		$collection="mongo_voucher_theme";
		$where=array('voucher_theme_id'=>(int)$voucher_theme_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('voucher_theme');
	}

	public function getVoucherTheme($voucher_theme_id) {
		$collection="mongo_voucher_theme";
		$where=array('voucher_theme_id'=>(int)$voucher_theme_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "voucher_theme vt LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE vt.voucher_theme_id = '" . (int)$voucher_theme_id . "' AND vtd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
	}

	public function getVoucherThemes($data = array()) {
		$collection="mongo_voucher_theme";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "voucher_theme vt LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE vtd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY vtd.name";
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
			$orderby = 'voucher_theme_description.'. (int)$this->config->get('config_language_id').'.name';	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$voucher_theme_data = $this->cache->get('voucher_theme.' . (int)$this->config->get('config_language_id'));
			if (!$voucher_theme_data) {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "voucher_theme vt LEFT JOIN " . DB_PREFIX . "voucher_theme_description vtd ON (vt.voucher_theme_id = vtd.voucher_theme_id) WHERE vtd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY vtd.name");
				//$voucher_theme_data = $query->rows;
				$where=array();
				$order=array('voucher_theme_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
				$voucher_theme_data= $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('voucher_theme.' . (int)$this->config->get('config_language_id'), $voucher_theme_data);
			}
			return $voucher_theme_data;
		}
	}
/*
	public function getVoucherThemeDescriptions($voucher_theme_id) {
		$voucher_theme_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "voucher_theme_description WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");

		foreach ($query->rows as $result) {
			$voucher_theme_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $voucher_theme_data;
	}

	public function getTotalVoucherThemes() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher_theme");

		return $query->row['total'];
	}*/
}