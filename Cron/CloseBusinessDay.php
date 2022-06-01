<?php

namespace MegaBank\Payment\Cron;

use MegaBank\Payment\Helper\Data;
use MegaBank\Payment\Model\Operation;

/**
 * Class CloseBusinessDay
 * @package MegaBank\Payment\Cron
 */
class CloseBusinessDay
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Operation
     */
    protected $operation;

    /**
     * CloseBusinessDay constructor.
     * @param Data $helper
     * @param Operation $operation
     */
    public function __construct(
        Data $helper,
        Operation $operation
    ) {
        $this->helper = $helper;
        $this->operation = $operation;
    }

    /**
     * Close Business Day Command
     */
    public function execute()
    {
        if (!$this->helper->isEnableCronCloseDay()) {
            return;
        }
        $this->operation->closeBusinessDay();
    }
}

