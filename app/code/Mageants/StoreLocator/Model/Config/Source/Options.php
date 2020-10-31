<?php
/**
 * @category Mageants StoreLocator
 * @package Mageants_StoreLocator
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@Mageants.com>
 */
namespace Mageants\StoreLocator\Model\Config\Source;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory;
use Magento\Framework\DB\Ddl\Table;

/**
 * Option for select option
 */
class Options extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $optionFactory;
    public function getAllOptions()
    {
        /* your Attribute options list*/
        $this->_options=[ ['label'=>'Select Options', 'value'=>''],
        ['label'=>'Yes', 'value'=>'1'],
        ['label'=>'No', 'value'=>'0'],
        ];
        return $this->_options;
    }
}
