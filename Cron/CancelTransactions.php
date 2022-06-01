<?php

namespace MegaBank\Payment\Cron;

use MegaBank\Payment\Api\Constraints;
use MegaBank\Payment\Api\QuoteTransactionInterface;
use MegaBank\Payment\Api\QuoteTransactionRepositoryInterface;
use MegaBank\Payment\Model\Operation;
use MegaBank\Payment\Model\ResourceModel\QuoteTransaction\Collection;
use MegaBank\Payment\Model\ResourceModel\QuoteTransaction\CollectionFactory;

/**
 * Class CancelTransactions
 * @package MegaBank\Payment\Cron
 */
class CancelTransactions
{
    /**
     * @var Operation
     */
    protected $operation;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var QuoteTransactionRepositoryInterface
     */
    protected $repository;

    /**
     * CancelTransactions constructor.
     * @param QuoteTransactionRepositoryInterface $repository
     * @param CollectionFactory $collectionFactory
     * @param Operation $operation
     */
    public function __construct(
        QuoteTransactionRepositoryInterface $repository,
        CollectionFactory $collectionFactory,
        Operation $operation
    ) {
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
        $this->operation = $operation;
    }

    /**
     * Close Business Day Command
     */
    public function cancelTransactions()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addCreatedFilter(Constraints::TRANSACTION_LIFE_TIME);
        $collection->addNotCompletedFilter();
        $collection->addNotCanceledFilter();

        foreach ($collection as $transaction) {
            /** @var QuoteTransactionInterface $transaction */
            $this->operation->cancel($transaction->getTransactionId());
            $transaction->setIsCanceled(1);
            $this->repository->save($transaction);
        }
    }

    /**
     *
     */
    public function clearTransactions()
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addCreatedFilter(Constraints::TRANSACTION_CLEAR_TIME);

        foreach ($collection as $transaction) {
            /** @var QuoteTransactionInterface $transaction */
            $this->repository->delete($transaction);
        }
    }
}

