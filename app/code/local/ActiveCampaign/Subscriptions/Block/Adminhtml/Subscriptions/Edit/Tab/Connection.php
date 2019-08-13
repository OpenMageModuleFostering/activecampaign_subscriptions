<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions_Edit_Tab_Connection extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('subscriptions_form', array('legend'=>Mage::helper('subscriptions')->__('Add Connection Details')));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('subscriptions')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      /*
      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('subscriptions')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  	));
	  	*/

      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('subscriptions')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('subscriptions')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('subscriptions')->__('Disabled'),
              ),
          ),
      ));

      $fieldset->addField('api_url', 'text', array(
          'label'     => Mage::helper('subscriptions')->__('API URL'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'api_url',
      ));

      $fieldset->addField('api_key', 'text', array(
          'label'     => Mage::helper('subscriptions')->__('API Key'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'api_key',
      ));

      /*
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('subscriptions')->__('Content'),
          'title'     => Mage::helper('subscriptions')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
      */

      if ( Mage::getSingleton('adminhtml/session')->getSubscriptionsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSubscriptionsData());
          Mage::getSingleton('adminhtml/session')->setSubscriptionsData(null);
      } elseif ( Mage::registry('subscriptions_data') ) {
          $form->setValues(Mage::registry('subscriptions_data')->getData());
      }
      return parent::_prepareForm();
  }
}