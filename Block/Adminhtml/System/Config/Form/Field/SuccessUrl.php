<?php

namespace MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field;

use Exception;
use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use MegaBank\Payment\Helper\Data;

/**
 * Class SuccessUrl
 * @package MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field
 */
class SuccessUrl extends Field
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * SuccessUrl constructor.
     * @param Template\Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(Template\Context $context, Data $helper, array $data = [])
    {
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        try {
            return '<input style="opacity:1;" readonly
                    id="' . $element->getHtmlId() . '" class="input-text admin__control-text"
                    value="' . $this->helper->getSuccessUrl() . '" onclick="this.select()" type="text"/>' .
                '<p class="note">' . __('Specify this url in the certificate request email') . '</p>';
        } catch (Exception $e) {
            return '<p class="note">' . $e->getMessage() . '</p>';
        }
    }
}

