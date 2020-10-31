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

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Eav\Model\Config as EavConfig;

class VisibilityFilter
{
    const VISIBILITY_FILTER_FIELD = 'visibility';
    const FILTER_BY_JOIN = 'join_filter';
    const FILTER_BY_WHERE = 'where_filter';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @param ResourceConnection $resourceConnection
     * @param ConditionManager $conditionManager
     * @param StoreManagerInterface $storeManager
     * @param EavConfig $eavConfig
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ConditionManager $conditionManager,
        StoreManagerInterface $storeManager,
        EavConfig $eavConfig
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->conditionManager = $conditionManager;
        $this->storeManager = $storeManager;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param Select $select
     * @param FilterInterface $filter
     * @param $type
     * @return Select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     */
    public function apply(Select $select, FilterInterface $filter, $type)
    {
        if ($type !== self::FILTER_BY_JOIN && $type !== self::FILTER_BY_WHERE) {
            throw new \InvalidArgumentException(sprintf('Invalid filter type: %s', $type));
        }

        $select = clone $select;

        $type === self::FILTER_BY_JOIN
            ? $this->applyFilterByJoin($filter, $select)
            : $this->applyFilterByWhere($filter, $select);

        return $select;
    }

    /**
     * @param FilterInterface $filter
     * @param Select $select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     */
    private function applyFilterByJoin(FilterInterface $filter, Select $select)
    {
        $mainTableAlias = $this->extractTableAliasFromSelect($select);

        $select->joinInner(
            ['visibility_filter' => $this->resourceConnection->getTableName('catalog_product_index_eav')],
            $this->conditionManager->combineQueries(
                [
                    sprintf('%s.entity_id = visibility_filter.entity_id', $mainTableAlias),
                    $this->conditionManager->generateCondition(
                        'visibility_filter.attribute_id',
                        '=',
                        $this->getVisibilityAttributeId()
                    ),
                    $this->conditionManager->generateCondition(
                        'visibility_filter.value',
                        is_array($filter->getValue()) ? 'in' : '=',
                        $filter->getValue()
                    ),
                    $this->conditionManager->generateCondition(
                        'visibility_filter.store_id',
                        '=',
                        $this->storeManager->getStore()->getId()
                    ),
                ],
                Select::SQL_AND
            ),
            []
        );
    }

    /**
     * @param FilterInterface $filter
     * @param Select $select
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Select_Exception
     */
    private function applyFilterByWhere(FilterInterface $filter, Select $select)
    {
        $mainTableAlias = $this->extractTableAliasFromSelect($select);

        $select->where(
            $this->conditionManager->combineQueries(
                [
                    $this->conditionManager->generateCondition(
                        sprintf('%s.attribute_id', $mainTableAlias),
                        '=',
                        $this->getVisibilityAttributeId()
                    ),
                    $this->conditionManager->generateCondition(
                        sprintf('%s.value', $mainTableAlias),
                        is_array($filter->getValue()) ? 'in' : '=',
                        $filter->getValue()
                    ),
                    $this->conditionManager->generateCondition(
                        sprintf('%s.store_id', $mainTableAlias),
                        '=',
                        $this->storeManager->getStore()->getId()
                    ),
                ],
                Select::SQL_AND
            )
        );
    }

    /**
     * Returns visibility attribute id
     *
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getVisibilityAttributeId()
    {
        $attr = $this->eavConfig->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            self::VISIBILITY_FILTER_FIELD
        );

        return (int) $attr->getId();
    }

    /**
     * @param Select $select
     * @return |null
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
