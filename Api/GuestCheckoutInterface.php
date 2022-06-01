<?php

namespace MegaBank\Payment\Api;

/**
 * Interface GuestCheckoutInterface
 * @package MegaBank\Payment\Api
 */
interface GuestCheckoutInterface
{
    /**
     * @param string $cartId
     * @return string
     */
    public function getWidget($cartId);

    /**
     * @param string $cartId
     * @return string
     */
    public function check($cartId);
}
