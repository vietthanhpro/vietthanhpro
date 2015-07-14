<?php
class ModelLocalisationCurrency extends Model {
	public function addCurrency($data) {	
		$collection="mongo_currency";
		$language_id=1+(int)$this->mongodb->getlastid($collection,'currency_id');
		$where=array('currency_id'=>(int)$currency_id, 'title'=>$data['title'], 'code'=>$data['code'], 'symbol_left'=>$data['symbol_left'], 'symbol_right'=>$data['symbol_right'], 'decimal_place'=>$data['decimal_place'], 'value'=>(float)$data['value'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$this->mongodb->create($collection,$where); 
		/*$this->db->query("INSERT INTO " . DB_PREFIX . "currency SET title = '" . $this->db->escape($data['title']) . "', code = '" . $this->db->escape($data['code']) . "', symbol_left = '" . $this->db->escape($data['symbol_left']) . "', symbol_right = '" . $this->db->escape($data['symbol_right']) . "', decimal_place = '" . $this->db->escape($data['decimal_place']) . "', value = '" . $this->db->escape($data['value']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW()");
		*/
		if ($this->config->get('config_currency_auto')) {
			$this->refresh(true);
		}

		$this->cache->delete('currency');
	}

	public function editCurrency($currency_id, $data) {	
		$collection="mongo_currency";
		$infoupdate=array('currency_id'=>(int)$currency_id, 'title'=>$data['title'], 'code'=>$data['code'], 'symbol_left'=>$data['symbol_left'], 'symbol_right'=>$data['symbol_right'], 'decimal_place'=>$data['decimal_place'], 'value'=>(float)$data['value'], 'status'=>(int)$data['status'], 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
		$where=array('currency_id'=>(int)$currency_id);
		$this->mongodb->update($collection,$infoupdate,$where); 
		/*$this->db->query("UPDATE " . DB_PREFIX . "currency SET title = '" . $this->db->escape($data['title']) . "', code = '" . $this->db->escape($data['code']) . "', symbol_left = '" . $this->db->escape($data['symbol_left']) . "', symbol_right = '" . $this->db->escape($data['symbol_right']) . "', decimal_place = '" . $this->db->escape($data['decimal_place']) . "', value = '" . $this->db->escape($data['value']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE currency_id = '" . (int)$currency_id . "'");
		*/
		$this->cache->delete('currency');
	}

	public function deleteCurrency($currency_id) {
		$collection="mongo_currency";
		$where=array('currency_id'=>(int)$currency_id);
		$this->mongodb->delete($collection,$where); 
		/*$this->db->query("DELETE FROM " . DB_PREFIX . "currency WHERE currency_id = '" . (int)$currency_id . "'");
		*/
		$this->cache->delete('currency');
	}

	public function getCurrency($currency_id) {
		/*$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE currency_id = '" . (int)$currency_id . "'");
		return $query->row;
		*/		
		$collection="mongo_currency";
		$where=array('currency_id'=>(int)$currency_id);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getCurrencyByCode($currency) {
		/*$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($currency) . "'");
		return $query->row;
		*/
		$collection="mongo_currency";
		$where=array('code'=>(string)$currency);
		return $this->mongodb->getBy($collection,$where);
	}

	public function getCurrencies($data = array()) {
		if ($data) {
			/*
			$sql = "SELECT * FROM " . DB_PREFIX . "currency";

			$sort_data = array(
				'title',
				'code',
				'value',
				'date_modified'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY title";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			//$query = $this->db->query($sql);

			return $query->rows;
			*/
			$collection="mongo_currency";
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
				'title',
				'code',
				'value',
				'date_modified'
			);
	
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$orderby = $data['sort'];
			} else {
				$orderby = "title";
			}
	
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$order[$orderby] = -1;
			} else {
				$order[$orderby]= 1;
			}
			return $this->mongodb->get($collection,$where, $order, $start, $limit);
		} else {
			$currency_data = $this->cache->get('currency');

			if (!$currency_data) {
				$currency_data = array();
				$collection="mongo_currency";
				$where=array();
				$order=array('title'=> 1);
				$currency_list=$this->mongodb->getall($collection,$where, $order);
				foreach ($currency_list as $result) {
					$currency_data[$result['code']] = array(
						'currency_id'   => $result['currency_id'],
						'title'         => $result['title'],
						'code'          => $result['code'],
						'symbol_left'   => $result['symbol_left'],
						'symbol_right'  => $result['symbol_right'],
						'decimal_place' => $result['decimal_place'],
						'value'         => $result['value'],
						'status'        => $result['status'],
						'date_modified' => $result['date_modified']
					);
				}
				/*$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency ORDER BY title ASC");

				foreach ($query->rows as $result) {
					$currency_data[$result['code']] = array(
						'currency_id'   => $result['currency_id'],
						'title'         => $result['title'],
						'code'          => $result['code'],
						'symbol_left'   => $result['symbol_left'],
						'symbol_right'  => $result['symbol_right'],
						'decimal_place' => $result['decimal_place'],
						'value'         => $result['value'],
						'status'        => $result['status'],
						'date_modified' => $result['date_modified']
					);
				}
				*/
				$this->cache->set('currency', $currency_data);
			}

			return $currency_data;
		}
	}

	public function refresh($force = false) {
		
		$collection="mongo_currency";
		if (extension_loaded('curl')) {
			$data = array();
			if ($force) {
				/*$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "'");
				*/
				$where=array('code'=>array('$ne'=> (string)$this->db->escape($this->config->get('config_currency')))); //print_r($where);
				$order = array();
				$currency_list=$this->mongodb->getall($collection,$where, $order);
			} else {
				//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE code != '" . $this->db->escape($this->config->get('config_currency')) . "' AND date_modified < '" .  $this->db->escape(date('Y-m-d H:i:s', strtotime('-1 day'))) . "'");
				$where=array('code'=>array('$ne'=> (string)$this->config->get('config_currency')),'date_modified'=>array('$lt'=> (string)date('Y-m-d H:i:s', strtotime('-1 day'))));
				$order = array();
				$currency_list=$this->mongodb->getall($collection,$where, $order);
			}
			//foreach ($query->rows as $result) {
			foreach ($currency_list as $result) {
				$data[] = $this->config->get('config_currency') . $result['code'] . '=X';
			}

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, 'http://download.finance.yahoo.com/d/quotes.csv?s=' . implode(',', $data) . '&f=sl1&e=.csv');
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);

			$content = curl_exec($curl);

			curl_close($curl);

			$lines = explode("\n", trim($content));

			foreach ($lines as $line) {
				$currency = utf8_substr($line, 4, 3);
				$value = utf8_substr($line, 11, 6);

				if ((float)$value) {

					//$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '" . (float)$value . "', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($currency) . "'");					
					$infoupdate=array('value'=>(float)$value, 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
					$where=array('code'=>(string)$currency);
					$this->mongodb->update($collection,$infoupdate,$where); 
				}
			}

			//$this->db->query("UPDATE " . DB_PREFIX . "currency SET value = '1.00000', date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE code = '" . $this->db->escape($this->config->get('config_currency')) . "'");				
					$infoupdate=array('value'=>(float)('1.00000'), 'date_modified'=>new MongoDate(strtotime(date('Y-m-d H:i:s'))));
					$where=array('code'=>(string)$this->config->get('config_currency'));
					$this->mongodb->update($collection,$infoupdate,$where); 

			$this->cache->delete('currency');
		}
	}

	public function getTotalCurrencies() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "currency");
		//return $query->row['total'];
		$collection="mongo_currency";$currency_data= array();
		$where=array();
		$currency_data=$this->mongodb->gettotal($collection,$where);
		return $currency_data;
	}
}