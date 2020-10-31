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
namespace Bss\LayerNavigation\Model\ResourceModel\Search;

use Magento\CatalogSearch\Model\Search\SelectContainer\SelectContainer;
use Magento\CatalogSearch\Model\Adapter\Mysql\Filter\AliasResolver;
use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogSearch\Model\Search\FilterMapper\FilterStrategyInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\DB\Select;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product;
use Bss\LayerNavigation\Helper\Data as DataHelper;

use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class FilterMapper
 * @package Bss\LayerNavigation\Model\ResourceModel\Search
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FilterMapper
{

    /**
     * @var AliasResolver
     */
    private $aliasResolver;

    /**
     * @var FilterStrategyInterface
     */
    private $filterStrategy;

    /**
     * @var VisibilityFilter
     */
    private $visibilityFilter;

    /**
     * @var StockStatusFilter
     */
    private $stockStatusFilter;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $productMetadata;

    /**
     * FilterMapper constructor.
     * @param AliasResolver $aliasResolver
     * @param FilterStrategyInterface $filterStrategy
     * @param VisibilityFilter $visibilityFilter
     * @param StockStatusFilter $stockStatusFilter
     * @param ResourceConnection $resourceConnection
     * @param ConditionManager $conditionManager
     * @param EavConfig $eavConfig
     * @param StoreManagerInterface $storeManager
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        AliasResolver $aliasResolver,
        FilterStrategyInterface $filterStrategy,
        VisibilityFilter $visibilityFilter,
        StockStatusFilter $stockStatusFilter,
        ResourceConnection $resourceConnection,
        ConditionManager $conditionManager,
        EavConfig $eavConfig,
        StoreManagerInterface $storeManager,
        ProductMetadataInterface $productMetadata
    ) {
        $this->aliasResolver = $aliasResolver;
        $this->filterStrategy = $filterStrategy;
        $this->visibilityFilter = $visibilityFilter;
        $this->stockStatusFilter = $stockStatusFilter;
        $this->resourceConnection = $resourceConnection;
        $this->conditionManager = $conditionManager;
        $this->eavConfig = $eavConfig;
        $this->storeManager = $storeManager;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param \Magento\CatalogSearch\Model\Search\FilterMapper\FilterMapper $subject
     * @param \Closure $proceed
     * @param SelectContainer $selectContainer
     * @return SelectContainer|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundApplyFilters(
        \Magento\CatalogSearch\Model\Search\FilterMapper\FilterMapper $subject,
        \Closure $proceed,
        SelectContainer $selectContainer
    ) {

        $version = $this->productMetadata->getVersion();
        $versionArray = explode(".", $version);

        if ($selectContainer->hasCustomAttributesFilters() && $versionArray[1] >= DataHelper::MAGENTO_VERSION_220) {
            $select = $selectContainer->getSelect();
            $select = $this->apply($select, ...$selectContainer->getCustomAttributesFilters());
            $filterType = StockStatusFilter::FILTER_JUST_ENTITY;
            if ($selectContainer->hasCustomAttributesFilters()) {
                $filterType = StockStatusFilter::FILTER_ENTITY_AND_SUB_PRODUCTS;
            }

            $select = $this->stockStatusFilter->apply(
                $select,
                Stock::STOCK_IN_STOCK,
                $filterType,
                $selectContainer->isShowOutOfStockEnabled()
            );

            $appliedFilters = [];

            if ($selectContainer->hasVisibilityFilter()) {
                $filterType = VisibilityFilter::FILTER_BY_WHERE;
                if ($selectContainer->hasCustomAttributesFilters()) {
                    $filterType = VisibilityFilter::FILTER_BY_JOIN;
                }

                $select = $this->visibilityFilter->apply($select, $selectContainer->getVisibilityFilter(), $filterType);
                $appliedFilters[$this->aliasResolver->getAlias($selectContainer->getVisibilityFilter())] = true;
            }

            foreach ($selectContainer->getNonCustomAttributesFilters() as $filter) {
                $alias = $this->aliasResolver->getAlias($filter);

                if (!array_key_exists($alias, $appliedFilters)) {
                    $isApplied = $this->filterStrategy->apply($filter, $select);
                    if ($isApplied) {
                        $appliedFilters[$alias] = true;
                    }
                }
            }

            $selectContainer = $selectContainer->updateSelect($select);

            return $selectContainer;
        }

        $returnValue = $proceed($selectContainer);
        return $returnValue;
    }

    /**
     * @param Select $select
     * @param FilterInterface ...$filters
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function apply(Select $select, FilterInterface ...$filters)
    {
        $select = clone $select;
        $mainTableAlias = $this->extractTableAliasFromSelect($select);
        $attributes = [];

        foreach ($filters as $filter) {
            $filterJoinAlias = $this->aliasResolver->getAlias($filter);

            $attributeId = $this->getAttributeIdByCode($filter->getField());

            if ($attributeId === null) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid attribute id for field: %s', $filter->getField())
                );
            }

            $attributes[] = $attributeId;

            $select->joinInner(
                [$filterJoinAlias => $this->resourceConnection->getTableName('catalog_product_index_eav')],
                $this->conditionManager->combineQueries(
                    $this->getJoinConditions($attributeId, $mainTableAlias, $filterJoinAlias),
                    Select::SQL_AND
                ),
                []
            );
        }

        if (count($attributes) === 1) {
            // forces usage of PRIMARY key in main table
            // is required to boost performance in case when we have just one filter by custom attribute
            $attribute = reset($attributes);
            $filter = reset($filters);
            $select->where(
                $this->conditionManager->generateCondition(
                    sprintf('%s.attribute_id', $mainTableAlias),
                    '=',
                    $attribute
                )
            )->where(
                $this->conditionManager->generateCondition(
                    sprintf('%s.value', $mainTableAlias),
                    is_array($filter->getValue()) ? 'in' : '=',
                    $filter->getValue()
                )
            );
        }

        return $select;
    }

    /**
     * @param $attrId
     * @param $mainTable
     * @param $joinTable
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getJoinConditions($attrId, $mainTable, $joinTable)
    {
        return [
            sprintf('`%s`.`entity_id` = `%s`.`entity_id`', $mainTable, $joinTable),
            sprintf(
                '(`%s`.`source_id` = `%s`.`source_id` OR `%s`.`entity_id` = `%s`.`source_id`)',
                $mainTable,
                $joinTable,
                $joinTable,
                $joinTable
            ),
            $this->conditionManager->generateCondition(
                sprintf('%s.attribute_id', $joinTable),
                '=',
                $attrId
            ),
            $this->conditionManager->generateCondition(
                sprintf('%s.store_id', $joinTable),
                '=',
                (int) $this->storeManager->getStore()->getId()
            )
        ];
    }

    /**
     * Returns attribute id by code
     *
     * @param string $field
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributeIdByCode($field)
    {
        $attr = $this->eavConfig->getAttribute(Product::ENTITY, $field);

        return ($attr && $attr->getId()) ? (int) $attr->getId() : null;
    }

    /**
     * Extracts alias for table that is used in FROM clause in Select
     *
     * @param Select $select
     * @return string|null
     * @throws \Zend_Db_Select_Exception
     */
    private function extractTableAliasFromSelect(Select $select)
    {
        $fromArr = array_filter(
            $select->getPart(Select::FROM),
            function ($fromPart) {
                return $fromPart['joinType'] === Select::FROM;
            }
        );

        return $fromArr ? array_keys($fromArr)[0] : null;
    }
}
