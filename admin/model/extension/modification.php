<?php
class ModelExtensionModification extends Model {
	public function addModification($data) {
		//$this->db->query("INSERT INTO " . DB_PREFIX . "modification SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', author = '" . $this->db->escape($data['author']) . "', version = '" . $this->db->escape($data['version']) . "', link = '" . $this->db->escape($data['link']) . "', xml = '" . $this->db->escape($data['xml']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
		$collection="mongo_modification";
		$modification_id=1+(int)$this->mongodb->getlastid($collection,'modification_id');
		$newdocument=array('modification_id'=>(int)$modification_id, 'code'=>$data['code'], 'name'=>$data['name'], 'author'=>$data['author'], 'version'=>$data['version'], 'link'=>$data['link'], 'xml'=>$data['xml'], 'status'=>(int)$data['status'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 
	}

	public function deleteModification($modification_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");
		$collection="mongo_modification";
		$where=array('modification_id'=>(int)$modification_id);
		$this->mongodb->delete($collection,$where); 
	}

	public function enableModification($modification_id) {
		//$this->db->query("UPDATE " . DB_PREFIX . "modification SET status = '1' WHERE modification_id = '" . (int)$modification_id . "'");
		$collection="mongo_modification";
		$infoupdate=array('status'=>1);
		$where=array('modification_id'=>(int)$modification_id);
		$this->mongodb->update($collection,$infoupdate,$where);
	}

	public function disableModification($modification_id) {
		//$this->db->query("UPDATE " . DB_PREFIX . "modification SET status = '0' WHERE modification_id = '" . (int)$modification_id . "'");
		$collection="mongo_modification";
		$infoupdate=array('status'=>0);
		$where=array('modification_id'=>(int)$modification_id);
		$this->mongodb->update($collection,$infoupdate,$where);
	}

	public function getModification($modification_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE modification_id = '" . (int)$modification_id . "'");
		//return $query->row;
		$modification_info = array();
		$collection="mongo_modification";
		$where=array('modification_id'=>(int)$modification_id);
		$modification_info=$this->mongodb->getBy($collection,$where);
		return $modification_info;
	}

	public function getModifications($data = array()) {
		//$sql = "SELECT * FROM " . DB_PREFIX . "modification";
		$collection="mongo_modification";
		$where = array();
		$order = array();
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
			'author',
			'version',
			'status',
			'date_added'
		);
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'name';
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} 
		//$query = $this->db->query($sql);
		//return $query->rows;
		return $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
	}

	public function getTotalModifications() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "modification");
		//return $query->row['total'];
		$collection="mongo_modification";$modification_data= array();
		$where=array();
		$modification_data=$this->mongodb->gettotal($collection,$where);
		return $modification_data;
	}
	
	public function getModificationByCode($code) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "modification WHERE code = '" . $this->db->escape($code) . "'");
		//return $query->row;
		$modification_info = array();
		$collection="mongo_modification";
		$where=array('code'=>$code);
		$modification_info=$this->mongodb->getBy($collection,$where);
		return $modification_info;
	}	
}