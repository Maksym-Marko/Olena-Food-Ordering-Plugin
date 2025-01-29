<?php

/**
 * Short Code [olena_food_ordering_single_item_button]
 * 
 * Here is a HTML markup of a shortcode.
 * 
 * Short Code: \includes\Frontend\Utilities\ShortCodeGenerator.php
 */

defined('ABSPATH') || exit;

$postId = NULL;

if (!empty($attributes['post_id']) && is_numeric($attributes['post_id'])) {
    $postId = intval($attributes['post_id']);
}

echo '<div id="olena-food-ordering-single-item-button" class="ofo-single-item-button-wrapper" data-post-id="' . esc_attr($postId) . '"></div>';
