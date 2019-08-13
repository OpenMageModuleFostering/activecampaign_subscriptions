<?php

class ActiveCampaign_Subscriptions_Model_Subscriptions extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('subscriptions/subscriptions');
    }
}