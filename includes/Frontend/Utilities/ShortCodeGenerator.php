<?php

/**
 * The ShortCodeGenerator class.
 *
 * This class will help you register
 * new shortcodes for your website.
 */

namespace VAJOFOWPPGNext\Frontend\Utilities;

class ShortCodeGenerator
{

    public function __construct()
    {

        $this->olenaFoodOrderingMenu();

        $this->olenaFoodOrderingSingleItemButton();

        $this->olenaFoodOrderingCart();
    }

    /**
     * Main menu shortcode.
     * 
     * @return void
     */
    public function olenaFoodOrderingMenu(): void
    {

        add_shortcode('olena_food_ordering_store', function () {

            ob_start();

            vajofoRequireFrontendComponent('main-menu-body');

            return ob_get_clean();
        });
    }

    /**
     * Single item button shortcode.
     * 
     * @return void
     */
    public function olenaFoodOrderingSingleItemButton(): void
    {
        
        add_shortcode('olena_food_ordering_single_item_button', function ($atts) {
            // Define default attributes
            $attributes = shortcode_atts([
                'post_id' => null
            ], $atts);

            ob_start();
            
            vajofoRequireFrontendComponent('single-item-button-body', ['post_id' => $attributes['post_id']]);
            
            return ob_get_clean();
        });
    }

    /**
     * Cart widget shortcode.
     * 
     * @return void
     */
    public function olenaFoodOrderingCart(): void
    {
        add_shortcode('olena_food_ordering_cart', function () {
            
            ob_start();
            
            vajofoRequireFrontendComponent('cart-widget-body');
            
            return ob_get_clean();
        });
    }
}
