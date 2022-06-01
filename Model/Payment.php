<?php

namespace MegaBank\Payment\Model;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\Method\Logger;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use MegaBank\Payment\Api\Constraints;
use MegaBank\Payment\Api\QuoteTransactionRepositoryInterface;
use MegaBank\Payment\Helper\Data as MegaBankData;

/**
 * Class Payment
 * @package MegaBank\Payment\Model
 */
class Payment extends AbstractMethod
{
    /**
     * METHOD CODE
     */
    const METHOD_CODE = 'megabank_payment';

    /**
     * ORDER STATUS PAID
     */
    const ORDER_STATUS_PAID = 'megabank_paid';

    /**
     * ORDER STATUS SUCCESS HOLD
     */
    const ORDER_STATUS_SUCCESS_HOLD = 'megabank_success_hold';

    /**
     * @var string
     */
    protected $_code = self::METHOD_CODE;

    /**
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = false;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * @var MegaBankData
     */
    protected $helper;

    /**
     * @var Operation
     */
    protected $operation;

    /**
     * @var QuoteTransactionRepositoryInterface
     */
    protected $quoteTransactionRepository;

    /**
     * Payment constructor.
     * @param QuoteTransactionRepositoryInterface $quoteTransactionRepository
     * @param Operation $operation
     * @param MegaBankData $helper
     * @param Context $context
     * @param Registry $registry
     * @param ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param Data $paymentData
     * @param ScopeConfigInterface $scopeConfig
     * @param Logger $logger
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     * @param DirectoryHelper|null $directory
     */
    public function __construct(
        QuoteTransactionRepositoryInterface $quoteTransactionRepository,
        Operation $operation,
        MegaBankData $helper,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
        ScopeConfigInterface $scopeConfig,
        Logger $logger,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $paymentData,
            $scopeConfig,
            $logger,
            $resource,
            $resourceCollection,
            $data,
            $directory
        );
        $this->quoteTransactionRepository = $quoteTransactionRepository;
        $this->operation = $operation;
        $this->helper = $helper;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isActive($storeId = null)
    {
        return parent::isActive($storeId) && $this->helper->isAllowedByIp();
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this|Payment
     * @throws LocalizedException
     */
    public function authorize(InfoInterface $payment, $amount)
    {
        $transaction = $payment->getAdditionalInformation('mb_transaction');
        if (!$transaction) {
            throw new LocalizedException(__('A payment error has occurred. Please repeat the data entry for the payment.'));
        }
        $startAmount = $payment->getAdditionalInformation('mb_amount');
        if ($startAmount == $amount) {
            $result = $this->operation->result($transaction);
            $this->checkResult($result);
            $this->setQuoteTransactionIsComplete($transaction);
            if ($responseData = $this->operation->getLastOperationResponse()) {
                $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $responseData);
            }

            $payment->setTransactionId($transaction)->setIsTransactionClosed(0);
        } else {
            throw new LocalizedException(__('The order amount has changed. Repeat the data entry for the payment.'));
        }
        return $this;
    }

