<?php

namespace MegaBank\Payment\Model;

use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use MegaBank\Payment\Api\QuoteTransactionInterface;
use MegaBank\Payment\Api\QuoteTransactionInterfaceFactory;
use MegaBank\Payment\Api\QuoteTransactionRepositoryInterface;
use MegaBank\Payment\Model\ResourceModel\QuoteTransaction as QuoteTransactionResource;

/**
 * Class QuoteTransaction
 * @package MegaBank\Payment\Model
 */
class QuoteTransactionRepository implements QuoteTransactionRepositoryInterface
{
    /**
     * @var QuoteTransactionInterfaceFactory
     */
    protected $entityFactory;

    /**
     * @var QuoteTransactionResource
     */
    protected $resource;

    /**
     * QuoteTransactionRepository constructor.
     * @param QuoteTransactionInterfaceFactory $entityFactory
     * @param QuoteTransactionResource $resource
     */
    public function __construct(
        QuoteTransactionInterfaceFactory $entityFactory,
        QuoteTransactionResource $resource
    ) {
        $this->entityFactory = $entityFactory;
        $this->resource = $resource;
    }

    /**
     * @param string $transactionId
     * @return false|QuoteTransactionInterface|QuoteTransaction
     */
    public function get($transactionId)
    {
        $id = $this->resource->getIdByTransactionId($transactionId);
        if ($id) {
            $object = $this->create();
            $this->resource->load($object, $id);
            return $object;
        }
        return false;
    }

    /**
     * @return QuoteTransactionInterface|QuoteTransaction
     */
    public function create()
    {
        return $this->entityFactory->create();
    }

    /**
     * @param QuoteTransactionInterface $entity
     * @return bool
     * @throws Exception
     */
    public function delete(QuoteTransactionInterface $entity)
    {
        $this->resource->delete($entity);
        return true;
    }

    /**
     * @param QuoteTransactionInterface $entity
     * @return QuoteTransactionInterface|QuoteTransaction
     * @throws AlreadyExistsException
     */
    public function save(QuoteTransactionInterface $entity)
    {
        $this->resource->save($entity);
        return $entity;
    }
}
