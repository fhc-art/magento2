<?php
/**
  * @author     DCKAP <extensions@dckap.com>
  * @package    DCKAP_Ordersearch
  * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
  * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  */

namespace DCKAP\Ordersearch\Block\Order\Html;

/**
 * Html pager block
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    public function getPagerUrl($params = [])
    {
        if($filterId = $this->getRequest()->getParam('filter_id', null)) {
            $params['filter_id'] = $filterId;
        }

        if($filterValue = $this->getRequest()->getParam('filter_value', null)) {
            $params['filter_value'] = $filterValue;
        }

        if($orderFromDate = $this->getRequest()->getParam('order_from_date', null)) {
            $orderFromDate = str_replace('/', '-', $orderFromDate);
            $params['order_from_date'] = $orderFromDate;
        }

        if($orderToDate = $this->getRequest()->getParam('order_to_date', null)) {
            $orderToDate = str_replace('/', '-', $orderToDate);
            $params['order_to_date'] = $orderToDate;
        }

        $urlParams = [];
        $urlParams['_current'] = true;
        $urlParams['_escape'] = true;
        $urlParams['_use_rewrite'] = true;
        $urlParams['_fragment'] = $this->getFragment();
        $urlParams['_query'] = $params;

        return $this->getUrl('sales/order/history', $urlParams);
    }
}
