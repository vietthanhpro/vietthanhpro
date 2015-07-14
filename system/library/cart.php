<?php
class Cart {
	private $config;
	private $db;
	private $data = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		//$this->db = $registry->get('db');
		$this->mongodb = $registry->get('mongodb');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');

		if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}
	}

	public function getProducts() {
		if (!$this->data) {
			foreach ($this->session->data['cart'] as $key => $quantity) {
				$product = unserialize(base64_decode($key));

				$product_id = $product['product_id'];

				$stock = true;

				// Options
				if (!empty($product['option'])) {
					$options = $product['option'];
				} else {
					$options = array();
				}

				// Profile
				if (!empty($product['recurring_id'])) {
					$recurring_id = $product['recurring_id'];
				} else {
					$recurring_id = 0;
				}

				//$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
				$product_query_data = array();
				$collection="mongo_product";
				$where=array('product_id'=>(int)$product_id, 'status'=>1, 'date_available'=> array('$lte'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
				$product_query_data=$this->mongodb->getBy($collection,$where);
				//if ($product_query->num_rows) {
				if ($product_query_data) {
					$option_price = 0;
					$option_points = 0;
					$option_weight = 0;

					$option_data = array();

					foreach ($options as $product_option_id => $value) {
						//$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");
						$option_query_data = array();
						$collection="mongo_product_option";
						$where=array('product_option_id'=>(int)$product_option_id, 'product_id'=>(int)$product_id);
						$option_query_data = $this->mongodb->getBy($collection,$where);
						if ($option_query_data) {
							$option_detail_query_data = array();
							$collection="mongo_option";
							$where=array('option_id'=>(int)$option_query_data['option_id']);
							$option_detail_query_data = $this->mongodb->getBy($collection,$where);
							$option_query_data['type']=$option_detail_query_data['type'];
							$option_query_data['name']=$option_detail_query_data['option_description'][(int)$this->config->get('config_language_id')]['name'];
						//if ($option_query->num_rows) {
							if ($option_query_data['type'] == 'select' || $option_query_data['type'] == 'radio' || $option_query_data['type'] == 'image') {
								//$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
								$option_value_query_data=array();
								$collection="mongo_product_option_value";
								$where=array('product_option_value_id'=>(int)$value, 'product_option_id'=>(int)$product_option_id);
								$option_value_query_data= $this->mongodb->getBy($collection,$where);
								//if ($option_value_query->num_rows) {
								if ($option_value_query_data) {
									$option_value_detail_query_data = array();
									$collection="mongo_option_value";
									$where=array('option_value_id'=>(int)$option_value_query_data['option_value_id']);
									$option_value_detail_query_data = $this->mongodb->getBy($collection,$where);
									$option_value_query_data['name']=$option_value_detail_query_data['option_value_description'][(int)$this->config->get('config_language_id')]['name'];
									if ($option_value_query_data['price_prefix'] == '+') {
										$option_price += $option_value_query_data['price'];
									} elseif ($option_value_query_data['price_prefix'] == '-') {
										$option_price -= $option_value_query_data['price'];
									}

									if ($option_value_query_data['points_prefix'] == '+') {
										$option_points += $option_value_query_data['points'];
									} elseif ($option_value_query_data['points_prefix'] == '-') {
										$option_points -= $option_value_query_data['points'];
									}

									if ($option_value_query_data['weight_prefix'] == '+') {
										$option_weight += $option_value_query_data['weight'];
									} elseif ($option_value_query_data['weight_prefix'] == '-') {
										$option_weight -= $option_value_query_data['weight'];
									}

									if ($option_value_query_data['subtract'] && (!$option_value_query_data['quantity'] || ($option_value_query_data['quantity'] < $quantity))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $value,
										'option_id'               => $option_query_data['option_id'],
										'option_value_id'         => $option_value_query_data['option_value_id'],
										'name'                    => $option_query_data['name'],
										'value'                   => $option_value_query_data['name'],
										'type'                    => $option_query_data['type'],
										'quantity'                => $option_value_query_data['quantity'],
										'subtract'                => $option_value_query_data['subtract'],
										'price'                   => $option_value_query_data['price'],
										'price_prefix'            => $option_value_query_data['price_prefix'],
										'points'                  => $option_value_query_data['points'],
										'points_prefix'           => $option_value_query_data['points_prefix'],
										'weight'                  => $option_value_query_data['weight'],
										'weight_prefix'           => $option_value_query_data['weight_prefix']
									);
								}
							} elseif ($option_query_data['type'] == 'checkbox' && is_array($value)) {
								foreach ($value as $product_option_value_id) {
									//$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
									$option_value_query_data=array();
									$collection="mongo_product_option_value";
									$where=array('product_option_value_id'=>(int)$product_option_value_id, 'product_option_id'=>(int)$product_option_id);
									$option_value_query_data= $this->mongodb->getBy($collection,$where);
									//if ($option_value_query->num_rows) {
									if ($option_value_query_data) {
										$option_value_detail_query_data = array();
										$collection="mongo_option_value";
										$where=array('option_value_id'=>(int)$option_value_query_data['option_value_id']);
										$option_value_detail_query_data = $this->mongodb->getBy($collection,$where);
										$option_value_query_data['name']=$option_value_detail_query_data['option_value_description'][(int)$this->config->get('config_language_id')]['name'];
										
										if ($option_value_query_data['price_prefix'] == '+') {
											$option_price += $option_value_query_data['price'];
										} elseif ($option_value_query_data['price_prefix'] == '-') {
											$option_price -= $option_value_query_data['price'];
										}

										if ($option_value_query_data['points_prefix'] == '+') {
											$option_points += $option_value_query_data['points'];
										} elseif ($option_value_query_data['points_prefix'] == '-') {
											$option_points -= $option_value_query_data['points'];
										}

										if ($option_value_query_data['weight_prefix'] == '+') {
											$option_weight += $option_value_query_data['weight'];
										} elseif ($option_value_query_data['weight_prefix'] == '-') {
											$option_weight -= $option_value_query_data['weight'];
										}

										if ($option_value_query_data['subtract'] && (!$option_value_query_data['quantity'] || ($option_value_query_data['quantity'] < $quantity))) {
											$stock = false;
										}

										$option_data[] = array(
											'product_option_id'       => $product_option_id,
											'product_option_value_id' => $product_option_value_id,
											'option_id'               => $option_query_data['option_id'],
											'option_value_id'         => $option_value_query_data['option_value_id'],
											'name'                    => $option_query_data['name'],
											'value'                   => $option_value_query_data['name'],
											'type'                    => $option_query_data['type'],
											'quantity'                => $option_value_query_data['quantity'],
											'subtract'                => $option_value_query_data['subtract'],
											'price'                   => $option_value_query_data['price'],
											'price_prefix'            => $option_value_query_data['price_prefix'],
											'points'                  => $option_value_query_data['points'],
											'points_prefix'           => $option_value_query_data['points_prefix'],
											'weight'                  => $option_value_query_data['weight'],
											'weight_prefix'           => $option_value_query_data['weight_prefix']
										);
									}
								}
							} elseif ($option_query_data['type'] == 'text' || $option_query_data['type'] == 'textarea' || $option_query_data['type'] == 'file' || $option_query_data['type'] == 'date' || $option_query_data['type'] == 'datetime' || $option_query_data['type'] == 'time') {
								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => '',
									'option_id'               => $option_query_data['option_id'],
									'option_value_id'         => '',
									'name'                    => $option_query_data['name'],
									'value'                   => $value,
									'type'                    => $option_query_data['type'],
									'quantity'                => '',
									'subtract'                => '',
									'price'                   => '',
									'price_prefix'            => '',
									'points'                  => '',
									'points_prefix'           => '',
									'weight'                  => '',
									'weight_prefix'           => ''
								);
							}
						}
					}

					$price = $product_query_data['price'];

					// Product Discounts
					$discount_quantity = 0;

					foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
						$product_2 = (array)unserialize(base64_decode($key_2));

						if ($product_2['product_id'] == $product_id) {
							$discount_quantity += $quantity_2;
						}
					}

					//$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");
					$collection="mongo_product_special";
					$product_discount_query_data = array();
					$where=array('product_id'=>(int)$product_id, 'customer_group_id'=>(int)$this->config->get('config_customer_group_id'), 'quantity'=> array('$lte'=>(int)$discount_quantity), 'date_start'=> array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_end'=> array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
					$order=array('quantity'=> -1, 'priority'=> 1, 'price'=>1);
					$product_discount_query_data = $this->mongodb->getall($collection,$where, $order);
					if ($product_discount_query_data) {
						$product_discount_query_data_info=array_shift($product_discount_query_data);
						$price = $product_discount_query_data_info['price'];
					}

					// Product Specials
					//$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");
					$collection="mongo_product_special";
					$product_special_query_data = array();
					$where=array('product_id'=>(int)$product_id, 'customer_group_id'=>(int)$this->config->get('config_customer_group_id'), 'date_start'=> array('$lt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))), 'date_end'=> array('$gt'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))));
					$order=array('priority'=> 1, 'price'=>1);
					$product_special_query_data = $this->mongodb->getall($collection,$where, $order);
					//if ($product_special_query->num_rows) {
					if ($product_special_query_data) {
						$product_special_query_data_info=array_shift($product_special_query_data);
						$price = $product_special_query_data_info['price']; break;
					}

					// Reward Points
					//$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");
					$collection="mongo_product_reward";
					$where=array('product_id'=>(int)$product_id, 'customer_group_id'=>(int)$this->config->get('config_customer_group_id'));
					$product_reward_query_data = $this->mongodb->getBy($collection,$where);
					//if ($product_reward_query->num_rows) {
					if ($product_reward_query_data) {
						//$reward = $product_reward_query->row['points'];
						$reward = $product_reward_query_data['points'];
					} else {
						$reward = 0;
					}

					// Downloads
					//$download_data = array();
					$collection="mongo_download";
					$download_data = $product_query_data['product_download'];
					//$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$product_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
					//foreach ($download_query->rows as $download) {	
					foreach ($download_data as $download_info) {		
						$download = array();		
						$where=array('download_id'=>(int)$download_info);
						$download =  $this->mongodb->getBy($collection,$where);
						$download_data[] = array(
							'download_id' => $download['download_id'],
							'name'        => $download['name'],
							'filename'    => $download['filename'],
							'mask'        => $download['mask']
						);
					}

					// Stock
					if (!$product_query_data['quantity'] || ($product_query_data['quantity'] < $quantity)) {
						$stock = false;
					}

					//$recurring_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recurring` `p` JOIN `" . DB_PREFIX . "product_recurring` `pp` ON `pp`.`recurring_id` = `p`.`recurring_id` AND `pp`.`product_id` = " . (int)$product_query_data['product_id'] . " JOIN `" . DB_PREFIX . "recurring_description` `pd` ON `pd`.`recurring_id` = `p`.`recurring_id` AND `pd`.`language_id` = " . (int)$this->config->get('config_language_id') . " WHERE `pp`.`recurring_id` = " . (int)$recurring_id . " AND `status` = 1 AND `pp`.`customer_group_id` = " . (int)$this->config->get('config_customer_group_id'));					
					$collection="mongo_product_recurring";
					$where=array('recurring_id'=>(int)$recurring_id, 'product_id'=>(int)$product_query_data['product_id'], 'customer_group_id'=>(int)$this->config->get('config_customer_group_id'));
					$recurring_query_data = $this->mongodb->getBy($collection,$where);
					//if ($recurring_query->num_rows) {
					if ($recurring_query_data) {
						$recurring_info_data = array();
						$collection="mongo_recurring";
						$where=array('recurring_id'=>(int)$recurring_id, 'status'=>1);
						$recurring_info_data = $this->mongodb->getBy($collection,$where);
						if ($recurring_info_data) {
							$recurring = array(
								'recurring_id'    => $recurring_id,
								'name'            => $recurring_info_data['recurring_description'][(int)$this->config->get('config_language_id')]['name'],
								'frequency'       => $recurring_info_data['frequency'],
								'price'           => $recurring_info_data['price'],
								'cycle'           => $recurring_info_data['cycle'],
								'duration'        => $recurring_info_data['duration'],
								'trial'           => $recurring_info_data['trial_status'],
								'trial_frequency' => $recurring_info_data['trial_frequency'],
								'trial_price'     => $recurring_info_data['trial_price'],
								'trial_cycle'     => $recurring_info_data['trial_cycle'],
								'trial_duration'  => $recurring_info_data['trial_duration']
							);
						} else {
							$recurring = false;
						}
					} else {
						$recurring = false;
					}

					$this->data[$key] = array(
						'key'             => $key,
						'product_id'      => $product_query_data['product_id'],
						'name'            => $product_query_data['product_description'][(int)$this->config->get('config_language_id')]['name'],
						'model'           => $product_query_data['model'],
						'shipping'        => $product_query_data['shipping'],
						'image'           => $product_query_data['image'],
						'option'          => $option_data,
						'download'        => $download_data,
						'quantity'        => $quantity,
						'minimum'         => $product_query_data['minimum'],
						'subtract'        => $product_query_data['subtract'],
						'stock'           => $stock,
						'price'           => ($price + $option_price),
						'total'           => ($price + $option_price) * $quantity,
						'reward'          => $reward * $quantity,
						'points'          => ($product_query_data['points'] ? ($product_query_data['points'] + $option_points) * $quantity : 0),
						'tax_class_id'    => $product_query_data['tax_class_id'],
						'weight'          => ($product_query_data['weight'] + $option_weight) * $quantity,
						'weight_class_id' => $product_query_data['weight_class_id'],
						'length'          => $product_query_data['length'],
						'width'           => $product_query_data['width'],
						'height'          => $product_query_data['height'],
						'length_class_id' => $product_query_data['length_class_id'],
						'recurring'       => $recurring
					);
				} else {
					$this->remove($key);
				}
			}
		}

		return $this->data;
	}

	public function getRecurringProducts() {
		$recurring_products = array();

		foreach ($this->getProducts() as $key => $value) {
			if ($value['recurring']) {
				$recurring_products[$key] = $value;
			}
		}

		return $recurring_products;
	}

	public function add($product_id, $qty = 1, $option = array(), $recurring_id = 0) {
		$this->data = array();

		$product['product_id'] = (int)$product_id;

		if ($option) {
			$product['option'] = $option;
		}

		if ($recurring_id) {
			$product['recurring_id'] = (int)$recurring_id;
		}

		$key = base64_encode(serialize($product));

		if ((int)$qty && ((int)$qty > 0)) {
			if (!isset($this->session->data['cart'][$key])) {
				$this->session->data['cart'][$key] = (int)$qty;
			} else {
				$this->session->data['cart'][$key] += (int)$qty;
			}
		}
	}

	public function update($key, $qty) {
		$this->data = array();

		if ((int)$qty && ((int)$qty > 0) && isset($this->session->data['cart'][$key])) {
			$this->session->data['cart'][$key] = (int)$qty;
		} else {
			$this->remove($key);
		}
	}

	public function remove($key) {
		$this->data = array();

		unset($this->session->data['cart'][$key]);
	}

	public function clear() {
		$this->data = array();

		$this->session->data['cart'] = array();
	}

	public function getWeight() {
		$weight = 0;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getSubTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getTaxes() {
		$tax_data = array();

		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->session->data['cart']);
	}

	public function hasRecurringProducts() {
		return count($this->getRecurringProducts());
	}

	public function hasStock() {
		$stock = true;

		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
				$stock = false;
			}
		}

		return $stock;
	}

	public function hasShipping() {
		$shipping = false;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$shipping = true;

				break;
			}
		}

		return $shipping;
	}

	public function hasDownload() {
		$download = false;

		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
				$download = true;

				break;
			}
		}

		return $download;
	}
}