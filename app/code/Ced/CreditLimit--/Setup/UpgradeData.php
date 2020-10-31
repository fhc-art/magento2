<?php

namespace Ced\CreditLimit\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{

	/**
	 * 
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Magento\Catalog\Model\Product $productModel
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct(
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\Product $productModel,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		$this->storeManager = $storeManager;
		$this->productFactory = $productFactory;
		$this->productModel = $productModel;
	}

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		if(version_compare($context->getVersion(), '0.0.9') < 0){
			$appState = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\State');
			$appState->setAreaCode('adminhtml');
			$product = $this->productFactory->create();
			$attributeSetId = $this->productModel->getDefaultAttributeSetId();
			$product->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
			$product->setWebsiteIds([$this->storeManager->getDefaultStoreView()->getWebsiteId()]);
			$product->setTypeId('virtual');
			$product->setPrice(0.00);
			$product->addData([
					'name' => 'Due Payment',
					'attribute_set_id' => $attributeSetId,
					'status' => \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED,
					'visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
					'sku' => \Ced\CreditLimit\Model\CreditLimit::CREDIT_LIMIT_SKU,
					'tax_class_id' => 0,
					'description' => 'Due Payment',
					'short_description' => 'Due Payment',
					'stock_data' => [
							'manage_stock' => 0,
							'qty' => 999,
							'is_in_stock' => 1
					]
			]);
			$product->save();
		}
	}
}