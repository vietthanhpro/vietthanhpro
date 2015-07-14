<?php
class ModelCatalogReview extends Model {
	public function addReview($product_id, $data) {
		$this->event->trigger('pre.review.add', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "review SET author = '" . $this->db->escape($data['name']) . "', customer_id = '" . (int)$this->customer->getId() . "', product_id = '" . (int)$product_id . "', text = '" . $this->db->escape($data['text']) . "', rating = '" . (int)$data['rating'] . "', date_added = NOW()");
		//$review_id = $this->db->getLastId();
		$collection="mongo_review";
		$review_id=1+(int)$this->mongodb->getlastid($collection,'review_id');
		$where=array('review_id'=>(int)$review_id, 'customer_id'=>(int)$this->customer->getId(), 'author'=>$data['name'], 'product_id'=>(int)$product_id, 'text'=>$data['text'], 'rating'=>(int)$data['rating'], 'status'=>0, 'date_added'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))), 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 

		if ($this->config->get('config_review_mail')) {
			$this->load->language('mail/review');
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($product_id);

			$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

			$message  = $this->language->get('text_waiting') . "\n";
			$message .= sprintf($this->language->get('text_product'), $this->db->escape(strip_tags($product_info['name']))) . "\n";
			$message .= sprintf($this->language->get('text_reviewer'), $this->db->escape(strip_tags($data['name']))) . "\n";
			$message .= sprintf($this->language->get('text_rating'), $this->db->escape(strip_tags($data['rating']))) . "\n";
			$message .= $this->language->get('text_review') . "\n";
			$message .= $this->db->escape(strip_tags($data['text'])) . "\n\n";

			$mail = new Mail($this->config->get('config_mail'));
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($this->config->get('config_name'));
			$mail->setSubject($subject);
			$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			$mail->send();

			// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_mail_alert'));

			foreach ($emails as $email) {
				if ($email && preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}
		}

		$this->event->trigger('post.review.add', $review_id);
	}

	public function getReviewsByProductId($product_id, $start = 0, $limit = 20) {
		if ($start < 0) {			$start = 0;		}
		if ($limit < 1) {			$limit = 20;		}
		/*$query = $this->db->query("SELECT r.review_id, r.author, r.rating, r.text, p.product_id, pd.name, p.price, p.image, r.date_added FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY r.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);
		return $query->rows;*/
		$review_data = array();
		$collection="mongo_review";
		$where=array();
		$where['product_id']=(int)$product_id;
		$where['status']=1;
		$order=array();
		$orderby = 'date_added';
		$order[$orderby]= -1;
		$review_result = $this->mongodb->get($collection,$where, $order, $start, $limit);
		$review_list=$review_result['results'];
		foreach ($review_list as $review_list_info) {
			$product_info=$this->getProduct($review_list_info['product_id']);
			if ($product_info) {
				$date_added=(array)$product_info['date_added'];
				$review_data[] = array(
					'review_id' => $review_list_info['review_id'],
					'author' => $review_list_info['author'],
					'rating' => $review_list_info['rating'],
					'text' => $review_list_info['text'],
					'product_id' => $product_info['product_id'],
					//'date_added' => $product_info['date_added'],
					'date_added'     => date($this->language->get('date_format_short'),$date_added['sec']),
					'name' => $product_info['product_description'][(int)$this->config->get('config_language_id')]['name'],
					'price' => $product_info['price'],
					'image' => $product_info['image'],
				);
			}
		}
		$data_results['results']=$review_data;
		$data_results['count']=$review_result['count'];
		return $data_results;
	}
/*
	public function getTotalReviewsByProductId($product_id) {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r LEFT JOIN " . DB_PREFIX . "product p ON (r.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND p.date_available <= NOW() AND p.status = '1' AND r.status = '1' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}*/

	public function getProduct($product_id) {
		$product_info = array();
		$collection="mongo_product";
		$where=array('product_id'=>(int)$product_id);
		$where=array('status'=>1);
		$product_info=$this->mongodb->getBy($collection,$where);
		return $product_info;
	}
}