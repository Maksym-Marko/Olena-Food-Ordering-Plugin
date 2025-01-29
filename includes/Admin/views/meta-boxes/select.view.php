<?php

/**
 * Select.
 */

defined('ABSPATH') || exit;
?>

<?php if (!is_array($options) || count($options) === 0) : ?>

    <?php printf('<h3 class="vajofo-color-error">' . esc_html__('You have to add some options to the "Options" array!', 'olena-food-ordering') . '</h3>'); ?>
<?php else : ?>

    <div>

        <label for="<?php echo esc_attr($postMetaKey); ?>">

            <?php echo esc_attr($title); ?>
        </label>

        <select 
            id="<?php echo esc_attr($postMetaKey); ?>" 
            name="<?php echo esc_attr($postMetaKey); ?>"
        >

            <option>---</option>

            <?php foreach ($options as $option) : ?>

                <?php if (isset($option['value'])) : ?>

                    <option 
                        value="<?php echo esc_html($option['value']); ?>" 

                        <?php if ($metaBoxValue == '') : ?> 

                            <?php echo isset($option['selected']) && $option['selected'] == true  ? 'selected' : ''; ?> 
                        <?php else : ?>

                            <?php echo $metaBoxValue == esc_html($option['value']) ? 'selected' : ''; ?>
                        <?php endif; ?>
                    >
                        <?php if (isset($option['label'])) : ?>

                            <?php echo esc_html($option['label']); ?>
                        <?php else : ?>

                            <?php echo esc_html($option['value']); ?>
                        <?php endif; ?>
                    </option>

                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
<?php endif; ?>
