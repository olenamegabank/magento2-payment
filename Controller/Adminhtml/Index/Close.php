<?php

namespace MegaBank\Payment\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use MegaBank\Payment\Model\Operation;

/**
 * Class Close
 * @package MegaBank\Payment\Controller\Adminhtml\Index
 */
class Close extends Action
{
    /**
     * @var Operation
     */
    protected $operation;

    /**
     * Close constructor.
     * @param Operation $operation
     * @param Action\Context $context
     */
    public function __construct(
        Operation $operation,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->operation = $operation;
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        try {
            $this->operation->closeBusinessDay($this->messageManager);
        } catch (Exception $e) {
            $this->messageManager->addExceptionMessage($e);
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}
