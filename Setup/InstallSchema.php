<?php

namespace MegaBank\Payment\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use MegaBank\Payment\Api\Constraints;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package MegaBank\Payment\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable(Constraints::QUOTE_TRANSACTIONS_TABLE))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ],
                'ID'
            )->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false,
                    'unsigned' => true,
                ],
                'Quote ID'
            )->addColumn(
                'transaction_id',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Transaction ID'
            )->addColumn(
                'transaction_type',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Transaction Type'
            )->addColumn(
                'is_completed',
                Table::TYPE_SMALLINT,
                2,
                [
                    'default' => 0,
                    'unsigned' => true,
                ],
                'Is Completed'
            )->addColumn(
                'is_canceled',
                Table::TYPE_SMALLINT,
                2,
                [
                    'default' => 0,
                    'unsigned' => true,
                ],
                'Is Canceled'
            )->addColumn(
                'created_at',
                Table::TYPE_TIMESTAMP,
                null,
                [
                    'nullable' => false,
                    'default' => Table::TIMESTAMP_INIT
                ],
                'Created At'
            )->addIndex(
                $installer->getIdxName(Constraints::QUOTE_TRANSACTIONS_TABLE, ['transaction_id']),
                ['transaction_id']
            )->addIndex(
                $installer->getIdxName(Constraints::QUOTE_TRANSACTIONS_TABLE, ['created_at']),
                ['created_at']
            )->setComment('MegaBank Payment Quote Transactions Table');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
