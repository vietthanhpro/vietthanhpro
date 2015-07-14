<?php
class ModelCatalogProduct extends Model {	
	public function addProduct($data) {
		$this->event->trigger('pre.admin.product.add', $data);
		$collection="mongo_product";
		$product_image= array();
		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image_value) {
				$product_image[]= array(
					'image'=>$product_image_value['image'],
					'sort_order'=>(int)$product_image_value['sort_order']
				);
			}
		}
		$product_download= array();
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$product_download[]= (int)$download_id;
			}
		}
		$product_category= array();
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$product_category[]= (int)$category_id;
			}
		}
		$product_tag= array();
		if (isset($data['product_tag'])) {
			foreach ($data['product_tag'] as $tag_id) {
				$product_tag[]= (int)$tag_id;
			}
		}
		$product_filter= array();
		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$product_filter[]= (int)$filter_id;
			}
		}
		$product_description= array();
		foreach ($data['product_description'] as $language_id => $value) {
			$product_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}
		$product_to_store=array();
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$product_to_store[]= (int)$store_id;
			}
		}
		//
		$product_id=1+(int)$this->mongodb->getlastid($collection,'product_id');
		$newdocument=array('product_id'=>(int)$product_id, 'product_description'=>$product_description, 'product_category'=>$product_category, 'product_tag'=>$product_tag, 'product_filter'=>$product_filter, 'product_download'=>$product_download, 'product_image'=>$product_image, 'product_to_store'=>$product_to_store, 'model'=>$data['model'], 'sku'=>$data['sku'], 'upc'=>$data['upc'], 'ean'=>$data['ean'], 'jan'=>$data['jan'], 'isbn'=>$data['isbn'], 'mpn'=>$data['mpn'], 'location'=>$data['location'],'quantity'=>(int)$data['quantity'], 'minimum'=>(int)$data['minimum'],'subtract'=>(int)$data['subtract'], 'stock_status_id'=>(int)$data['stock_status_id'], 'date_available'=>new MongoDate(strtotime($data['date_available'])), 'manufacturer_id'=>(int)$data['manufacturer_id'], 'shipping'=>(int)$data['shipping'], 'price'=>(float)$data['price'], 'points'=>(int)$data['points'], 'weight'=>(float)$data['weight'], 'weight_class_id'=>(int)$data['weight_class_id'], 'length'=>(float)$data['length'], 'width'=>(float)$data['width'], 'height'=>(float)$data['height'], 'length_class_id'=>(int)$data['length_class_id'], 'status'=>(int)$data['status'], 'viewed'=>(int)$data['viewed'], 'tax_class_id'=>$data['tax_class_id'], 'sort_order'=>(int)$data['sort_order'], 'createby'=>(int)$this->user->getId(), 'updateby'=>(int)$this->user->getId(), 'classname'=>trim($data['classname']),'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))),'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$newdocument); 

		if (isset($data['image'])) {
			$infoupdate=array('image'=>$data['image']);
			$where=array('product_id'=>(int)$product_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		if (isset($data['product_related'])) {
			$collection="mongo_product_related";
			foreach ($data['product_related'] as $related_id) {
				$where=array('product_id'=>(int)$product_id,'related_id'=>(int)$related_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('product_id'=>(int)$product_id, 'related_id'=>$related_id);
				$this->mongodb->create($collection,$newdocument); 
				$where=array('product_id'=>(int)$related_id,'related_id'=>(int)$product_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('product_id'=>(int)$related_id, 'related_id'=>$product_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['product_attribute'])) {
			$collection="mongo_product_attribute";
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$where=array('product_id'=>(int)$product_id, 'attribute_id'=>(int)$product_attribute['attribute_id']);
					$this->mongodb->delete($collection,$where); 

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$newdocument=array('product_id'=>(int)$product_id, 'attribute_id'=>(int)$product_attribute['attribute_id'], 'language_id'=>(int)$language_id, 'text'=>$product_attribute_description['text']);
						$this->mongodb->create($collection,$newdocument); 
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$collection="mongo_product_option";
						$product_option_id=1+(int)$this->mongodb->getlastid($collection,'product_option_id');
						$newdocument=array('product_option_id'=>(int)$product_option_id, 'product_id'=>(int)$product_id, 'option_id'=>(int)$product_option['option_id'], 'value'=>'', 'required'=>(int)$product_option['required']);
						$this->mongodb->create($collection,$newdocument); 
						
						$collection="mongo_product_option_value";
						$product_option_value_id=1+(int)$this->mongodb->getlastid($collection,'product_option_value_id');
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$newdocument=array('product_option_value_id'=>(int)$product_option_value_id, 'product_option_id'=>(int)$product_option_id, 'product_id'=>(int)$product_id, 'option_id'=>(int)$product_option['option_id'], 'option_value_id'=>(int)$product_option_value['option_value_id'], 'quantity'=>(int)$product_option_value['quantity'], 'subtract'=>(int)$product_option_value['subtract'], 'price'=>(float)$product_option_value['price'], 'price_prefix'=>$product_option_value['price_prefix'], 'points'=>(int)$product_option_value['points'], 'points_prefix'=>$product_option_value['points_prefix'], 'weight'=>(float)$product_option_value['weight'], 'weight_prefix'=>(int)$product_option_value['weight_prefix']);
							$this->mongodb->create($collection,$newdocument); 
						}
					}
				} else {
					$collection="mongo_product_option";
					$product_option_id=1+(int)$this->mongodb->getlastid($collection,'product_option_id');
					$newdocument=array('product_option_id'=>(int)$product_option_id, 'product_id'=>(int)$product_id, 'option_id'=>(int)$product_option['option_id'], 'value'=>$product_option['value'], 'required'=>(int)$product_option['required']);
					$this->mongodb->create($collection,$newdocument); 
				}
			}
		}

		if (isset($data['product_discount'])) {
			$collection="mongo_product_discount";
			foreach ($data['product_discount'] as $product_discount) {
				$product_discount_id=1+(int)$this->mongodb->getlastid($collection,'product_discount_id');
				$newdocument=array('product_discount_id'=>(int)$product_discount_id, 'product_id'=>(int)$product_id, 'customer_group_id'=>(int)$product_discount['customer_group_id'], 'quantity'=>(int)$product_discount['quantity'], 'priority'=>(int)$product_discount['priority'], 'price'=>(float)$product_discount['price'], 'date_start'=>new MongoDate(strtotime($product_discount['date_start'])), 'date_end'=>new MongoDate(strtotime($product_discount['date_end'])));
				$this->mongodb->create($collection,$newdocument);
			}
		}

		if (isset($data['product_special'])) {
			$collection="mongo_product_special";
			foreach ($data['product_special'] as $product_special) {
				$product_special_id=1+(int)$this->mongodb->getlastid($collection,'product_special_id');
				$newdocument=array('product_special_id'=>(int)$product_special_id, 'product_id'=>(int)$product_id, 'customer_group_id'=>(int)$product_special['customer_group_id'], 'priority'=>(int)$product_special['priority'], 'price'=>(float)$product_special['price'], 'date_start'=>new MongoDate(strtotime($product_special['date_start'])), 'date_end'=>new MongoDate(strtotime($product_special['date_end'])));
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['product_reward'])) {
			$collection="mongo_product_reward";
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$product_reward_id=1+(int)$this->mongodb->getlastid($collection,'product_reward_id');
				$newdocument=array('product_reward_id'=>(int)$product_reward_id, 'product_id'=>(int)$product_id, 'customer_group_id'=>(int)$customer_group_id, 'points'=>(int)$product_reward['points']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['product_layout'])) {
			$collection="mongo_product_to_layout";
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$newdocument=array('product_id'=>(int)$product_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['product_recurrings'])) {
			$collection="mongo_product_recurring";
			foreach ($data['product_recurrings'] as $recurring) {
				$newdocument=array('product_id'=>(int)$product_id, 'customer_group_id'=>(int)$recurring['customer_group_id'], 'recurring_id'=>(int)$recurring['recurring_id']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		if (isset($data['keyword'])) {
			$collection="mongo_url_alias";
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'product_id=' . (int)$product_id, 'keyword'=>$data['keyword']);
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.add', $product_id);

		return $product_id;
	}

	public function editProduct($product_id, $data) {
		$this->event->trigger('pre.admin.product.edit', $data);
		$collection="mongo_product";
		$product_image= array();

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image_value) {
				$product_image[]= array(
					'image'=>$product_image_value['image'],
					'sort_order'=>(int)$product_image_value['sort_order']
				);
			}
		}
		$product_download= array();
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$product_download[]= (int)$download_id;
			}
		}
		$product_category= array();
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$product_category[]= (int)$category_id;
			}
		}
		$product_tag= array();
		if (isset($data['product_tag'])) {
			foreach ($data['product_tag'] as $tag_id) {
				$product_tag[]= (int)$tag_id;
			}
		}
		$product_filter= array();
		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$product_filter[]= (int)$filter_id;
			}
		}
		$product_description= array();
		foreach ($data['product_description'] as $language_id => $value) {
			$product_description[(int)$language_id]= array(
				'language_id'=>(int)$language_id,
				'name'=>$value['name'],
				'description'=>$value['description'],
				'meta_title'=>$value['meta_title'],
				'meta_description'=>$value['meta_description'],
				'meta_keyword'=>$value['meta_keyword']
			);
		}

		$product_to_store=array();
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$product_to_store[]= (int)$store_id;
			}
		}
		$infoupdate=array('product_description'=>$product_description, 'product_category'=>$product_category, 'product_tag'=>$product_tag, 'product_filter'=>$product_filter, 'product_download'=>$product_download, 'product_image'=>$product_image, 'product_to_store'=>$product_to_store, 'model'=>$data['model'], 'sku'=>$data['sku'], 'upc'=>$data['upc'], 'ean'=>$data['ean'], 'jan'=>$data['jan'], 'isbn'=>$data['isbn'], 'mpn'=>$data['mpn'], 'location'=>$data['location'],'quantity'=>(int)$data['quantity'], 'minimum'=>(int)$data['minimum'],'subtract'=>(int)$data['subtract'], 'stock_status_id'=>(int)$data['stock_status_id'], 'date_available'=>new MongoDate(strtotime($data['date_available'])), 'manufacturer_id'=>(int)$data['manufacturer_id'], 'shipping'=>(int)$data['shipping'], 'price'=>(float)$data['price'], 'points'=>(int)$data['points'], 'weight'=>(float)$data['weight'], 'weight_class_id'=>(int)$data['weight_class_id'], 'length'=>(float)$data['length'], 'width'=>(float)$data['width'], 'height'=>(float)$data['height'], 'length_class_id'=>(int)$data['length_class_id'], 'status'=>(int)$data['status'], 'tax_class_id'=>$data['tax_class_id'], 'sort_order'=>(int)$data['sort_order'], 'updateby'=>(int)$this->user->getId(), 'classname'=>trim($data['classname']),'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s')))); 
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->update($collection,$infoupdate,$where);

		if (isset($data['image'])) {
			$infoupdate=array('image'=>$data['image']);
			$where=array('product_id'=>(int)$product_id);
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		$collection="mongo_product_related";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$where=array('related_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$where=array('product_id'=>(int)$product_id,'related_id'=>(int)$related_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('product_id'=>(int)$product_id, 'related_id'=>$related_id);
				$this->mongodb->create($collection,$newdocument); 
				$where=array('product_id'=>(int)$related_id,'related_id'=>(int)$product_id);
				$this->mongodb->delete($collection,$where); 
				$newdocument=array('product_id'=>(int)$related_id, 'related_id'=>$product_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$collection="mongo_product_attribute";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					$where=array('product_id'=>(int)$product_id, 'attribute_id'=>(int)$product_attribute['attribute_id']);
					$this->mongodb->delete($collection,$where); 

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$newdocument=array('product_id'=>(int)$product_id, 'attribute_id'=>(int)$product_attribute['attribute_id'], 'language_id'=>(int)$language_id, 'text'=>$product_attribute_description['text']);
						$this->mongodb->create($collection,$newdocument); 
					}
				}
			}
		}

		$collection="mongo_product_option";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_option_value";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$collection="mongo_product_option";
						$product_option_id=1+(int)$this->mongodb->getlastid($collection,'product_option_id');
						$newdocument=array('product_option_id'=>(int)$product_option_id, 'product_id'=>(int)$product_id, 'option_id'=>(int)$product_option['option_id'], 'value'=>'', 'required'=>(int)$product_option['required']);
						$this->mongodb->create($collection,$newdocument); 
						
						$collection="mongo_product_option_value";
						$product_option_value_id=1+(int)$this->mongodb->getlastid($collection,'product_option_value_id');
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$newdocument=array('product_option_value_id'=>(int)$product_option_value_id, 'product_option_id'=>(int)$product_option_id, 'product_id'=>(int)$product_id, 'option_id'=>(int)$product_option['option_id'], 'option_value_id'=>(int)$product_option_value['option_value_id'], 'quantity'=>(int)$product_option_value['quantity'], 'subtract'=>(int)$product_option_value['subtract'], 'price'=>(float)$product_option_value['price'], 'price_prefix'=>$product_option_value['price_prefix'], 'points'=>(int)$product_option_value['points'], 'points_prefix'=>$product_option_value['points_prefix'], 'weight'=>(float)$product_option_value['weight'], 'weight_prefix'=>(int)$product_option_value['weight_prefix']);
							$this->mongodb->create($collection,$newdocument); 
						}
					}
				} else {
					$collection="mongo_product_option";
					$product_option_id=1+(int)$this->mongodb->getlastid($collection,'product_option_id');
					$newdocument=array('product_option_id'=>(int)$product_option_id, 'product_id'=>(int)$product_id, 'option_id'=>(int)$product_option['option_id'], 'value'=>$product_option['value'], 'required'=>(int)$product_option['required']);
					$this->mongodb->create($collection,$newdocument); 
				}
			}
		}

		$collection="mongo_product_discount";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$product_discount_id=1+(int)$this->mongodb->getlastid($collection,'product_discount_id');
				$newdocument=array('product_discount_id'=>(int)$product_discount_id, 'product_id'=>(int)$product_id, 'customer_group_id'=>(int)$product_discount['customer_group_id'], 'quantity'=>(int)$product_discount['quantity'], 'priority'=>(int)$product_discount['priority'], 'price'=>(float)$product_discount['price'], 'date_start'=>new MongoDate(strtotime($product_discount['date_start'])), 'date_end'=>new MongoDate(strtotime($product_discount['date_end'])));
				$this->mongodb->create($collection,$newdocument);
			}
		}

		$collection="mongo_product_special";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$product_special_id=1+(int)$this->mongodb->getlastid($collection,'product_special_id');
				$newdocument=array('product_special_id'=>(int)$product_special_id, 'product_id'=>(int)$product_id, 'customer_group_id'=>(int)$product_special['customer_group_id'], 'priority'=>(int)$product_special['priority'], 'price'=>(float)$product_special['price'], 'date_start'=>new MongoDate(strtotime($product_special['date_start'])), 'date_end'=>new MongoDate(strtotime($product_special['date_end'])));
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$collection="mongo_product_reward";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$product_reward_id=1+(int)$this->mongodb->getlastid($collection,'product_reward_id');
				$newdocument=array('product_reward_id'=>(int)$product_reward_id, 'product_id'=>(int)$product_id, 'customer_group_id'=>(int)$customer_group_id, 'points'=>(int)$product_reward['points']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$collection="mongo_product_to_layout";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$newdocument=array('product_id'=>(int)$product_id, 'store_id'=>$store_id, 'layout_id'=>(int)$layout_id);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$collection="mongo_product_recurring";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if (isset($data['product_recurrings'])) {
			$collection="mongo_product_recurring";
			foreach ($data['product_recurrings'] as $recurring) {
				$newdocument=array('product_id'=>(int)$product_id, 'customer_group_id'=>(int)$recurring['customer_group_id'], 'recurring_id'=>(int)$recurring['recurring_id']);
				$this->mongodb->create($collection,$newdocument); 
			}
		}

		$collection="mongo_url_alias";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		if ($data['keyword']) {
			$url_alias_id=1+(int)$this->mongodb->getlastid($collection,'url_alias_id');
			$newdocument=array('url_alias_id'=>(int)$url_alias_id, 'query'=>'product_id=' . (int)$product_id, 'keyword'=>$data['keyword']);
		}

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.edit', $product_id);
	}

	public function copyProduct($product_id) {	
		$product_info = array();
		$collection="mongo_product";
		$where=array('product_id'=>(int)$product_id);
		$product_info=$this->mongodb->getBy($collection,$where);
		if ($category_info) {
			$data = array();
			$data = $product_info;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';

			$data = array_merge($data, array('product_attribute' => $this->getProductAttributes($product_id)));
			$data = array_merge($data, array('product_discount' => $this->getProductDiscounts($product_id)));
			$data = array_merge($data, array('product_option' => $this->getProductOptions($product_id)));
			$data = array_merge($data, array('product_related' => $this->getProductRelated($product_id)));
			$data = array_merge($data, array('product_reward' => $this->getProductRewards($product_id)));
			$data = array_merge($data, array('product_special' => $this->getProductSpecials($product_id)));
			$data = array_merge($data, array('product_layout' => $this->getProductLayouts($product_id)));
			$data = array_merge($data, array('product_recurrings' => $this->getRecurrings($product_id)));

			$this->addProduct($data);
		}
	}

	public function deleteProduct($product_id) {
		$this->event->trigger('pre.admin.product.delete', $product_id);
		$collection="mongo_product";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_attribute";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_discount";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_option";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_option_value";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_related";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_reward";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_special";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_to_layout";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_review";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_product_recurring";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 
		$collection="mongo_url_alias";
		$where=array('product_id'=>(int)$product_id);
		$this->mongodb->delete($collection,$where); 

		$this->cache->delete('product');

		$this->event->trigger('post.admin.product.delete', $product_id);
	}

	public function getProduct($product_id) {
		$product_info = array();
		$collection="mongo_product";
		$where=array('product_id'=>(int)$product_id);
		$product_info=$this->mongodb->getBy($collection,$where);
		if ($product_info) {
			$collection="mongo_url_alias";
			$where=array('query'=>'product_id='.(int)$product_id);
			$url_alias_info=$this->mongodb->getBy($collection,$where);
			if ($url_alias_info) {$product_info['keyword']=$url_alias_info['keyword'];}
			else {$product_info['keyword']='';}
		} 
		return $product_info;
	}

	public function getProducts($data = array()) {
		$product_data = array();
		$collection="mongo_product";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['product_description.'. (int)$this->config->get('config_language_id').'.name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		if (isset($data['filter_category_id']) && !is_null($data['filter_category_id'])) {			
			$where['product_category']=(int)$data['filter_category_id'];
		}
		if (!empty($data['filter_model'])) {
			$where['model']=new MongoRegex('/^'.$data['filter_model'].'/');
		}
		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$where['price']=(float)$data['filter_price'];
		}		
		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$where['quantity']=(int)$data['filter_quantity'];
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {			
			$where['status']=(int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
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
			'model',
			'price',
			'quantity',
			'status',
			'date_added',
			'date_modified',
			'sort_order'
		);	
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$orderby = $data['sort'];
		} else {
			$orderby = 'product_description.'. (int)$this->config->get('config_language_id').'.name';
		}
		if ($orderby == 'name') $orderby = 'product_description.'. (int)$this->config->get('config_language_id').'.name';	

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$order[$orderby] = -1;
		} else {
			$order[$orderby]= 1;
		} 
		$product_data = $this->mongodb->get($collection,$where, $order, $start, $limit);
		return $product_data;
	}

	public function getTotalProducts($data = array()) {
		$collection="mongo_product";
		$where=array();
		if (!empty($data['filter_name'])) {
			$where['name']=new MongoRegex('/^'.$data['filter_name'].'/');
		}
		if (isset($data['filter_category_id']) && !is_null($data['filter_category_id'])) {			
			$where['product_category']=(int)$data['filter_category_id'];
		}
		if (!empty($data['filter_model'])) {
			$where['model']=new MongoRegex('/^'.$data['filter_model'].'/');
		}
		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$where['price']=new MongoRegex('/^'.$data['filter_price'].'/');
		}		
		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$where['quantity']=(int)$data['filter_quantity'];
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {			
			$where['status']=(int)$data['filter_status'];
		}
		if (!empty($data['filter_date_added'])) {
			$where['date_added']=array('$gte'=>new MongoDate(strtotime($data['filter_date_added'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_added'].' 23:59:59')));
		}
		if (!empty($data['filter_date_modified'])) {
			$where['date_modified']=array('$gte'=>new MongoDate(strtotime($data['filter_date_modified'].' 00:00:01')), '$lte'=>new MongoDate(strtotime($data['filter_date_modified'].' 23:59:59')));
		}
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getProductsByCategoryId($category_id) {
		$product_data = array();
		$collection="mongo_product";
		$where=array('category_id'=>(int)$category_id);
		$order=array('name'=> 1);
		$product_data = $this->mongodb->getall($collection,$where, $order);
		return $product_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();
		$collection='mongo_product_attribute';
		$product_attribute_query_data= array();
		$keys = array('attribute_id'=>1);
		$initial = array("count" => 0);
		$reduce = 'function (obj, prev) {}';
		$condition = array('condition' => array('product_id' =>(int)$product_id));
		$product_attribute_query_data=$this->mongodb->getgroupby($collection, $keys, $initial, $reduce, $condition);
		
		foreach ($product_attribute_query_data as $product_attribute) {
			$product_attribute_description_data = array();
			$product_attribute_description_query_data = array();
			$where=array('product_id'=>(int)$product_id, 'attribute_id'=>(int)$product_attribute['attribute_id']);
			$order=array(); 
			$product_attribute_description_query_data = $this->mongodb->getall($collection,$where, $order);
			foreach ($product_attribute_description_query_data as $product_attribute_description) { 
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			} 
			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}
		return $product_attribute_data;
	}
	
	public function getOption($option_id) {
		$collection="mongo_option";
		$where=array('option_id'=>(int)$option_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();
		$collection='mongo_product_option';
		$product_option_query_data= array();
		$where=array('product_id'=>(int)$product_id);
		$order=array();
		$product_option_query_data = $this->mongodb->getall($collection,$where, $order);
			
		foreach ($product_option_query_data as $product_option) {
			$product_option_value_data = array();
			$collection='mongo_product_option_value';
			$product_option_value_query_data= array();	
			$where=array('product_option_id'=>(int)$product_option['product_option_id']);
			$order=array();
			$product_option_value_query_data = $this->mongodb->getall($collection,$where, $order);
		
			foreach ($product_option_value_query_data as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			} 
			$product_option_description=$this->getOption($product_option['option_id']);
			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option_description['option_description'][$this->config->get('config_language_id')]['name'],
				'type'                 => $product_option_description['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}
		return $product_option_data;
	}

	public function getProductDiscounts($product_id) {
		$product_data = array();
		$collection="mongo_product_discount";
		$where=array('product_id'=>(int)$product_id);
		$order=array('quantity'=> 1, 'priority'=> 1, 'price'=> 1);
		$product_data = $this->mongodb->getall($collection,$where, $order);
		return $product_data;
	}

	public function getProductSpecials($product_id) {
		$product_data = array();
		$collection="mongo_product_special";
		$where=array('product_id'=>(int)$product_id);
		$order=array('quantity'=> 1, 'priority'=> 1, 'price'=> 1);
		$product_data = $this->mongodb->getall($collection,$where, $order);
		return $product_data;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();
		$product_data = array();
		$collection="mongo_product_reward";
		$where=array('product_id'=>(int)$product_id);
		$order=array();
		$product_data = $this->mongodb->getall($collection,$where, $order);
		
		foreach ($product_data as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}
		return $product_reward_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();
		$product_data = array();
		$collection="mongo_product_to_layout";
		$where=array('product_id'=>(int)$product_id);
		$order=array();
		$product_data = $this->mongodb->getall($collection,$where, $order);
		
		foreach ($product_data as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}
		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();
		$product_data = array();
		$collection="mongo_product_related";
		$where=array('product_id'=>(int)$product_id);
		$order=array();
		$product_data = $this->mongodb->getall($collection,$where, $order);
		
		foreach ($product_data as $result) {
			$product_related_data[] = $result['related_id'];
		}
		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$product_data = array();
		$collection="mongo_product_recurring";
		$where=array('product_id'=>(int)$product_id);
		$order=array();
		$product_data = $this->mongodb->getall($collection,$where, $order);
		return $product_data;
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$product_data= array();
		$collection="mongo_product";
		$where=array('tax_class_id'=>(int)$tax_class_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$product_data= array();
		$collection="mongo_product";
		$where=array('stock_status_id'=>(int)$stock_status_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$product_data= array();
		$collection="mongo_product";
		$where=array('weight_class_id'=>(int)$weight_class_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$product_data= array();
		$collection="mongo_product";
		$where=array('length_class_id'=>(int)$length_class_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByDownloadId($download_id) {
		$product_data= array();
		$collection="mongo_product_to_download";
		$where=array('download_id'=>(int)$download_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$product_data= array();
		$collection="mongo_product";
		$where=array('manufacturer_id'=>(int)$manufacturer_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$product_data= array();
		$collection="mongo_product_attribute";
		$where=array('attribute_id'=>(int)$attribute_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByOptionId($option_id) {
		$product_data= array();
		$collection="mongo_product_option";
		$where=array('option_id'=>(int)$option_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$product_data= array();
		$collection="mongo_product_recurring";
		$where=array('recurring_id'=>(int)$recurring_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$product_data= array();
		$collection="mongo_product_to_layout";
		$where=array('layout_id'=>(int)$layout_id);
		$product_data=$this->mongodb->gettotal($collection,$where);
		return $product_data;
	}
}