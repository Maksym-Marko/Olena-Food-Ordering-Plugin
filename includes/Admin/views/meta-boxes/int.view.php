<?php

/**
 * Input type number/int.
*/

defined('ABSPATH') || exit;
?>

<input 
    type="number"
    id="<?php echo esc_attr($postMetaKey); ?>"
    name="<?php echo esc_attr($postMetaKey); ?>"
    value="<?php echo intval($metaBoxValue); ?>"
    <?php echo $readonly === true ? esc_attr('readonly') : ''; ?>
/>
