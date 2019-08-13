<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
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

	protected function connection_data($connection_data) {

		$api_url = $api_key = $list_value = "";
		$list_ids = array();
		$form_id = 0;

		foreach ($connection_data as $connection) {
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
					end($acct_listid); // go to the last item, which should be the list ID
					$list_ids[] = (int)current($acct_listid);
				}
			}

			$form_value = trim($connection["form_value"], "\"");
			if ($form_value) {
				// example form saved: "mthommes6.activehosted.com-1269"
				$acct_formid = explode("-", $form_value);
				$form_id = (int)$acct_formid[1];
			}

			break;
		}

		return array(
			"data" => $connection_data,
			"api_url" => $api_url,
			"api_key" => $api_key,
			"list_ids" => $list_ids,
			"form_id" => $form_id,
		);
	}

	protected function _prepareForm()
	{
		$magento_form = new Varien_Data_Form();
		$this->setForm($magento_form);

		$fieldset = $magento_form->addFieldset('subscriptions_form', array('legend'=>Mage::helper('subscriptions')->__('Choose Form (To use Opt-in settings)')));

		$connection_ = Mage::registry('subscriptions_data')->getData();
//$this->dbg($connection_,1);

		$connection = $this->connection_data(array($connection_));
//$this->dbg($connection);

		$forms_ = array();

		$forms_[] = array(
			"value" => $connection_["account_url"] . "-" . "0",
			"label" => Mage::helper('subscriptions')->__("(Optional) Select a form..."),
		);

		if ($connection) {

			$api_url = $connection["api_url"];
			$api_key = $connection["api_key"];

			$ac = new ActiveCampaign($api_url, $api_key);

			$forms = $ac->api("form/getforms");
			$forms = get_object_vars($forms);

			foreach ($forms as $k => $form) {
				if (is_int($k)) {
					// avoid "result_code", "result_message", etc items
					$form = get_object_vars($form);

					$form__ = array(
						"value" => $connection_["account_url"] . "-" . $form["id"],
						"label" => $form["name"],
					);

					foreach ($form["lists"] as $listid) {
						if (in_array($listid, $connection["list_ids"])) {
							// only add to main array if this form is associated with the list(s) they chose
							$forms_[] = $form__;
							continue(2);
						}
					}
				}
			}
//$this->dbg($forms_,1);

		}

		// hidden field that stores all of the forms from the install (so we can reference data from them later, based on what they choose).
		$fieldset->addField('forms', 'hidden', array(
			'label'     => Mage::helper('subscriptions')->__('Forms'),
			'name'      => 'forms',
		));

		$fieldset->addField('form_value', 'select', array(
			'label'     => Mage::helper('subscriptions')->__('Forms'),
			'name'      => 'form_value',
			'values'    => $forms_,
		));

		if ( Mage::getSingleton('adminhtml/session')->getSubscriptionsData() ) {
			$data = Mage::getSingleton('adminhtml/session')->getSubscriptionsData();
			Mage::getSingleton('adminhtml/session')->setSubscriptionsData(null);
		}
		elseif ( Mage::registry('subscriptions_data') ) {
			$data = Mage::registry('subscriptions_data')->getData();
		}

		$data["forms"] = json_encode($forms_);
		$data["form_value"] = json_decode($data["form_value"]);
		$magento_form->setValues($data);

		return parent::_prepareForm();

	}

}