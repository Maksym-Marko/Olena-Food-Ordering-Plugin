<?php

/**
 * Input type radio.
 */

defined('ABSPATH') || exit;
?>

<?php if (!is_array($options) || count($options) === 0) : ?>

    <?php printf('<h3 class="vajofo-color-error">' . esc_html__('You have to add some options to the "Options" array!', 'olena-food-ordering') . '</h3>'); ?>
<?php else : ?>

    <?php $i = 0; ?>

    <?php foreach ($options as $option) : ?>

        <?php if (isset($option['value'])) : ?>

            <div>

                <input 
                    type="radio" 
                    id="<?php echo esc_attr($postMetaKey) . esc_attr($i); ?>" 
                    name="<?php echo esc_attr($postMetaKey); ?>"
                    value="<?php echo esc_html($option['value']); ?>" 
                    
                    <?php if ($metaBoxValue === '') : ?>

                        <?php echo isset($option['checked']) && $option['checked'] === true  ? 'checked' : ''; ?>
                    <?php else : ?>

                        <?php echo $metaBoxValue === $option['value'] ? 'checked' : ''; ?> 
                    <?php endif; ?> 
                />

                <label for="<?php echo esc_attr($postMetaKey) . esc_attr($i); ?>">

                    <?php if (isset($option['label'])) : ?>

                        <?php echo esc_html($option['label']); ?>
                    <?php else : ?>
                        
                        <?php echo esc_html($option['value']); ?>
                    <?php endif; ?>
                </label>
            </div>

            <?php $i++; ?>

        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>