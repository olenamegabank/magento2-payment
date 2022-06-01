<?php

namespace MegaBank\Payment\Api;

/**
 * Interface Constraints
 * @package MegaBank\Payment\Api
 */
interface Constraints
{
    /**
     * QUOTE TRANSACTIONS TABLE
     */
    const QUOTE_TRANSACTIONS_TABLE = 'megabank_payment_quote_transactions';

    /**
     * ALLOWED DAYS COUNT FOR ACTION
     */
    const ALLOWED_DAYS_COUNT_FOR_ACTION = 14; //days

    /**
     * MAX HOURS FOR CANCEL OPERATION
     */
    const MAX_HOURS_FOR_CANCEL_OPERATION = 24; //hours

    /**
     * TRANSACTION LIFE TIME
     */
    const TRANSACTION_LIFE_TIME = 11; //hours

    /**
     * TRANSACTION CLEAR TIME
     */
    const TRANSACTION_CLEAR_TIME = 168; //hours
}

