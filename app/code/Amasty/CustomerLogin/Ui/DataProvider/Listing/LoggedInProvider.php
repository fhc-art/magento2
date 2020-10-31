<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_CustomerLogin
 */


namespace Amasty\CustomerLogin\Ui\DataProvider\Listing;

use Amasty\CustomerLogin\Api\Data\LoggedInInterface;
use Amasty\CustomerLogin\Model\ResourceModel\LoggedIn\Collection;
use Magento\Framework\UrlInterface;

class LoggedInProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Collection $collection,
        UrlInterface $url,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collection;
        $this->url = $url;
    }

    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as &$item) {
            if (!empty($item[LoggedInInterface::CUSTOMER_ID])) {
                $item[LoggedInInterface::CUSTOMER_EMAIL] = '<a href="' . $this->url->getUrl(
                    'customer/index/edit',
                    ['id' => $item[LoggedInInterface::CUSTOMER_ID], '_current' => false]
                ) . '" target="_blank">' . $item[LoggedInInterface::CUSTOMER_EMAIL] . '</a>';
            }
        }
        return $data;
    }
}
