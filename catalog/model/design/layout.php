<?php
class ModelDesignLayout extends Model {
	public function getLayout($route) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE '" . $this->db->escape($route) . "' LIKE route AND store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY route DESC LIMIT 1");
		$route_array = explode("/", $route);
		$route_left=(string)$route_array[0].'/%';
		$collection="mongo_layout_route";
		$where=array('store_id'=>(int)$this->config->get('config_store_id'),'$or'=> array(array('route'=> $route),array('route'=> $route_left)));
		$query_result= $this->mongodb->getBy($collection,$where); //print_r($query_result); die();
		return $query_result['layout_id'];
		/*
		if ($query->num_rows) {
			return $query->row['layout_id'];
		} else {
			return 0;
		}*/
	}
	
	public function getLayoutModules($layout_id, $position) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "' AND position = '" . $this->db->escape($position) . "' ORDER BY sort_order");
		//return $query->rows;
		$collection="mongo_layout_module";
		$where=array('layout_id'=>(int)$layout_id, 'position' => $position);
		$order=array('sort_order'=> 1);
		$attribute_group_data = $this->mongodb->getall($collection,$where, $order);
		return $attribute_group_data;
	}
}