<?php

namespace MegaBank\Payment\Controller\Payment;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

/**
 * Class Success
 * @package MegaBank\Payment\Controller\Payment
 */
class Success extends Action implements CsrfAwareActionInterface
{
    /**
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        $data = [
            'transaction' => false,
            'error' => false,
        ];
        if ($error = $request->getParam('error')) {
            $data['error'] = $error;
        }
        if ($trans = $request->getParam('trans_id')) {
            $data['transaction'] = $trans;
        }
        //todo setSameSite method of PublicCookieMetadata only from 2.4.x Magento version
        if (version_compare(PHP_VERSION, '7.3.0') >= 0) {
            // samesite option is available from PHP 7.3.0
            setcookie ( 'mb_result' , rawurlencode(json_encode($data)) , [
                'secure' => true,
                'path' => '/',
                'samesite' => 'None'
            ]);
        } else {
            setcookie('mb_result', rawurlencode(json_encode($data)), 0, '/; SameSite=none', '', true);
        }
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
