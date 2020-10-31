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
 * @package    Bss_B2bRegistration
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomB2bRegistration\Plugin;

use Magento\Framework\UrlInterface;
use Bss\B2bRegistration\Helper\Data;

class CustomerListing
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * CustomerListing constructor.
     * @param UrlInterface $urlBuilder
     * @param Data $helper
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Data $helper
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helper = $helper;
    }

    /**
     * Create Massaction in Admin
     * @param \Magento\Framework\View\Layout\Generic $subject
     * @param \Closure $proceed
     * @param string $component
     * @return array|mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuild(\Magento\Framework\View\Layout\Generic $subject, \Closure $proceed, $component)
    {
        if ($component->getName() == 'customer_listing') {
            $result = $proceed($component);
            if (is_array($result)) {
                if (isset($result['components']['customer_listing']['children']['customer_listing']['children']
                    ['listing_top']['children']['listing_massaction'])) {
                    $approvePrimaryUrl = $this->urlBuilder->getUrl(
                        'custombtwob/index/massApprovedPrimary',
                        $paramsHere = []
                    );
                    $approvedPrimaryAction = [
                        'component' => 'uiComponent',
                        'type' => 'approvedprimary',
                        'label' => 'Approved Primary Account B2b Customer(s)',
                        'url' => $approvePrimaryUrl,
                        'confirm' => [
                            'title' => 'Approved Primary Account B2b Customer(s)',
                            'message' => __('Are you sure to Approved Primary selected customers ?')
                        ]
                    ];

                    $result['components']['customer_listing']['children']['customer_listing']['children']
                    ['listing_top']['children']['listing_massaction']['config']['actions'][] = $approvedPrimaryAction;
                }
            }
        }

        if (isset($result)) {
            return $result;
        }

        return $proceed($component);
    }
}
