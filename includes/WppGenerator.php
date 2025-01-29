<?php

/**
 * The WppGenerator final class.
 *
 * Here you can turn off/on big part of features
 * of the plugin.
 */

namespace VAJOFOWPPGNext;

use VAJOFOWPPGNext\Admin\AdminSoul;
use VAJOFOWPPGNext\Frontend\FrontendSoul;
use VAJOFOWPPGNext\Features\FeaturesSoul;

final class WppGenerator
{

    public function __construct()
    {

        // config

        new AdminSoul;

        new FrontendSoul;

        new FeaturesSoul;
    }
}
