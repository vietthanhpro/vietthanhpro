<?php
class ModelAccountAddress extends Model {
	public function addAddress($data) {
		$this->event->trigger('pre.customer.add.address', $data);

		//$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$this->customer->getId() . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "'");
		//$address_id = $this->db->getLastId();
		$collection="mongo_address";
		$address_id=1+(int)$this->mongodb->getlastid($collection,'address_id');
		$newdocument=array('address_id'=>(int)$address_id, 'customer_id'=>(int)$this->customer->getId(), 'firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'company'=>$data['company'], 'address_1'=>$data['address_1'], 'address_2'=>$data['address_2'], 'city'=>$data['city'], 'postcode'=>$data['postcode'], 'country_id'=>(int)$data['country_id'], 'zone_id'=>(int)$data['zone_id'], 'custom_field'=>isset($data['custom_field']) ? serialize($data['custom_field']) : '');
		$this->mongodb->create($collection,$newdocument); 

		if (!empty($data['default'])) {
			//$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
			$collection="mongo_customer";
			$infoupdate=array('address_id'=>(int)$address_id);
			$where=array('customer_id'=>(int)$this->customer->getId());
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		$this->event->trigger('post.customer.add.address', $address_id);

		return $address_id;
	}

	public function editAddress($address_id, $data) {
		$this->event->trigger('pre.customer.edit.address', $data);

		//$this->db->query("UPDATE " . DB_PREFIX . "address SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', city = '" . $this->db->escape($data['city']) . "', zone_id = '" . (int)$data['zone_id'] . "', country_id = '" . (int)$data['country_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? serialize($data['custom_field']) : '') . "' WHERE address_id  = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		$collection="mongo_address";
		$newdocument=array('firstname'=>$data['firstname'], 'lastname'=>$data['lastname'], 'company'=>$data['company'], 'address_1'=>$data['address_1'], 'address_2'=>$data['address_2'], 'city'=>$data['city'], 'postcode'=>$data['postcode'], 'country_id'=>(int)$data['country_id'], 'zone_id'=>(int)$data['zone_id'], 'custom_field'=>isset($data['custom_field']) ? serialize($data['custom_field']) : '');
		$where=array('address_id'=>(int)$address_id,'customer_id'=>(int)$this->customer->getId());
		$this->mongodb->update($collection,$infoupdate,$where);

		if (!empty($data['default'])) {
			//$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$this->customer->getId() . "'");
			$collection="mongo_customer";
			$infoupdate=array('address_id'=>(int)$address_id);
			$where=array('customer_id'=>(int)$this->customer->getId());
			$this->mongodb->update($collection,$infoupdate,$where);
		}

		$this->event->trigger('post.customer.edit.address', $address_id);
	}

	public function deleteAddress($address_id) {
		$this->event->trigger('pre.customer.delete.address', $address_id);

		//$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		$collection="mongo_address";
		$where=array('address_id'=>(int)$address_id, 'customer_id'=>(int)$this->customer->getId());
		$this->mongodb->delete($collection,$where); 

		$this->event->trigger('post.customer.delete.address', $address_id);
	}

	public function getAddress($address_id) {
		//$address_query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "address WHERE address_id = '" . (int)$address_id . "' AND customer_id = '" . (int)$this->customer->getId() . "'");
		$address_query_data = array();
		$collection="mongo_address";
		$where=array('address_id'=>(int)$address_id, 'customer_id'=>(int)$this->customer->getId());
		$address_query_data=$this->mongodb->getBy($collection,$where);

		//if ($address_query->num_rows) {
		if ($address_query_data) {
			//$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$address_query->row['country_id'] . "'");
			$country_query_data = array();
			$collection="mongo_country";
			$where=array('country_id'=>(int)$address_query_data['country_id']);
			$country_query_data=$this->mongodb->getBy($collection,$where);

			//if ($country_query->num_rows) {
			if ($country_query_data) {/*
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];*/
				$country = $country_query_data['name'];
				$iso_code_2 = $country_query_data['iso_code_2'];
				$iso_code_3 = $country_query_data['iso_code_3'];
				$address_format = $country_query_data['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			//$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$address_query->row['zone_id'] . "'");
			$zone_query_data = array();
			$collection="mongo_zone";
			$where=array('zone_id'=>(int)$address_query_data['zone_id']);
			$zone_query_data=$this->mongodb->getBy($collection,$where);

			//if ($zone_query->num_rows) {
			if ($zone_query_data) {/*
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];*/
				$zone = $zone_query_data['name'];
				$zone_code = $zone_query_data['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$address_data = array(/*
				'address_id'     => $address_query->row['address_id'],
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'country_id'     => $address_query->row['country_id'],
				'custom_field'   => unserialize($address_query->row['custom_field']),*/
				'address_id'     => $address_query_data['address_id'],
				'firstname'      => $address_query_data['firstname'],
				'lastname'       => $address_query_data['lastname'],
				'company'        => $address_query_data['company'],
				'address_1'      => $address_query_data['address_1'],
				'address_2'      => $address_query_data['address_2'],
				'postcode'       => $address_query_data['postcode'],
				'city'           => $address_query_data['city'],
				'zone_id'        => $address_query_data['zone_id'],
				'country_id'     => $address_query_data['country_id'],
				'custom_field'   => unserialize($address_query_data['custom_field']),
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format
			);

			return $address_data;
		} else {
			return false;
		}
	}

	public function getAddresses() {
		$address_data = array();

		//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		$query_data = array();
		$collection="mongo_address";
		$where=array('customer_id'=>(int)$this->customer->getId());
		$order=array();
		$query_data=$this->mongodb->getall($collection,$where,$order);

		//foreach ($query->rows as $result) {
		foreach ($query_data as $result) {
			//$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$result['country_id'] . "'");
			$country_query_data = array();
			$collection="mongo_country";
			$where=array('country_id'=>(int)$result['country_id']);
			$country_query_data=$this->mongodb->getBy($collection,$where);

			/*if ($country_query->num_rows) {
				$country = $country_query->row['name'];
				$iso_code_2 = $country_query->row['iso_code_2'];
				$iso_code_3 = $country_query->row['iso_code_3'];
				$address_format = $country_query->row['address_format'];*/
			if ($country_query_data) {
				$country = $country_query_data['name'];
				$iso_code_2 = $country_query_data['iso_code_2'];
				$iso_code_3 = $country_query_data['iso_code_3'];
				$address_format = $country_query_data['address_format'];
			} else {
				$country = '';
				$iso_code_2 = '';
				$iso_code_3 = '';
				$address_format = '';
			}

			//$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$result['zone_id'] . "'");
			$zone_query_data = array();
			$collection="mongo_zone";
			$where=array('zone_id'=>(int)$result['zone_id']);
			$zone_query_data=$this->mongodb->getBy($collection,$where);

			if ($zone_query_data) {
			/*if ($zone_query->num_rows) {
				$zone = $zone_query->row['name'];
				$zone_code = $zone_query->row['code'];*/
				$zone = $zone_query_data['name'];
				$zone_code = $zone_query_data['code'];
			} else {
				$zone = '';
				$zone_code = '';
			}

			$address_data[$result['address_id']] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'address_1'      => $result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city'           => $result['city'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $zone,
				'zone_code'      => $zone_code,
				'country_id'     => $result['country_id'],
				'country'        => $country,
				'iso_code_2'     => $iso_code_2,
				'iso_code_3'     => $iso_code_3,
				'address_format' => $address_format,
				'custom_field'   => unserialize($result['custom_field'])

			);
		}

		return $address_data;
	}

	public function getTotalAddresses() {
		//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$this->customer->getId() . "'");
		//return $query->row['total'];
		$address_data = array();
		$collection="mongo_address";
		$where=array('customer_id'=>(int)$this->customer->getId());
		$address_data=$this->mongodb->gettotal($collection,$where);
		return $address_data;
	}
}