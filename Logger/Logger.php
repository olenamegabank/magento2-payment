<?php

namespace MegaBank\Payment\Logger;

use MegaBank\LoggerInterface;
use MegaBank\Payment\Helper\Data;

/**
 * Class Logger
 * @package MegaBank\Payment\Logger
 */
class Logger extends \Monolog\Logger implements LoggerInterface
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Logger constructor.
     * @param Data $helper
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        Data $helper,
        $name,
        array $handlers = array(),
        array $processors = array()
    ) {
        parent::__construct($name, $handlers, $processors);
        $this->helper = $helper;
    }

    /**
     * @param string $response
     */
    public function logResponse($response)
    {
        $this->debug('Response: ' . $response);
    }

    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function debug($message, array $context = array())
    {
        if ($this->helper->isDebugLogEnable()) {
            return parent::debug($message, $context);
        }
        return false;
    }

    /**
     * @param string $request
     */
    public function logRequest($request)
    {
        $this->debug('Request: ' . $request);
    }
}
