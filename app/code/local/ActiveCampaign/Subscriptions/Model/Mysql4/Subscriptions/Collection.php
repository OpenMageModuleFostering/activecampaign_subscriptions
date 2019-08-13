<?php

class ActiveCampaign_Subscriptions_Model_Mysql4_Subscriptions_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('subscriptions/subscriptions');
    }
}