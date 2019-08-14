<?php

class Shopalize_Purchasesharingwidget_Helper_Event extends Mage_Core_Helper_Abstract {
	
	public function core_block_abstract_to_html_after_checkout_success($p_oObserver) {
	
		/* @var $l_oBlock Mage_Core_Block_Abstract */
		$l_oBlock = $p_oObserver->getBlock();
		
		if ('checkout.success' === $l_oBlock->getNameInLayout()) {
		
			$l_oTransport = $p_oObserver->getTransport();
			
			$_widget = Mage::helper('purchasesharingwidget');
	 
			$l_sHtml = $l_oTransport->getHtml();
			$l_sHtml = $l_sHtml. '<div style="clear: both;"></div>'. $_widget->getWidget($l_oBlock->getOrderId());
			$l_oTransport->setHtml($l_sHtml);
			
		}
	
	}
	
	public function core_block_abstract_to_html_before_checkout_success($p_oObserver) {
	
		/* @var $l_oBlock Mage_Core_Block_Abstract */
		$l_oBlock = $p_oObserver->getBlock();
		
		if ('checkout.success' === $l_oBlock->getNameInLayout()) {
		
			if (Mage::getStoreConfig('purchasesharingwidget/settings/active')) {
		
				$l_oChild = $l_oBlock->getLayout()->createBlock(
					'core/template',
					'purchasesharingwidget.success',
					array(
						'template' => 'purchasesharingwidget/widget.phtml'
					)
				);
				
				$l_oBlock->append($l_oChild);
			}
		}
	
	}
	
}