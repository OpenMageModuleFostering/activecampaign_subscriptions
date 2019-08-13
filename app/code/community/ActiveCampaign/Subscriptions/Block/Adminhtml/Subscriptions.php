<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_subscriptions';
    $this->_blockGroup = 'subscriptions';
    $this->_headerText = Mage::helper('subscriptions')->__('ActiveCampaign Settings');
    $this->_addButtonLabel = Mage::helper('subscriptions')->__('Add Connection');
    parent::__construct();
  }
}