<?php
class ModelCatalogDownload extends Model {
	public function addDownload($data) {
		$this->event->trigger('pre.admin.download.add', $data);
		//$this->db->query("INSERT INTO " . DB_PREFIX . "download SET filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_added = NOW()");
		//$download_id = $this->db->getLastId();
		$collection="mongo_download";
		$download_id=1+(int)$this->mongodb->getlastid($collection,'download_id');
		$download_description= array();

		foreach ($data['download_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");		
			$download_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$where=array('download_id'=>(int)$download_id, 'download_description'=>$download_description, 'filename'=>$data['filename'], 'mask'=>$data['mask'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 

		$this->event->trigger('post.admin.download.add', $download_id);

		return $download_id;
	}

	public function editDownload($download_id, $data) {
		$this->event->trigger('pre.admin.download.edit', $data);
		//$this->db->query("UPDATE " . DB_PREFIX . "download SET filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "' WHERE download_id = '" . (int)$download_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id = '" . (int)$download_id . "'");
		$collection="mongo_download";
		$download_description= array();

		foreach ($data['download_description'] as $language_id => $value) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET download_id = '" . (int)$download_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");		
			$download_description[$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name']
			);
		}
		$infoupdate=array('download_id'=>(int)$download_id, 'download_description'=>$download_description, 'filename'=>$data['filename'], 'mask'=>$data['mask'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('download_id'=>(int)$download_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->event->trigger('post.admin.download.edit', $download_id);
	}

	public function deleteDownload($download_id) {
		$this->event->trigger('pre.admin.download.delete', $download_id);
		//$this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
		//$this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id = '" . (int)$download_id . "'");
		$collection="mongo_download";
		$where=array('download_id'=>(int)$download_id);
		$this->mongodb->delete($collection,$where); 
		$this->event->trigger('post.admin.download.delete', $download_id);
	}

	public function getDownload($download_id) {
		$collection="mongo_download";
		$where=array('download_id'=>(int)$download_id);
		return $this->mongodb->getBy($collection,$where);
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "download d LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE d.download_id = '" . (int)$download_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		//return $query->row;
	}

	public function getDownloads($data = array()) {
		$collection="mongo_download";
		if ($data) {/*
			$sql = "SELECT * FROM " . DB_PREFIX . "download d LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'";	
			if (!empty($data['filter_name'])) {
				$sql .= " AND dd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			}	
			$sort_data = array(
				'dd.name',
				'd.date_added'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY dd.name";
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
				$where['download_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
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
				'date_added'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'download_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'download_description.'. (int)$this->config->get('config_language_id').'.name';
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
				$where=array();
				$order=array('download_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
				$download_data = $this->mongodb->getall($collection,$where, $order);
				return $download_data;
		}
	}
/*
	public function getDownloadDescriptions($download_id) {
		$download_description_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "download_description WHERE download_id = '" . (int)$download_id . "'");

		foreach ($query->rows as $result) {
			$download_description_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $download_description_data;
	}*/

	public function getTotalDownloads() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "download");
		//return $query->row['total'];
		$collection="mongo_download";
		$where=array();
		$download_total=$this->mongodb->gettotal($collection,$where);
		return $download_total;
	}
}