    /**
     * @param $result
     * @param bool $isFrontend
     * @return $this
     * @throws LocalizedException
     */
    protected function checkResult($result, $isFrontend = true)
    {
        if ($result !== true) {
            $error = $this->operation->getLastOperationError();

            if ($isFrontend && $error) {
                throw new LocalizedException(__('A payment error has occurred. Error: %1. Please try again.', $error));
            } elseif ($isFrontend) {
                throw new LocalizedException(__('A payment error has occurred. Please repeat the data entry for the payment.'));
            } elseif ($error) {
                throw new LocalizedException(__('A payment error has occurred. Operation result: %1. Api error: %2', $result, $error));
            }
            throw new LocalizedException(__('A payment error has occurred. Operation result: %1', $result));
        }
        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this|Payment
     * @throws LocalizedException
     */
    public function capture(InfoInterface $payment, $amount)
    {
        if ($payment->getParentTransactionId()) {
            $transaction = $payment->getAdditionalInformation('mb_transaction');
            if (!$transaction) {
                throw new LocalizedException(__('Can\'t find transaction for capture.'));
            }

            $result = $this->operation->completeDms($transaction, $amount, $this->helper->getCurrencyNumber($payment->getOrder()->getBaseCurrencyCode()));
            if ($responseData = $this->operation->getLastOperationResponse()) {
                $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $responseData);
            }
            $this->checkResult($result, false);
            return $this;
        } else {
            $transaction = $payment->getAdditionalInformation('mb_transaction');
            if (!$transaction) {
                throw new LocalizedException(__('A payment error has occurred. Please repeat the data entry for the payment.'));
            }
            $startAmount = $payment->getAdditionalInformation('mb_amount');
            if ($startAmount == $amount) {
                $result = $this->operation->result($transaction);
                $this->checkResult($result);
                $this->setQuoteTransactionIsComplete($transaction);
                if ($responseData = $this->operation->getLastOperationResponse()) {
                    $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $responseData);
                }
                $payment->setTransactionId($transaction);
            } else {
                throw new LocalizedException(__('The order amount has changed. Repeat the data entry for the payment.'));
            }
            return $this;
        }
    }

    /**
     * @param $transactionId
     */
    protected function setQuoteTransactionIsComplete($transactionId)
    {
        $quoteTransaction = $this->quoteTransactionRepository->get($transactionId);
        if ($quoteTransaction) {
            $quoteTransaction->setIsCompleted(1);
            $this->quoteTransactionRepository->save($quoteTransaction);
        }
    }

    /**
     * @return bool
     * @throws LocalizedException
     */
    public function canRefund()
    {
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $this->getInfoInstance();
        $order = $payment->getOrder();
        if ($order) {
            $creationTime = $order->getCreatedAt();
            if (strtotime('-' . Constraints::ALLOWED_DAYS_COUNT_FOR_ACTION . 'days') > strtotime($creationTime)) {
                return false;
            }
        }
        return $this->_canRefund;
    }

    /**
     * @param InfoInterface $payment
     * @param float $amount
     * @return $this|Payment
     * @throws LocalizedException
     */
    public function refund(InfoInterface $payment, $amount)
    {
        parent::refund($payment, $amount);
        $transaction = $payment->getAdditionalInformation('mb_transaction');
        $transactionType = $payment->getAdditionalInformation('mb_type');
        $result = $this->operation->cancelTransaction($transaction, $transactionType, $payment->getOrder()->getCreatedAt(), $amount);

        if ($responseData = $this->operation->getLastOperationResponse()) {
            if (isset($responseData['REFUND_TRANS_ID']) && $refundTransaction = $responseData['REFUND_TRANS_ID']) {
                $payment->setTransactionId($refundTransaction);
            }
            $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $responseData);
        }
        $this->checkResult($result, false);
        $payment->setIsTransactionClosed(true);
        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @return $this|Payment
     * @throws LocalizedException
     */
    public function cancel(InfoInterface $payment)
    {
        $transaction = $payment->getRefundTransactionId();
        $result = $this->operation->cancel($transaction);

        if ($responseData = $this->operation->getLastOperationResponse()) {
            $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $responseData);
        }
        $this->checkResult($result, false);
        $payment->setIsTransactionClosed(true);
        return $this;
    }

    /**
     * @param InfoInterface $payment
     * @return $this|Payment
     * @throws LocalizedException
     */
    public function void(InfoInterface $payment)
    {
        parent::void($payment);
        $transaction = $payment->getAuthorizationTransaction();
        $result = $this->operation->cancel($transaction->getTxnId());

        if ($responseData = $this->operation->getLastOperationResponse()) {
            $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $responseData);
        }
        $this->checkResult($result, false);
        return $this;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

    /**
     * @param string $field
     * @param null $storeId
     * @return mixed|string
     * @throws LocalizedException
     */
    public function getConfigData($field, $storeId = null)
    {
        if ('order_status' === $field) {
            if ($this->getConfigPaymentAction() == 'authorize_capture') {
                return self::ORDER_STATUS_PAID;
            } elseif ($this->getConfigPaymentAction() == 'authorize') {
                $info = $this->getInfoInstance();
                if ($info && $info->getCreatedTransaction() && $info->getCreatedTransaction()->getTxnType() == TransactionInterface::TYPE_CAPTURE) {
                    return self::ORDER_STATUS_PAID;
                }
                return self::ORDER_STATUS_SUCCESS_HOLD;
            }
        }
        return parent::getConfigData($field, $storeId);
    }
}
