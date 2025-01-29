<?php

namespace VAJOFOWPPGNext\Features\API\Routes\DemoImport;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use VAJOFOWPPGNext\Admin\Utilities\DemoImporter;
use WP_REST_Response;
use Exception;

/**
 * Handles demo import functionality via REST API
 */
class DemoImportRoute extends AbstractRestRouteHandler
{

    protected $route = '/demo-import';

    /**
     * Handles incoming REST API requests for demo import
     * 
     * @param mixed $request The REST request object
     * @return WP_REST_Response
     */
    public function handleRequest($request): WP_REST_Response
    {

        do_action('ofo_before_handle_demo_import_request', $request);

        if (!$this->verifyNonce($request)) {
            return $this->createErrorResponse('Invalid Nonce.', 401);
        }

        $requiredCapability = apply_filters('ofo_demo_import_capability', 'edit_posts');
        if (!$this->verifyUserCapability($requiredCapability)) {
            return $this->createErrorResponse('Invalid Capability.', 401);
        }

        $step = $request->get_param('step');
        if (!in_array($step, DemoImporter::VALID_STEPS, true)) {
            return $this->createErrorResponse('Invalid step specified', 400);
        }

        do_action('ofo_before_demo_import', $step);

        try {
            return $this->processStep($step);
        } catch (Exception $e) {
            return $this->createErrorResponse($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    /**
     * Processes individual import steps
     * 
     * @param string $step The import step to process
     * @return WP_REST_Response
     * @throws Exception
     */
    private function processStep(string $step): WP_REST_Response
    {

        $importMethods = apply_filters('ofo_demo_import_methods', [
            'step-1' => ['importAddOnCategories', 'Add-on Categories'],
            'step-2' => ['importAddOns', 'Add-ons'],
            'step-3' => ['importMenuCategories', 'Menu Categories'],
            'step-4' => ['importMenuTags', 'Menu Tags'],
            'step-5' => ['importMenuItems', 'Menu Items'],
            'step-6' => ['createMenuPage', 'Menu Page'],
        ]);

        $method = $importMethods[$step][0];
        $label = $importMethods[$step][1];

        $importProgress = get_option(DemoImporter::IMPORT_PROGRESS_META_KEY, []);

        $importProgress[$step] = [
            'status' => 'in_progress',
            'started_at' => current_time('mysql'),
            'label' => esc_html($label)
        ];

        $done = DemoImporter::$method();

        if (!$done) {

            $importProgress[$step] = [
                'status' => 'failed',
                'failed_at' => current_time('mysql'),
                'label' => esc_html($label)
            ];

            update_option(DemoImporter::IMPORT_PROGRESS_META_KEY, $importProgress);

            throw new Exception(sprintf(
                esc_html__('Import Failed. ', 'olena-food-ordering') . '%s',
                esc_html($label)
            ));
        }

        $importProgress[$step] = [
            'status' => 'success',
            'completed_at' => current_time('mysql'),
            'label' => esc_html($label)
        ];

        update_option(DemoImporter::IMPORT_PROGRESS_META_KEY, $importProgress);

        do_action("ofo_after_demo_import_{$step}_success", $step, $importMethods);

        return new WP_REST_Response([
            'status' => 'success',
            'message' => sprintf(
                '%s' . esc_html__(' Imported successfully', 'olena-food-ordering'),
                esc_html($label)
            )
        ], 200);
    }

    /**
     * Creates an error response
     * 
     * @param string $message Error message
     * @param int $code HTTP status code
     * @return WP_REST_Response
     */
    private function createErrorResponse(string $message, int $code): WP_REST_Response
    {
        return new WP_REST_Response([
            'status' => 'error',
            'message' => $message
        ], $code);
    }
}
