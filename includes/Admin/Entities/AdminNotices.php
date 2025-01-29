<?php

/**
 * Class AdminNotices.
 * This class is used to create an admin notices.
 */

namespace VAJOFOWPPGNext\Admin\Entities;

class AdminNotices
{

    /**
     * Types of admin notices.
     * 
     * @var array
     */
    const TYPES = [
        'error',
        'warning',
        'success',
        'info',
    ];

    /**
     * Type of the notice.
     * 
     * @var string
     */
    protected $type = 'info';

    /**
     * If it's possible to dismiss the notice.
     * 
     * @var bool
     */
    protected $dismissible = false;

    /**
     * List of messages.
     * 
     * @var array
     */
    protected $messages = [];

    public function __construct($type = NULL)
    {

        if (!in_array($type, self::TYPES)) return;

        $this->type = $type;
    }

    /**
     * Add a message to the admin notice.
     * 
     * @param string $message   Any message.
     * 
     * @return object
     */
    public function addMessage($message): object
    {

        if (!empty($message)) {

            array_push($this->messages, $message);
        }

        return $this;
    }

    /**
     * Dismiss the admin notice.
     * 
     * @param bool $dismissible
     * 
     * @return object
     */
    public function dismissible($dismissible = true): object
    {

        $this->dismissible = (bool) $dismissible;

        return $this;
    }

    /**
     * Display the notice.
     * 
     * @return void
     */
    public function display(): void
    {

        if (empty($this->messages) || !is_array($this->messages)) return;

        foreach ($this->messages as $message) {

            add_action('admin_notices', function () use ($message) {

                $this->notice($message);
            }, 100);
        }
    }

    /**
     * Admin notice body.
     * 
     * @return void
     */
    public function notice($message): void
    {

        $class = "notice notice-{$this->type}";

        $class .= $this->dismissible ? ' is-dismissible' : '';

        printf(
            '<div class="%1$s"><p>%2$s</p></div>',
            esc_attr($class),
            esc_html($message)
        );
    }
}
