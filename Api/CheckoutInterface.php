<?php

namespace MegaBank\Payment\Api;

/**
 * Interface CheckoutInterface
 * @package MegaBank\Payment\Api
 */
interface CheckoutInterface
{
    /**
     * @param int $cartId
     * @return string
     */
    public function getWidget($cartId);

    /**
     * @param int $cartId
     * @return string
     */
    public function check($cartId);
}
