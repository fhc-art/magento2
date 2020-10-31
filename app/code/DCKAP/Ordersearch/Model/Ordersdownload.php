<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Ordersearch\Model;

/**
 * Sales order collection model
 */
class Ordersdownload extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \DCKAP\Ordersearch\Helper\Data
     */
    protected $helper;


    /**
     * @param \DCKAP\Ordersearch\Helper\Data $helper
     */

    public function __construct(
        \DCKAP\Ordersearch\Helper\Data $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @return int                                        .,
     */
    public function getStoreId()
    {
        return $this->helper->getCurrentStoreId();
    }


}
