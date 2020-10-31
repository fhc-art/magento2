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
 * @category   BSS
 * @package    Bss_CatalogPermission
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CatalogPermission\Plugin\Model\Page;

use Magento\Framework\Json\Helper\Data as Json;

/**
 * Class DataProvider
 *
 * @package Bss\CatalogPermission\Plugin\Model\Page
 */
class DataProvider
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * DataProvider constructor.
     * @param Json $json
     */
    public function __construct(Json $json)
    {
        $this->json = $json;
    }

    /**
     * Plugin after get data
     *
     * @param \Magento\Cms\Model\Page\DataProvider $subject
     * @param array $result
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(
        \Magento\Cms\Model\Page\DataProvider $subject,
        $result
    ) {
        if (is_array($result)) {
            foreach ($result as &$item) {
                if (isset($item['bss_customer_group']) && ($item['bss_customer_group'])) {
                    $item['bss_customer_group'] = $this->json->jsonDecode($item['bss_customer_group']);
                }
            }
        }
        return $result;
    }
}
