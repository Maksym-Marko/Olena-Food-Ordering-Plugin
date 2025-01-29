<?php

/**
 * Input type number/float.
*/

defined('ABSPATH') || exit;
?>

<input 
    type="number"
    id="<?php echo esc_attr($postMetaKey); ?>"
    name="<?php echo esc_attr($postMetaKey); ?>"
    value="<?php echo floatval($metaBoxValue); ?>"
    <?php echo $readonly === true ? esc_attr('readonly') : ''; ?>
    step="0.01"
/>
