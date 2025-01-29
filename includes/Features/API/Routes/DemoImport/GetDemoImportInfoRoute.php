<?php

namespace VAJOFOWPPGNext\Features\API\Routes\DemoImport;

use VAJOFOWPPGNext\Features\API\AbstractClasses\AbstractRestRouteHandler;
use VAJOFOWPPGNext\Admin\Utilities\DemoImporter;
use WP_REST_Response;

class GetDemoImportInfoRoute extends AbstractRestRouteHandler
{

    protected $route = '/get-demo-import-info';

    protected $methods = 'GET';

    /**
     * Handles REST API request to get demo import progress information.
     * 
     * @param \WP_REST_Request $request The WordPress REST request object.
     * @return WP_REST_Response Response containing import progress data.
     *
     * Response Format:
     * - importProgress: Array containing status of each import step
     *
     * Response Codes:
     * - 200: Success
     */
    public function handleRequest($request): WP_REST_Response
    {

        do_action('ofo_before_getting_info_demo_import_data', $request);

        $importProgress = get_option(DemoImporter::IMPORT_PROGRESS_META_KEY, []);

        return new WP_REST_Response([
            'importProgress' => $importProgress
        ], 200);
    }
}
