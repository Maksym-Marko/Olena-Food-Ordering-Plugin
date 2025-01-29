<?php

/**
 * The FrontendSoul class.
 *
 * Here you can add or remove frontend features.
 */

namespace VAJOFOWPPGNext\Frontend;

use VAJOFOWPPGNext\Frontend\Utilities\WPEnqueueScripts;

use VAJOFOWPPGNext\Frontend\Utilities\ShortCodeGenerator;

class FrontendSoul
{

    /**
     * Unique string to avoid conflicts.
     * 
     * @var string
     */
    protected $uniqueString = VAJOFO_PLUGIN_UNIQUE_STRING;

    public function __construct()
    {

        $this->enqueueScripts();

        $this->shortCodes();
    }

    /**
     * Enqueue styles and scripts.
     * 
     * @return void
     */
    public function enqueueScripts(): void
    {

        (new WPEnqueueScripts)->enqueue();
    }

    /**
     * Add short codes.
     * 
     * @return void
     */
    public function shortCodes(): void
    {

        new ShortCodeGenerator;
    }
}
