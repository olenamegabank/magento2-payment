<?php

namespace MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field;

use IntlTimeZone;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Locale\ResolverInterface;

/**
 * Class CronExpr
 * @package MegaBank\Payment\Block\Adminhtml\System\Config\Form\Field
 */
class CronExpr extends Field
{
    /**
     *
     * @var ResolverInterface
     */
    protected $_localeResolver;

    /**
     * CronExpr constructor.
     * @param Context $context
     * @param ResolverInterface $localeResolver
     * @param array $data
     */
    public function __construct(Context $context, ResolverInterface $localeResolver, array $data = [])
    {
        parent::__construct($context, $data);
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Retrieve HTML markup for given form element
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->setType('hidden');
        $element->setExtType('hiddenfield');
        $this->addJqCronPlugin($element);
        $this->addTimezoneComment($element);
        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     * @throws LocalizedException
     */
    protected function addJqCronPlugin(AbstractElement $element)
    {
        $_htmlId = $element->getHtmlId();
        $assetCss = $this->_assetRepo->createAsset('MegaBank_Payment::css/jqCron.css');

        $afterJs ="<link rel=\"stylesheet\" href=\"{$assetCss->getUrl()}\">
            <script>
            require([
                'jquery',
                'domReady!',
                'MegaBank_Payment/js/jqCron'
            ], function ($) {
                $('#{$_htmlId}').jqCron({
                   no_reset_button: false,
                   multiple_dom: true,
                   multiple_month: true,
                   multiple_mins: true,
                   multiple_dow: true,
                   multiple_time_hours: true,
                   multiple_time_minutes: true
                });
            });
            </script>";
        $element->setAfterElementJs($afterJs);
    }

    /**
     * @param AbstractElement $element
     */
    protected function addTimezoneComment(AbstractElement $element)
    {
        $timeZoneCode = $this->_localeDate->getConfigTimezone();
        $locale = $this->_localeResolver->getLocale();
        $getLongTimeZoneName = IntlTimeZone::createTimeZone($timeZoneCode)->getDisplayName(false, IntlTimeZone::DISPLAY_LONG, $locale);
        $element->setData('comment', sprintf("%s (%s)", $getLongTimeZoneName, $timeZoneCode));
    }
}
