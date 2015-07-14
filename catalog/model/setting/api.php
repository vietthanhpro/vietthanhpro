<?php
class ModelSettingApi extends Model {
	public function login($username, $password) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "api WHERE username = '" . $this->db->escape($username) . "' AND password = '" . $this->db->escape($password) . "'");
		//return $query->row;
		$collection="mongo_api";
		$where=array('username'=>$username, 'password'=>$password);
		return $this->mongodb->getBy($collection,$where);
	}
}