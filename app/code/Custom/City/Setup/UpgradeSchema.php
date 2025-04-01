<?php
/**
 * Copyright © Aneel Kumar, Inc. All rights reserved.
 */
namespace Custom\City\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '4.0.0') < 0) {
            $ziptableName = $installer->getTable('cities_zips');
            // Check if the table cities already exists
            if ($installer->getConnection()->isTableExists($ziptableName) != true) {

                $table = $installer->getConnection()
                    ->newTable($ziptableName)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true
                        ],
                        'ID'
                    )
                    ->addColumn(
                        'zip_name',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Zip Code'
                    )
                    ->addColumn(
                        'city_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'City Id'
                    )
                    ->addColumn(
                        'state_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'State Id'
                    )
                    ->addColumn(
                        'country_id',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Country Id'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_DATE,
                        null,
                        ['nullable' => false],
                        'Created At'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'Status'
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['city_id']),
                        ['city_id']
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['state_id']),
                        ['state_id']
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['country_id']),
                        ['country_id']
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['zip_name']),
                        ['zip_name']
                    )
                    ->setComment('cities_zips Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), '4.1.0') < 0) {
            $tableName = $installer->getTable('cities_names');
            // Check if the table cities already exists
            if ($installer->getConnection()->isTableExists($tableName) != true) {

                $table = $installer->getConnection()
                    ->newTable($tableName)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true
                        ],
                        'ID'
                    )
                    ->addColumn(
                        'locale',
                        Table::TYPE_TEXT,
                        25,
                        [],
                        'Locale example en_US'
                    )
                    ->addColumn(
                        'city_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'City Id'
                    )->addColumn(
                        'name',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Translated name'
                    )->addIndex(
                        $installer->getIdxName('cities_names', ['name']),
                        ['name']
                    )->addIndex(
                        $installer->getIdxName('cities_names', ['locale']),
                        ['locale']
                    )->addIndex(
                        $installer->getIdxName('cities_names', ['city_id']),
                        ['city_id']
                    )
                    ->setComment('cities_names Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($table);
            }
        }
        $installer->endSetup();
    }
}
