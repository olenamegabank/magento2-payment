<?php

namespace MegaBank\Payment\Model\ResourceModel;

use MegaBank\Payment\Api\Constraints;
use MegaBank\Payment\Api\QuoteTransactionInterface;

class QuoteTransaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function _construct()
    {
        $this->_init(Constraints::QUOTE_TRANSACTIONS_TABLE, 'id');
    }

    public function getIdByTransactionId($transactionId)
    {
        $connection = $this->getConnection();
        return $connection->fetchOne($connection->select()
            ->from($this->getMainTable(), QuoteTransactionInterface::ID)
            ->where(QuoteTransactionInterface::TRANSACTION_ID .  '=?', $transactionId));
    }
}
