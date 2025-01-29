<?php

/**
 * The Router class.
 *
 * This class manages the admin pages
 * register and displaying.
 */

namespace VAJOFOWPPGNext\Admin;

use VAJOFOWPPGNext\Admin\Entities\AdminMenu;
use VAJOFOWPPGNext\Admin\Utilities\Notices\PathNotice;

class Router
{

    /**
     * Path to controllers folder.
     */
    protected $rootFolder = 'includes/Admin/controllers/';

    /**
     * List of routes.
     */
    protected $routes = [];

    /**
     * Add an admin page.
     * 
     * @param string $controller   File name in the controllers folder.
     * 
     * @return object
     */
    public function add($controller): object
    {

        $path = VAJOFO_PLUGIN_ABS_PATH . "{$this->rootFolder}{$controller}.php";

        if (!file_exists($path)) {

            PathNotice::throw($path);
        }

        $this->routes[] = [
            'path'       => $path,
            'properties' => [],
            'menuAction' => 'addMenuPage',
        ];

        return $this;
    }

    /**
     * Get controller.
     * 
     * @param string $controller   File name in the controllers folder.
     * 
     * @return object
     */
    public function get(string $controller): object
    {

        return $this->add($controller);
    }

    /**
     * Set properties for an admin page.
     * 
     * @param array $attributes   List of attributes.
     * 
     * @return object
     */
    public function properties(array $attributes): object
    {

        if (!empty($this->routes)) {

            $properties = wp_parse_args($attributes, $this->routes[array_key_last($this->routes)]['properties']);

            $this->routes[array_key_last($this->routes)]['properties'] = $properties;
        }

        return $this;
    }

    /**
     * Set an action for the menu.
     * 
     * @param string $action   Particular action.
     *                         addMenuPage, addSubmenuPage ...
     * 
     * @return object
     */
    public function menuAction(string $action): object
    {

        $this->routes[array_key_last($this->routes)]['menuAction'] = $action;

        return $this;
    }

    /**
     * Go through the routes and register menus.
     * 
     * @return void
     */
    public function route(): void
    {

        foreach ($this->routes as $route) {

            $this->menuPage($route);
        }
    }

    /**
     * Get AdminMenu instance and register a menu item.
     * 
     * @return void
     */
    public function menuPage($route): void
    {

        $menuPage = new AdminMenu($route);

        $menuPage->add();
    }
}
