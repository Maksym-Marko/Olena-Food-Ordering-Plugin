<?php

/**
 * The MetaBoxGenerator class.
 *
 * Here you can find several examples 
 * of the meta boxes.
 */

namespace VAJOFOWPPGNext\Admin\Utilities;

use VAJOFOWPPGNext\Admin\Entities\MetaBox;
use VAJOFOWPPGNext\Shared\SettingsManager;
use VAJOFOWPPGNext\Shared\OrderManager;
use VAJOFOWPPGNext\Shared\AddOnsManager;
use VAJOFOWPPGNext\Shared\MenuManager;


class MetaBoxGenerator extends MetaBox
{

    /**
     * Unique string to avoid conflicts.
     * 
     * @var string
     */
    protected static $uniqueString = VAJOFO_PLUGIN_UNIQUE_STRING;

    /**
     * Create an instance of MetaBox class and use it 
     * for meta boxes creation.
     * 
     * @param array $args   Arguments for the meta box.
     * 
     * @return object
     */
    public static function add(array $args): object
    {
        $instance = new static($args);

        return $instance;
    }

    /**
     * Create meta boxes for add-ons post type
     * 
     * @return void
     */
    public static function addAddOnMetaBoxes(): void
    {

        // Item Price
        self::add([
            'id'           => AddOnsManager::ADD_ON_PRICE_META_KEY,
            'title'        => __('Add-On Price', 'olena-food-ordering'),            
            'metaBoxType'  => 'float',
            'postTypes'    => SettingsManager::ADD_ONS_SLUG,
        ]);
    }

    /**
     * Create meta boxes for menu post type
     * 
     * @return void
     */
    public static function addMenuMetaBoxes(): void
    {

        // Item Price
        self::add([
            'id'           => MenuManager::MENU_ITEM_PRICE_KEY,
            'title'        => __('Price', 'olena-food-ordering'),
            'metaBoxType'  => 'float',
            'postTypes'    => SettingsManager::MENU_SLUG,
        ]);

        // Add-ons
        self::add([
            'id'           => MenuManager::MENU_ITEM_ADD_ONS_MANAGER_META_KEY,
            'title'        => __('Add-ons manager', 'olena-food-ordering'),
            'metaBoxType'  => 'add-on-wrapper',
            'postTypes'    => SettingsManager::MENU_SLUG,
        ]);
    }

    /**
     * Create meta boxes for orders post type
     * 
     * @return void
     */
    public static function addOrderMetaBoxes(): void
    {

        // Order data
        self::add([
            'id'           => OrderManager::ORDER_DATA_META_KEY,
            'title'        => __('Order Data', 'olena-food-ordering'),
            'metaBoxType'  => 'order-data-wrapper',
            'postTypes'    => SettingsManager::ORDERS_SLUG,
        ]);

        // Order Status
        self::add([
            'id'           => OrderManager::ORDER_STATUS_META_KEY,
            'title'        => __('Order Status', 'olena-food-ordering'),
            'postTypes'    => SettingsManager::ORDERS_SLUG,
            'metaBoxType'  => 'radio',
            'context'      => 'side',
            'options'      => array_map(function($key, $label) {
                return [
                    'label'   => $label,
                    'value'   => $key,
                    'checked' => $key === OrderManager::ORDER_STATUS_PENDING
                ];
            }, array_keys(OrderManager::getOrderStatuses()), OrderManager::getOrderStatuses()),
        ]);

        // Delivery Status
        self::add([
            'id'           => OrderManager::DELIVERY_STATUS_META_KEY,
            'title'        => __('Delivery Status', 'olena-food-ordering'),
            'postTypes'    => SettingsManager::ORDERS_SLUG,
            'metaBoxType'  => 'radio',
            'context'      => 'side',
            'options'      => array_map(function($key, $label) {
                return [
                    'label'   => $label,
                    'value'   => $key,
                    'checked' => $key === OrderManager::DELIVERY_STATUS_PENDING
                ];
            }, array_keys(OrderManager::getDeliveryStatuses()), OrderManager::getDeliveryStatuses()),
        ]);
    }
}
