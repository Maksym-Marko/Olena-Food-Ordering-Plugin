<?php

namespace VAJOFOWPPGNext\Admin\Utilities\Tables;

use VAJOFOWPPGNext\Shared\SettingsManager;
use VAJOFOWPPGNext\Shared\OrderManager;

class OrdersTable
{
    /**
     * Add custom columns to orders table
     *
     * @param array $columns Default columns
     * @return array Modified columns
     */
    public function addCustomColumns($columns)
    {
        // Modify the 'title' label to 'Order'
        $columns['title'] = __('Order', 'olena-food-ordering');

        $new_columns = array();

        // Insert columns after title
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;

            if ($key === 'title') {
                $new_columns['customer_name'] = __('Customer Name', 'olena-food-ordering');
                $new_columns['customer_phone'] = __('Phone', 'olena-food-ordering');
                $new_columns['order_status'] = __('Order Status', 'olena-food-ordering');
                $new_columns['delivery_status'] = __('Delivery Status', 'olena-food-ordering');
                $new_columns['total_price'] = __('Total', 'olena-food-ordering');
            }
        }

        return $new_columns;
    }

    /**
     * Display custom column content
     *
     * @param string $column Column ID
     * @param int $post_id Post ID
     */
    public function displayCustomColumnContent($column, $postId)
    {
        $orderData = OrderManager::getOrderData($postId);

        switch ($column) {
            case 'customer_name':
                echo esc_html(OrderManager::getCustomerName($orderData));
                break;

            case 'customer_phone':
                echo esc_html(OrderManager::getCustomerPhone($orderData));
                break;

            case 'order_status':
                echo esc_html(OrderManager::getOrderStatus($postId));
                break;

            case 'delivery_status':
                echo esc_html(OrderManager::getDeliveryStatus($postId));
                break;

            case 'total_price':
                echo esc_html(OrderManager::getTotal($orderData));
                break;
        }
    }

    /**
     * Initialize the custom columns
     */
    public function init()
    {
        add_filter(
            "manage_" . SettingsManager::ORDERS_SLUG . "_posts_columns",
            [$this, 'addCustomColumns']
        );

        add_action(
            "manage_" . SettingsManager::ORDERS_SLUG . "_posts_custom_column",
            [$this, 'displayCustomColumnContent'],
            10,
            2
        );

        // Add default sorting by date
        add_action('pre_get_posts', [$this, 'setDefaultSort']);
    }

    /**
     * Set default sorting to date, newest first
     *
     * @param \WP_Query $query
     */
    public function setDefaultSort($query)
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') === SettingsManager::ORDERS_SLUG) {
            $query->set('orderby', 'date');
            $query->set('order', 'DESC');
        }
    }
}
