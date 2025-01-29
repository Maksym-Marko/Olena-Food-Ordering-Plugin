<?php

namespace VAJOFOWPPGNext\Features\API\Routes\Order;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use VAJOFOWPPGNext\Shared\OrderManager;
use WP_REST_Response;

class GetReceipt extends AbstractRestRouteHandler
{

    protected $route = '/get-receipt';

    protected $methods = 'GET';

    public function handleRequest($request): WP_REST_Response
    {

        do_action('ofo_before_handle_get_receipt', $request);

        // Verify security nonce
        $nonceCheck = $this->verifyNonce($request);
        if ($nonceCheck !== true) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Invalid Nonce.', 'olena-food-ordering')
            ], 401);
        }

        // Get receipt data from the request
        $orderId = $request->get_param('orderId');

        if (empty($orderId)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Order ID is required.', 'olena-food-ordering')
            ], 400);
        }

        $receipt = get_post_meta($orderId, OrderManager::ORDER_DATA_META_KEY, true);

        if (empty($receipt)) {
            return new WP_REST_Response([
                'status' => 'error',
                'message' => esc_html__('Order not found.', 'olena-food-ordering')
            ], 404);
        }

        return new WP_REST_Response([
            'status' => 'success',
            'orderData' => $receipt,
            'orderId' => $orderId
        ], 200);

        // Action after successful order creation
        do_action('ofo_after_get_receipt_success', $orderId, $receipt);
    }
}
