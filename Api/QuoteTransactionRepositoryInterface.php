<?php

namespace MegaBank\Payment\Api;

/**
 * Interface QuoteTransactionRepositoryInterface
 * @package MegaBank\Payment\Api
 */
interface QuoteTransactionRepositoryInterface
{
    /**
     * Loads a specified transaction.
     *
     * @param string $transactionId The transaction ID.
     * @return QuoteTransactionInterface Transaction interface.
     */
    public function get($transactionId);

    /**
     * Deletes a specified transaction.
     *
     * @param QuoteTransactionInterface $entity The transaction.
     * @return bool
     */
    public function delete(QuoteTransactionInterface $entity);

    /**
     * Saves a specified transaction.
     *
     * @param QuoteTransactionInterface $entity The transaction.
     * @return QuoteTransactionInterface Transaction interface.
     */
    public function save(QuoteTransactionInterface $entity);

    /**
     * Creates new Transaction instance.
     *
     * @return QuoteTransactionInterface Transaction interface.
     */
    public function create();
}
