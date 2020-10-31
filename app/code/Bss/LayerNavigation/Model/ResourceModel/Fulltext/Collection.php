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

namespace Bss\LayerNavigation\Model\ResourceModel\Fulltext;

use Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitationFactory;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\DefaultFilterStrategyApplyCheckerInterface;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchCriteriaResolverFactory;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\SearchResultApplierFactory;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection\TotalRecordsResolverFactory;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class Collection full text
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{
    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    private $helper;

    /**
     * @var array
     */
    protected $addedFilters = [];

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    private $filterBuilder;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Search\Model\QueryFactory $catalogSearchData
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory
     * @param \Bss\LayerNavigation\Helper\Data $helper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param string $searchRequestName
     * @param SearchResultFactory|null $searchResultFactory
     * @param ProductLimitationFactory|null $productLimitationFactory
     * @param MetadataPool|null $metadataPool
     * @param \Magento\Search\Api\SearchInterface|null $search
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder|null $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder|null $filterBuilder
     * @param SearchCriteriaResolverFactory|null $searchCriteriaResolverFactory
     * @param SearchResultApplierFactory|null $searchResultApplierFactory
     * @param TotalRecordsResolverFactory|null $totalRecordsResolverFactory
     * @param DefaultFilterStrategyApplyCheckerInterface|null $defaultFilterStrategyApplyChecker
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Search\Model\QueryFactory $catalogSearchData,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Bss\LayerNavigation\Helper\Data $helper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        $searchRequestName = 'catalog_view_container',
        SearchResultFactory $searchResultFactory = null,
        ProductLimitationFactory $productLimitationFactory = null,
        MetadataPool $metadataPool = null,
        \Magento\Search\Api\SearchInterface $search = null,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder = null,
        \Magento\Framework\Api\FilterBuilder $filterBuilder = null,
        SearchCriteriaResolverFactory $searchCriteriaResolverFactory = null,
        SearchResultApplierFactory $searchResultApplierFactory = null,
        TotalRecordsResolverFactory $totalRecordsResolverFactory = null,
        DefaultFilterStrategyApplyCheckerInterface $defaultFilterStrategyApplyChecker = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $catalogSearchData,
            $requestBuilder,
            $searchEngine,
            $temporaryStorageFactory,
            $connection,
            $searchRequestName,
            $searchResultFactory,
            $productLimitationFactory,
            $metadataPool,
            $search,
            $searchCriteriaBuilder,
            $filterBuilder,
            $searchCriteriaResolverFactory,
            $searchResultApplierFactory,
            $totalRecordsResolverFactory,
            $defaultFilterStrategyApplyChecker
        );
        $this->helper = $helper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\Search\SearchCriteriaBuilder::class);
        $this->filterBuilder = $filterBuilder ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Api\FilterBuilder::class);
    }

    /**
     * Apply attribute filter to facet collection
     *
     * @param string $field
     * @param null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (is_string($field)) {
            $this->addedFilters[$field] = $condition;
        }
        if (is_string($field)) {
            $this->addedFilters[$field] = $condition;
        }
        $this->getSearchCriteriaBuilder();
        $this->getFilterBuilder();
        if (!is_array($condition) || !in_array(key($condition), ['from', 'to'], true)) {
            $this->filterBuilder->setField($field);
            $this->filterBuilder->setValue($condition);
            $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
        } else {
            if (!empty($condition['from'])) {
                $this->filterBuilder->setField("{$field}.from");
                $this->filterBuilder->setValue($condition['from']);
                $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
            }
            if (!empty($condition['to'])) {
                $this->filterBuilder->setField("{$field}.to");
                $this->filterBuilder->setValue($condition['to']);
                $this->searchCriteriaBuilder->addFilter($this->filterBuilder->create());
            }
        }
        return $this;
    }

    /**
     * Get filter builder.
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Api\FilterBuilder
     */
    private function getFilterBuilder()
    {
        if ($this->filterBuilder === null) {
            $this->filterBuilder = ObjectManager::getInstance()->get(\Magento\Framework\Api\FilterBuilder::class);
        }
        return $this->filterBuilder;
    }

    /**
     * Set search criteria builder.
     *
     * @deprecated 100.1.0
     * @return \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private function getSearchCriteriaBuilder()
    {
        if ($this->searchCriteriaBuilder === null) {
            $this->searchCriteriaBuilder = ObjectManager::getInstance()
                ->get(\Magento\Framework\Api\Search\SearchCriteriaBuilder::class);
        }
        return $this->searchCriteriaBuilder;
    }

    /**
     * @param array $categoriesFilter
     * @return \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
     */
    public function addCategoriesFilter(array $categoriesFilter)
    {
        if (!empty($categoriesFilter['in'])) {
            $this->addedFilters['category_ids'] = $categoriesFilter['in'];
        }
        return parent::addCategoriesFilter($categoriesFilter);
    }

    /**
     *
     * @param array $condition
     *
     */
    public function multipleCategoriesFilter($condition)
    {
        if ($this->helper->isElasticSearchEngine()) {
            $this->addFieldToFilter('category_ids', $condition);
        } else {
            if (!is_array($condition)) {
                $condition = [$condition];
            }
            $countCondition = count($condition);
            if ($countCondition > 1) {
                $this->addCategoriesFilter(['in' => $condition]);
            } else {
                $this->addFieldToFilter('category_ids', implode(',', $condition));
            }
        }
    }

    /**
     * Get applied filters
     *
     * @return array
     */
    public function getAddedFilters()
    {
        return $this->addedFilters;
    }

    /**
     * @return $this
     */
    public function updateSearchCriteriaBuilder()
    {
        $searchCriteriaBuilder = ObjectManager::getInstance()
            ->create(\Magento\Framework\Api\Search\SearchCriteriaBuilder::class);
        $this->setSearchCriteriaBuilder($searchCriteriaBuilder);
        return $this;
    }

    /**
     * @return \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
     */
    protected function _prepareStatisticsData()
    {
        $this->_renderFilters();
        return parent::_prepareStatisticsData();
    }
}
