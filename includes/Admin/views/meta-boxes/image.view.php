<?php

/**
 * Image.
 */

defined('ABSPATH') || exit;

?>

<div class="mx-image-uploader"
    data-post-meta-key="<?php echo esc_attr($postMetaKey); ?>"
    data-post-meta-value="<?php echo intval($metaBoxValue); ?>"
    data-post-id="<?php echo esc_attr(get_the_ID()); ?>"
></div>
