<?php

namespace MegaBank\Payment\Model\ResourceModel\QuoteTransaction;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MegaBank\Payment\Api\Constraints;
use MegaBank\Payment\Api\QuoteTransactionInterface;
use MegaBank\Payment\Model\QuoteTransaction;
use MegaBank\Payment\Model\ResourceModel\QuoteTransaction as QuoteTransactionResource;

/**
 * Class Collection
 * @package MegaBank\Payment\Model\ResourceModel\QuoteTransaction
 */
class Collection extends AbstractCollection
{
    /**
     * Id field name
     *
     * @var string
     */
    public $_idFieldName = 'id';

    /**
     * Constructor
     */
    public function _construct()
    {
        $this->_init(QuoteTransaction::class, QuoteTransactionResource::class);
    }

    public function addCreatedFilter($hours)
    {
        $this->getSelect()->where(QuoteTransactionInterface::CREATED_AT . ' <= DATE_ADD(NOW(), INTERVAL -' . $hours . ' HOUR)');
        return $this;
    }

    public function addNotCompletedFilter()
    {
        $this->addFieldToFilter(QuoteTransactionInterface::IS_COMPLETED, ['eq' => 0]);
        return $this;
    }

    public function addNotCanceledFilter()
    {
        $this->addFieldToFilter(QuoteTransactionInterface::IS_CANCELED, ['eq' => 0]);
        return $this;
    }
}
