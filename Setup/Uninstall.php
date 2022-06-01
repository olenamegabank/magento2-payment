<?php

namespace MegaBank\Payment\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use MegaBank\Payment\Model\Payment;

/**
 * Class Uninstall
 * @package MegaBank\Payment\Setup
 */
class Uninstall implements UninstallInterface
{
    /**
     * Invoked when remove-data flag is set during module uninstall.
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $statuses = [
            Payment::ORDER_STATUS_PAID,
            Payment::ORDER_STATUS_SUCCESS_HOLD,
        ];
        foreach ($statuses as $status) {
            $setup->getConnection()->delete(
                $setup->getTable('sales_order_status'),
                "status='{$status}'"
            );
            $setup->getConnection()->delete(
                $setup->getTable('sales_order_status_state'),
                "status='{$status}'"
            );
        }
        $setup->getConnection()->delete(
            $setup->getTable('core_config_data'),
            "path LIKE 'payment/megabank_payment/%'"
        );

        $setup->endSetup();
    }
}
