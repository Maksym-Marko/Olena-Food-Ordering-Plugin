<?php

/**
 * Class MetaBox.
 * This class is used to create meta boxes.
 * You can find examples here:
 * \includes\Admin\Utilities\MetaBoxGenerator.php
 */

namespace VAJOFOWPPGNext\Admin\Entities;

use VAJOFOWPPGNext\Admin\Utilities\Notices\MetaBoxTypeNotice;

class MetaBox
{

    /**
     * List of allowed meta boxes.
     * 
     * @var array
     */
    const TYPES = [
        'text',
        'email',
        'url',
        'int',
        'float',
        'textarea',
        'image',
        'radio',
        'select',
        'checkbox',
        'add-on-wrapper',
        'order-data-wrapper'
    ];

    /**
     * Compare and complement default arguments with user arguments.
     * 
     * @var array
     */
    private $args = [];

    /**
     * Default arguments.
     * 
     * @var array
     */
    protected $defaults = [
        'id'           => 'mx-meta-box',
        'postTypes'    => 'page',
        'title'         => 'Meta Box',
        'metaBoxType'  => 'text',
        'options'      => [],
        'context'      => 'normal',
        'priority'     => 'default',
        'callbackArgs' => NULL,
        'readonly'     => false,
        'defaultValue' => '',
        'data'         => NULL
    ];

    /** 
     * @param array $metaBoxArguments   List of meta box properties:
     *   [
     *      'id'           'mx-meta-box' (string)
     *      'postTypes'    'page' (string|array) can be: ['page', 'post']
     *      'title'         'Meta Box' (string)
     *      'metaBoxType'  'text'   (string)     can be: 'email',
     *                                                   'url',
     *                                                   'int',
     *                                                   'float',
     *                                                   'textarea',
     *                                                   'image',
     *                                                   'radio',
     *                                                   'select',
     *                                                   'checkbox',
     *                                                   'add-on-wrapper',
     *                                                   'order-data-wrapper'
     * 
     *      'options'       []       (array)    used for: 'radio',
     *                                                    'select',
     *                                                    'checkbox'
     * 
     *      'context'       'normal' (string)     can be: 'side',
     *                                                    'advanced'
     * 
     *      'priority'      'default' (string)     can be: 'high',
     *                                                     'core',
     *                                                     'low'
     * 
     * 
     *      'readonly'      false    (boolean)
     *      'defaultValue'  ''       (string)
     *   ]
     *
     */
    public function __construct(array $metaBoxArguments)
    {

        $this->args = wp_parse_args($metaBoxArguments, $this->defaults);

        if (!$this->validateMetaBoxType()) {

            MetaBoxTypeNotice::throw($this->args['metaBoxType']);

            return;
        }

        $this->setMetaBoxId();

        $this->setPostMetaKey();

        $this->nonceManager();

        $this->actionsManager();
    }

    /**
     * Check if the input type exists.
     * 
     * @return bool
     */
    protected function validateMetaBoxType(): bool
    {

        return in_array($this->args['metaBoxType'], self::TYPES);
    }

    /**
     * Generate meta box id.
     * 
     * @return void
     */
    protected function setMetaBoxId(): void
    {

        if (is_array($this->args['postTypes'])) {

            $postTypes = array_map('sanitize_key', $this->args['postTypes']);
            $sanitizedId = sanitize_key($this->args['id']);

            if (!empty($postTypes) && !empty($sanitizedId)) {

                $this->args['metaBoxId'] = $sanitizedId . '_' . implode('_', $postTypes);
            } else {

                $this->args['metaBoxId'] = 'default_meta_box';
            }

            return;
        }

        $this->args['metaBoxId'] = sanitize_key($this->args['id']);
    }

    /**
     * Generate meta box key.
     * 
     * @return void
     */
    protected function setPostMetaKey(): void
    {

        $this->args['postMetaKey'] = sanitize_key($this->args['metaBoxId']);
    }

    /**
     * Get meta box key.
     * 
     * @return string
     */
    protected function getPostMetaKey(): string
    {

        return wp_unslash($this->args['postMetaKey']);
    }


    /**
     * Manage nonce name and action.
     * 
     * @return void
     */
    protected function nonceManager(): void
    {

        $this->args['nonceAction']  = sanitize_key($this->args['metaBoxId']) . '_nonce_action';

        $this->args['nonceName']    = sanitize_key($this->args['metaBoxId']) . '_nonce_name';
    }

    /**
     * Manage meta box actions.
     * 
     * @return void
     */
    protected function actionsManager(): void
    {

        add_action('add_meta_boxes', [$this, 'addMetaBox']);

        add_action('save_post', [$this, 'saveMetaBox']);
    }

    /**
     * Add meta box.
     * 
     * @return void
     */
    public function addMetaBox(): void
    {

        add_meta_box(
            $this->args['metaBoxId'],
            $this->args['title'],
            [$this, 'render'],
            $this->args['postTypes'],
            $this->args['context'],
            $this->args['priority'],
            $this->args['priority'],
            $this->args['callbackArgs']
        );
    }

    /**
     * Save meta box.
     * 
     * @param int $postId   Post ID.
     * 
     * @return void
     */
    public function saveMetaBox($postId): void
    {

        if (!isset($_POST)) return;

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) return;

