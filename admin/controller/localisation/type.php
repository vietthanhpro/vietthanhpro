<?php
class ControllerLocalisationType extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('localisation/type');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/type');

		$this->getList();
	}

	public function add() {
		$this->load->language('localisation/type');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/type');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_type->addType($this->request->post);

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

			$this->response->redirect($this->url->link('localisation/type', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('localisation/type');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/type');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_type->editType($this->request->get['type_id'], $this->request->post);

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

			$this->response->redirect($this->url->link('localisation/type', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/type');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('localisation/type');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $type_id) {
				$this->model_localisation_type->deleteType($type_id);
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

			$this->response->redirect($this->url->link('localisation/type', 'token=' . $this->session->data['token'] . $url, 'SSL'));
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
			'href' => $this->url->link('localisation/type', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('localisation/type/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('localisation/type/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['types'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		//$type_total = $this->model_localisation_type->getTotalTypes();
		//$results = $this->model_localisation_type->getTypes($filter_data);
		$data_results = $this->model_localisation_type->getTypes($filter_data);
		$results=$data_results['results'];
		$type_total = $data_results['count'];
		
		foreach ($results as $result) {
			$data['types'][] = array(
				'type_id' => $result['type_id'],
				'name'           => $result['type_description'][(int)$this->config->get('config_language_id')]['name'],
				'code'            => $result['code'],
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'sort_order'           => $result['sort_order'],
				'edit'            => $this->url->link('localisation/type/edit', 'token=' . $this->session->data['token'] . '&type_id=' . $result['type_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_name'] = $this->language->get('column_name');
		$data['column_code'] = $this->language->get('column_code');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_status'] = $this->language->get('column_status');
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('localisation/type', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_code'] = $this->url->link('localisation/type', 'token=' . $this->session->data['token'] . '&sort=code' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('localisation/type', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $type_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('localisation/type', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($type_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($type_total - $this->config->get('config_limit_admin'))) ? $type_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $type_total, ceil($type_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/type_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['type_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_code'] = $this->language->get('entry_code');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = array();
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
			'href' => $this->url->link('localisation/type', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['type_id'])) {
			$data['action'] = $this->url->link('localisation/type/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('localisation/type/edit', 'token=' . $this->session->data['token'] . '&type_id=' . $this->request->get['type_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('localisation/type', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['type_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$type_info = $this->model_localisation_type->getType($this->request->get['type_id']);
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['type_description'])) {
			$data['type_description'] = $this->request->post['type_description'];
		} elseif (!empty($type_info)) {
			$data['type_description'] = $type_info['type_description']; 
		} else {
			$data['type_description'] = array();
		}

		if (isset($this->request->post['code'])) {
			$data['code'] = $this->request->post['code'];
		} elseif (!empty($type_info)) {
			$data['code'] = $type_info['code'];
		} else {
			$data['code'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/type_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/type')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['type_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 256)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if ((utf8_strlen($this->request->post['code']) < 1) || (utf8_strlen($this->request->post['code']) > 256)) {
			$this->error['code'] = $this->language->get('error_code');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/type')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}