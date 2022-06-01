<?php

namespace MegaBank\Payment\Model\Operations;

use Exception;
use MegaBank\Payment\Logger\Logger;

/**
 * Class CloseBusinessDay
 * @package MegaBank\Payment\Model\Operations
 */
class CloseBusinessDay extends \MegaBank\Commands\CloseBusinessDay
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * CloseBusinessDay constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param false $messageManager
     * @return array
     */
    public function execute($messageManager = false)
    {
        try {
            parent::execute();
            if ($this->getResult() !== true) {
                $error = $this->getError();
                $resultCode = $this->getResultCode();
                if ($error) {
                    throw new Exception($error);
                } elseif ($resultCode) {
                    throw new Exception($this->getResultCodeDescription());
                } else {
                    throw new Exception();
                }
            }
            if ($messageManager) {
                $messageManager->addSuccessMessage(__('Business day has been successfully closed'));
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
            if (!$message) {
                $message = __('Unexpected error');
            }
            $this->logger->error('Close business day error: ' . $message);
            if ($messageManager) {
                $messageManager->addErrorMessage(__('Close business day error: %1', $message));
            }
        }
        return $this->getResponseData();
    }
}