        if (!current_user_can('edit_post', $postId)) return;

        if (!isset($_POST[$this->args['postMetaKey']])) return;

        $metaBoxType = ucfirst(strtolower($this->args['metaBoxType']));

        $value = NULL;

        if (method_exists($this, "saveMetaBox{$metaBoxType}")) {

            $value = call_user_func([$this, "saveMetaBox{$metaBoxType}"]);
        }

        if ($value === NULL) return;

        $postMetaKey = $this->getPostMetaKey();

        update_post_meta($postId, $postMetaKey, $value);
    }

    /**
     * Return Text meta box value for saving.
     * 
     * @return string
     */
    public function saveMetaBoxText(): string
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return '';
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return '';

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? sanitize_text_field(wp_unslash($_POST[$postMetaKey])) : '';
    }

    /**
     * Return Email meta box value for saving.
     * 
     * @return string
     */
    public function saveMetaBoxEmail(): string
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return '';
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return '';

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? sanitize_email(wp_unslash($_POST[$postMetaKey])) : '';
    }

    /**
     * Return URL meta box value for saving.
     * 
     * @return string
     */
    public function saveMetaBoxUrl(): string
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return '';
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return '';

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? esc_url_raw(wp_unslash($_POST[$postMetaKey])) : '';
    }

    /**
     * Return INT meta box value for saving.
     * 
     * @return int
     */
    public function saveMetaBoxInt(): int
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return 0;
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return 0;

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? intval($_POST[$postMetaKey]) : 0;
    }

    /**
     * Return Float meta box value for saving.
     * 
     * @return float
     */
    public function saveMetaBoxFloat(): float
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return 0;
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return 0;

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? floatval($_POST[$postMetaKey]) : 0;
    }

    /**
     * Return Textarea meta box value for saving.
     * 
     * @return string
     */
    public function saveMetaBoxTextarea(): string
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return '';
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return '';

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? sanitize_textarea_field(wp_unslash($_POST[$postMetaKey])) : '';
    }

    /**
     * Return Radio meta box value for saving.
     * 
     * @return string
     */
    public function saveMetaBoxRadio(): string
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return '';
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return '';

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? sanitize_text_field(wp_unslash($_POST[$postMetaKey])) : '';
    }

    /**
     * Return Select meta box value for saving.
     * 
     * @return string
     */
    public function saveMetaBoxSelect(): string
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return '';
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return '';

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? sanitize_text_field(wp_unslash($_POST[$postMetaKey])) : '';
    }

    /**
     * Return Checkbox meta box value for saving.
     * 
     * @return string
     */
    public function saveMetaBoxCheckbox(): string
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return '';
        }

        $postMetaKey = $this->getPostMetaKey();
        $options = [];
        
        // Only process checkbox fields that match our meta key pattern
        $checkboxPattern = '/^' . preg_quote($postMetaKey, '/') . '(\d+)$/';
        
        // Get all possible checkbox keys based on defined options
        foreach (array_keys($this->args['options']) as $index => $option) {
            $fieldKey = $postMetaKey . ($index + 1);
            
            // Only process if the field exists in POST and matches our pattern
            if (isset($_POST[$fieldKey]) && preg_match($checkboxPattern, $fieldKey)) {
                // Sanitize the checkbox value
                $value = sanitize_text_field(wp_unslash($_POST[$fieldKey]));
                if (!empty($value)) {
                    $options[] = $value;
                }
            }
        }

        return implode(',', array_map('sanitize_text_field', $options));
    }

    /**
     * Return Image meta box value for saving.
     * 
     * @return int
     */
    public function saveMetaBoxImage(): int
    {

        if (!isset($_POST[$this->args['nonceName']]) || !wp_verify_nonce(sanitize_key(wp_unslash($_POST[$this->args['nonceName']])), $this->args['nonceAction'])) {
            return 0;
        }

        if (!isset($this->args['postMetaKey']) || !isset($_POST[$this->args['postMetaKey']])) return 0;

        $postMetaKey = $this->getPostMetaKey();

        return isset($_POST[$postMetaKey]) ? intval($_POST[$postMetaKey]) : 0;
    }

    /**
     * Render Metabox body.
     * 
     * @param object  $post   Post object.
     * 
     * @return void
     */
    public function render($post, $meta): void
    {

        $postMetaKey = $this->getPostMetaKey();

        $metaBoxValue = get_post_meta(
            $post->ID,
            $postMetaKey,
            true
        );

        if (empty($metaBoxValue)) {

            $metaBoxValue = $this->args['defaultValue'];
        }

        $postMetaKey = $this->getPostMetaKey();

        if (!vajofoView("meta-boxes/{$this->args['metaBoxType']}", [
            'metaBoxValue' => $metaBoxValue,
            'postMetaKey'  => $postMetaKey,
            'readonly'     => $this->args['readonly'],
            'options'      => $this->args['options'],
            'title'        => $this->args['title'],
            'data'         => $this->args['data'],
        ])) {

            vajofoView('meta-boxes/404');
        }

        wp_nonce_field($this->args['nonceAction'], $this->args['nonceName'], true, true);
    }
}
