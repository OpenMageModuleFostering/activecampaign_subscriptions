<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions_Edit_Tab_List extends Mage_Adminhtml_Block_Widget_Form
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

  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('subscriptions_form', array('legend'=>Mage::helper('subscriptions')->__('Choose Lists (To add new customers to)')));

			$connection = Mage::registry('subscriptions_data')->getData();
//$this->dbg($connection,1);

			$lists_ = array();

			$lists_[] = array(
				"value" => $connection["account_url"] . "-" . "0",
				"label" => Mage::helper('subscriptions')->__("Please select one or more lists..."),
			);

			if ($connection) {
				$api_url = $connection["api_url"];
				$api_key = $connection["api_key"];

				$ac = new ActiveCampaign($api_url, $api_key);

				// get lists from AC

				$lists = $ac->api("list/list?ids=all&full=0");
//$this->dbg($lists);
				$lists = get_object_vars($lists);

				foreach ($lists as $k => $list) {
					if (is_int($k)) {
						// avoid "result_code", "result_message", etc items
						$list = get_object_vars($list);
						$list__ = array(
							"value" => $connection["account_url"] . "-" . $list["id"],
							"label" => $list["name"],
						);
						$lists_[] = $list__;
					}
				}
//$this->dbg($lists_);
			}

			// hidden field that stores all of the lists from the install (so we can reference data from them later, based on what they choose).
      $fieldset->addField('lists', 'hidden', array(
          'label'     => Mage::helper('subscriptions')->__('Lists'),
          'name'      => 'lists',
      ));

      $fieldset->addField('list_value', 'multiselect', array(
          'label'     => Mage::helper('subscriptions')->__('Lists'),
          'name'      => 'list_value',
          'values'    => $lists_,
          /*'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('subscriptions')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('subscriptions')->__('Disabled'),
              ),
          ),*/
      ));

      if ( Mage::getSingleton('adminhtml/session')->getSubscriptionsData() ) {
      		$data = Mage::getSingleton('adminhtml/session')->getSubscriptionsData();
      		$data["lists"] = json_encode($lists_);
      		$data["list_value"] = json_decode($data["list_value"]);
          $form->setValues($data);
          Mage::getSingleton('adminhtml/session')->setSubscriptionsData(null);
      }
      elseif ( Mage::registry('subscriptions_data') ) {
      		$data = Mage::registry('subscriptions_data')->getData();
      		$data["lists"] = json_encode($lists_);
      		$data["list_value"] = json_decode($data["list_value"]);
          $form->setValues($data);
      }
      return parent::_prepareForm();
  }
}