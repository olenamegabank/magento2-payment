<?php

namespace MegaBank\Payment\Model;

use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;
use MegaBank\Payment\Api\CheckoutInterface;
use MegaBank\Payment\Api\GuestCheckoutInterface;

/**
 * Class GuestCheckout
 * @package MegaBank\Payment\Model
 */
class GuestCheckout implements GuestCheckoutInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CheckoutInterface
     */
    protected $checkout;

    /**
     * GuestCheckout constructor.
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CheckoutInterface $checkout
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CheckoutInterface $checkout
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->checkout = $checkout;
    }

    /**
     * @param string $cartId
     * @return string
     */
    public function getWidget($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->checkout->getWidget($quoteIdMask->getQuoteId());
    }

    /**
     * @param string $cartId
     * @return string
     */
    public function check($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->checkout->check($quoteIdMask->getQuoteId());
    }
}
