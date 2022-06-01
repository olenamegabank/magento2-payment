<?php

namespace MegaBank\Payment\Model;

use Magento\Framework\Model\AbstractModel;
use MegaBank\Payment\Api\QuoteTransactionInterface;

/**
 * Class QuoteTransaction
 * @package MegaBank\Payment\Model
 */
class QuoteTransaction extends AbstractModel implements QuoteTransactionInterface
{
    /**
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * @param int $id
     * @return QuoteTransactionInterface|QuoteTransaction
     */
    public function setQuoteId($id)
    {
        return $this->setData(self::QUOTE_ID, $id);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * @param string $id
     * @return QuoteTransactionInterface|QuoteTransaction
     */
    public function setTransactionId($id)
    {
        return $this->setData(self::TRANSACTION_ID, $id);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getTransactionType()
    {
        return $this->getData(self::TRANSACTION_TYPE);
    }

    /**
     * @param string $type
     * @return QuoteTransactionInterface|QuoteTransaction
     */
    public function setTransactionType($type)
    {
        return $this->setData(self::TRANSACTION_TYPE, $type);
    }

    /**
     * @return array|int|mixed|null
     */
    public function getIsCompleted()
    {
        return $this->getData(self::IS_COMPLETED);
    }

    /**
     * @param int $isCompleted
     * @return QuoteTransactionInterface|QuoteTransaction
     */
    public function setIsCompleted($isCompleted)
    {
        return $this->setData(self::IS_COMPLETED, $isCompleted);
    }

    /**
     * @return int|null
     */
    public function getIsCanceled()
    {
        return $this->getData(self::IS_CANCELED);
    }

    /**
     * @param int $isCanceled
     * @return QuoteTransactionInterface|QuoteTransaction
     */
    public function setIsCanceled($isCanceled)
    {
        return $this->setData(self::IS_CANCELED, $isCanceled);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $date
     * @return QuoteTransactionInterface|QuoteTransaction
     */
    public function setCreatedAt($date)
    {
        return $this->setData(self::CREATED_AT, $date);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\QuoteTransaction::class);
    }
}
