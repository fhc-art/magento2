# Mage2 Module Tbi CustomerIntegration

    ``tbi/module-customerintegration``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
API Customer Integration

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Tbi`
 - Enable the module by running `php bin/magento module:enable Tbi_CustomerIntegration`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require tbi/module-customerintegration`
 - enable the module by running `php bin/magento module:enable Tbi_CustomerIntegration`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - url (customerintegration/general/url)


## Specifications

 - Observer
	- customer_register_success > Tbi\CustomerIntegration\Observer\Customer\RegisterSuccess

 - Observer
	- customer_delete_after > Tbi\CustomerIntegration\Observer\Customer\DeleteAfter

 - Observer
	- adminhtml_customer_save_after > Tbi\CustomerIntegration\Observer\Backend\Adminhtml\CustomerSaveAfter

 - Observer
	- customer_account_edited > Tbi\CustomerIntegration\Observer\Customer\AccountEdited

 - API Endpoint
	- POST - Tbi\CustomerIntegration\Api\AddCustomerManagementInterface > Tbi\CustomerIntegration\Model\AddCustomerManagement

 - API Endpoint
	- PUT - Tbi\CustomerIntegration\Api\UpdateCustomerManagementInterface > Tbi\CustomerIntegration\Model\UpdateCustomerManagement

 - API Endpoint
	- GET - Tbi\CustomerIntegration\Api\GetCustomerManagementInterface > Tbi\CustomerIntegration\Model\GetCustomerManagement

 - API Endpoint
	- DELETE - Tbi\CustomerIntegration\Api\DeleteCustomerManagementInterface > Tbi\CustomerIntegration\Model\DeleteCustomerManagement


## Attributes



