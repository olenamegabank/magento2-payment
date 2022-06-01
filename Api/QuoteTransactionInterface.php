<?php

namespace MegaBank\Payment\Api;

/**
 * Interface QuoteTransactionInterface
 * @package MegaBank\Payment\Api
 */
interface QuoteTransactionInterface
{
    const ID = 'id';
    const QUOTE_ID = 'quote_id';
    const TRANSACTION_ID = 'transaction_id';
    const TRANSACTION_TYPE = 'transaction_type';
    const IS_COMPLETED = 'is_completed';
    const IS_CANCELED = 'is_canceled';
    const CREATED_AT = 'created_at';

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param int $id
     * @return QuoteTransactionInterface
     */
    public function setQuoteId($id);

    /**
     * @return string
     */
    public function getTransactionId();

    /**
     * @param string $id
     * @return QuoteTransactionInterface
     */
    public function setTransactionId($id);

    /**
     * @return string
     */
    public function getTransactionType();

    /**
     * @param string $type
     * @return QuoteTransactionInterface
     */
    public function setTransactionType($type);

    /**
     * @return int
     */
    public function getIsCompleted();

    /**
     * @param int $isCompleted
     * @return QuoteTransactionInterface
     */
    public function setIsCompleted($isCompleted);

    /**
     * @return int
     */
    public function getIsCanceled();

    /**
     * @param int $isCanceled
     * @return QuoteTransactionInterface
     */
    public function setIsCanceled($isCanceled);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $date
     * @return QuoteTransactionInterface
     */
    public function setCreatedAt($date);
}
