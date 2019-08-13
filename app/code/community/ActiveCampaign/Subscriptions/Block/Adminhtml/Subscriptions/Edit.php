<?php

class ActiveCampaign_Subscriptions_Block_Adminhtml_Subscriptions_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'subscriptions';
        $this->_controller = 'adminhtml_subscriptions';

        $this->_updateButton('save', 'label', Mage::helper('subscriptions')->__('Save Connection'));

        $this->_updateButton('delete', 'label', Mage::helper('subscriptions')->__('Delete Connection'));

        // hide these buttons
        $this->_updateButton('delete', 'style', 'display: none;');
        $this->_updateButton('reset', 'style', 'display: none;');

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('subscriptions_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'subscriptions_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'subscriptions_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('subscriptions_data') && Mage::registry('subscriptions_data')->getId() ) {
            return Mage::helper('subscriptions')->__("Edit Connection '%s'", $this->htmlEscape(Mage::registry('subscriptions_data')->getTitle()));
        } else {
            return Mage::helper('subscriptions')->__('Add Connection');
        }
    }
}