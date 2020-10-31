<?php
namespace Bss\CustomizeCustomerAttribute\Override\Customer;

use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Grid extends \Magento\Customer\Model\ResourceModel\Grid\Collection
{
    /**
     * @inheritdoc
     */
    protected $document = Document::class;

    /**
     * @inheritdoc
     */
    protected $_map = ['fields' => ['entity_id' => 'main_table.entity_id']];

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\App\Request\Http $request,
        $mainTable = 'customer_grid_flat',
        $resourceModel = \Magento\Customer\Model\ResourceModel\Customer::class
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        if (trim($this->request->getParam('search')) && is_numeric($this->request->getParam('search'))) {
            $value = $this->request->getParam('search');
            $this->getSelect()->reset(\Zend_Db_Select::WHERE);
            $this->addFieldToFilter([
                                        'ca_customer_number',
                                        'name',
                                        'email',
                                        'created_in',
                                        'taxvat',
                                        'shipping_full',
                                        'billing_full',
                                        'billing_firstname',
                                        'billing_lastname',
                                        'billing_telephone',
                                        'billing_postcode',
                                        'billing_region',
                                        'billing_city',
                                        'billing_fax',
                                        'billing_company'],
                                    [
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"],
                                        ['like' => "%{$value}%"]
                                    ]
                                    );

        }
        $this->_eventManager->dispatch('core_collection_abstract_load_before', ['collection' => $this]);
        if ($this->_eventPrefix && $this->_eventObject) {
            $this->_eventManager->dispatch($this->_eventPrefix . '_load_before', [$this->_eventObject => $this]);
        }
        return $this;
    }
}
