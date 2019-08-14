<?php
/**
 * @company    Bytes Technolab <www.bytestechnolab.com> 
 * @author     Bytes Technolab <info@bytestechnolab.com>
 *
 * @category   Shopalize
 * @package    Shopalize_Purchasesharing_Block_Success 
 */

class Shopalize_Purchasesharing_Block_Success extends Mage_Checkout_Block_Success
{
  	/**
     * Shopalized Product Sharing Enabled
    */
	public function isEnabled(){
		return Mage::getStoreConfig('purchasesharing/settings/active');
	}
	
	/**
     * Product Shopalized Merchant Id
    */
	public function getShopalizeMerchantId(){
		/* if(Mage::getStoreConfig('purchasesharing/settings/active')){
			return Mage::getStoreConfig('purchasesharing/settings/merchant_id');
		}else{
			return Mage::getStoreConfig('productsharing/settings/merchant_id');
		}*/
	}

	/**
     * Product Shopalized Store Id
    */
	public function getShopalizeStoreId(){
		if(Mage::getStoreConfig('purchasesharing/settings/active')){
			return Mage::getStoreConfig('purchasesharing/settings/store_id');
		}else{
			return Mage::getStoreConfig('productsharing/settings/store_id');
		}
	}
}