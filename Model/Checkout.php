<?php

namespace MegaBank\Payment\Model;

use Exception;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use MegaBank\Payment\Api\CheckoutInterface;
use MegaBank\Payment\Api\QuoteTransactionRepositoryInterface;
use MegaBank\Payment\Helper\Data;
use MegaBank\Payment\Logger\Logger;

/**
 * Class Checkout
 * @package MegaBank\Payment\Model
 */
class Checkout implements CheckoutInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var Operation
     */
    protected $operation;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var QuoteTransactionRepositoryInterface
     */
    protected $quoteTransactionRepository;

    /**
     * Checkout constructor.
     * @param QuoteTransactionRepositoryInterface $quoteTransactionRepository
     * @param Logger $logger
     * @param Data $helper
     * @param Operation $operation
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        QuoteTransactionRepositoryInterface $quoteTransactionRepository,
        Logger $logger,
        Data $helper,
        Operation $operation,
        CartRepositoryInterface $cartRepository
    ) {
        $this->quoteTransactionRepository = $quoteTransactionRepository;
        $this->logger = $logger;
        $this->cartRepository = $cartRepository;
        $this->operation = $operation;
        $this->helper = $helper;
    }

    /**
     * @param int $cartId
     * @return false|string
     */
    public function getWidget($cartId)
    {
        try {
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($cartId);

            $payment = $quote->getPayment();

            $amount = $quote->getBaseGrandTotal();
            $currency = $quote->getBaseCurrencyCode();
            $description = $this->helper->getOrderDescription();
            $lang = $this->helper->getLanguage();
            $type = $this->helper->getTransactionType();

            $transactionId = $this->operation->registerTransaction(
                $type,
                $amount,
                $this->helper->getCurrencyNumber($currency),
                $description,
                $lang
            );
            if ($transactionId) {
                $quoteTransaction = $this->quoteTransactionRepository->create();
                $quoteTransaction
                    ->setQuoteId($quote->getId())
                    ->setTransactionId($transactionId)
                    ->setTransactionType($type);
                $this->quoteTransactionRepository->save($quoteTransaction);

                $payment->setAdditionalInformation('mb_transaction', $transactionId);
                $payment->setAdditionalInformation('mb_amount', $amount);
                $payment->setAdditionalInformation('mb_type', $this->helper->getTransactionType());
                $payment->setAdditionalInformation('mb_currency', $currency);
                $quote->save();
                $widget = $this->operation->widget($transactionId, $lang);
                return json_encode(['widget' => $widget]);
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return json_encode(['message' => __('An error occurred while creating a payment. Please try again.')]);
    }

    /**
     * @param int $cartId
     * @return false|string
     * @throws NoSuchEntityException
     */
    public function check($cartId)
    {
        $message = false;
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $payment = $quote->getPayment();
        $transaction = $payment->getAdditionalInformation('mb_transaction');
        if ($transaction) {
            $startAmount = $payment->getAdditionalInformation('mb_amount');
            $startCurrency = $payment->getAdditionalInformation('mb_currency');
            if ($startAmount != $quote->getBaseGrandTotal() || $startCurrency != $quote->getBaseCurrencyCode()) {
                $message = __('The order amount has changed. Repeat the data entry for the payment.');
            }
        } else {
            $message = __('An error occurred while creating a payment. Please try again.');
        }
        if ($message) {
            return json_encode(['success' => false, 'message' => $message]);
        }
        return json_encode(['success' => true]);
    }
}
