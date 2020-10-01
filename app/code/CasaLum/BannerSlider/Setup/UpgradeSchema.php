<?php
namespace CasaLum\BannerSlider\Setup;

use Exception;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 * @package CasaLum\BannerSlider\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Update table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') < 0) {
            $this->addColumnMarginTableSlider($installer);
        }
        

        $installer->endSetup();
    }

    public function addColumnMarginTableSlider($installer){

        if ($installer->tableExists('casalum_bannerslider_slider')) {
            $installer->getConnection()
                ->addColumn(
                    $installer->getTable('casalum_bannerslider_slider'),
                    'margin',
                    [
                        'type' => Table::TYPE_SMALLINT,
                        'nullable' => true,
                        'default' => "0",
                        'comment' => 'Margin between banners',
                        'after' => 'responsive_items'
                    ]
                );
        }
    }


}
