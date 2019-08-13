<?php

define("ACTIVECAMPAIGN_URL", "");
define("ACTIVECAMPAIGN_API_KEY", "");
require_once(Mage::getBaseDir() . "/app/code/community/ActiveCampaign/Subscriptions/activecampaign-api-php/ActiveCampaign.class.php");

class ActiveCampaign_Subscriptions_Block_Subscriptions extends Mage_Core_Block_Template
{
	public function _prepareLayout()
  {
		return parent::_prepareLayout();
	}

	public function getSubscriptions()
	{
		if (!$this->hasData('subscriptions')) {
			$this->setData('subscriptions', Mage::registry('subscriptions'));
		}
    return $this->getData('subscriptions');
	}
}