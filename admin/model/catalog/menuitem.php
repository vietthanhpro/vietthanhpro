<?php
class ModelCatalogMenuitem extends Model {
	public function addMenuitem($data) { 
		$this->event->trigger('pre.admin.menuitem.add', $data);
		$collection="mongo_menuitem";
		$menuitem_description= array();
		$menuitem_to_store= array();

		foreach ($data['menuitem_description'] as $language_id => $value) {
			$menuitem_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'html'=>$value['html'],
			);
		}

		if (isset($data['menuitem_store'])) {
			foreach ($data['menuitem_store'] as $store_id) {
				$menuitem_to_store[]= (int)$store_id;
			}
		}
		switch ($data['type']) {
			case 'category': // product category
				$iteminfo = $data['menuitem_id'];$itemtype=1;
				break;
			case 'product': // product
				$iteminfo = $data['product_id'];$itemtype=2;
				break;
			case 'danhmucbaiviet': // article category
				$iteminfo = $data['danhmucbaiviet_id'];$itemtype=3;
				break;
			case 'baiviet': // article
				$iteminfo = $data['baiviet_id'];$itemtype=4;
				break;
			case 'information': //information
				$iteminfo = $data['information_id'];$itemtype=5;
				break;
			case 'urlcommon': // url common
				$iteminfo = $data['urlcommon'];$itemtype=6;
				break;
			case 'url': // url 
				$iteminfo = $data['urllink'];$itemtype=7;
				break;
			case 'manufacturer': //manufacturer
				$iteminfo = $data['manufacturer_id'];$itemtype=8;
				break;
			case 'html': //html
			default:
				$iteminfo = '';$itemtype=9;
				break;
		}
		$menuitem_id=1+(int)$this->mongodb->getlastid($collection,'menuitem_id');
		$newdocument=array('menuitem_id'=>(int)$menuitem_id, 'menuitem_description'=>$menuitem_description, 'menuitem_to_store'=>$menuitem_to_store, 'menu_id'=>(int)$data['menu_id'], 'parent_id'=>(int)$data['parent_id'], 'id_group'=>(int)$data['id_group'], 'showtitle'=>(int)$data['showtitle'], 'showdescription'=>(int)$data['showdescription'], 'menuclass'=>$data['menuclass'], 'type'=>$itemtype, 'iteminfo'=>$iteminfo, 'itemwidth'=>$data['itemwidth'], 'columns'=>(int)$data['columns'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$this->mongodb->create($collection,$newdocument); 

		$this->cache->delete('menuitem');

		$this->event->trigger('post.admin.menuitem.add', $menuitem_id);

		return $menuitem_id;
	}

	public function editMenuitem($menuitem_id, $data) {
		$this->event->trigger('pre.admin.menuitem.edit', $data);
		$collection="mongo_menuitem";
		$menuitem_description= array();
		$menuitem_to_store= array();

		foreach ($data['menuitem_description'] as $language_id => $value) {
			$menuitem_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'html'=>$value['html'],
			);
		}
		if (isset($data['menuitem_store'])) {
			foreach ($data['menuitem_store'] as $store_id) {
				$menuitem_to_store[]= (int)$store_id;
			}
		}
		switch ($data['type']) {
			case 'category': // product category
				$iteminfo = $data['menuitem_id'];$itemtype=1;
				break;
			case 'product': // product
				$iteminfo = $data['product_id'];$itemtype=2;
				break;
			case 'danhmucbaiviet': // article category
				$iteminfo = $data['danhmucbaiviet_id'];$itemtype=3;
				break;
			case 'baiviet': // article
				$iteminfo = $data['baiviet_id'];$itemtype=4;
				break;
			case 'information': //information
				$iteminfo = $data['information_id'];$itemtype=5;
				break;
			case 'urlcommon': // url common
				$iteminfo = $data['urlcommon'];$itemtype=6;
				break;
			case 'url': // url 
				$iteminfo = $data['urllink'];$itemtype=7;
				break;
			case 'manufacturer': //manufacturer
				$iteminfo = $data['manufacturer_id'];$itemtype=8;
				break;
			case 'html': //html
			default:
				$iteminfo = '';$itemtype=9;
				break;
		}
		$infoupdate=array('menuitem_description'=>$menuitem_description, 'menuitem_to_store'=>$menuitem_to_store, 'menu_id'=>(int)$data['menu_id'], 'parent_id'=>(int)$data['parent_id'], 'id_group'=>(int)$data['id_group'], 'showtitle'=>(int)$data['showtitle'], 'showdescription'=>(int)$data['showdescription'], 'menuclass'=>$data['menuclass'], 'type'=>$itemtype, 'iteminfo'=>$iteminfo, 'itemwidth'=>$data['itemwidth'], 'columns'=>(int)$data['columns'], 'sort_order'=>(int)$data['sort_order'], 'status'=>(int)$data['status']);
		$where=array('menuitem_id'=>(int)$menuitem_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		$this->cache->delete('menuitem');

		$this->event->trigger('post.admin.menuitem.edit', $menuitem_id);
	}

	public function deleteMenuitem($menuitem_id) {
		$this->event->trigger('pre.admin.menuitem.delete', $menuitem_id);
		$collection="mongo_menuitem";
		$where=array('menuitem_id'=>(int)$menuitem_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('menuitem');

		$this->event->trigger('post.admin.menuitem.delete', $menuitem_id);
	}

	public function getMenuitem($menuitem_id) {
		$menuitem_info = array();
		$collection="mongo_menuitem";
		$where=array('menuitem_id'=>(int)$menuitem_id);
		$menuitem_info=$this->mongodb->getBy($collection,$where);
		return $menuitem_info;
	}

	public function getMenuitems($data = array()) {
		if (isset($data['filter_menu_id']) && !is_null($data['filter_menu_id'])) {	
			$collection="mongo_menuitem";
			$where=array();
			if (!empty($data['filter_name'])) {
				$where['menuitem_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
			}		
			$where['menu_id']=(int)$data['filter_menu_id'];
			if (isset($data['filter_store_id']) && !is_null($data['filter_store_id'])) {			
				$where['menuitem_to_store']=(int)$data['filter_store_id'];
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
				'sort_order'
			);	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "sort_order";
			}
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = 'menuitem_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'menuitem_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			return array();
		}
	}

	public function getAllMenuitems($data = array()) {
		if (isset($data['filter_menu_id']) && !is_null($data['filter_menu_id'])) {	
			$collection="mongo_menuitem";
			$menuitem_data = array();
			$where=array();
			$where['menu_id']=(int)$data['filter_menu_id'];
			if (!empty($data['filter_parent_id'])) {
				$where['parent_id']=(int)$data['filter_parent_id']; 
			} else {
				$where['parent_id']=0;
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
				$orderby = 'menuitem_description.'. (int)$this->config->get('config_language_id').'.name';
			}
			if ($orderby == 'name') $orderby = 'menuitem_description.'. (int)$this->config->get('config_language_id').'.name';	
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			} 
			$menuitem_list = $this->mongodb->getall($collection,$where, $order);
			foreach ($menuitem_list as $result) {
				$menuitem_data[] = array(
					'menuitem_id' => $result['menuitem_id'],
					'path'        => $this->getPath($result['menuitem_id']),
					'name'        => $result['menuitem_description'][$this->config->get('config_language_id')]['name'],
					'columns'  	  => $result['columns'],
					'status'  	  => $result['status'],
					'sort_order'  => $result['sort_order']
				); 
				$filter_data = array(
					'filter_menu_id' => $data['filter_menu_id'],
					'filter_parent_id' => $result['menuitem_id'],
					'sort'  => $data['sort'],
					'order' => $data['order']
				);		
				$menuitem_data_child=$this->getAllMenuitems($filter_data);
				if ($menuitem_data_child) {
					$menuitem_data = array_merge($menuitem_data, $menuitem_data_child); 
				} 
			}	
			return $menuitem_data;
		} else {
			return array();
		}
	}

	public function getTotalMenuitems($data = array()) {
		if (isset($data['filter_menu_id']) && !is_null($data['filter_menu_id'])) {	
			$collection="mongo_menuitem";
			$where=array();
			if (!empty($data['filter_name'])) {
				$where['menuitem_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
			}		
			$where['menu_id']=(int)$data['filter_menu_id'];
			if (isset($data['filter_store_id']) && !is_null($data['filter_store_id'])) {			
				$where['menuitem_to_store']=(int)$data['filter_store_id'];
			}
			$menuitem_data=$this->mongodb->gettotal($collection,$where);
			return $menuitem_data;
		} else { return 0;
		}
	}
		
	public function getPath($menuitem_id) {
		$category_info=$this->getMenuitem($menuitem_id);
		if ($category_info['parent_id']) {
				return $this->getPath($category_info['parent_id']) .'&nbsp;&nbsp;&gt;&nbsp;&nbsp;'. $category_info['menuitem_description'][$this->config->get('config_language_id')]['name'];
		} else {
				return $category_info['menuitem_description'][$this->config->get('config_language_id')]['name'];
		}
	}
}