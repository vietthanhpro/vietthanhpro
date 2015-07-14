<?php
class ModelCatalogUrlAlias extends Model {
	public function getUrlAlias($keyword) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_alias WHERE keyword = '" . $this->db->escape($keyword) . "'");
		//return $query->row;
		$collection="mongo_url_alias";
		$where=array('keyword'=>$keyword);
		return $this->mongodb->getBy($collection,$where);
	}
}