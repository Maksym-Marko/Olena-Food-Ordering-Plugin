<?php

namespace VAJOFOWPPGNext\Shared;

defined('ABSPATH') || exit;

use VAJOFOWPPGNext\Shared\SettingsManager;

class OrderManager
{

    /**
     * Order
     */
    // Order data meta key
    const ORDER_DATA_META_KEY = 'olena-orders-data';

    // Order status meta key
    const ORDER_STATUS_META_KEY = 'olena-orders-order-status';

    // Order statuses
    const ORDER_STATUS_PENDING = 'pending';
    const ORDER_STATUS_PROCESSING = 'processing';
    const ORDER_STATUS_COMPLETED = 'completed';
    const ORDER_STATUS_CANCELLED = 'cancelled';
    const ORDER_STATUS_REFUNDED = 'refunded';

    /**
     * Delivery
     */
    // Delivery status meta key
    const DELIVERY_STATUS_META_KEY = 'olena-orders-delivery-status';

    // Delivery statuses
    const DELIVERY_STATUS_PENDING = 'pending';
    const DELIVERY_STATUS_IN_TRANSIT = 'in_transit';
    const DELIVERY_STATUS_DELIVERED = 'delivered';
    const DELIVERY_STATUS_CANCELLED = 'cancelled';

    // Delivery methods
    const DELIVERY_METHOD_CARRYOUT = 'carryout';
    const DELIVERY_METHOD_FREE_DELIVERY = 'free-delivery';

    /**
     * Payment
     */
    // Payment method meta key
    const PAYMENT_METHOD_META_KEY = 'olena-orders-payment-method';

    // Payment methods
    const PAYMENT_METHOD_PICKUP = 'pickup';

    /**
     * Get all possible order statuses
     *
     * @return array
     */
    public static function getOrderStatuses()
    {
        $order_statuses = [
            self::ORDER_STATUS_PENDING => esc_html__('Pending', 'olena-food-ordering'),
            self::ORDER_STATUS_PROCESSING => esc_html__('Processing', 'olena-food-ordering'),
            self::ORDER_STATUS_COMPLETED => esc_html__('Completed', 'olena-food-ordering'),
            self::ORDER_STATUS_CANCELLED => esc_html__('Cancelled', 'olena-food-ordering'),
            self::ORDER_STATUS_REFUNDED => esc_html__('Refunded', 'olena-food-ordering'),
        ];

        return apply_filters('ofo_get_order_statuses', $order_statuses);
    }

    /**
     * Get all possible delivery statuses
     *
     * @return array
     */
    public static function getDeliveryStatuses()
    {
        $delivery_statuses = [
            self::DELIVERY_STATUS_PENDING => esc_html__('Pending', 'olena-food-ordering'),
            self::DELIVERY_STATUS_IN_TRANSIT => esc_html__('In Transit', 'olena-food-ordering'),
            self::DELIVERY_STATUS_DELIVERED => esc_html__('Delivered', 'olena-food-ordering'),
            self::DELIVERY_STATUS_CANCELLED => esc_html__('Cancelled', 'olena-food-ordering'),
        ];

        return apply_filters('ofo_get_delivery_statuses', $delivery_statuses);
    }

    /**
     * Get all possible delivery methods
     *
     * @return array
     */
    public static function getDeliveryMethods()
    {
        $delivery_methods = [
            self::DELIVERY_METHOD_CARRYOUT => esc_html__('Carryout', 'olena-food-ordering'),
            self::DELIVERY_METHOD_FREE_DELIVERY => esc_html__('Free Delivery', 'olena-food-ordering'),
        ];

        return apply_filters('ofo_get_delivery_methods', $delivery_methods);
    }

    /**
     * Get all possible payment methods
     *
     * @return array
     */
    public static function getPaymentMethods()
    {
        $payment_methods = [
            self::PAYMENT_METHOD_PICKUP => esc_html__('Pay at Pickup', 'olena-food-ordering'),
        ];

        return apply_filters('ofo_get_payment_methods', $payment_methods);
    }

