<?php

/**
 * Class AdminMenu.
 * This class is used to create admin menu items
 * and pages. Generally this class uses Router.php file.
 */

namespace VAJOFOWPPGNext\Admin\Entities;

class AdminMenu
{

    /**
     * List of allowed actions.
     * 
     * @var array
     */
    const ACTIONS = [
        'addMenuPage',
        'addSubmenuPage',
        'addOptionsPage',
        'addOptionLink',
    ];

    /**
     * The default action (create a high level menu item).
     * 
     * @var string
     */
    protected $menuAction = 'addMenuPage';

    /**
     * Path to controller (file).
     * 
     * @var string
     */
    protected $path = NULL;

    /**
     * These properties are used for admin menu
     * and page maintenance.
     */
    protected $properties = [
        'pageTitle'  => 'WPPG-NEXT',
        'menuTitle'  => 'WPPG-NEXT',
        'capability' => 'manage_options',
        'menuSlug'   => 'vajofo-admin-page',
        'dashicons'  => 'dashicons-image-filter',
        'position'   => 100,
        'parentSlug' => 'hidden',
    ];

    /**
     * Options page
     */
    protected $optionsPage = 'options-general.php';

    /** 
     * @param array $menuAttributes   List of menu properties:
     *   [
     *      'file'            Rendered file (includes/Admin/controllers/)
     *      'properties'      List of menu properties:
     *      [
     *          'pageTitle'   Default 'WPPG-NEXT'
     *          'menuTitle'   Default 'WPPG-NEXT'
     *          'capability'  Default 'manage_options' (see https://wordpress.org/documentation/article/roles-and-capabilities/)
     *          'menuSlug'    Default 'vajofo-admin-page'
     *          'dashicons'   Default 'dashicons-image-filter'
     *          'position'    Default 100
     *      ]
     *      'menuAction'      Default 'addMenuPage'
     *   ]
     *
     */
    public function __construct($menuAttributes)
    {

        $this->properties = wp_parse_args($menuAttributes['properties'], $this->properties);

        if (in_array($menuAttributes['menuAction'], self::ACTIONS)) {

            $this->menuAction = $menuAttributes['menuAction'];
        }

        $this->path = $menuAttributes['path'];

        // Change options page
        if(isset($menuAttributes['properties']['optionsPage'])) {

            $this->optionsPage = $menuAttributes['properties']['optionsPage'];
        }
    }

    /**
     * This function dynamically runs particular
     * menu action (eg. create main menu or sub menu).
     * 
     * @return void   Fire "admin_menu" action.
     */
    public function add(): void
    {

        add_action('admin_menu', [$this, $this->menuAction]);
    }

    /**
     * Add a page and top hierarchy menu item.
     * 
     * @return void   Add menu item to the WP admin panel.
     */
    public function addMenuPage(): void
    {

        add_menu_page(
            $this->properties['pageTitle'],
            $this->properties['menuTitle'],
            $this->properties['capability'],
            $this->properties['menuSlug'],
            [$this, 'render'],
            $this->properties['dashicons'], // icons https://developer.wordpress.org/resource/dashicons/#id
            $this->properties['position']
        );
    }

    /**
     * Add a page and sub menu item.
     * 
     * @return void   Add a sub/hidden menu item to the WP admin panel.
     */
    public function addSubmenuPage(): void
    {

        add_submenu_page(
            $this->properties['parentSlug'],
            $this->properties['pageTitle'],
            $this->properties['menuTitle'],
            $this->properties['capability'],
            $this->properties['menuSlug'],
            [$this, 'render'],
            $this->properties['position']
        );
    }

    /**
     * Add an option menu item.
     * 
     * @return void   Add an option menu item under the Settings menu item.
     */
    public function addOptionsPage(): void
    {

        add_options_page(
            $this->properties['pageTitle'],
            $this->properties['menuTitle'],
            $this->properties['capability'],
            $this->properties['menuSlug'],
            [$this, 'render'],
            $this->properties['position']
        );
    }

    /**
     * Add a link beside Deactivate link on plugins page.
     * 
     * @return void   Add a link to created options page.
     */
    public function addOptionLink(): void
    {

        $baseName = VAJOFO_PLUGIN_BASE_NAME;

        add_filter("plugin_action_links_{$baseName}", [$this, 'addLink']);
    }

    /**
     * Add any number of links below plugin name
     * on plugins page.
     * 
     * @return array    Array of links.
     */
    public function addLink($links): array
    {

        $settingsLink = '<a href="' . get_admin_url() . $this->optionsPage . '?page=' . $this->properties['menuSlug'] . '">' . 
            $this->properties['menuTitle'] . '</a>';

        array_push($links, $settingsLink);

        return $links;
    }

    /**
     * This function requires a file's path.
     * 
     * @return array    Array of links.
     */
    public function render(): void
    {

        if (!file_exists($this->path)) return;

        require_once $this->path;
    }
}
