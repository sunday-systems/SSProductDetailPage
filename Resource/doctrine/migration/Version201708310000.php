<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version201708310000 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable("plg_ss_product_detail_layout");
        $table->addColumn('page_id', 'integer', array(
            'notnull' => true,
        ));
        
        $table->addColumn('device_type_id', 'integer', array(
            'notnull' => false,
        ));
        
        $table->addColumn('page_name', 'text', array(
            'notnull' => false,
        ));
        
        $table->addColumn('edit_flg', 'integer', array(
            'notnull' => true,
            'default' => 1,
        ));
        
        $table->addColumn('author', 'text', array(
            'notnull' => false,
        ));
        
        $table->addColumn('description', 'text', array(
            'notnull' => false,
        ));
        
        $table->addColumn('keyword', 'text', array(
            'notnull' => false,
        ));
        
        $table->addColumn('update_url', 'text', array(
            'notnull' => false,
        ));
        
        $table->addColumn('create_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));
        
        $table->addColumn('update_date', 'datetime', array(
            'notnull' => true,
            'unsigned' => false,
        ));
        
        $table->addColumn('meta_robots', 'text', array(
            'notnull' => false,
        ));
        
        $table->addColumn('meta_tags', 'text', array(
            'notnull' => false,
        ));
        
        $table->setPrimaryKey(array('page_id'));
        
        $table = $schema->createTable('plg_ss_product_detail_block_position');
        
        $table->addColumn('page_id', 'integer', array(
            'notnull' => true,
        ));
        
        $table->addColumn('target_id', 'integer', array(
            'notnull' => true,
        ));
        
        $table->addColumn('block_id', 'integer', array(
            'notnull' => true,
        ));
        
        $table->addColumn('block_row', 'integer', array(
            'notnull' => false,
        ));
        
        $table->addColumn('anywhere', 'integer', array(
            'notnull' => true,
            'default' => 0,
        ));
        
        $table->setPrimaryKey(array('page_id', 'target_id', 'block_id'));
        $table->addForeignKeyConstraint('plg_ss_product_detail_layout', array('page_id'), array('page_id'));
        $table->addForeignKeyConstraint('dtb_block', array('block_id'), array('block_id'));
        
    }
    
    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('plg_ss_product_detail_layout');
        $schema->dropTable('plg_ss_product_detail_block_position');
    }
}