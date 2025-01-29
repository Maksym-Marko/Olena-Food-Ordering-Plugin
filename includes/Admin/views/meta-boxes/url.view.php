<?php

/**
 * Input type url.
*/

defined('ABSPATH') || exit;
?>

<input 
    type="url"
    id="<?php echo esc_attr($postMetaKey); ?>"
    name="<?php echo esc_attr($postMetaKey); ?>"
    value="<?php echo esc_url($metaBoxValue); ?>"
    <?php echo $readonly === true ? esc_attr('readonly') : ''; ?>
/>
