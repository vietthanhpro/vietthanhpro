<?php
class ModelSettingStore extends Model {
	public function addStore($data) {
		$this->event->trigger('pre.admin.store.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "', `url` = '" . $this->db->escape($data['config_url']) . "', `ssl` = '" . $this->db->escape($data['config_ssl']) . "'");
		//$store_id = $this->db->getLastId();
		
		$collection="mongo_store";
		$store_id=1+(int)$this->mongodb->getlastid($collection,'store_id');
		$newdocument=array('store_id'=>(int)$store_id, 'name'=>$data['config_name'], 'url'=>$data['config_url'], 'ssl'=>$data['config_ssl']);
		$this->mongodb->create($collection,$newdocument); 

		// Layout Route
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE store_id = '0'");
		$layout_route_info = array();
		$collection="mongo_layout_route";
		$where=array('store_id'=>0);
		$order=array();
		$layout_route_info=$this->mongodb->getall($collection,$where,$order);

		//foreach ($query->rows as $layout_route) {
		foreach ($layout_route_info as $layout_route) {
			//$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_route['layout_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "', store_id = '" . (int)$store_id . "'");
			$layout_route_id=1+(int)$this->mongodb->getlastid($collection,'layout_route_id');
			$newdocument=array('layout_route_id'=>(int)$layout_route_id, 'layout_id'=>(int)$layout_id, 'route'=>$layout_route['route'], 'store_id'=>(int)$store_id);
			$this->mongodb->create($collection,$newdocument); 
		}

		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.add', $store_id);

		return $store_id;
	}

	public function editStore($store_id, $data) {
		$this->event->trigger('pre.admin.store.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "store SET name = '" . $this->db->escape($data['config_name']) . "', `url` = '" . $this->db->escape($data['config_url']) . "', `ssl` = '" . $this->db->escape($data['config_ssl']) . "' WHERE store_id = '" . (int)$store_id . "'");
		$collection="mongo_store";
		$infoupdate=array('name'=>$data['config_name'], 'url'=>$data['config_url'], 'ssl'=>$data['config_ssl']);
		$where=array('store_id'=>(int)$store_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.edit', $store_id);
	}

	public function deleteStore($store_id) {
		$this->event->trigger('pre.admin.store.delete', $store_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
		$collection="mongo_store";
		$where=array('store_id'=>(int)$store_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE store_id = '" . (int)$store_id . "'");
		$collection="mongo_layout_route";
		$where=array('store_id'=>(int)$store_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('store');

		$this->event->trigger('post.admin.store.delete', $store_id);
	}

	public function getStore($store_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
		//return $query->row;
		$collection="mongo_store";
		$store_info = array();
		$where=array('store_id'=>(int)$store_id);
		$store_info = $this->mongodb->getBy($collection,$where);
		return $store_info;
	}

	public function getStores($data = array()) {
		$store_data = $this->cache->get('store');

		if (!$store_data) {
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");
			$collection="mongo_store";
			$where=array();
			$order=array('url'=> 1);
			$store_data = $this->mongodb->getall($collection,$where, $order);
			//$store_data = $query->rows;

			$this->cache->set('store', $store_data);
		}

		return $store_data;
	}

	public function getTotalStores() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "store");
		//return $query->row['total'];
		$collection="mongo_store";
		$where=array();
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}

	public function getTotalStoresByLayoutId($layout_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_layout_id' AND `value` = '" . (int)$layout_id . "' AND store_id != '0'");
		//return $query->row['total'];
		$collection="mongo_setting";
		$where=array('key'=>'config_layout_id', 'value'=>(int)$layout_id, 'store_id'=> array('$ne'=>0));
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}

	public function getTotalStoresByLanguage($language) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_language' AND `value` = '" . $this->db->escape($language) . "' AND store_id != '0'");
		//return $query->row['total'];
		$collection="mongo_setting";
		$where=array('key'=>'config_language', 'value'=>$language, 'store_id'=> array('$ne'=>0));
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}

	public function getTotalStoresByCurrency($currency) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_currency' AND `value` = '" . $this->db->escape($currency) . "' AND store_id != '0'");
		//return $query->row['total'];
		$collection="mongo_setting";
		$where=array('key'=>'config_currency', 'value'=>$currency, 'store_id'=> array('$ne'=>0));
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}

	public function getTotalStoresByCountryId($country_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_country_id' AND `value` = '" . (int)$country_id . "' AND store_id != '0'");
		//return $query->row['total'];
		$collection="mongo_setting";
		$where=array('key'=>'config_country_id', 'value'=>(int)$country_id, 'store_id'=> array('$ne'=>0));
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}

	public function getTotalStoresByZoneId($zone_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_zone_id' AND `value` = '" . (int)$zone_id . "' AND store_id != '0'");
		//return $query->row['total'];
		$collection="mongo_setting";
		$where=array('key'=>'config_zone_id', 'value'=>(int)$zone_id, 'store_id'=> array('$ne'=>0));
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}

	public function getTotalStoresByCustomerGroupId($customer_group_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_customer_group_id' AND `value` = '" . (int)$customer_group_id . "' AND store_id != '0'");
		//return $query->row['total'];
		$collection="mongo_setting";
		$where=array('key'=>'config_customer_group_id', 'value'=>(int)$customer_group_id, 'store_id'=> array('$ne'=>0));
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}

	public function getTotalStoresByInformationId($information_id) {
		//$account_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_account_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");
		$collection="mongo_setting";
		$where=array('key'=>'config_account_id', 'value'=>(int)$information_id, 'store_id'=> array('$ne'=>0));
		$account_query_data=$this->mongodb->gettotal($collection,$where);

		//$checkout_query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_checkout_id' AND `value` = '" . (int)$information_id . "' AND store_id != '0'");
		$collection="mongo_setting";
		$where=array('key'=>'config_checkout_id', 'value'=>(int)$information_id, 'store_id'=> array('$ne'=>0));
		$checkout_query_data=$this->mongodb->gettotal($collection,$where);

		//return ($account_query->row['total'] + $checkout_query->row['total']);
		return ($account_query_data['total'] + $checkout_query_data['total']);
	}

	public function getTotalStoresByOrderStatusId($order_status_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "setting WHERE `key` = 'config_order_status_id' AND `value` = '" . (int)$order_status_id . "' AND store_id != '0'");
		//return $query->row['total'];
		$collection="mongo_setting";
		$where=array('key'=>'config_order_status_id', 'value'=>(int)$order_status_id, 'store_id'=> array('$ne'=>0));
		$store_data=$this->mongodb->gettotal($collection,$where);
		return $store_data;
	}
}