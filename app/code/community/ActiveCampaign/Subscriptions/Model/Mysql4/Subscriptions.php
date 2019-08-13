<?php

class ActiveCampaign_Subscriptions_Model_Mysql4_Subscriptions extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the subscriptions_id refers to the key field in your database table.
        $this->_init('subscriptions/subscriptions', 'subscriptions_id');
    }
}