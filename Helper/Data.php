<?php

namespace MegaBank\Payment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Store\Model\ScopeInterface;
use Payum\ISO4217\ISO4217;

/**
 * Class Data
 * @package MegaBank\Payment\Helper
 */
class Data extends AbstractHelper
{
    /**
     * XML PATH IS ENABLED
     */
    const XML_PATH_IS_ENABLED = 'payment/megabank_payment/active';

    /**
     * XML PATH TRANSACTION TYPE
     */
    const XML_PATH_TRANSACTION_TYPE = 'payment/megabank_payment/payment_action';

    /**
     * XML PATH DESCRIPTION
     */
    const XML_PATH_DESCRIPTION = 'payment/megabank_payment/description';

    /**
     * XML PATH ENABLE LOG
     */
    const XML_PATH_ENABLE_LOG = 'payment/megabank_payment/enable_log';

    /**
     * XML PATH LANG
     */
    const XML_PATH_LANG = 'payment/megabank_payment/language';

    /**
     * XML PATH ALLOWED IP
     */
    const XML_PATH_ALLOWED_IP = 'payment/megabank_payment/allowed_ip';

    /**
     * XML PATH CLOSE DAY CRON ENABLE
     */
    const XML_PATH_CLOSE_DAY_CRON_ENABLE = 'payment/megabank_payment/close_day/enable';

    /**
     * @var ISO4217
     */
    protected $ISO4217;

    /**
     * Data constructor.
     * @param ISO4217 $ISO4217
     * @param Context $context
     */
    public function __construct(ISO4217 $ISO4217, Context $context)
    {
        parent::__construct($context);
        $this->ISO4217 = $ISO4217;
    }

    /**
     * @return bool
     */
    public function isAllowedByIp()
    {
        $allowedIps = $this->scopeConfig->getValue(self::XML_PATH_ALLOWED_IP);
        if ($allowedIps) {
            $allowedIps = array_filter(array_map('trim', explode(PHP_EOL, $allowedIps)));
            $currentIp = $this->_remoteAddress->getRemoteAddress();
            if (!$currentIp || !in_array($currentIp, $allowedIps)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function isEnableCronCloseDay()
    {
        return $this->isEnable() && $this->scopeConfig->isSetFlag(self::XML_PATH_CLOSE_DAY_CRON_ENABLE);
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    /**
     * @return bool
     */
    public function isDebugLogEnable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_LOG);
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TRANSACTION_TYPE,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @param string $currencyCode
     * @return int
     */
    public function getCurrencyNumber($currencyCode)
    {
        $quoteCurrency = $this->ISO4217->findByAlpha3($currencyCode);
        return $quoteCurrency->getNumeric();
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        $lang = $this->scopeConfig->getValue(
            self::XML_PATH_LANG,
            ScopeInterface::SCOPE_STORE
        );
        return $lang ? $lang : 'en';
    }

    /**
     * @return string
     */
    public function getOrderDescription()
    {
        return trim($this->scopeConfig->getValue(
            self::XML_PATH_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        ));
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->_getUrl('megabankpayment/payment/success/');
    }
}
