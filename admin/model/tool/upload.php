<?php
class ModelToolUpload extends Model {
	public function addUpload($name, $filename) {
		$code = sha1(uniqid(mt_rand(), true));

		//$this->db->query("INSERT INTO `" . DB_PREFIX . "upload` SET `name` = '" . $this->db->escape($name) . "', `filename` = '" . $this->db->escape($filename) . "', `code` = '" . $this->db->escape($code) . "', `date_added` = NOW()");
		$collection="mongo_upload";
		$upload_id=1+(int)$this->mongodb->getlastid($collection,'upload_id');
		$newdocument=array('upload_id'=>(int)$upload_id, 'name'=>$name, 'filename'=>$filename, 'code'=>$code, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		return $code;
	}
		
	public function deleteUpload($upload_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "upload WHERE upload_id = '" . (int)$upload_id . "'");
		$collection="mongo_upload";
		$where=array('upload_id'=>(int)$upload_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function getUpload($upload_id) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "upload` WHERE upload_id = '" . (int)$upload_id . "'");
		//return $query->row;
		$upload_info = array();
		$collection="mongo_upload";
		$where=array('upload_id'=>(int)$upload_id);
		$upload_info=$this->mongodb->getBy($collection,$where);
		return $upload_info;
	}

	public function getUploadByCode($code) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "upload WHERE code = '" . $this->db->escape($code) . "'");
		//return $query->row;
		$upload_info = array();
		$collection="mongo_upload";
		$where=array('code'=>$code);
		$upload_info=$this->mongodb->getBy($collection,$where);
		return $upload_info;
	}

	public function getUploads($data = array()) {
		//$sql = "SELECT * FROM " . DB_PREFIX . "upload";
		$upload_data = array();
		$collection="mongo_upload";
		$where=array();
		//$implode = array();
		if (!empty($data['filter_name'])) {
			//$implode[] = "name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
			$where['name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		if (!empty($data['filter_filename'])) {
			//$implode[] = "filename LIKE '" . $this->db->escape($data['filter_filename']) . "%'";
			$where['filename']=new MongoRegex('/^'.$data['filter_filename'].'/');
		}
		if (!empty($data['filter_date_added'])) {
			//$implode[] = "date_added = '" . $this->db->escape($data['filter_date_added']) . "%'";
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		//if ($implode) {
			//$sql .= " WHERE " . implode(" AND ", $implode);
		//}
		$sort_data = array(
			'name',
			'filename',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			//$sql .= " ORDER BY " . $data['sort'];
			$orderby = $data['sort'];
		} else {
			//$sql .= " ORDER BY date_added";
			$orderby = 'date_added';
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
			$start=$data['start'];			$limit=$data['limit'];
			//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
		//$query = $this->db->query($sql);
		//return $query->rows;
		$upload_data = $this->mongodb->get($collection,$where, $order, $start, $limit);
		return $upload_data;
	}
/*
	public function getTotalUploads($data) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "upload";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_filename'])) {
			$implode[] = "filename LIKE '" . $this->db->escape($data['filter_filename']) . "%'";
		}
		if (!empty($data['filter_date_added'])) {
			$implode[] = "date_added = '" . $this->db->escape($data['filter_date_added']) . "'";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		//$query = $this->db->query($sql);
		return $query->row['total'];
	}*/
}