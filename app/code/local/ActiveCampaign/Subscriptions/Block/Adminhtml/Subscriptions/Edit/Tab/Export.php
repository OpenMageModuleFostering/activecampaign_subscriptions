<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions_Edit_Tab_Export extends Mage_Adminhtml_Block_Widget_Form
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
      $fieldset = $form->addFieldset('subscriptions_form', array('legend'=>Mage::helper('subscriptions')->__('Export Newsletter Subscribers To ActiveCampaign')));

			// gets all customers
			/*
			$customer_collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('*');
			$customers = array();
			foreach ($customer_collection as $customer) {
				$customers[] = $customer->toArray();
			}
			*/
//$this->dbg($customers);

			// gets all customers that are subscribed to the newsletter
			$subscribers = Mage::getResourceModel('newsletter/subscriber_collection')->showStoreInfo()->showCustomerInfo()->getData();
//$this->dbg($subscribers);

			$connection = Mage::registry('subscriptions_data')->getData();
//$this->dbg($connection);

			if ($connection) {
				$api_url = $connection["api_url"];
				$api_key = $connection["api_key"];

				$ac = new ActiveCampaign($api_url, $api_key);


			}

      $fieldset->addField('export_note', 'note', array(
          'text'     => Mage::helper('subscriptions')->__('Check the box below, then click the Save Connection button to export ' . count($subscribers) . ' subscribers to ActiveCampaign.'),
      ));

      $fieldset->addField('export_confirm', 'checkbox', array(
          'label'     => Mage::helper('subscriptions')->__('Confirm?'),
          'name'      => 'export_confirm',
      ));

      /*
      $fieldset->addField('list_value', 'multiselect', array(
          'label'     => Mage::helper('subscriptions')->__('Lists'),
          'name'      => 'list_value',
          'values'    => $lists_,
      ));
      */

      if ( Mage::getSingleton('adminhtml/session')->getSubscriptionsData() ) {
      		$data = Mage::getSingleton('adminhtml/session')->getSubscriptionsData();
					$data["export_confirm"] = 1;
          $form->setValues($data);
          Mage::getSingleton('adminhtml/session')->setSubscriptionsData(null);
      }
      elseif ( Mage::registry('subscriptions_data') ) {
      		$data = Mage::registry('subscriptions_data')->getData();
					$data["export_confirm"] = 1;
          $form->setValues($data);
      }
      return parent::_prepareForm();
  }
}