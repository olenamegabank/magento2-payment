<?php

namespace MegaBank\Payment\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Environment
 * @package MegaBank\Payment\Model\Config
 */
class Environment implements OptionSourceInterface
{
    /**
     * ENVIRONMENT PRODUCTION
     */
    const ENVIRONMENT_PRODUCTION = 'production';

    /**
     * ENVIRONMENT SANDBOX
     */
    const ENVIRONMENT_SANDBOX = 'sandbox';

    /**
     * Possible environment types
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => self::ENVIRONMENT_SANDBOX,
                'label' => __('Sandbox'),
            ],
            [
                'value' => self::ENVIRONMENT_PRODUCTION,
                'label' => __('Production')
            ]
        ];
    }
}
