<?php
/**
 * @website    http://www.shopalize.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Shopalize_Purchasesharingwidget_Helper_Data extends Mage_Core_Helper_Abstract
{

	const PRODUCT_IMAGE_SIZE = 256;

  public function getConfig($field, $group = 'settings', $default = null){
        $value = Mage::getStoreConfig('purchasesharingwidget/'.$group.'/'.$field);
        if(!isset($value) or trim($value) == ''){
            return $default;         
        }else{
            return $value;
        }   
	}
  
  public function getWidget($order_id) {
 
  if($order_id) 
  
  {
	
	$order = Mage::getModel('sales/order')->loadByIncrementId($order_id); 
	$_items = $order->getAllVisibleItems();
	$_count = count($_items);
	
	if($_count>0)	
	{
	$orderDate=explode(" ",$order->getCreatedAt());
	$orderDate1=explode("-",$orderDate[0]);
	$_output='<!-- Shopalize Integration BEGIN -->';
	$_output.="\n".'<div id="shopalize-purchase-sharing-wrapper"></div>';
	$_output.="\n".'<script type="text/javascript">';
    $_output.="\nvar Shopalize = window.Shopalize || {};
    (function() {
      // Populate Purchase Order details
     var order_details = {'is_debug' : false, 'merchant_id': '', 'widget_width': '', 'campaign_id': '', 'customer_email': '', 'order_number': '', 'order_total': '', 'order_currency': '', 'order_date': '', 'items' : []},
         num_items = '".$_count."';
    for(var cnt = 0; cnt < num_items; cnt++) { order_details.items[cnt] = {'id':'', 'title':'', 'current_price':'', 'list_price':'', 'url':'', 'image_url':''};}

    // Provide Merchant account, Order, and Product Details\n";
	 
		$_output.="\n\torder_details['is_debug'] = true;";
		$_output.="\n\torder_details['merchant_id']  = '".$this->getConfig('merchant_id', 'settings')."';";
		$_output.="\n\torder_details['store_id']  = '".$this->getConfig('store_id', 'settings')."';";
		$_output.="\n\torder_details['widget_width']  = '".$this->getConfig('widget_width', 'settings')."px';";
		$_output.="\n\torder_details['customer_email']  = '".$this->escapeHtml($order->getCustomerEmail())."';";
		$_output.="\n\torder_details['order_number']  = '".$order_id."';";
		$_output.="\n\torder_details['order_total']  = '".$this->escapeHtml($order->getGrandTotal())."';";
		$_output.="\n\torder_details['order_currency']  = '".$this->escapeHtml($order->getOrderCurrencyCode())."';";
		$_output.="\n\torder_details['order_date']  = '".$orderDate1[1]."-".$orderDate1[2]."-".$orderDate1[0]."';";
		$_output.="\n";
		
		/*get ordered items*/
		
		$k=0; foreach ($_items as $_item):	
		$_product = Mage::getModel('catalog/product');
		$_product->load($_product->getIdBySku($_item->getSku()));
		/*additional changes for configurable products*/
		$parent_url='';
		$product_url='';
		$parentIdArray='';
		
		$_version_array=Mage::getVersionInfo();
		$_version=$_version_array['major'].$_version_array['minor'].$_version_array['revision'];
		
		$l_bHasParent = false;
		
		if($_product->getvisibility()=='1')
		{
			if($_version<='141')
			{
			list($parentIdArray) = $_product->loadParentProductIds()->getData('parent_product_ids');
			if(isset($parentIdArray[0]))
			{
			 $parent = Mage::getModel('catalog/product')->load($parentIdArray[0]);
			 $parent_url=$parent->getProductUrl();
			 
			 // product has parent			 
			 $l_bHasParent = true;
			 }
			}
			else
			{
		
		 	list($parentIdArray) = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getIdBySku($_item->getSku()));
		
			if(isset($parentIdArray))
			{
          	$parent = Mage::getModel('catalog/product')->load($parentIdArray);
         	$parent_url=$parent->getProductUrl();
			
			// product has parent
			$l_bHasParent = true;
		 	}
			}
			$product_url=$parent_url;
		}
		else
		{
			$product_url=$_product->getProductUrl();
			
		}
		/*additional changes for configurable products*/
		$_helper = Mage::helper('catalog/output');
		
		if ($l_bHasParent && (!$_product->getImage() || 'no_selection' === $_product->getImage())) {
			// product has parent, we use the parent image
			$_img = Mage::helper('catalog/image')->init($parent, 'image', $parent->getImage())->resize(self::PRODUCT_IMAGE_SIZE)->__toString();
		}
		else {
			if ($_product->getImage() != 'no_selection' && $_product->getImage()):
			$_img = Mage::helper('catalog/image')->init($_product, 'image')->resize(self::PRODUCT_IMAGE_SIZE);
			else:
			$_img = Mage::helper('catalog/image')->init($_product, 'image')->resize(self::PRODUCT_IMAGE_SIZE);
			endif;
		}
		
		if($_item->getPrice()==$_product->getPrice()):
		$price=$_item->getPrice(); $special_price=$price;
		else:
		$price=$_product->getPrice(); $special_price=$_item->getPrice();
		endif;
		
		//$_output .= "/*". $_product->getShortDescription(). "*/";
		
		$_output.="\n\torder_details['items'][".$k."]['id']  = '".$_item->getSku()."';";
		$_output.="\n\torder_details['items'][".$k."]['title']  = '".str_replace("'", "", $_item->getName())."';";
		$_output.="\n\torder_details['items'][".$k."]['current_price']  = '".$special_price."';";
		$_output.="\n\torder_details['items'][".$k."]['list_price']  = '".$price."';";
		$_output.="\n\torder_details['items'][".$k."]['url']  = '".$product_url."';";
		$_output.="\n\torder_details['items'][".$k."]['image_url']  = '".$_img."';";
		$_output.="\n\torder_details['items'][".$k."]['description']  = '". $this->_sanitizeDescription($_product). "';";
		
		$k++; endforeach; 	
			
		/*get ordered items*/
	 
	 $_output.="\n\n// Assign Purchase Order details to Shopalize scope
      Shopalize.order_details = order_details;
	  Shopalize.base_url = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'www.shopalize.com';

      // Load Widget script Asynchronously
      var script = document.createElement('script');
      script.type = 'text/javascript'; script.async = true;
      script.src = Shopalize.base_url + '/js/purchase_sharing.js';
      var entry = document.getElementsByTagName('script')[0];
      entry.parentNode.insertBefore(script, entry);
    }());";
	
  	$_output.="\n</script>\n".'<!-- Shopalize Integration END -->';
	
	}
	  
   	return $_output;
  }
  else {
   return false;
  }
 } 
 public function getOrderinfoWidget($order_id) {
 
  if($order_id) 
  
  {
	
	$order = Mage::getModel('sales/order')->loadByIncrementId($order_id); 
	$_items = $order->getAllVisibleItems();
	$_count = count($_items);
	
	
	$_output="<h3>Order Details (".$_count." Items)</h3>";
	$_output.="<p>Customer Name : ".$this->escapeHtml($order->getCustomerName())."</p>";
	$_output.="<p>Customer Email : ".$this->escapeHtml($order->getCustomerEmail())."</p>";
	$_output.="<p>Order No : ".$order_id."</p>";
	$_output.="<p>Order Total : ".$this->escapeHtml($order->getGrandTotal())."</p>";
	$_output.="<p>Order Currency : ".$this->escapeHtml($order->getOrderCurrencyCode())."</p>";
	$_output.="<p>Order Date : ".$order->getCreatedAtDate()."</p>";

	if($_count>0)	
	{
	$_output.='<h3>Ordered Items</h3>';
	$_output.='<table width="100%" class="data-table">
		<tr style="background:#eee;">
			<th>Item</th>
			<th>Product Name</th>
			<th>Product Code</th>
			<th>Url</th>
			<th>Visibility</th>
			<th>Parent Id</th>
			<th>Product Id</th>
			<th>Price</th>
			<th>Special Price</th>
		</tr>';
	
	 
		
		/*get ordered items*/
		
		$k=0; foreach ($_items as $_item):	
		$_product = Mage::getModel('catalog/product');
		$_product->load($_product->getIdBySku($_item->getSku()));
		$parent_url='';
		$product_url='';
		$parentIdArray='';
		
		$_version_array=Mage::getVersionInfo();
		$_version=$_version_array['major'].$_version_array['minor'].$_version_array['revision'];
		
		$l_bHasParent = false;
		
		if($_product->getvisibility()=='1')
		{
			if($_version<='141')
			{
			list($parentIdArray) = $_product->loadParentProductIds()->getData('parent_product_ids');
			if(isset($parentIdArray[0]))
			{
			 $parent = Mage::getModel('catalog/product')->load($parentIdArray[0]);
			 $parent_url=$parent->getProductUrl();
			 // product has parent
			 $l_bHasParent = true;
			 }
			}
			else
			{
		
		 	list($parentIdArray) = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($_product->getIdBySku($_item->getSku()));
		
			if(isset($parentIdArray))
			{
          	$parent = Mage::getModel('catalog/product')->load($parentIdArray);
         	$parent_url=$parent->getProductUrl();
			// product has parent
			$l_bHasParent = true;
		 	}
			}
			$product_url=$parent_url;
		}
		else
		{
			$product_url=$_product->getProductUrl();
			
		}
		$_helper = Mage::helper('catalog/output');
		
		$l_bUseParentImage = false;
		if ($l_bHasParent && (!$_product->getImage() || 'no_selection' === $_product->getImage())) {
			// product has parent we use the parent's image
			$_img = Mage::helper('catalog/image')->init($parent, 'image', $parent->getImage())->resize(self::PRODUCT_IMAGE_SIZE);
			$l_bUseParentImage = true;
		}
		else {
			if ($_product->getImage() != 'no_selection' && $_product->getImage()):
			$_img = Mage::helper('catalog/image')->init($_product, 'image')->resize(self::PRODUCT_IMAGE_SIZE);
			else:
			$_img = Mage::helper('catalog/image')->init($_product, 'image')->resize(self::PRODUCT_IMAGE_SIZE);
			endif;
		}
		
		if($_item->getPrice()==$_product->getPrice()):
		$price=$_item->getPrice(); $special_price=$price;
		else:
		$price=$_product->getPrice(); $special_price=$_item->getPrice();
		endif;
		
		//print items
		$_output.='<tr>';
		$_output.='<td>'.$_helper->productAttribute($l_bUseParentImage?$parent:$_product, $_img, 'image').'</td>';
		$_output.='<td>'.$_item->getName().'</td>';
		$_output.='<td>'.$_item->getSku().'</td>';
		$_output.='<td>'.$product_url.'</td>';
		$_output.='<td>'.$_product->getvisibility().'</td>';
		$_output.='<td>'.$parentIdArray.'</td>';
		$_output.='<td>'.$_product->getIdBySku($_item->getSku()).'</td>';
		$_output.='<td>'.$price.'</td>';
		$_output.='<td>'.$special_price.','.$_item->getCost().'</td>';
		$_output.='</tr>';
		
		$k++; endforeach; 	
			
		/*get ordered items*/
	$_output.='</table>';
	}
	  
   	return $_output;
  }
  else {
   return false;
  }
 }

	protected function _sanitizeDescription($p_oProduct) {
		
		$_helper = Mage::helper('catalog/output');
		
		$l_sDescription = $_helper->productAttribute($p_oProduct, strip_tags(str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $p_oProduct->getShortDescription())), 'short_description');
		$l_sDescription = str_replace('\'', '\\\'', $l_sDescription);		
		
		return $l_sDescription;
	
	}
  
}