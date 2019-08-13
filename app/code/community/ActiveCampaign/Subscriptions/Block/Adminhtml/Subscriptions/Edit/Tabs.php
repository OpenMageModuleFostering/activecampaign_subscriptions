<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('subscriptions_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('subscriptions')->__('ActiveCampaign Settings'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section_connection', array(
          'label'     => Mage::helper('subscriptions')->__('API Information'),
          'title'     => Mage::helper('subscriptions')->__('API Information'),
          'content'   => $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit_tab_connection')->toHtml(),
      ));

      $this->addTab('form_section_lists', array(
          'label'     => Mage::helper('subscriptions')->__('Lists'),
          'title'     => Mage::helper('subscriptions')->__('Lists'),
          'content'   => $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit_tab_list')->toHtml(),
      ));

      $this->addTab('form_section_forms', array(
          'label'     => Mage::helper('subscriptions')->__('Forms'),
          'title'     => Mage::helper('subscriptions')->__('Forms'),
          'content'   => $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit_tab_form')->toHtml(),
      ));

      $this->addTab('form_section_export', array(
          'label'     => Mage::helper('subscriptions')->__('Export Magento Subscribers'),
          'title'     => Mage::helper('subscriptions')->__('Export Magento Subscribers'),
          'content'   => $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit_tab_export')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}