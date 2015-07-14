<?php
class ControllerCatalogBaiviet extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/baiviet');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/baiviet');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/baiviet');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/baiviet');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_baiviet->addBaiviet($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$url .= '&filter_danhmucbaiviet_id=' . $this->request->get['filter_danhmucbaiviet_id'];
		}

		if (isset($this->request->get['filter_createby'])) {
			$url .= '&filter_createby=' . $this->request->get['filter_createby'];
		}

		if (isset($this->request->get['filter_updateby'])) {
			$url .= '&filter_updateby=' . $this->request->get['filter_updateby'];
		}

		if (isset($this->request->get['filter_accessby_id'])) {
			$url .= '&filter_accessby_id=' . $this->request->get['filter_accessby_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/baiviet');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/baiviet');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_baiviet->editBaiviet($this->request->get['baiviet_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$url .= '&filter_danhmucbaiviet_id=' . $this->request->get['filter_danhmucbaiviet_id'];
		}

		if (isset($this->request->get['filter_createby'])) {
			$url .= '&filter_createby=' . $this->request->get['filter_createby'];
		}

		if (isset($this->request->get['filter_updateby'])) {
			$url .= '&filter_updateby=' . $this->request->get['filter_updateby'];
		}

		if (isset($this->request->get['filter_accessby_id'])) {
			$url .= '&filter_accessby_id=' . $this->request->get['filter_accessby_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/baiviet');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/baiviet');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $baiviet_id) {
				$this->model_catalog_baiviet->deleteBaiviet($baiviet_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$url .= '&filter_danhmucbaiviet_id=' . $this->request->get['filter_danhmucbaiviet_id'];
		}

		if (isset($this->request->get['filter_createby'])) {
			$url .= '&filter_createby=' . $this->request->get['filter_createby'];
		}

		if (isset($this->request->get['filter_updateby'])) {
			$url .= '&filter_updateby=' . $this->request->get['filter_updateby'];
		}

		if (isset($this->request->get['filter_accessby_id'])) {
			$url .= '&filter_accessby_id=' . $this->request->get['filter_accessby_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}
		
		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$filter_danhmucbaiviet_id = $this->request->get['filter_danhmucbaiviet_id'];
		} else {
			$filter_danhmucbaiviet_id = null;
		}
		
		if (isset($this->request->get['filter_createby'])) {
			$filter_createby = $this->request->get['filter_createby'];
		} else {
			$filter_createby = null;
		}
		
		if (isset($this->request->get['filter_updateby'])) {
			$filter_updateby = $this->request->get['filter_updateby'];
		} else {
			$filter_updateby = null;
		}
		
		if (isset($this->request->get['filter_accessby_id'])) {
			$filter_accessby_id = $this->request->get['filter_accessby_id'];
		} else {
			$filter_accessby_id = null;
		}
		
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$url .= '&filter_danhmucbaiviet_id=' . $this->request->get['filter_danhmucbaiviet_id'];
		}

		if (isset($this->request->get['filter_createby'])) {
			$url .= '&filter_createby=' . $this->request->get['filter_createby'];
		}

		if (isset($this->request->get['filter_updateby'])) {
			$url .= '&filter_updateby=' . $this->request->get['filter_updateby'];
		}

		if (isset($this->request->get['filter_accessby_id'])) {
			$url .= '&filter_accessby_id=' . $this->request->get['filter_accessby_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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
			'href' => $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		$data['add'] = $this->url->link('catalog/baiviet/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['copy'] = $this->url->link('catalog/baiviet/copy', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$data['delete'] = $this->url->link('catalog/baiviet/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$data['baiviets'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_danhmucbaiviet_id'   => $filter_danhmucbaiviet_id,
			'filter_createby'   => $filter_createby,
			'filter_updateby'   => $filter_updateby,
			'filter_accessby_id'   => $filter_accessby_id,
			'filter_date_added'   => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'           => $this->config->get('config_limit_admin')
		);

		$this->load->model('tool/image');

		$data_results = $this->model_catalog_baiviet->getBaiviets($filter_data);
		$results=$data_results['results'];
		$baiviet_total = $data_results['count'];
		
		$this->load->model('user/user');
		$this->load->model('localisation/accessby');
		
		foreach ($results as $result) {
			
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
			$date_added=(array)$result['date_added'];
			$date_modified=(array)$result['date_modified'];
			$createby_info = $this->model_user_user->getUser($result['createby']);
			$updateby_info = $this->model_user_user->getUser($result['updateby']);
			$accessby_info = $this->model_localisation_accessby->getAccessby($result['accessby_id']);
			$data['baiviets'][] = array(
				'baiviet_id' => $result['baiviet_id'],
				'image'      => $image,
				'name'       => $result['baiviet_description'][(int)$this->config->get('config_language_id')]['name'],
				'sort_order'  => $result['sort_order'],
				'viewed'  => $result['viewed'],
				'createby' => $createby_info['username'],
				'updateby' => $updateby_info['username'],
				'accessby' => $accessby_info['accessby_description'][(int)$this->config->get('config_language_id')]['name'],
				'status'     => ($result['status']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added'     => date($this->language->get('date_format_short'),$date_added['sec']),
				'date_modified'     => date($this->language->get('date_format_short'),$date_modified['sec']),
				'edit'       => $this->url->link('catalog/baiviet/edit', 'token=' . $this->session->data['token'] . '&baiviet_id=' . $result['baiviet_id'] . $url, 'SSL')
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_accessby'] = $this->language->get('column_accessby');
		$data['column_view'] = $this->language->get('column_view');
		$data['column_sort_order'] = $this->language->get('column_sort_order');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_date_modified'] = $this->language->get('column_date_modified');
		$data['column_action'] = $this->language->get('column_action');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_danhmucbaiviet'] = $this->language->get('entry_danhmucbaiviet');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_createby'] = $this->language->get('entry_createby');
		$data['entry_updateby'] = $this->language->get('entry_updateby');
		$data['entry_accessby'] = $this->language->get('entry_accessby');
		$data['entry_date_added'] = $this->language->get('entry_date_added');
		$data['entry_date_modified'] = $this->language->get('entry_date_modified');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

		$data['token'] = $this->session->data['token'];

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$url .= '&filter_danhmucbaiviet_id=' . $this->request->get['filter_danhmucbaiviet_id'];
		}

		if (isset($this->request->get['filter_createby'])) {
			$url .= '&filter_createby=' . $this->request->get['filter_createby'];
		}

		if (isset($this->request->get['filter_updateby'])) {
			$url .= '&filter_updateby=' . $this->request->get['filter_updateby'];
		}

		if (isset($this->request->get['filter_accessby_id'])) {
			$url .= '&filter_accessby_id=' . $this->request->get['filter_accessby_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		$data['sort_sort_order'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, 'SSL');
		$data['sort_viewed'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . '&sort=viewed' . $url, 'SSL');
		$data['sort_status'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$data['sort_date_added'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
		$data['sort_date_modified'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . '&sort=date_modified' . $url, 'SSL');
		$data['sort_accessby'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . '&sort=accessby_id' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$url .= '&filter_danhmucbaiviet_id=' . $this->request->get['filter_danhmucbaiviet_id'];
		}

		if (isset($this->request->get['filter_createby'])) {
			$url .= '&filter_createby=' . $this->request->get['filter_createby'];
		}

		if (isset($this->request->get['filter_updateby'])) {
			$url .= '&filter_updateby=' . $this->request->get['filter_updateby'];
		}

		if (isset($this->request->get['filter_accessby_id'])) {
			$url .= '&filter_accessby_id=' . $this->request->get['filter_accessby_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $baiviet_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($baiviet_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($baiviet_total - $this->config->get('config_limit_admin'))) ? $baiviet_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $baiviet_total, ceil($baiviet_total / $this->config->get('config_limit_admin')));
		
		$data['config_language_id'] = (int)$this->config->get('config_language_id');
		
		$data['accessbys'] = $this->model_localisation_accessby->getAccessbys();		
		$data['users'] = $this->model_user_user->getUsers();
		
		$this->load->model('catalog/danhmucbaiviet');
		$filter_danhmucbaiviet_info = $this->model_catalog_danhmucbaiviet->getDanhmucbaiviet($filter_danhmucbaiviet_id);

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;
		$data['filter_danhmucbaiviet'] = $filter_danhmucbaiviet_info['danhmucbaiviet_description'][(int)$this->config->get('config_language_id')]['name'];
		$data['filter_danhmucbaiviet_id'] = $filter_danhmucbaiviet_id;
		$data['filter_createby'] = $filter_createby;
		$data['filter_updateby'] = $filter_updateby;
		$data['filter_accessby_id'] = $filter_accessby_id;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/baiviet_list.tpl', $data));
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['baiviet_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_plus'] = $this->language->get('text_plus');
		$data['text_minus'] = $this->language->get('text_minus');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_option_value'] = $this->language->get('text_option_value');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_classname'] = $this->language->get('entry_classname');
		$data['entry_accessby'] = $this->language->get('entry_accessby');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_danhmucbaiviet'] = $this->language->get('entry_danhmucbaiviet');
		$data['entry_related'] = $this->language->get('entry_related');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_required'] = $this->language->get('entry_required');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_layout'] = $this->language->get('entry_layout');
		$data['entry_tag'] = $this->language->get('entry_tag');

		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_danhmucbaiviet'] = $this->language->get('help_danhmucbaiviet');
		$data['help_tag'] = $this->language->get('help_tag');
		$data['help_related'] = $this->language->get('help_related');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_image_add'] = $this->language->get('button_image_add');
		$data['button_remove'] = $this->language->get('button_remove');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_image'] = $this->language->get('tab_image');
		$data['tab_links'] = $this->language->get('tab_links');
		$data['tab_design'] = $this->language->get('tab_design');

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_danhmucbaiviet_id'])) {
			$url .= '&filter_danhmucbaiviet_id=' . $this->request->get['filter_danhmucbaiviet_id'];
		}

		if (isset($this->request->get['filter_createby'])) {
			$url .= '&filter_createby=' . $this->request->get['filter_createby'];
		}

		if (isset($this->request->get['filter_updateby'])) {
			$url .= '&filter_updateby=' . $this->request->get['filter_updateby'];
		}

		if (isset($this->request->get['filter_accessby_id'])) {
			$url .= '&filter_accessby_id=' . $this->request->get['filter_accessby_id'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			'href' => $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . $url, 'SSL')
		);

		if (!isset($this->request->get['baiviet_id'])) {
			$data['action'] = $this->url->link('catalog/baiviet/add', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$data['action'] = $this->url->link('catalog/baiviet/edit', 'token=' . $this->session->data['token'] . '&baiviet_id=' . $this->request->get['baiviet_id'] . $url, 'SSL');
		}

		$data['cancel'] = $this->url->link('catalog/baiviet', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['baiviet_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$baiviet_info = $this->model_catalog_baiviet->getBaiviet($this->request->get['baiviet_id']);
		}

		$data['token'] = $this->session->data['token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['baiviet_description'])) {
			$data['baiviet_description'] = $this->request->post['baiviet_description'];
		} elseif (!empty($baiviet_info)) {
			$data['baiviet_description'] = $baiviet_info['baiviet_description']; 
		} else {
			$data['baiviet_description'] = array();
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($baiviet_info)) {
			$data['image'] = $baiviet_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($baiviet_info) && is_file(DIR_IMAGE . $baiviet_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($baiviet_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['classname'])) {
			$data['classname'] = $this->request->post['classname'];
		} elseif (!empty($baiviet_info)) {
			$data['classname'] = $baiviet_info['classname'];
		} else {
			$data['classname'] = '';
		}
		
		if (isset($this->request->post['accessby_id'])) {
			$data['accessby_id'] = $this->request->post['accessby_id'];
		} elseif (!empty($baiviet_info)) {
			$data['accessby_id'] = $baiviet_info['accessby_id'];
		} else {
			$data['accessby_id'] = 1;
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['baiviet_store'])) {
			$data['baiviet_store'] = $this->request->post['baiviet_store'];
		} elseif (!empty($baiviet_info)) {
			$data['baiviet_store'] = $baiviet_info['baiviet_to_store']; 
		} else {
			$data['baiviet_store'] = array(0);
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($baiviet_info)) {
			$data['keyword'] = $baiviet_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($baiviet_info)) {
			$data['sort_order'] = $baiviet_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($baiviet_info)) {
			$data['status'] = $baiviet_info['status'];
		} else {
			$data['status'] = true;
		}

		// Danhmucbaiviets
		$this->load->model('catalog/danhmucbaiviet');

		if (isset($this->request->post['baiviet_danhmucbaiviet'])) {
			$danhmucbaiviets = $this->request->post['baiviet_danhmucbaiviet'];
		} elseif (!empty($baiviet_info)) {
			$danhmucbaiviets = $baiviet_info['baiviet_danhmucbaiviet'];
		} else {
			$danhmucbaiviets = array();
		}

		$data['baiviet_danhmucbaiviets'] = array();

		foreach ($danhmucbaiviets as $danhmucbaiviet_id) {
			$danhmucbaiviet_info = $this->model_catalog_danhmucbaiviet->getDanhmucbaiviet($danhmucbaiviet_id);
			if ($danhmucbaiviet_info) {
				$path_info = $this->model_catalog_danhmucbaiviet->getPath($danhmucbaiviet_id);
				$data['baiviet_danhmucbaiviets'][] = array(
					'danhmucbaiviet_id' => $danhmucbaiviet_info['danhmucbaiviet_id'],
					'name' => $path_info
				);
			}
		}
		
		// Tags
		$this->load->model('catalog/tag');

		if (isset($this->request->post['baiviet_tag'])) {
			$tags = $this->request->post['baiviet_tag'];
		} elseif (!empty($baiviet_info)) {
			$tags = $baiviet_info['baiviet_tag'];
		} else {
			$tags = array();
		}

		$data['baiviet_tags'] = array();

		foreach ($tags as $tag_id) {
			$tag_info = $this->model_catalog_tag->getTag($tag_id);
			if ($tag_info) {
				$data['baiviet_tags'][] = array(
					'tag_id' => $tag_info['tag_id'],
					'name' => $tag_info['tag_description'][(int)$this->config->get('config_language_id')]['name']
				);
			}
		}

		// Images
		if (isset($this->request->post['baiviet_image'])) {
			$baiviet_images = $this->request->post['baiviet_image'];
		} elseif (!empty($baiviet_info)) {
			$baiviet_images = $baiviet_info['baiviet_image'];
		} else {
			$baiviet_images = array();
		}
		
		$data['baiviet_images'] = array();

		foreach ($baiviet_images as $baiviet_image) {
			if (is_file(DIR_IMAGE . $baiviet_image['image'])) {
				$image = $baiviet_image['image'];
				$thumb = $baiviet_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['baiviet_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $baiviet_image['sort_order']
			);
		}

		if (isset($this->request->post['baiviet_related'])) {
			$baiviets = $this->request->post['baiviet_related'];
		} elseif (isset($this->request->get['baiviet_id'])) {
			$baiviets = $this->model_catalog_baiviet->getBaivietRelated($this->request->get['baiviet_id']);
		} else {
			$baiviets = array();
		}

		$data['baiviet_relateds'] = array();

		foreach ($baiviets as $baiviet_id) {
			$related_info = $this->model_catalog_baiviet->getBaiviet($baiviet_id);

			if ($related_info) {
				$data['baiviet_relateds'][] = array(
					'baiviet_id' => $related_info['baiviet_id'],
					'name'       => $related_info['baiviet_description'][$this->config->get('config_language_id')]['name']
				);
			}
		}
		
		if (isset($this->request->post['baiviet_layout'])) {
			$data['baiviet_layout'] = $this->request->post['baiviet_layout'];
		} elseif (isset($this->request->get['baiviet_id'])) {
			$data['baiviet_layout'] = $this->model_catalog_baiviet->getBaivietLayouts($this->request->get['baiviet_id']);
		} else {
			$data['baiviet_layout'] = array();
		}
		$data['config_language_id'] =(int)$this->config->get('config_language_id');

		$this->load->model('localisation/accessby');
		$data['accessbys'] = $this->model_localisation_accessby->getAccessbys();
		
		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/baiviet_form.tpl', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/baiviet')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['baiviet_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
		}
		
		if (utf8_strlen($this->request->post['keyword']) > 0) {
			$this->load->model('catalog/url_alias');

			$url_alias_info = $this->model_catalog_url_alias->getUrlAlias($this->request->post['keyword']);

			if ($url_alias_info && isset($this->request->get['baiviet_id']) && $url_alias_info['query'] != 'baiviet_id=' . $this->request->get['baiviet_id']) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}

			if ($url_alias_info && !isset($this->request->get['baiviet_id'])) {
				$this->error['keyword'] = sprintf($this->language->get('error_keyword'));
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/baiviet')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'catalog/baiviet')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/baiviet');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'start'        => 0,
				'limit'        => $limit
			);

			$data_results = $this->model_catalog_baiviet->getBaiviets($filter_data);
			$results=$data_results['results'];

			foreach ($results as $result) {

				$json[] = array(
					'baiviet_id' => $result['baiviet_id'],
					'name'       => strip_tags(html_entity_decode($result['baiviet_description'][(int)$this->config->get('config_language_id')]['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}