<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Ordersearch
 * @copyright  Copyright (c) 2016 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\CustomerPoNumberCustomization\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * XML config path search enable
     */
    const XML_PATH_ENABLE = 'ordersearch/general/enable';

    /**
     * XML config path date field enable
     */
    const XML_PATH_ENABLE_DATE_FILTER = 'ordersearch/settings/enable_date_filter';

    /**
     * XML config path filter list
     */
    const XML_PATH_ORDER_FIELDS_LIST = 'ordersearch/settings/order_fields_list';

    /**
     * XML config path date format
     */
    const XML_PATH_ORDER_DATE_REQUIRED = 'ordersearch/settings/date_require';

    /**
     * XML config path enable product name suggestion
     */
    const XML_PATH_ENABLE_PRODUCT_NAME_SUGGESTION = 'ordersearch/settings/enable_product_name_suggestion';

    const DATETIME_FROM_FORMAT = 'Y-m-d H:i:s';

    const DATETIME_TO_FORMAT = 'Y-m-d 23:59:59';

    /**
     * XML config path product name min query length
     */
    const XML_PATH_PRODUCT_NAME_MIN_LENGTH = 'ordersearch/settings/productname_min_query_lenth';

    /**
     * XML config path product name max query length
     */
    const XML_PATH_PRODUCT_NAME_SUGGEST_ERROR = 'ordersearch/settings/suggest_query_error';

    /**
     * XML config path product name results length
     */
    const XML_PATH_PRODUCT_NAME_RESULT_LENGTH = 'ordersearch/settings/suggested_result_number';

    /**
     * XML config path enable order download
     */
    const XML_PATH_ORDER_DOWNLOAD_ENABLE = 'ordersearch/order_download/order_download_enable';

    /**
     * XML config path enable order download PDF
     */
    const XML_PATH_ORDER_DOWNLOAD_PDF_ENABLE = 'ordersearch/order_download/order_download_pdf';

    /**
     * XML config path enable order download CSV
     */
    const XML_PATH_ORDER_DOWNLOAD_CSV_ENABLE = 'ordersearch/order_download/order_download_csv';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezoneInterface;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    private $dateTime;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magento\Framework\Intl\DateTimeFactory $dateTimeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->timezoneInterface = $timezoneInterface;
        $this->storeManager = $storeManager;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->dateTime = $dateTime;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getCurrentStoreId()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $this->storeManager->getStore()->getId();
    }

    /**
     * @return int
     */
    public function isEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function isDateFilterEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_DATE_FILTER, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function isDateRequired()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDER_DATE_REQUIRED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getOrderFilterList()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDER_FIELDS_LIST, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $selectedDate
     * @return date
     */
    public function getFromDate($fromDate)
    {
//        return $this->converFromTz($fromDate, $this->timezoneInterface->getDefaultTimezone(), $this->timezoneInterface->getConfigTimezone());
        return $this->converFromTz($fromDate, $this->timezoneInterface->getDefaultTimezone());
    }

    /**
     * @param $selectedDate
     * @return date
     */
    public function getToDate($toData)
    {
//        return $this->converToTz($toData, $this->timezoneInterface->getDefaultTimezone(), $this->timezoneInterface->getConfigTimezone());
        return $this->converToTz($toData, $this->timezoneInterface->getDefaultTimezone());
    }


    protected function converToTz($dateTime = "", $toTz = '')
    {
        $date = $this->dateTimeFactory->create($dateTime);
        date_default_timezone_set($toTz);
        $dateTime = $date->format(self::DATETIME_TO_FORMAT);
        return $dateTime;
    }

    protected function converFromTz($dateTime = "", $toTz = '')
    {
        $date = $this->dateTimeFactory->create($dateTime);
        date_default_timezone_set($toTz);
        $dateTime = $date->format(self::DATETIME_FROM_FORMAT);
        return $dateTime;
    }


    /**
     * @return int
     */
    public function isNameSuggestionEnable()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE_PRODUCT_NAME_SUGGESTION, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getMinQueryLength()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME_MIN_LENGTH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getProductResultsCount()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME_RESULT_LENGTH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function getSuggestLimitError()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_PRODUCT_NAME_SUGGEST_ERROR, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function isEnableOrderDownload()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDER_DOWNLOAD_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function isEnableOrderDownloadPDF()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDER_DOWNLOAD_PDF_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return int
     */
    public function isEnableOrderDownloadCSV()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ORDER_DOWNLOAD_CSV_ENABLE, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    protected function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
}
