<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2017 Mageants
 * @author Mageants Team <support@mageants.com>
 */
 
namespace Mageants\StorePickup\Block\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
/**
 * Class Locations Backend system config array field renderer
 */
class Locations extends AbstractFieldArray
{
    /**
     * Initialise columns for 'Store Locations'
     * Label is name of field
     * Class is storefront validation action for field
     *
     * @return void
     */
    protected function _construct()
    {
        $this->addColumn(
            'firstName',
            [
                'label' => __('Store First Name'),
                'class' => 'validate-no-empty validate-alphanum-with-spaces',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'lastName',
            [
                'label' => __('Store Last Name'),
                'class' => 'validate-no-empty validate-alphanum-with-spaces',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'street1',
            [
                'label' => __('Street Address 1'),
                'class' => 'validate-no-empty',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'street2',
            [
                'label' => __('Street Address 2'),
                'class' => 'validate-no-empty',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'city',
            [
                'label' => __('City'),
                'class' => 'validate-no-empty',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'zipCode',
            [
                'label' => __('Zip Code'),
                'class' => 'validate-no-empty validate-number',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'state',
            [
                'label' => __('State'),
                'class' => 'validate-no-empty',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'countryId',
            [
                'label' => __('Country Id'),
                'class' => 'validate-no-empty',
                'style' => 'width:150px'
            ]
        );
        $this->addColumn(
            'phone',
            [
                'label' => __('Phone Number'),
                'class' => 'validate-no-empty validate-number',
                'style' => 'width:150px'
            ]
        );


        $this->_addAfter = false;
        parent::_construct();
    }
    
    /* public function renderCellTemplate($columnName)
		{
			/* if ($columnName == "street") {
				$this->_columns[$columnName]['class'] = 'input-text required-entry';
				//$this->_columns[$columnName]['style'] = 'width:50px';
				$this->_columns[$columnName]['renderer'] = '';
			} */
            //return parent::renderCellTemplate($columnName);
        //}
        
}
