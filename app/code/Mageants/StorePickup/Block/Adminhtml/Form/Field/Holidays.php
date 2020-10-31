<?php
/**
 * @category Mageants StorePickup
 * @package Mageants_StorePickup
 * @copyright Copyright (c) 2019 Mageants
 * @author Mageants Team <support@mageants.com>
 */

namespace Mageants\StorePickup\Block\Adminhtml\Form\Field;

class Holidays extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{

    /**
     * {@inheritdoc}
     */    
    public $_dateRenderer;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct( $context, $data );

    }

    public function _getDateColumnRenderer() {
        if( !$this->_dateRenderer ) {
            $this->_dateRenderer = $this->getLayout()->createBlock(
                '\Mageants\StorePickup\Block\Adminhtml\Form\Field\HolidaysCalendarRender',
                '',
                [
                    'data' => [
                        'is_render_to_js_template' => true,
                        'date_format'              => 'dd/mm/Y'
                    ]
                ]
                );
        }
        return $this->_dateRenderer;
    }

    public function _prepareToRender() {
        $this->addColumn( 'Date', [ 'label' => __( 'Date' ),'renderer' => $this->_getDateColumnRenderer()]);
        $this->_addAfter       = false;
        $this->_addButtonLabel = __( 'Add' );
    }

    

    
}

?>
