<?php

namespace MegaBank\Payment\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Model\MethodInterface;

/**
 * Class TransactionType
 * @package MegaBank\Payment\Model\Config
 */
class TransactionType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['value' => MethodInterface::ACTION_AUTHORIZE_CAPTURE, 'label' => __('Sms (Single Message System)')];
        $options[] = ['value' => MethodInterface::ACTION_AUTHORIZE, 'label' => __('Dms (Dual Message System)')];
        return $options;
    }
}
