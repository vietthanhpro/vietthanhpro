<?php
class ControllerCatalogMenuitem extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menuitem');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menuitem');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_menuitem->addMenuitem($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['menu_id'])) {
				$url .= '&menu_id=' . (int)$this->request->get['menu_id'];
			} else {
				$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menuitem');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_menuitem->editMenuitem($this->request->get['menuitem_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['menu_id'])) {
				$url .= '&menu_id=' . (int)$this->request->get['menu_id'];
			} else {
				$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/menuitem');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/menuitem');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $menuitem_id) {
				$this->model_catalog_menuitem->deleteMenuitem($menuitem_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['menu_id'])) {
				$url .= '&menu_id=' . (int)$this->request->get['menu_id'];
			} else {
				$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['menu_id'])) {
			$menu_id = (int)$this->request->get['menu_id'];
			if ($menu_id==0) {
				$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
			}
		} else {
			$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['menu_id'])) {
			$url .= '&menu_id=' . (int)$this->request->get['menu_id'];
		} else {
			$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		$data['add'] = $this->url->link('catalog/menuitem/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/menuitem/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['menuitems'] = array();

		$filter_data = array(
			'filter_menu_id'  => $menu_id,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		/*
		$data_results = $this->model_catalog_menuitem->getMenuitems($filter_data);
		if ($data_results) {
			$results=$data_results['results'];
			$menuitem_total = $data_results['count'];
		} else {
			$results= array();
			$menuitem_total =0;
		}
		*/
		$start=($page - 1) * $this->config->get('config_limit_admin');
		$limit=$this->config->get('config_limit_admin');
		$menuitem_total = $this->model_catalog_menuitem->getTotalMenuitems($filter_data);
		$results = $this->model_catalog_menuitem->getAllMenuitems($filter_data);
		$results=array_slice($results,($page - 1) * $this->config->get('config_limit_admin'),$this->config->get('config_limit_admin'));
		//
		foreach ($results as $result) {
			$data['menuitems'][] = array(
				'menuitem_id' => $result['menuitem_id'],
				'name'          => $result['path'],
				//'name'          => $result['name'],
				'columns'     => $result['columns'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('catalog/menuitem/edit', 'token=' . $this->session->data['token'] . '&menuitem_id=' . $result['menuitem_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_columns'] = $this->language->get('column_columns');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['menu_id'])) {
			$url .= '&menu_id=' . (int)$this->request->get['menu_id'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['menu_id'])) {
			$url .= '&menu_id=' . (int)$this->request->get['menu_id'];
		} else {
			$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $menuitem_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($menuitem_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($menuitem_total - $this->config->get('config_limit_admin'))) ? $menuitem_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $menuitem_total, ceil($menuitem_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/menuitem_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['menuitem_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_html'] = $this->language->get('entry_html');
		$data['entry_parent'] = $this->language->get('entry_parent');
		$data['entry_group'] = $this->language->get('entry_group');
		$data['entry_showtitle'] = $this->language->get('entry_showtitle');
		$data['entry_showdescription'] = $this->language->get('entry_showdescription');
		$data['entry_menuclass'] = $this->language->get('entry_menuclass');
		$data['entry_type'] = $this->language->get('entry_type');
		$data['entry_itemwidth'] = $this->language->get('entry_itemwidth');
		$data['entry_columns'] = $this->language->get('entry_columns');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_danhmucbaiviet'] = $this->language->get('entry_danhmucbaiviet');
		$data['entry_baiviet'] = $this->language->get('entry_baiviet');
		$data['entry_urlcommon'] = $this->language->get('entry_urlcommon');
		$data['entry_url'] = $this->language->get('entry_url');
		$data['entry_information'] = $this->language->get('entry_information');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}
		
		$url = '';
		$menu_id=0;
		
		if (isset($this->request->get['menu_id'])) {
			$url .= '&menu_id=' . (int)$this->request->get['menu_id'];
			$menu_id= $this->request->get['menu_id'];
		} else {
			$this->response->redirect($this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		if (!isset($this->request->get['menuitem_id'])) {
			$data['action'] = $this->url->link('catalog/menuitem/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/menuitem/edit', 'token=' . $this->session->data['token'] . '&menuitem_id=' . $this->request->get['menuitem_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/menuitem', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['menuitem_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$menuitem_info = $this->model_catalog_menuitem->getMenuitem($this->request->get['menuitem_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['menuitem_description'])) {
			$data['menuitem_description'] = $this->request->post['menuitem_description'];
		} elseif (!empty($menuitem_info)) {
			$data['menuitem_description'] = $menuitem_info['menuitem_description']; 
		} else {
			$data['menuitem_description'] = array();
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['menuitem_store'])) {
			$data['menuitem_store'] = $this->request->post['menuitem_store'];
		} elseif (!empty($menuitem_info)) {
			$data['menuitem_store'] = $menuitem_info['menuitem_to_store']; 
		} else {
			$data['menuitem_store'] = array(0);
		}

		if (isset($this->request->post['menu_id'])) {
			$data['menu_id'] = $this->request->post['menu_id'];
		} elseif (!empty($menuitem_info)) {
			$data['menu_id'] = $menuitem_info['menu_id'];
		} else {
			$data['menu_id'] = $menu_id;
		}

		if (isset($this->request->post['parent_id'])) {
			$data['parent_id'] = $this->request->post['parent_id'];
		} elseif (!empty($menuitem_info)) {
			$data['parent_id'] = $menuitem_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		if (isset($this->request->post['id_group'])) {
			$data['id_group'] = $this->request->post['id_group'];
		} elseif (!empty($menuitem_info)) {
			$data['id_group'] = $menuitem_info['id_group'];
		} else {
			$data['id_group'] = false;
		}

		if (isset($this->request->post['showtitle'])) {
			$data['showtitle'] = $this->request->post['showtitle'];
		} elseif (!empty($menuitem_info)) {
			$data['showtitle'] = $menuitem_info['showtitle'];
		} else {
			$data['showtitle'] = true;
		}

		if (isset($this->request->post['showdescription'])) {
			$data['showdescription'] = $this->request->post['showdescription'];
		} elseif (!empty($menuitem_info)) {
			$data['showdescription'] = $menuitem_info['showdescription'];
		} else {
			$data['showdescription'] = false;
		}

		if (isset($this->request->post['columns'])) {
			$data['columns'] = $this->request->post['columns'];
		} elseif (!empty($menuitem_info)) {
			$data['columns'] = $menuitem_info['columns'];
		} else {
			$data['columns'] = 1;
		}

		if (isset($this->request->post['menuclass'])) {
			$data['menuclass'] = $this->request->post['menuclass'];
		} elseif (!empty($menuitem_info)) {
			$data['menuclass'] = $menuitem_info['menuclass'];
		} else {
			$data['menuclass'] = '';
		}

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (!empty($menuitem_info)) {
			$data['type'] = $menuitem_info['type'];
		} else {
			$data['type'] = 0;
		}

		if (isset($this->request->post['iteminfo'])) {
			$data['iteminfo'] = $this->request->post['iteminfo'];
		} elseif (!empty($menuitem_info)) {
			$data['iteminfo'] = $menuitem_info['iteminfo'];
		} else {
			$data['iteminfo'] = '';
		}
		$data['iteminfo_detail'] = '';
		switch ($data['type']) {
			case 1: // product category
				$this->load->model('catalog/category');
				$category_info = $this->model_catalog_category->getCategory((int)$data['iteminfo']);
				$data['iteminfo_detail'] = $category_info['category_description'][(int)$this->config->get('config_language_id')]['name'];
				break;
			case 2: // product
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct((int)$data['iteminfo']);
				$data['iteminfo_detail'] = $product_info['product_description'][(int)$this->config->get('config_language_id')]['name'];
				break;
			case 3: // article category
				$this->load->model('catalog/danhmucbaiviet');
				$danhmucbaiviet_info = $this->model_catalog_danhmucbaiviet->getDanhmucbaiviet((int)$data['iteminfo']);
				$data['iteminfo_detail'] = $danhmucbaiviet_info['danhmucbaiviet_description'][(int)$this->config->get('config_language_id')]['name'];
				break;
			case 4: // article
				$this->load->model('catalog/baiviet');
				$baiviet_info = $this->model_catalog_baiviet->getBaiviet((int)$data['iteminfo']);
				$data['iteminfo_detail'] = $baiviet_info['baiviet_description'][(int)$this->config->get('config_language_id')]['name'];
				break;
			case 5: //information
				$this->load->model('catalog/information');
				$information_info = $this->model_catalog_information->getInformation((int)$data['iteminfo']);
				$data['iteminfo_detail'] = $information_info['information_description'][(int)$this->config->get('config_language_id')]['title'];
				break;
			case 6: // url common
				break;
			case 7: // url 
				break;
			case 8: //manufacturer
				$this->load->model('catalog/manufacturer');
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer((int)$data['iteminfo']);
				$data['iteminfo_detail'] = $manufacturer_info['name'];
				break;
			case 9: //html
				break;
			default:
				//echo "Your favorite color is neither red, blue, nor green!";
		}

		if (isset($this->request->post['itemwidth'])) {
			$data['itemwidth'] = $this->request->post['itemwidth'];
		} elseif (!empty($menuitem_info)) {
			$data['itemwidth'] = $menuitem_info['itemwidth'];
		} else {
			$data['itemwidth'] = 12;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($menuitem_info)) {
			$data['status'] = $menuitem_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($menuitem_info)) {
			$data['sort_order'] = $menuitem_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}
		$this->load->model('localisation/lienketlink_class');
		$data['lienketlinks'] = $this->model_localisation_lienketlink_class->getLienketlinkClasses();
		$data['menuitems'] = $this->model_catalog_menuitem->getMenuitems();
		$data['itemwidths'] = array();
		$data['itemwidths'][] = array(
			'itemwidth_id' => 12,
			'name' => '100%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 11,
			'name' => '91.66666667%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 10,
			'name' => '83.33333333%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 9,
			'name' => '75%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 8,
			'name' => '66.66666667%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 7,
			'name' => '58.33333333%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 6,
			'name' => '50%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 5,
			'name' => '41.66666667%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 4,
			'name' => '33.33333333%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 3,
			'name' => '25%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 2,
			'name' => '16.66666667%',
		);
		$data['itemwidths'][] = array(
			'itemwidth_id' => 1,
			'name' => '8.33333333%',
		);
		$data['menutypes'] = array();
		$data['menutypes'][] = array(
			'type_id' => 9,
			'typename' => 'html',
			'name' => $this->language->get('entry_html'),
		);
		$data['menutypes'][] = array(
			'type_id' => 1,
			'typename' => 'category',
			'name' => $this->language->get('entry_category'),
		);
		$data['menutypes'][] = array(
			'type_id' => 2,
			'typename' => 'product',
			'name' => $this->language->get('entry_product'),
		);
		$data['menutypes'][] = array(
			'type_id' => 3,
			'typename' => 'danhmucbaiviet',
			'name' => $this->language->get('entry_danhmucbaiviet'),
		);
		$data['menutypes'][] = array(
			'type_id' => 4,
			'typename' => 'baiviet',
			'name' => $this->language->get('entry_baiviet'),
		);
		$data['menutypes'][] = array(
			'type_id' => 5,
			'typename' => 'information',
			'name' => $this->language->get('entry_information'),
		);
		$data['menutypes'][] = array(
			'type_id' => 6,
			'typename' => 'urlcommon',
			'name' => $this->language->get('entry_urlcommon'),
		);
		$data['menutypes'][] = array(
			'type_id' => 7,
			'typename' => 'url',
			'name' => $this->language->get('entry_url'),
		);
		$data['menutypes'][] = array(
			'type_id' => 8,
			'typename' => 'manufacturer',
			'name' => $this->language->get('entry_manufacturer'),
		);
		
		$this->load->model('catalog/information');
		$data['informations'] = $this->model_catalog_information->getInformations();
		$data['config_language_id'] = (int)$this->config->get('config_language_id');
		
		$filter_data = array(
			'filter_menu_id'  => $data['menu_id'],
			'sort'  => $data['sort_order'],
			'order'  => 'ASC',
		);
		$data['menuparents'] = $this->model_catalog_menuitem->getAllMenuitems($filter_data);

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/menuitem_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/menuitem')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['menuitem_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/menuitem')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}
}