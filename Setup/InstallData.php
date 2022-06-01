<?php

namespace MegaBank\Payment\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Model\Order;
use MegaBank\Payment\Model\Payment;

/**
 * Class InstallData
 * @package MegaBank\Payment\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $statuses = [
            Payment::ORDER_STATUS_PAID => 'Mega Bank Paid',
            Payment::ORDER_STATUS_SUCCESS_HOLD => 'Mega Bank Success Hold',
        ];
        foreach ($statuses as $code => $info) {
            $setup->getConnection()->insertOnDuplicate(
                $setup->getTable('sales_order_status'),
                [
                    'status' => $code,
                    'label' => $info
                ]
            );
        }

        $statesData = [
            Payment::ORDER_STATUS_PAID => Order::STATE_PROCESSING,
            Payment::ORDER_STATUS_SUCCESS_HOLD => Order::STATE_PROCESSING,
        ];

        foreach ($statesData as $status => $state) {
            $setup->getConnection()->insertOnDuplicate(
                $setup->getTable('sales_order_status_state'),
                [
                    'status' => $status,
                    'state' => $state,
                    'is_default' => 0,
                    'visible_on_front' => 1
                ]
            );
        }

        $setup->endSetup();
    }
}
