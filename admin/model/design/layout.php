<?php
class ModelDesignLayout extends Model {
	public function addLayout($data) {
		$this->event->trigger('pre.admin.layout.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "layout SET name = '" . $this->db->escape($data['name']) . "'");
		//$layout_id = $this->db->getLastId();
		$collection="mongo_layout";
		$layout_id=1+(int)$this->mongodb->getlastid($collection,'layout_id');
		$newdocument=array('layout_id'=>(int)$layout_id, 'name'=>$data['name']);
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['layout_route'])) {
			$collection="mongo_layout_route";
			foreach ($data['layout_route'] as $layout_route) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', store_id = '" . (int)$layout_route['store_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "'");
				$layout_route_id=1+(int)$this->mongodb->getlastid($collection,'layout_route_id');
				$newdocument=array('layout_route_id'=>(int)$layout_route_id, 'layout_id'=>(int)$layout_id, 'store_id'=>(int)$layout_route['store_id'], 'route'=>$layout_route['route']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}
		
		if (isset($data['layout_module'])) {
			$collection="mongo_layout_module";
			foreach ($data['layout_module'] as $layout_module) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape($layout_module['code']) . "', position = '" . $this->db->escape($layout_module['position']) . "', sort_order = '" . (int)$layout_module['sort_order'] . "'");
				$layout_module_id=1+(int)$this->mongodb->getlastid($collection,'layout_module_id');
				$newdocument=array('layout_module_id'=>(int)$layout_module_id, 'layout_id'=>(int)$layout_id, 'code'=>$layout_module['code'], 'position'=>$layout_module['position'], 'sort_order'=>(int)$layout_module['sort_order']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}
		
		$this->event->trigger('post.admin.layout.add', $layout_id);

		return $layout_id;
	}

	public function editLayout($layout_id, $data) {
		$this->event->trigger('pre.admin.layout.edit', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "layout SET name = '" . $this->db->escape($data['name']) . "' WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_layout";
		$infoupdate=array('name'=>$data['name']);
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_layout_route";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['layout_route'])) {
			foreach ($data['layout_route'] as $layout_route) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "layout_route SET layout_id = '" . (int)$layout_id . "', store_id = '" . (int)$layout_route['store_id'] . "', route = '" . $this->db->escape($layout_route['route']) . "'");
				$layout_route_id=1+(int)$this->mongodb->getlastid($collection,'layout_route_id');
				$newdocument=array('layout_route_id'=>(int)$layout_route_id, 'layout_id'=>(int)$layout_id, 'store_id'=>(int)$layout_route['store_id'], 'route'=>$layout_route['route']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}
		
		//$this->db->query("DELETE FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_layout_module";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 
		
		if (isset($data['layout_module'])) {
			foreach ($data['layout_module'] as $layout_module) {
				//$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module SET layout_id = '" . (int)$layout_id . "', code = '" . $this->db->escape($layout_module['code']) . "', position = '" . $this->db->escape($layout_module['position']) . "', sort_order = '" . (int)$layout_module['sort_order'] . "'");
				$layout_module_id=1+(int)$this->mongodb->getlastid($collection,'layout_module_id');
				$newdocument=array('layout_module_id'=>(int)$layout_module_id, 'layout_id'=>(int)$layout_id, 'code'=>$layout_module['code'], 'position'=>$layout_module['position'], 'sort_order'=>(int)$layout_module['sort_order']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}
		
		$this->event->trigger('post.admin.layout.edit', $layout_id);
	}

	public function deleteLayout($layout_id) {
		$this->event->trigger('pre.admin.layout.delete', $layout_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "layout WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_layout_route";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_layout_module";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_category_to_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_product_to_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 
		//$this->db->query("DELETE FROM " . DB_PREFIX . "information_to_layout WHERE layout_id = '" . (int)$layout_id . "'");
		$collection="mongo_information_to_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.admin.layout.delete', $layout_id);
	}

	public function getLayout($layout_id) {
		//$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "layout WHERE layout_id = '" . (int)$layout_id . "'");
		//return $query->row;
		$layout_info = array();
		$collection="mongo_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$layout_info=$this->mongodb->getBy($collection,$where);
		return $layout_info;
	}

	public function getLayouts($data = array()) {
		//$sql = "SELECT * FROM " . DB_PREFIX . "layout";
		$collection="mongo_layout";$layout_query_data= array();
		$where=array();
		$order=array();
		$sort_data = array('name');
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			//$sql .= " ORDER BY " . $data['sort'];
			$orderby = $data['sort'];
		} else {
			//$sql .= " ORDER BY name";
			$orderby = 'name';
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
			//$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			$start=(int)$data['start'];
			$limit=(int)$data['limit'];
		} else {$start=0; $limit=0;}
		$layout_query_data = $this->mongodb->getlimit($collection,$where, $order, $start, $limit);
		return $layout_query_data;
		//$query = $this->db->query($sql);
		//return $query->rows;
	}

	public function getLayoutRoutes($layout_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE layout_id = '" . (int)$layout_id . "'");
		//return $query->rows;
		$collection="mongo_layout_route";$layout_route_data=array();
		$where=array('layout_id'=> (int)$layout_id);
		$order=array();
		$layout_route_data = $this->mongodb->getall($collection,$where, $order);
		return $layout_route_data;
	}
	
	public function getLayoutModules($layout_id) {
		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_module WHERE layout_id = '" . (int)$layout_id . "'");
		//return $query->rows;
		$collection="mongo_layout_module";$layout_module_data=array();
		$where=array('layout_id'=> (int)$layout_id);
		$order=array();
		$layout_module_data = $this->mongodb->getall($collection,$where, $order);
		return $layout_module_data;
	}
	
	public function getTotalLayouts() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "layout");
		//return $query->row['total'];
		$layout_data= array();
		$collection="mongo_layout";
		$where=array();
		$layout_data=$this->mongodb->gettotal($collection,$where);
		return $layout_data;
	}
}