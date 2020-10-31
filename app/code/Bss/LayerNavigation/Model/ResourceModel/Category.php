<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Model\ResourceModel;

use Magento\CatalogSearch\Model\Layer\Filter\Category as CoreCategory;

/**
 * Layer attribute filter
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends CoreCategory
{
    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var
     */
    protected $request;

    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /** @var bool Is Filterable Flag */
    protected $isFilter = false;

    /**
     * @var
     */
    protected $dataProvider;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory
     */
    protected $categoryDataProviderFactory;

    /**
     * @var
     */
    protected $logger;

    /**
     * @var \Bss\LayerNavigation\Model\Layer\ItemCollectionProvider
     */
    protected $collectionProvider;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Bss\LayerNavigation\Helper\Data $dataHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Bss\LayerNavigation\Helper\Data $dataHelper,
        \Psr\Log\LoggerInterface $logger,
        \Bss\LayerNavigation\Model\Layer\ItemCollectionProvider $collectionProvider,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $escaper,
            $categoryDataProviderFactory,
            $data
        );
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        $this->escaper = $escaper;
        $this->logger = $logger;
        $this->categoryDataProviderFactory = $categoryDataProviderFactory;
        $this->collectionProvider = $collectionProvider;
    }

    /**
     * @return mixed
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->dataHelper->isEnabled()) {
            return parent::apply($request);
        }

        $this->request = $request;

        $categoryId = $request->getParam($this->_requestVar);
        if (empty($categoryId)) {
            return $this;
        }

        $this->dataProvider  = $this->categoryDataProviderFactory->create(['layer' => $this->getLayer()]);

        $categoryIds = [];
        foreach (explode('_', $categoryId) as $id) {
            $this->dataProvider->setCategoryId($id);
            if ($this->dataProvider->isValid()) {
                $category = $this->dataProvider->getCategory();
                if ($request->getParam('id') != $id) {
                    $categoryIds[] = $id;
                    $this->getLayer()->getState()->addFilter($this->_createItem($category->getName(), $id));
                }
            }
        }

        if (!empty($categoryIds)) {
            $this->isFilter = true;
            if ($this->dataHelper->isElasticSearchEngine()) {
                $this->getLayer()->getProductCollection()
                    ->addFieldToFilter('category_ids', ['in' => $categoryIds]);
            } else {
                $this->getLayer()->getProductCollection()->addCategoriesFilter(['in' => $categoryIds]);
            }
        }

        if ($parentCategoryId = $request->getParam('id')) {
            $this->dataProvider->setCategoryId($parentCategoryId);
        }

        return $this;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        return __('Category');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\StateException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getItemsData()
    {
        if (!$this->dataHelper->isEnabled()) {
            return parent::_getItemsData();
        }

        $categoryTreeLevel = (int)$this->dataHelper->getCategoryLevel();
        $category = $this->categoryDataProviderFactory->create(['layer' => $this->getLayer()])->getCategory();
        $categories = $category->getChildrenCategories();
        $usedOptions = $this->getValueAsArray();

        $optionsFacetedData = $this->getFacetedData();
        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                $levelCurrent = (int)$category->getLevel();

                if ($category->getIsActive()
                    && isset($optionsFacetedData[$category->getId()])
                ) {
                    $checked = in_array($category->getId(), $usedOptions) ? 'checked' : '';
                    $this->itemDataBuilder->addItemData(
                        '<b><input type="checkbox" class="layer-input-filter" name="filter_cat" ' . $checked . '>' .
                        $this->escaper->escapeHtml($category->getName()).'</b>',
                        $category->getId(),
                        isset($optionsFacetedData[$category->getId()]['count']) ? ''
                            . $optionsFacetedData[$category->getId()]['count'] : 0
                    );
                }

                if ($category->getChildren()) {
                    $categoriesArray = $this->getAllCategories($category->getId());
                    foreach ($categoriesArray as $categoryArray) {
                        $categoryChildLevel = (int)$categoryArray->getLevel();
                        $currentPath = $categoryChildLevel - $levelCurrent;

                        if ($categoryArray->getIsActive()
                            && $categoryTreeLevel > $currentPath
                            && isset($optionsFacetedData[$categoryArray->getId()])
                        ) {
                            $checked = in_array($categoryArray->getId(), $usedOptions) ? 'checked' : '';
                            $currentString = $this->getSpace($currentPath);
                            $currentString = $currentString.'
                            <input type="checkbox" class="layer-input-filter" name="filter_cat" ' . $checked . '>';
                            $this->itemDataBuilder->addItemData(
                                $currentString.
                                $this->escaper->escapeHtml($categoryArray->getName()),
                                $categoryArray->getId(),
                                isset($optionsFacetedData[$categoryArray->getId()]['count']) ?
                                    '' . $optionsFacetedData[$categoryArray->getId()]['count'] : 0
                            );
                        }
                    }
                }
            }
        }
        return $this->itemDataBuilder->build();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\StateException
     */
    protected function getFacetedData()
    {
        $productCollection = $this->getLayer()->getProductCollection();

        /** @var \Bss\LayerNavigation\Model\ResourceModel\Fulltext\Collection $collection */
        $mainCategory = $this->getLayer()->getCurrentCategory();
        $collection = $this->collectionProvider->getCollection($mainCategory);
        $collection->updateSearchCriteriaBuilder();
        $this->getLayer()->prepareProductCollection($collection);

        foreach ($productCollection->getAddedFilters() as $field => $condition) {
            if ($field === 'category_ids') {
                $collection->addFieldToFilter($field, $mainCategory->getId());
                continue;
            }
            $collection->addFieldToFilter($field, $condition);
        }

        return $collection->getFacetedData('category');
    }

    /**
     * @param $currentPath
     * @return string
     */
    protected function getSpace($currentPath)
    {
        $currentString = '';
        for ($i = 1; $i <= $currentPath; $i++) {
            $currentString = $currentString.'&nbsp;&nbsp;&nbsp;';
        }
        return $currentString;
    }

    /**
     * @param null $categoryId
     * @param null $categoriesArray
     * @return array|bool|null
     */
    public function getAllCategories($categoryId = null, $categoriesArray = null)
    {
        if ($categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId, $this->storeManager->getStore()->getId());
                if ($category->hasChildren()) {
                    $childrenCategories = $category->getChildrenCategories();
                    foreach ($childrenCategories as $childrenCategory) {
                        $categoriesArray[] = $childrenCategory;
                        if ($childrenCategory->hasChildren()) {
                            $categoriesArray = $this->getAllCategories($childrenCategory->getId(), $categoriesArray);
                        }
                    }
                }

                return $categoriesArray;
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getValueAsArray()
    {
        $paramValue = $this->getRequest()->getParam($this->_requestVar);
        if (!$paramValue) {
            return [];
        }
        $requestValue = $this->getRequest()->getParam($this->_requestVar);

        return array_filter(
            explode('_', $requestValue),
            function ($value) {
                return (string)(int)$value === $value;
            }
        );
    }

    /**
     * @param $value
     * @return string
     */
    public function getResetOptionValue($value)
    {
        $attributeValues = $this->getValueAsArray();
        $key = array_search($value, $attributeValues);
        unset($attributeValues[$key]);
        return implode('_', $attributeValues);
    }
}
