<?php

namespace MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class CloseBusinessDay
 * @package MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field
 */
class CloseBusinessDay extends Field
{
    /**
     * CloseBusinessDay constructor.
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('value', __("Close Business Day Now"));
        $element->setData('class', "action-default");
        $element->setData('onclick', "location.href = '" . $this->getActionUrl() . "'");

        return parent::_getElementHtml($element);
    }

    /**
     * @return string
     */
    public function getActionUrl()
    {
        return $this->_urlBuilder->getUrl('megabankpayment/index/close');
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(AbstractElement $element)
    {
        return '';
    }
}
