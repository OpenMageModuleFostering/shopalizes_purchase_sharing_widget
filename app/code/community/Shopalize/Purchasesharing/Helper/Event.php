<?php
/**
 * @company    Bytes Technolab<www.bytestechnolab.com> 
 * @author     Bytes Technolab<info@bytestechnolab.com>
 *
 * @category   Shopalize
 * @package    Shopalize_Purchasesharing_Helper_Event 
 */

class Shopalize_Purchasesharing_Helper_Event extends Mage_Core_Helper_Abstract {

	
	public function core_block_abstract_to_html_before_checkout_success($p_oObserver) {

		/* @var $l_oBlock Mage_Core_Block_Abstract */
		$l_oBlock = $p_oObserver->getBlock();

		if ('checkout.success' === $l_oBlock->getNameInLayout()) {
			if (Mage::getStoreConfig('purchasesharing/settings/active')) {
		
				$l_oChild = $l_oBlock->getLayout()->createBlock(
					'core/template',
					'purchasesharing.success',
					array(
						'template' => 'shopalizepurchasesharing/purchase_integration.phtml'
					)
				);

				$l_oBlock->append($l_oChild);
			}
		}
	}
}