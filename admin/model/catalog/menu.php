<?php
class ModelCatalogMenu extends Model {
	public function addMenu($data) {
		$this->event->trigger('pre.admin.menu.add', $data);
		$collection="mongo_menu";
		$menu_id=1+(int)$this->mongodb->getlastid($collection,'menu_id');
		$menu_description= array();
		$menu_to_store= array();

		foreach ($data['menu_description'] as $language_id => $value) {
			$menu_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
			);
		}

		if (isset($data['menu_store'])) {
			foreach ($data['menu_store'] as $store_id) {
				$menu_to_store[]= (int)$store_id;
			}
		}
		$newdocument=array('menu_id'=>(int)$menu_id, 'menu_description'=>$menu_description, 'menu_to_store'=>$menu_to_store, 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$newdocument); 

		$this->cache->delete('menu');

		$this->event->trigger('post.admin.menu.add', $menu_id);

		return $menu_id;
	}

	public function editMenu($menu_id, $data) {
		$this->event->trigger('pre.admin.menu.edit', $data);
		$collection="mongo_menu";
		$menu_description= array();
		$menu_to_store= array();

		foreach ($data['menu_description'] as $language_id => $value) {
			$menu_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
			);
		}
		if (isset($data['menu_store'])) {
			foreach ($data['menu_store'] as $store_id) {
				$menu_to_store[]= (int)$store_id;
			}
		}
		$infoupdate=array('menu_id'=>(int)$menu_id, 'menu_description'=>$menu_description, 'menu_to_store'=>$menu_to_store, 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$where=array('menu_id'=>(int)$menu_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('menu');

		$this->event->trigger('post.admin.menu.edit', $menu_id);
	}

	public function deleteMenu($menu_id) {
		$this->event->trigger('pre.admin.menu.delete', $menu_id);
		$collection="mongo_menu";
		$where=array('menu_id'=>(int)$menu_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('menu');

		$this->event->trigger('post.admin.menu.delete', $menu_id);
	}

	public function getMenu($menu_id) {
		$menu_info = array();
		$collection="mongo_menu";
		$where=array('menu_id'=>(int)$menu_id);
		$menu_info=$this->mongodb->getBy($collection,$where);
		return $menu_info;
	}

	public function getMenus($data = array()) {
		$collection="mongo_menu";
		if ($data) {
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
			$sort_data = array(
				'name',
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "name";
			}
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'menu_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'menu_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$menu_data = $this->cache->get('menu');

			if (!$menu_data) {
				$where=array();
				$order=array('menu_description.'. (int)$this->config->get('config_language_id').'.name'=> 1);
				$menu_data = $this->mongodb->getall($collection,$where, $order);
				$this->cache->set('menu', $menu_data);
			}

			return $menu_data;
		}
	}

	public function getTotalMenus() {
		$collection="mongo_menu";
		$where=array();
		$menu_data=$this->mongodb->gettotal($collection,$where);
		return $menu_data;
	}
}