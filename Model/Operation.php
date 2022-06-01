<?php

namespace MegaBank\Payment\Model;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Payment\Model\MethodInterface;
use MegaBank\AbstractCommand;
use MegaBank\Commands\Cancel;
use MegaBank\Commands\CompleteDms;
use MegaBank\Commands\CreateDmsTransaction;
use MegaBank\Commands\CreateSmsTransaction;
use MegaBank\Commands\Refund;
use MegaBank\Commands\TransactionResult;
use MegaBank\Commands\Widget;
use MegaBank\Payment\Api\Constraints;
use MegaBank\Payment\Helper\ApiConfig;
use MegaBank\Payment\Model\Operations\CloseBusinessDay;

/**
 * Class Operation
 * @package MegaBank\Payment\Model
 */
class Operation
{
    /**
     * @var AbstractCommand[]
     */
    protected $pool = [];

    /**
     * @var ApiConfig
     */
    protected $apiConfigHelper;

    /**
     * @var AbstractCommand|null
     */
    protected $lastCommand;

    /**
     * Operation constructor.
     * @param ApiConfig $apiConfigHelper
     * @param array $pool
     */
    public function __construct(
        ApiConfig $apiConfigHelper,
        $pool = []
    ) {
        $this->pool = $pool;
        $this->apiConfigHelper = $apiConfigHelper;
    }

    /**
     * @param string $operation
     * @return AbstractCommand
     * @throws LocalizedException
     */
    public function getOperation($operation)
    {
        if (isset($this->pool[$operation])) {
            $operation = $this->pool[$operation];
            $command = $operation->create();
            $command->setConfig($this->apiConfigHelper->getApiConfig());
        } else {
            throw new Exception('Unknown Command');
        }
        $this->lastCommand = $command;
        return $command;
    }

    /**
     * Remove Tmp Files
     */
    protected function afterExecute()
    {
        $this->apiConfigHelper->destroyTmpFiles();
    }

    /**
     * @param MessageManagerInterface|false $messageManager
     * @return bool
     * @throws LocalizedException
     */
    public function closeBusinessDay($messageManager = false)
    {
        /** @var CloseBusinessDay $operation */
        $operation = $this->getOperation('closeBusinessDay');
        $operation->execute($messageManager);
        $this->afterExecute();
        return $operation->getResult();
    }

    /**
     * @param string $transId
     * @param string|int|float $amount
     * @param numeric $currency
     * @param string $description
     * @return bool
     * @throws LocalizedException
     */
    public function completeDms($transId, $amount, $currency, $description = '')
    {
        /** @var CompleteDms $operation */
        $operation = $this->getOperation('completeDms');
        $operation->execute($transId, $amount, $currency, $description);
        $this->afterExecute();
        return $operation->getResult();
    }

    /**
     * @param $transId
     * @param string $transactionType
     * @param string $createdAt
     * @param string $amount
     * @param string $suspectedFraud
     * @return bool
     * @throws LocalizedException
     */
    public function cancelTransaction($transId, $transactionType, $createdAt = '', $amount = '', $suspectedFraud = '')
    {
        if ($transactionType == MethodInterface::ACTION_AUTHORIZE) {
            return $this->refund($transId, $amount);
        }
        if ($createdAt && strtotime('-' . Constraints::MAX_HOURS_FOR_CANCEL_OPERATION . 'hours') < strtotime($createdAt)) {
            return $this->cancel($transId, $amount, $suspectedFraud);
        }
        return $this->refund($transId, $amount);
    }

    /**
     * @param string $transId
     * @param string|int|float $amount
     * @return bool
     * @throws LocalizedException
     */
    public function refund($transId, $amount = '')
    {
        /** @var Refund $operation */
        $operation = $this->getOperation('refund');
        $operation->execute($transId, $amount);
        $this->afterExecute();
        return $operation->getResult();
    }

    /**
     * @param string $transId
     * @param string|int|float $amount
     * @param string $suspectedFraud
     * @return bool
     * @throws LocalizedException
     */
    public function cancel($transId, $amount = '', $suspectedFraud = '')
    {
        /** @var Cancel $operation */
        $operation = $this->getOperation('cancel');
        $operation->execute($transId, $amount, $suspectedFraud);
        $this->afterExecute();
        return $operation->getResult();
    }

    /**
     * @param string $transId
     * @return bool
     * @throws LocalizedException
     */
    public function result($transId)
    {
        /** @var TransactionResult $operation */
        $operation = $this->getOperation('result');
        $operation->execute($transId);
        $this->afterExecute();
        return $operation->getResult();
    }

    /**
     * @param string $transId
     * @param string $lang
     * @param int|string $width
     * @param int|string $height
     * @param string $attrs
     * @return bool|string
     * @throws LocalizedException
     */
    public function widget($transId, $lang = '', $width = '100%', $height = 600, $attrs = '')
    {
        /** @var Widget $operation */
        $operation = $this->getOperation('widget');
        $operation->execute($transId, $lang, $width, $height, $attrs);
        $this->afterExecute();
        return $operation->getResult();
    }

    /**
     * @param string $type
     * @param string|int|float $amount
     * @param numeric $currency
     * @param string $description
     * @param string $lang
     * @return false|string
     * @throws LocalizedException
     */
    public function registerTransaction($type, $amount, $currency, $description = '', $lang = '')
    {
        /** @var CreateSmsTransaction|CreateDmsTransaction $operation */
        $operation = $this->getOperation($type);
        $operation->execute($amount, $currency, $description, $lang);
        $this->afterExecute();
        return $operation->getResult();
    }

    /**
     * @return bool|string
     */
    public function getLastOperationError()
    {
        if ($this->lastCommand) {
            $error = $this->lastCommand->getError();
            if ($error) {
                return $error;
            }
            $codeDescription = $this->lastCommand->getResultCodeDescription();
            return $codeDescription ? __($codeDescription) : false;
        }
        return false;
    }

    /**
     * @return array|false|null
     */
    public function getLastOperationResponse()
    {
        if ($this->lastCommand) {
            return $this->lastCommand->getResponseData();
        }
        return false;
    }
}
