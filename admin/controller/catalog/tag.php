<?php
class ControllerCatalogTag extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/tag');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/tag');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_tag->addTag($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/tag');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_tag->editTag($this->request->get['tag_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/tag');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $tag_id) {
				$this->model_catalog_tag->deleteTag($tag_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	public function repair() {
		$this->load->language('catalog/tag');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/tag');

		if ($this->validateRepair()) {
			$this->model_catalog_tag->repairTag();

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('catalog/tag', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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
			'href' => $this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		$data['add'] = $this->url->link('catalog/tag/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/tag/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['repair'] = $this->url->link('catalog/tag/repair', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['tags'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$start=($page - 1) * $this->config->get('config_limit_admin');
		$limit=$this->config->get('config_limit_admin');
		$tag_total = $this->model_catalog_tag->getTotalTags();
		$results = $this->model_catalog_tag->getTags($filter_data);
		$results=array_slice($results,($page - 1) * $this->config->get('config_limit_admin'),$this->config->get('config_limit_admin'));
		
		foreach ($results as $result) {
			$data['tags'][] = array(
				'tag_id' => $result['tag_id'],
				'name'        => $result['name'],
				'sort_order'  => $result['sort_order'],
				'edit'        => $this->url->link('catalog/tag/edit', 'token=' . $this->session->data['token'] . '&tag_id=' . $result['tag_id'] . $url, 'SSL'),
				'delete'      => $this->url->link('catalog/tag/delete', 'token=' . $this->session->data['token'] . '&tag_id=' . $result['tag_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_rebuild'] = $this->language->get('button_rebuild');

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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $tag_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($tag_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($tag_total - $this->config->get('config_limit_admin'))) ? $tag_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $tag_total, ceil($tag_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/tag_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['tag_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_type'] = $this->language->get('entry_type');
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

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
		
		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}
		
		$url = '';

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
			'href' => $this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);
		
		if (!isset($this->request->get['tag_id'])) {
			$data['action'] = $this->url->link('catalog/tag/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/tag/edit', 'token=' . $this->session->data['token'] . '&tag_id=' . $this->request->get['tag_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/tag', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['tag_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$tag_info = $this->model_catalog_tag->getTag($this->request->get['tag_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['tag_description'])) {
			$data['tag_description'] = $this->request->post['tag_description'];
		} elseif (!empty($tag_info)) {
			$data['tag_description'] = $tag_info['tag_description']; 
		} else {
			$data['tag_description'] = array();
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['tag_store'])) {
			$data['tag_store'] = $this->request->post['tag_store'];
		} elseif (!empty($tag_info)) {
			$data['tag_store'] = $tag_info['tag_to_store']; 
		} else {
			$data['tag_store'] = array(0);
		}

		$this->load->model('localisation/type');

		$data['types'] = $this->model_localisation_type->getTypes();

		if (isset($this->request->post['tag_type'])) {
			$data['tag_type'] = $this->request->post['tag_type'];
		} elseif (!empty($tag_info)) {
			$data['tag_type'] = $tag_info['tag_to_type']; 
		} else {
			$data['tag_type'] = array(0);
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($tag_info)) {
			$data['sort_order'] = $tag_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($tag_info)) {
			$data['status'] = $tag_info['status'];
		} else {
			$data['status'] = true;
		}
		
		$data['config_language_id']=$this->config->get('config_language_id');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/tag_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/tag')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['tag_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 512)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/tag')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateRepair() {
		if (!$this->user->hasPermission('modify', 'catalog/tag')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) && isset($this->request->get['tag_to_type'])) {
			$this->load->model('catalog/tag');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'tag_to_type' => $this->request->get['tag_to_type'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			//$results = $this->model_catalog_tag->getTags($filter_data);
			$data_results = $this->model_catalog_tag->getTags($filter_data);
			$results=$data_results['results'];
			$results=array_slice($results,0,5);
			foreach ($results as $result) {
				$json[] = array(
					'tag_id' => $result['tag_id'],
					'name'        => strip_tags(html_entity_decode($result['tag_description'][$this->config->get('config_language_id')]['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}