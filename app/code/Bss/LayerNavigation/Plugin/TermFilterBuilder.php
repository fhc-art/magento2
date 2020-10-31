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
namespace Bss\LayerNavigation\Plugin;

use Magento\Framework\Search\Request\Filter\Term as TermFilterRequest;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;

class TermFilterBuilder
{
    /**
     * @var FieldMapperInterface
     */
    protected $fieldMapper;

    /**
     * @var \Bss\LayerNavigation\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    protected $magentoVersion;

    /**
     * @param FieldMapperInterface $fieldMapper
     * @param \Bss\LayerNavigation\Helper\Data $helper
     * @param \Magento\Framework\App\ProductMetadataInterface $magentoVersion
     */
    public function __construct(
        FieldMapperInterface $fieldMapper,
        \Bss\LayerNavigation\Helper\Data $helper,
        \Magento\Framework\App\ProductMetadataInterface $magentoVersion
    ) {
        $this->fieldMapper = $fieldMapper;
        $this->helper = $helper;

        $this->magentoVersion = $magentoVersion;
    }

    /**
     * @param \Magento\Elasticsearch\SearchAdapter\Filter\Builder\Term $subject
     * @param $proceed
     * @param RequestFilterInterface $filter
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuildFilter(
        \Magento\Elasticsearch\SearchAdapter\Filter\Builder\Term $subject,
        $proceed,
        RequestFilterInterface $filter
    ) {
        if ($this->helper->isEnabled() && $this->helper->isElasticSearchEngine()) {
            $filterQuery = [];
            $value = $filter->getValue();
            if (isset($value['in'])) {
                $value = $value['in'];
            }
            if ($value) {
                $operator = is_array($value) ? 'terms' : 'term';
                $filterQuery []= [
                    $operator => [
                        $this->fieldMapper->getFieldName($filter->getField()) => $value,
                    ],
                ];
            }
            return $filterQuery;
        }
        return $proceed($filter);
    }
}
