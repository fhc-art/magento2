<?php
 
namespace DCKAP\CustomizedOrderSearch\Model\ResourceModel;
 
class Ordersearch extends \DCKAP\Ordersearch\Model\ResourceModel\Ordersearch
{

     public function getOrders($dataObject)
    {
        if (!($customerId = $this->customerSession->create()->getCustomerId())) {
            return false;
        }
        if (!$this->orders) {
            $this->orders = $this->orderCollectionFactory->create()->addFieldToSelect('*')->addAttributeToFilter('customer_id', ['eq' => $customerId]);

            if ($this->helper->isDateFilterEnable()) {
                if (!empty($dataObject['order_from_date']) || !empty($dataObject['order_to_date'])) {
                    $this->orderDateFilter($dataObject);
                }
            }

            if ((isset($dataObject['filter_id']) && !empty($dataObject['filter_id'])) && isset($dataObject['filter_value']) && !empty($dataObject['filter_value'])):
                if ($dataObject['filter_id'] == 'sku' || $dataObject['filter_id'] == 'name') {
                    $this->orderProductFilter($dataObject);
                } elseif ($dataObject['filter_id'] == 'city' || $dataObject['filter_id'] == 'postcode') {

                    $this->orderAddressFilter($dataObject);
                }
                elseif ($dataObject['filter_id'] == 'purchase_order') 
                {

                    $poOrders = $this->orderPOFilter($dataObject);
                    return $poOrders;
                }
                else {
                    $this->orders->addAttributeToFilter($dataObject['filter_id'], ['eq' => $dataObject['filter_value']]
                    );

                }
            endif;
            
                $this->orders->addFieldToFilter(
                    'status',
                    ['in' => $this->orderConfig->getVisibleOnFrontStatuses()]
                )->setOrder(
                    'created_at',
                    'desc'
                );

            $this->orders->getSelect()->group(self::MAIN_TABLE . '.entity_id');

        


        }
        return $this->orders;
    }
    protected function orderPOFilter($dataObject)
    {
        $filterValue = $dataObject['filter_value'];
        $poOrdersId=array();
        $ordersData = $this->orders->getData();
        foreach ($ordersData as $order) 
        {
            if(array_key_exists('bss_customfield' , $order))
            {
                if(isset($order['bss_customfield']))
                {
                    $bssCustomField = json_decode($order['bss_customfield'],true);
                    if(!empty($bssCustomField) && array_key_exists('purchase_order' , $bssCustomField))
                    {
                        $poValue = $bssCustomField['purchase_order']['value'];
                       
                        if($poValue == $filterValue || $hasValue = stripos($poValue, $filterValue) !== false)
                        {                           
                            array_push($poOrdersId,$order['entity_id']);
                        }
                    }
                }
            }
           
        }
         $poOrders= $this->orderCollectionFactory->create()->addFieldToSelect('*')->addAttributeToFilter('entity_id',  array('in' => $poOrdersId));
        return $poOrders;
       
       
    }

}
?>