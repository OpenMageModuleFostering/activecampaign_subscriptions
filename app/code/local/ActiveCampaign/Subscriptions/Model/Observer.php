<?php

// public side stuff - happens when registering or modifying account

define("ACTIVECAMPAIGN_URL", "");
define("ACTIVECAMPAIGN_API_KEY", "");
require_once(Mage::getBaseDir() . "/app/code/local/ActiveCampaign/Subscriptions/activecampaign-api-php/ActiveCampaign.class.php");

class ActiveCampaign_Subscriptions_Model_Observer {

	protected function dbg($var, $continue = 0, $element = "pre")
	{
	  echo "<" . $element . ">";
	  echo "Vartype: " . gettype($var) . "\n";
	  if ( is_array($var) )
	  {
	  	echo "Elements: " . count($var) . "\n\n";
	  }
	  elseif ( is_string($var) )
	  {
			echo "Length: " . strlen($var) . "\n\n";
	  }
	  print_r($var);
	  echo "</" . $element . ">";
		if (!$continue) exit();
	}

	public function connection_data() {
		// get saved API connections
		$collection = Mage::getModel("subscriptions/subscriptions")->getCollection();
		$connection_data = $collection->getData();
//$this->dbg($collection_data);

		$api_url = $api_key = $list_value = "";
		$list_ids = array();

		foreach ($connection_data as $connection) {
			if ((int)$connection["status"] == 1) {
				// find first one that is enabled
				$api_url = $connection["api_url"];
				$api_key = $connection["api_key"];
				$list_value = $connection["list_value"];
				if ($list_value) {
					// example for single list saved: ["mthommes6.activehosted.com-13"]
					// example for multiple lists saved: ["mthommes6.activehosted.com-5","mthommes6.activehosted.com-13"]
					$list_values = json_decode($list_value);
					foreach ($list_values as $acct_listid) {
						// IE: mthommes6.activehosted.com-13
						$acct_listid = explode("-", $acct_listid);
						$list_ids[] = (int)$acct_listid[1];
					}
				}
				break;
			}
		}

		return array(
			"data" => $connection_data,
			"api_url" => $api_url,
			"api_key" => $api_key,
			"list_ids" => $list_ids,
		);
	}

	public function register_subscribe(Varien_Event_Observer $observer) {

		// called when they initially register as a new customer

		$customer = $observer->getCustomer();
		$customer_data = $customer->getData();
//$this->dbg($customer_data);

		if (isset($customer_data["is_subscribed"]) && (int)$customer_data["is_subscribed"]) {

			$connection = $this->connection_data();

			$customer_first_name = $customer_data["firstname"];
			$customer_last_name = $customer_data["lastname"];
			$customer_email = $customer_data["email"];
			$group_id = $customer_data["group_id"];
			$store_id = $customer_data["store_id"];

			if ($connection["api_url"] && $connection["api_key"] && $connection["list_ids"]) {

				$ac = new ActiveCampaign($connection["api_url"], $connection["api_key"]);
				$test_connection = $ac->credentials_test();

				if ($test_connection) {

					$subscriber = array(
						"email" => $customer_email,
						"first_name" => $customer_first_name,
						"last_name" => $customer_last_name,
					);

					// add lists
					foreach ($connection["list_ids"] as $list_id) {
						$subscriber["p[{$list_id}]"] = $list_id;
						$subscriber["status[{$list_id}]"] = 1;
					}

					$subscriber_request = $ac->api("subscriber/sync?service=magento", $subscriber);

					if ((int)$subscriber_request->success) {
						// successful request
						//$subscriber_id = (int)$subscriber_request->subscriber_id;
					}
					else {
						// request failed
						//print_r($subscriber_request->error);
						//exit();
					}
				}
			}

		}

	}

	public function edit_subscribe(Varien_Event_Observer $observer) {

		// called when they update their profile (already registered as a customer)

		$customer = $observer->getCustomer();
		$customer_data = $customer->getData();
//$this->dbg($customer_data);

		$is_subscribed = (int)$customer_data["is_subscribed"];
		$list_status = ($is_subscribed) ? 1 : 2;

		$connection = $this->connection_data();

		$customer_first_name = $customer_data["firstname"];
		$customer_last_name = $customer_data["lastname"];
		$customer_email = $customer_data["email"];
		$group_id = $customer_data["group_id"];
		$store_id = $customer_data["store_id"];

		if ($connection["api_url"] && $connection["api_key"] && $connection["list_ids"]) {

				$ac = new ActiveCampaign($connection["api_url"], $connection["api_key"]);
				$test_connection = $ac->credentials_test();

				if ($test_connection) {

					$subscriber = array(
						"email" => $customer_email,
						"first_name" => $customer_first_name,
						"last_name" => $customer_last_name,
					);

					// add lists
					foreach ($connection["list_ids"] as $list_id) {
						$subscriber["p[{$list_id}]"] = $list_id;
						$subscriber["status[{$list_id}]"] = $list_status;
					}

					$subscriber_request = $ac->api("subscriber/sync?service=magento", $subscriber);

					if ((int)$subscriber_request->success) {
						// successful request
						//$subscriber_id = (int)$subscriber_request->subscriber_id;
					}
					else {
						// request failed
						//print_r($subscriber_request->error);
						//exit();
					}

				}

		}

	}

}

?>