    /**
     * Create a new order
     *
     * @param array $order_data Order data including items, customer info, etc.
     * @return int|WP_Error The order ID on success, WP_Error on failure
     */
    public static function createOrder($orderData)
    {

        $postData = [
            'post_title' => self::generateOrderTitle($orderData['items']),
            'post_type' => SettingsManager::ORDERS_SLUG,
            'post_status' => 'publish',
        ];

        $postData = apply_filters('ofo_create_order_post_data', $postData, $orderData);

        $orderId = wp_insert_post($postData, true);

        if (is_wp_error($orderId)) {
            return new \WP_Error('order_creation_failed', esc_html__('Failed to create order post. Error: ', 'olena-food-ordering') . $orderId->get_error_message());
        }

        // Save order data
        update_post_meta($orderId, self::ORDER_DATA_META_KEY, $orderData);

        // Save order status
        update_post_meta($orderId, self::ORDER_STATUS_META_KEY, self::ORDER_STATUS_PENDING);

        // Save delivery status
        update_post_meta($orderId, self::DELIVERY_STATUS_META_KEY, self::DELIVERY_STATUS_PENDING);

        return $orderId;
    }

    /**
     * Update order status
     *
     * @param int $orderId Order ID
     * @param string $status New status
     * @return bool
     */
    public function updateOrderStatus($orderId, $status)
    {
        if (!array_key_exists($status, self::getOrderStatuses())) {
            return false;
        }

        return update_post_meta($orderId, self::ORDER_STATUS_META_KEY, $status);
    }

    /**
     * Get order details
     *
     * @param int $orderId Order ID
     * @return array|false Order details or false if not found
     */
    public static function getOrder($orderId)
    {


        $post = get_post($orderId);

        if (!$post || $post->post_type !== SettingsManager::ORDERS_SLUG) {
            return false;
        }

        $orderData = [
            'id' => $orderId,
            'orderStatus' => get_post_meta($orderId, self::ORDER_STATUS_META_KEY, true),
            'deliveryStatus' => get_post_meta($orderId, self::DELIVERY_STATUS_META_KEY, true),
            'orderData' => get_post_meta($orderId, self::ORDER_DATA_META_KEY, true),
        ];

        return apply_filters('ofo_get_order', $orderData, $orderId);
    }

    /**
     * Calculate the total price of an order
     *
     * @param array $orderItems Order items
     * @return float Total price
     */
    private function calculateOrderTotal($orderItems): float
    {
        $total = 0.0;

        foreach ($orderItems as $item) {
            $itemTotal = floatval($item['price_per_unit']);

            // Add add-ons total
            if (!empty($item['selected_add_ons'])) {
                foreach ($item['selected_add_ons'] as $addOn) {
                    $itemTotal += (floatval($addOn['price']) * intval($addOn['quantity']));
                }
            }

            $total += $itemTotal * intval($item['quantity']);
        }

        return round($total, 2);
    }

    /**
     * Generate order title
     *
     * @param array $orderItems Order items
     * @return string Order title
     */
    public static function generateOrderTitle($orderItems)
    {

        if (!is_array($orderItems) || empty($orderItems)) {
            return sprintf(esc_html__('Order #', 'olena-food-ordering') . '%s', esc_attr(uniqid()));
        }

        $orderTitle = '';

        $orderItems = array_map(function ($item) {
            $addOnsText = '';
            if (!empty($item['selected_add_ons'])) {
                $addOnsText = ' ' . implode(' ', array_map(function ($addOn) {
                    return sprintf(
                        '(+%s %s)',
                        esc_html($addOn['quantity']),
                        esc_html($addOn['name'])
                    );
                }, $item['selected_add_ons']));
            }
            return sprintf(
                '[%s] %sx%s',
                esc_html($item['name']),
                esc_html($item['quantity']),
                esc_html($addOnsText)
            );
        }, $orderItems);

        $orderTitle = apply_filters('ofo_order_title', implode(' | ', array_map('esc_html', $orderItems)), $orderItems);

        if (strlen($orderTitle) > 90) {
            $orderTitle = substr($orderTitle, 0, 87) . '...';
        }

        return $orderTitle;
    }

