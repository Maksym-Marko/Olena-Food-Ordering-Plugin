<?php

/**
 * The RestRouteHandlerInterface interface.
 *
 * This interface helps to create an rest API endpoints
 */

namespace VAJOFOWPPGNext\Features\API\Interfaces;

interface RestRouteHandlerInterface
{

    /**
     * Register new endpoint.
     */
    public function registerRoute();

    /**
     * Check the user's permissions.
     */
    public function checkPermissions();

    /**
     * Handle request.
     * 
     * @param $request  Current request.
     */
    public function handleRequest($request);
}
