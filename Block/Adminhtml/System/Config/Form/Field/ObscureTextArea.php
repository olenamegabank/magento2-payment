<?php

namespace MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\Textarea;

/**
 * Class ObscureTextArea
 * @package MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field
 */
class ObscureTextArea extends Textarea
{
    /**
     * @var string
     */
    protected $_obscuredValue = '******';

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('textarea admin__control-textarea');
        $html = "<span>";
        $html .= __('Paste %1 here', __($this->getFieldConfig('id')));
        $html .= '</span><br>';

        $html .= '<textarea id="' . $this->getHtmlId() . '" name="' . $this->getName() . '" '
            . $this->serialize($this->getHtmlAttributes()) . $this->_getUiId() . ' >';
        $html .= $this->getEscapedValue();
        $html .= "</textarea>";
        $html .= "<br><span>";
        $html .= __('or select file on your computer');
        $html .= '</span> <input type="file" id="' . $this->getHtmlId() . '-inputfile"/>';

        $js = "<script>//<![CDATA[
                var control = document.getElementById('" . $this->getHtmlId() . "-inputfile');
                control.addEventListener('change', function(event){
                    var reader = new FileReader();
                    reader.onload = function(event){
                        document.getElementById('" . $this->getHtmlId() . "').value = event.target.result;
                    };
                    reader.onerror = function(event){
                        alert('File could not be read! Code ' + event.target.error.code);
                    };
                    reader.readAsText(event.target.files[0]);
                }, false);
            //]]></script>";
        $html .= $js;
        $html .= $this->getAfterElementHtml();

        return $html;
    }

    /**
     * @param null $index
     * @return string
     */
    public function getEscapedValue($index = null)
    {
        $value = parent::getEscapedValue($index);
        if (!empty($value)) {
            return $this->_obscuredValue;
        }
        return $value;
    }
}
