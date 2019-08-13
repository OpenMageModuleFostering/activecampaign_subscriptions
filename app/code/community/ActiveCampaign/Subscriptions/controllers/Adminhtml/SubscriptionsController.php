<?php

define("ACTIVECAMPAIGN_URL", "");
define("ACTIVECAMPAIGN_API_KEY", "");
require_once(Mage::getBaseDir() . "/app/code/community/ActiveCampaign/Subscriptions/activecampaign-api-php/ActiveCampaign.class.php");

class ActiveCampaign_Subscriptions_Adminhtml_SubscriptionsController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('subscriptions/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Connections Manager'), Mage::helper('adminhtml')->__('Connection Manager'));

		return $this;
	}

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

	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('subscriptions/subscriptions')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('subscriptions_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('subscriptions/items');

			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Connection Manager'), Mage::helper('adminhtml')->__('Connection Manager'));
			//$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit'))
				->_addLeft($this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('subscriptions')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}

	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction() {

		if ($data = $this->getRequest()->getPost()) {

//$this->dbg($data);

			/*
			if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
				try {
					// Starting upload
					$uploader = new Varien_File_Uploader('filename');

					// Any extention would work
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);

					// Set the file upload mode
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader->setFilesDispersion(false);

					// We set media as the upload dir
					$path = Mage::getBaseDir('media') . DS ;
					$uploader->save($path, $_FILES['filename']['name'] );

				} catch (Exception $e) {

		        }

		        //this way the name is saved in DB
	  			$data['filename'] = $_FILES['filename']['name'];
			}
			*/

			$model = Mage::getModel('subscriptions/subscriptions');

			$api_url = $data["api_url"];
			$api_key = $data["api_key"];

			$ac = new ActiveCampaign($api_url, $api_key);

			$test_connection = $ac->credentials_test();

			if (!$test_connection) {
				Mage::getSingleton("adminhtml/session")->addError("Invalid API URL or Key. Please check to make sure both values are correct.");
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        return;
			}
			else {
				// get AC account details
				$account = $ac->api("account/view");
				$data["account_url"] = $account->account;

				//$data["cdate"] = "NOW()";

				$list_values = $data["list_value"];
				$list_ids = array();

				// example (converts to): ["mthommes6.activehosted.com-5","mthommes6.activehosted.com-13"]
				$data["list_value"] = json_encode($data["list_value"]);
				$data["form_value"] = json_encode($data["form_value"]);

//$this->dbg($data);

				$model->setData($data)->setId($this->getRequest()->getParam('id'));

				if (isset($data["export_confirm"]) && (int)$data["export_confirm"]) {

					// exporting Newsletter subscribers to ActiveCampaign

					$subscribers_magento = Mage::getResourceModel('newsletter/subscriber_collection')->showStoreInfo()->showCustomerInfo()->getData();
//$this->dbg($subscribers_magento);

					$subscribers_ac = array();

					foreach ($list_values as $acct_listid) {
						// IE: mthommes6.activehosted.com-13
						$acct_listid = explode("-", $acct_listid);
						$list_ids[] = (int)$acct_listid[1];
					}

					foreach ($subscribers_magento as $subscriber) {

						$subscribers_ac_ = array(
							"email" => $subscriber["subscriber_email"],
							"first_name" => $subscriber["customer_firstname"],
							"last_name" => $subscriber["customer_lastname"],
						);

						// add lists
						$p = array();
						$status = array();
						foreach ($list_ids as $list_id) {
							$p[$list_id] = $list_id;
							$status[$list_id] = 1;
						}

						$subscribers_ac_["p"] = $p;
						$subscribers_ac_["status"] = $status;

//$this->dbg($subscribers_ac_);

						$subscribers_ac[] = $subscribers_ac_;

					}

//$this->dbg($subscribers_ac);

					$subscribers_ac_serialized = serialize($subscribers_ac);
//$this->dbg($subscribers_ac_serialized);

					$subscriber_request = $ac->api("subscriber/sync?service=magento", $subscribers_ac_serialized);
//$this->dbg($subscriber_request);

					if ((int)$subscriber_request->success) {
						// successful request
						//$subscriber_id = (int)$subscriber_request->subscriber_id;
					}
					else {
						// request failed
						//print_r($subscriber_request->error);
						//exit();
					}

				}

				try {
					if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
						$model->setCreatedTime(now())
							->setUpdateTime(now());
					} else {
						$model->setUpdateTime(now());
					}

					$model->save();
					Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('subscriptions')->__('Settings were successfully saved'));
					Mage::getSingleton('adminhtml/session')->setFormData(false);

					if ($this->getRequest()->getParam('back')) {
						$this->_redirect('*/*/edit', array('id' => $model->getId()));
						return;
					}
					$this->_redirect('*/*/');
					return;
	      }
	      catch (Exception $e) {
	      	Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
	      	Mage::getSingleton('adminhtml/session')->setFormData($data);
	      	$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	      	return;
	      }

			}

    }

    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('subscriptions')->__('Unable to find item to save'));
    $this->_redirect('*/*/');
	}

	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('subscriptions/subscriptions');

				$model->setId($this->getRequest()->getParam('id'))
					->delete();

				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $subscriptionsIds = $this->getRequest()->getParam('subscriptions');
        if(!is_array($subscriptionsIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($subscriptionsIds as $subscriptionsId) {
                    $subscriptions = Mage::getModel('subscriptions/subscriptions')->load($subscriptionsId);
                    $subscriptions->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($subscriptionsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $subscriptionsIds = $this->getRequest()->getParam('subscriptions');
        if(!is_array($subscriptionsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($subscriptionsIds as $subscriptionsId) {
                    $subscriptions = Mage::getSingleton('subscriptions/subscriptions')
                        ->load($subscriptionsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($subscriptionsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'subscriptions.csv';
        $content    = $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'subscriptions.xml';
        $content    = $this->getLayout()->createBlock('subscriptions/adminhtml_subscriptions_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}