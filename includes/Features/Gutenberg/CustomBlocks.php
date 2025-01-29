<?php

/**
 * The CustomBlocks class.
 *
 * Here you can find lots of examples 
 * of Gutenberg blocks.
 * The main code of the blocks is here src\gutenberg.
 * Each block is in separate folder and the built 
 * version you can find here: build\gutenberg.
 */

namespace VAJOFOWPPGNext\Features\Gutenberg;

class CustomBlocks
{

    /**
     * The unique string.
     *
     * @var string
     */
    protected $uniqueString  = VAJOFO_PLUGIN_UNIQUE_STRING;

    /**
     * Absolute path.
     * 
     * Something like this:
     * D:\xampp\htdocs\my-domain.com\wp-content\plugins\olena-food-ordering/
     *
     * @var string
     */
    protected $absPath       = VAJOFO_PLUGIN_ABS_PATH;

    /**
     * Plugin url.
     * 
     * Something like this:
     * http://my-domain.com/wp-content/plugins/olena-food-ordering/
     *
     * @var string
     */
    protected $pluginURL     = VAJOFO_PLUGIN_URL;

    /**
     * Plugin version.
     * 
     * Something like this:
     * '1.0.0'
     *
     * @var string
     */
    protected $pluginVersion = VAJOFO_PLUGIN_VERSION;

    /**
     * Here you can find all registered blocks.
     * 
     * @return void      Gutenberg blocks register.
     */
    public static function registerBlocks(): void
    {

        $instance = new static();

        /**
         * Open menu item button
         */
        // add_action('init', [$instance, 'openMenuItemButton']);
    }

    /**
     * GUTENBERG BLOCK.
     * 
     * Open menu item button.
     * 
     * @return void      Create a Gutenberg block.
     */
    public function openMenuItemButton()
    {

        register_block_type("{$this->absPath}build/gutenberg/open-menu-item-button");
    }
}
