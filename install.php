<?php

defined('ABSPATH') || exit;

use VAJOFOWPPGNext\Activator;
use VAJOFOWPPGNext\Deactivator;

/*
* Run during an activation.
*/

register_activation_hook(VAJOFO_PLUGIN_BASE_NAME, [Activator::class, 'init']);

/*
* Run during a deactivation.
*/

register_deactivation_hook(VAJOFO_PLUGIN_BASE_NAME, [Deactivator::class, 'init']);
