<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_B2bCustomerAttributes
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\B2bCustomerAttributes\Plugin\Model\Metadata\Form;

class File
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension
     */
    protected $fileValidator;

    /**
     * @var \Bss\CustomerAttributes\Helper\Customerattribute
     */
    protected $customerAttribute;

    /**
     * File constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $fileValidator
     * @param \Bss\CustomerAttributes\Helper\Customerattribute $customerAttribute
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $fileValidator,
        \Bss\CustomerAttributes\Helper\Customerattribute $customerAttribute
    ) {
        $this->request = $request;
        $this->fileValidator = $fileValidator;
        $this->customerAttribute = $customerAttribute;
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param $value
     * @return array|bool|string[]
     */
    public function aroundValidateValue(
        $subject,
        callable $proceed,
        $value
    ) {
        $attribute = $subject->getAttribute();
        $attributeCollection = $this->customerAttribute->getUserDefinedAttribures();
        $customAttributeArr = [];
        foreach ($attributeCollection as $customAttribute) {
            $customAttributeArr[] = $customAttribute->getAttributeCode();
        }

        $attributeCode = $attribute->getAttributeCode();
        $type = $attribute->getFrontendInput();

        if ($type == 'file' && in_array($attributeCode, $customAttributeArr)) {
            $files = $this->request->getFiles();
            $fileData = $files[$attribute->getAttributeCode()];

            $errors = $this->validateAttribute($subject, $attribute, $fileData);
            if (count($errors) == 0) {
                return true;
            }
            return $errors;
        } else {
            return $proceed($value);
        }
    }

    /**
     * @param $subject
     * @param $attribute
     * @param $fileData
     * @return array|bool
     */
    public function validateAttribute($subject, $attribute, $fileData)
    {
        $page = $this->request->getFullActionName();
        $usedInForms = $attribute->getUsedInForms();
        $errors = [];
        $toDelete = !empty($fileData['delete']) ? true : false;
        $toUpload = !empty($fileData['tmp_name']) ? true : false;
        if (in_array('is_customer_attribute', $usedInForms)) {
            if (!$this->customerAttribute->getConfig('bss_customer_attribute/general/enable')) {
                return $errors;
            }

            if (!in_array('customer_account_create_frontend', $usedInForms)
                && $page == 'customer_account_createpost'
            ) {
                return $errors;
            }

            if (!in_array('b2b_account_create', $usedInForms)
                && $page == 'customer_account_createpost'
            ) {
                return $errors;
            }

            if (!in_array('customer_account_edit_frontend', $usedInForms)
                && $page == 'customer_account_editPost'
            ) {
                return $errors;
            }

            if (!in_array('b2b_account_edit', $usedInForms)
                && $page == 'customer_account_editPost'
            ) {
                return $errors;
            }

            if (!$toUpload && !$toDelete && $subject->getEntity()->getData($attribute->getAttributeCode())) {
                return $errors;
            }

            if (!$attribute->getIsRequired() && !$toUpload) {
                return $errors;
            }

            if ($attribute->getIsRequired() && !$toUpload) {
                $label = __($attribute->getStoreLabel());
                $errors[] = __('"%1" is a required value.', $label);
            }

            if ($toUpload) {
                $errors = array_merge($errors, $this->validateByRules($attribute, $fileData));
            }

            if (count($errors) == 0) {
                return true;
            }
        }

        return $errors;
    }

    /**
     * Validate file by attribute validate rules
     * Return array of errors
     *
     * @param $attribute
     * @param $fileData
     * @return array
     */
    protected function validateByRules($attribute, $fileData)
    {
        $label = $attribute->getStoreLabel();
        $rules = $attribute->getValidateRules();
        $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);

        if (!empty($rules['file_extensions'])) {
            $extensions = explode(',', $rules['file_extensions']);
            $extensions = array_map('trim', $extensions);
            if (!in_array($extension, $extensions)) {
                return [__('"%1" is not a valid file extension.', $label)];
            }
        }

        /**
         * Check protected file extension
         */
        if (!$this->fileValidator->isValid($extension)) {
            return $this->fileValidator->getMessages();
        }

        if (!empty($rules['max_file_size'])) {
            $size = $fileData['size'];
            if ($rules['max_file_size'] < $size) {
                return [__('"%1" exceeds the allowed file size.', $label)];
            }
        }

        return [];
    }
}
