<?php

namespace MegaBank\Payment\Logger;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Handler
 * @package MegaBank\Payment\Logger
 */
class Handler extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/megabank_payment.log';
}
