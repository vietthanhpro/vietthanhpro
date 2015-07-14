<?php
class ModelToolUpload extends Model {
	public function addUpload($name, $filename) {
		$code = sha1(uniqid(mt_rand(), true));

		//$this->db->query("INSERT INTO `" . DB_PREFIX . "upload` SET `name` = '" . $this->db->escape($name) . "', `filename` = '" . $this->db->escape($filename) . "', `code` = '" . $this->db->escape($code) . "', `date_added` = NOW()");
		$collection="mongo_upload";
		$upload_id=1+(int)$this->mongodb->getlastid($collection,'upload_id');
		$where=array('upload_id'=>(int)$upload_id, 'name'=>$name, 'filename'=>$data['filename'], 'code'=>$data['code'], 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 

		return $code;
	}

	public function getUploadByCode($code) {
		//$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "upload` WHERE code = '" . $this->db->escape($code) . "'");
		//return $query->row;
		$collection="mongo_upload";
		$where=array('code'=>(int)$code);
		return $this->mongodb->getBy($collection,$where);
	}
}