    /**
     * Get order data by post ID
     *
     * @param int $postId Post ID
     * @return array|null Order data array or null if not found
     */
    public static function getOrderData($postId)
    {
        if (!$postId) {
            return null;
        }

        $orderData = get_post_meta($postId, self::ORDER_DATA_META_KEY, true);
        if (empty($orderData)) {
            return null;
        }

        return $orderData;
    }

    /**
     * Get customer full name from order data
     * 
     * @param array $customerData Customer data array containing firstName and lastName
     * @return string Customer's full name
     */
    public static function getCustomerName($orderData)
    {

        $customerData = empty($orderData['customerData']) ? [] : $orderData['customerData'];

        if (!is_array($customerData) || empty($customerData)) {
            return '';
        }

        $firstName = isset($customerData['firstName']) ? trim($customerData['firstName']) : '';
        $lastName = isset($customerData['lastName']) ? trim($customerData['lastName']) : '';

        if (empty($firstName) && empty($lastName)) {
            return '';
        }

        return apply_filters('ofo_customer_name', trim(sprintf('%s %s', esc_html($firstName), esc_html($lastName))), $firstName, $lastName);
    }

    /**
     * Get customer phone from order data
     * 
     * @param array $orderData Order data array containing customerData
     * @return string Customer's phone number or empty string if not found
     */
    public static function getCustomerPhone($orderData)
    {
        $customerData = empty($orderData['customerData']) ? [] : $orderData['customerData'];

        if (!is_array($customerData) || empty($customerData)) {
            return '';
        }

        return isset($customerData['phone']) ? esc_html(trim($customerData['phone'])) : '';
    }

    /**
     * Get order status from post meta
     * 
     * @param int $postId Post ID of the order
     * @return string Order status or pending if not set
     */
    public static function getOrderStatus($postId)
    {

        $status = get_post_meta($postId, self::ORDER_STATUS_META_KEY, true);
        $status = !empty($status) ? $status : self::ORDER_STATUS_PENDING;
        echo '<span class="order-status ' . esc_attr($status) . '">' .
            esc_html(ucfirst($status)) . '</span>';
    }

    /**
     * Get delivery status from post meta
     * 
     * @param int $postId Post ID of the order
     * @return string Delivery status or pending if not set
     */
    public static function getDeliveryStatus($postId)
    {
        $status = get_post_meta($postId, self::DELIVERY_STATUS_META_KEY, true);
        $status = !empty($status) ? $status : self::DELIVERY_STATUS_PENDING;
        echo '<span class="delivery-status ' . esc_attr($status) . '">' .
            esc_html(ucfirst($status)) . '</span>';
    }

    /**
     * Get order total from order data
     * 
     * @param array $orderData Order data array containing total
     * @return float|int Order total amount or 0 if not set
     */
    public static function getTotal($orderData)
    {
        $total = empty($orderData['totals']['total']) ? 0 : $orderData['totals']['total'];

        return apply_filters('ofo_get_order_total', !empty($total) ? (float) $total : 0, $orderData);
    }

    /**
     * Get count of pending orders
     * 
     * @return int Number of pending orders
     */
    public static function getPendingOrdersCount()
    {
        $args = array(
            'post_type' => SettingsManager::ORDERS_SLUG,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => self::ORDER_STATUS_META_KEY,
                    'value' => self::ORDER_STATUS_PENDING,
                    'compare' => '='
                )
            ),
            'posts_per_page' => -1
        );

        $args = apply_filters('ofo_get_pending_orders_args', $args);

        $query = new \WP_Query($args);
        return $query->found_posts;
    }

    /**
     * Add pending orders count to admin menu
     */
    public static function addPendingOrdersCount()
    {
        global $menu;

        $count = self::getPendingOrdersCount();

        if ($count < 1) {
            return;
        }

        foreach ($menu as $key => $value) {
            if ($value[2] === 'edit.php?post_type=' . SettingsManager::ORDERS_SLUG) {
                $menu[$key][0] .= ' <span class="awaiting-mod update-plugins count-' . $count . '"><span class="pending-count">' . $count . '</span></span>';
                break;
            }
        }
    }
}
