<?php
 
namespace DCKAP\CustomizedOrderSearch\Block\Order\Search;
 
class Result extends \DCKAP\Ordersearch\Block\Order\Search\Result
{

    protected $_template = 'order/result/result.phtml';

    public function _prepareLayout()
    {   
        parent::_prepareLayout();
        $this->setTemplate('DCKAP_CustomizedOrderSearch::order/result/result.phtml');
        return $this;
    }


}
?>