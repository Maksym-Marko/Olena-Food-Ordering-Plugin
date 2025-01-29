<?php

namespace VAJOFOWPPGNext\Features\API\Routes\Order;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use WP_REST_Response;
use VAJOFOWPPGNext\Shared\OrderManager;
use VAJOFOWPPGNext\Shared\Validator;

class SubmitOrder extends AbstractRestRouteHandler
{

    protected $route = '/submit-order';

    public function checkPermissions(): bool
    {

        return true;
    }

    public function handleRequest($request): WP_REST_Response
    {
        // Action before processing request
        do_action('ofo_before_handle_submit_order', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Get order data from request
        $orderData = $request->get_param('orderData');

        // Validate order items
        if (empty($orderData)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Order items are required', 'olena-food-ordering')
            ], 400);
        }

        // Add order placed timestamp to order data
        $orderData['orderPlaced'] = [
            'utc' => gmdate('Y-m-d\TH:i:s.v\Z')
        ];

        // Order status
        $orderData['orderStatus'] = OrderManager::ORDER_STATUS_PENDING;

        // Delivery status
        $orderData['deliveryStatus'] = OrderManager::DELIVERY_STATUS_PENDING;

        // Validate customer name
        $firstNameValidation = Validator::validateCustomerName($orderData['customerData']['firstName']);
        if (!$firstNameValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $firstNameValidation['message']
            ], 400);
        }

        // Validate customer last name
        $lastNameValidation = Validator::validateCustomerName($orderData['customerData']['lastName']);
        if (!$lastNameValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $lastNameValidation['message']
            ], 400);
        }

        // Validate customer email
        $emailValidation = Validator::validateEmail($orderData['customerData']['email']);
        if (!$emailValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $emailValidation['message']
            ], 400);
        }

        // Validate customer phone
        $phoneValidation = Validator::validatePhone($orderData['customerData']['phone']);
        if (!$phoneValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $phoneValidation['message']
            ], 400);
        }

        // Validate delivery address if delivery method is not carryout
        if (isset($orderData['deliveryData']['method']) && $orderData['deliveryData']['method'] !== 'carryout') {

            // Validate customer city
            $cityValidation = isset($orderData['deliveryData']['address']['city']) ? Validator::validateCity($orderData['deliveryData']['address']['city']) : ['isValid' => false, 'message' => esc_html__('City is required', 'olena-food-ordering')];
            if (!$cityValidation['isValid']) {
                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => $cityValidation['message']
                ], 400);
            }

            // Validate delivery postalCode
            $postalCodeValidation = isset($orderData['deliveryData']['address']['postalCode']) ? Validator::validatePostcode($orderData['deliveryData']['address']['postalCode']) : ['isValid' => false, 'message' => esc_html__('Postal code is required', 'olena-food-ordering')];
            if (!$postalCodeValidation['isValid']) {
                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => $postalCodeValidation['message']
                ], 400);
            }

            // Validate delivery street
            $streetValidation = isset($orderData['deliveryData']['address']['street']) ? Validator::validateAddress($orderData['deliveryData']['address']['street']) : ['isValid' => false, 'message' => esc_html__('Street is required', 'olena-food-ordering')];
            if (!$streetValidation['isValid']) {
                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => $streetValidation['message']
                ], 400);
            }

            // Validate delivery method
            $deliveryMethodValidation = Validator::validateDeliveryMethod($orderData['deliveryData']['method']);
            if (!$deliveryMethodValidation['isValid']) {
                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => $deliveryMethodValidation['message']
                ], 400);
            }
        }

        // Validate delivery fee
        $deliveryFeeValidation = Validator::validatePrice($orderData['deliveryData']['fee']);
        if (!$deliveryFeeValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $deliveryFeeValidation['message']
            ], 400);
        }

        // Validate cart items
        $cartItemsValidation = Validator::validateCartItems($orderData['items']);
        if (!$cartItemsValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $cartItemsValidation['message']
            ], 400);
        }

        // Validate payment method
        $paymentMethodValidation = isset($orderData['paymentData']['method']) ? Validator::validatePaymentMethod($orderData['paymentData']['method']) : ['isValid' => false, 'message' => esc_html__('Payment method is required', 'olena-food-ordering')];
        if (!$paymentMethodValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Payment method issue.', 'olena-food-ordering') . ' ' . $paymentMethodValidation['message']
            ], 400);
        }

        //Validate subtotal
        $subtotalValidation = isset($orderData['totals']['subtotal']) ? Validator::validatePrice($orderData['totals']['subtotal']) : ['isValid' => false, 'message' => esc_html__('Subtotal is required', 'olena-food-ordering')];
        if (!$subtotalValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $subtotalValidation['message']
            ], 400);
        }

        // Validate total
        $totalValidation = isset($orderData['totals']['total']) ? Validator::validatePrice($orderData['totals']['total']) : ['isValid' => false, 'message' => esc_html__('Total is required', 'olena-food-ordering')];
        if (!$totalValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $totalValidation['message']
            ], 400);
        }

        // Validate delivery price
        $deliveryPriceValidation = isset($orderData['totals']['delivery']) ? Validator::validatePrice($orderData['totals']['delivery']) : ['isValid' => false, 'message' => esc_html__('Delivery price is required', 'olena-food-ordering')];
        if (!$deliveryPriceValidation['isValid']) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $deliveryPriceValidation['message']
            ], 400);
        }

        try {
            // Action before creating order
            do_action('ofo_before_create_order', $orderData);

            $orderId = OrderManager::createOrder($orderData);

            if (is_wp_error($orderId)) {
                return new WP_REST_Response([
                    'status' => 'error',
                    'message' => $orderId->get_error_message()
                ], 400);
            }

            return new WP_REST_Response([
                'status' => 'success',
                'message' => esc_html__('Order submitted successfully', 'olena-food-ordering'),
                'orderData' => $orderData,
                'orderId' => $orderId
            ], 200);

            // Action after successful order creation
            do_action('ofo_after_create_order_success', $orderId, $orderData);
        } catch (\Exception $e) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => $e->getMessage(),
                'code' => $e->getCode() ?: 500
            ], $e->getCode() ?: 500);
        }
    }
}
