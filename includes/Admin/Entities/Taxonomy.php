<?php

namespace VAJOFOWPPGNext\Admin\Entities;

class Taxonomy
{

    /**
     * Post type must be registered earlier.
     * 
     * @var array
     */
    protected $postTypes;

    /**
     * Taxonomy key. Must not exceed 32 characters 
     * and may only contain lowercase alphanumeric characters,
     * dashes, and underscores
     * 
     * @var string
     */
    protected $taxonomy;

    /**
     * Default labels. Will be override with user's labels.
     * 
     * @var array
     */
    protected $labels = [
        'name' => 'Book Type',
    ];

    /**
     * Default properties. Will be override with user's properties.
     * 
     * @var array
     */
    protected $properties = [
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => ['slug' => 'book-type'],
    ];

    public function __construct(string $taxonomy, array $postTypes)
    {

        $this->taxonomy = $taxonomy;

        $this->postTypes = $postTypes;
    }

    /**
     * Parse args, rewrite or complete the default ones.
     * 
     * @param array $labels   Taxonomy args.
     * 
     * @return object
     */
    public function labels(array $labels): object
    {

        $this->labels = wp_parse_args($labels, $this->labels);

        return $this;
    }

    /**
     * Parse args, rewrite or complete the default ones.
     * 
     * @param array $properties   Taxonomy args.
     * 
     * @return object
     */
    public function properties(array $properties): object
    {

        $this->properties = wp_parse_args($properties, $this->properties);

        return $this;
    }

    /**
     * Register Taxonomy.
     * 
     * @return void
     */
    public function register(): void
    {

        add_action('init', function () {

            $options = [
                'labels' => $this->labels,
            ];

            $options = array_merge($options, $this->properties);

            register_taxonomy($this->taxonomy, $this->postTypes, $options);
        });
    }
}
