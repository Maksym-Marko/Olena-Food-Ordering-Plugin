<?php

/**
 * The AdminSoul class.
 *
 * Here you can add or remove admin features.
 */

namespace VAJOFOWPPGNext\Admin;

use VAJOFOWPPGNext\Admin\Utilities\AdminEnqueueScripts;
use VAJOFOWPPGNext\Admin\Utilities\PostTypeGenerator;
use VAJOFOWPPGNext\Admin\Utilities\TaxonomyGenerator;
use VAJOFOWPPGNext\Admin\Utilities\MetaBoxGenerator;
use VAJOFOWPPGNext\Shared\SettingsManager;
use VAJOFOWPPGNext\Admin\Utilities\Tables\OrdersTable;
use VAJOFOWPPGNext\Shared\OrderManager;

class AdminSoul
{

    public function __construct()
    {

        $this->routing();

        $this->enqueueScripts();

        $this->registerPostTypes();

        $this->registerTaxonomies();

        $this->addMetaBoxes();

        $this->manageOrdersTable();

        $this->addPendingOrdersCount();
    }

    /**
     * Routes are used to add pages to admin panel.
     * 
     * @return void
     */
    public function routing(): void
    {

        require_once VAJOFO_PLUGIN_ABS_PATH . 'includes/Admin/routes.php';
    }

    /**
     * Enqueue styles and scripts.
     * 
     * @return void
     */
    public function enqueueScripts(): void
    {

        (new AdminEnqueueScripts)->enqueue();
    }

    /**
     * Register CPT.
     * 
     * @return void
     */
    public function registerPostTypes(): void
    {

        // Olena Menu
        PostTypeGenerator::registerMenuPostType(SettingsManager::MENU_SLUG, SettingsManager::getMenuSlug());

        // Olena Menu Add-ons
        PostTypeGenerator::registerAddOnsPostType(SettingsManager::ADD_ONS_SLUG, SettingsManager::getAddOnsSlug());

        // Olena Orders
        PostTypeGenerator::registerOrdersPostType(SettingsManager::ORDERS_SLUG, SettingsManager::getOrdersSlug());
    }

    /**
     * Register Taxonomies.
     * 
     * @return void
     */
    public function registerTaxonomies(): void
    {

        // Olena Menu Types
        TaxonomyGenerator::registerMenuTypeTaxonomy(SettingsManager::TAXONOMY_MENU_TYPE_SLUG, SettingsManager::getTaxonomyMenuTypeSlug(), [SettingsManager::MENU_SLUG]);

        // Olena Menu Tags
        TaxonomyGenerator::registerMenuTagTaxonomy(SettingsManager::TAXONOMY_MENU_TAG_SLUG, SettingsManager::getTaxonomyMenuTagSlug(), [SettingsManager::MENU_SLUG]);

        // Olena Menu Add-on Types
        TaxonomyGenerator::registerAddOnTypeTaxonomy(SettingsManager::TAXONOMY_ADD_ON_TYPE_SLUG, SettingsManager::getTaxonomyAddOnTypeSlug(), [SettingsManager::ADD_ONS_SLUG]);
    }

    /**
     * Register MetaBoxes.
     * 
     * @return void
     */
    public function addMetaBoxes()
    {

        MetaBoxGenerator::addMenuMetaBoxes();
        MetaBoxGenerator::addAddOnMetaBoxes();
        MetaBoxGenerator::addOrderMetaBoxes();
    }

    /**
     * Manage orders table
     * 
     * @return void
     */
    public function manageOrdersTable(): void
    {

        // Initialize orders table columns
        $ordersTable = new OrdersTable();
        $ordersTable->init();
    }

    /**
     * Add pending orders count to admin menu
     */
    public function addPendingOrdersCount()
    {

        add_action('admin_menu', [OrderManager::class, 'addPendingOrdersCount']);
    }
}
