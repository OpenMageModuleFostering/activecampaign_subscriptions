<?php
class ActiveCampaign_Subscriptions_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/subscriptions?id=15 
    	 *  or
    	 * http://site.com/subscriptions/id/15 	
    	 */
    	/* 
		$subscriptions_id = $this->getRequest()->getParam('id');

  		if($subscriptions_id != null && $subscriptions_id != '')	{
			$subscriptions = Mage::getModel('subscriptions/subscriptions')->load($subscriptions_id)->getData();
		} else {
			$subscriptions = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($subscriptions == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$subscriptionsTable = $resource->getTableName('subscriptions');
			
			$select = $read->select()
			   ->from($subscriptionsTable,array('subscriptions_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$subscriptions = $read->fetchRow($select);
		}
		Mage::register('subscriptions', $subscriptions);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}