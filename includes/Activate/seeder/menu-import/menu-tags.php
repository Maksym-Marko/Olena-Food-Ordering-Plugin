<?php

defined('ABSPATH') || exit;

return $menuTags = [
    // Dietary Preferences
    [
        'id' => 1,
        'name' => __('Vegetarian', 'olena-food-ordering'),
        'slug' => __('vegetarian', 'olena-food-ordering'),
        'description' => __('Dishes that contain no meat or fish products', 'olena-food-ordering')
    ],
    [
        'id' => 2,
        'name' => __('Vegan', 'olena-food-ordering'),
        'slug' => __('vegan', 'olena-food-ordering'),
        'description' => __('Dishes free from all animal products', 'olena-food-ordering')
    ],
    [
        'id' => 3,
        'name' => __('Gluten-Free', 'olena-food-ordering'),
        'slug' => __('gluten-free', 'olena-food-ordering'),
        'description' => __('Dishes that contain no gluten or wheat products', 'olena-food-ordering')
    ],
    [
        'id' => 4,
        'name' => __('Dairy-Free', 'olena-food-ordering'),
        'slug' => __('dairy-free', 'olena-food-ordering'),
        'description' => __('Dishes that contain no dairy products', 'olena-food-ordering')
    ],
    [
        'id' => 5,
        'name' => __('Keto', 'olena-food-ordering'),
        'slug' => __('keto', 'olena-food-ordering'),
        'description' => __('Low-carb, high-fat dishes suitable for ketogenic diet', 'olena-food-ordering')
    ],
    [
        'id' => 6,
        'name' => __('Low-Carb', 'olena-food-ordering'),
        'slug' => __('low-carb', 'olena-food-ordering'),
        'description' => __('Dishes with reduced carbohydrate content', 'olena-food-ordering')
    ],

    // Spice Level
    [
        'id' => 7,
        'name' => __('Mild', 'olena-food-ordering'),
        'slug' => __('mild', 'olena-food-ordering'),
        'description' => __('No added spice, suitable for sensitive palates', 'olena-food-ordering')
    ],
    [
        'id' => 8,
        'name' => __('Medium', 'olena-food-ordering'),
        'slug' => __('medium', 'olena-food-ordering'),
        'description' => __('Moderately spiced dishes', 'olena-food-ordering')
    ],
    [
        'id' => 9,
        'name' => __('Spicy', 'olena-food-ordering'),
        'slug' => __('spicy', 'olena-food-ordering'),
        'description' => __('Dishes with significant heat level', 'olena-food-ordering')
    ],
    [
        'id' => 10,
        'name' => __('Extra Spicy', 'olena-food-ordering'),
        'slug' => __('extra-spicy', 'olena-food-ordering'),
        'description' => __('Very hot dishes for spice enthusiasts', 'olena-food-ordering')
    ],

    // Special Status
    [
        'id' => 11,
        'name' => __('New', 'olena-food-ordering'),
        'slug' => __('new', 'olena-food-ordering'),
        'description' => __('Recently added to our menu', 'olena-food-ordering')
    ],
    [
        'id' => 12,
        'name' => __('Popular', 'olena-food-ordering'),
        'slug' => __('popular', 'olena-food-ordering'),
        'description' => __('Customer favorites and most ordered dishes', 'olena-food-ordering')
    ],
    [
        'id' => 13,
        'name' => __("Chef's Special", 'olena-food-ordering'),
        'slug' => __('chef-special', 'olena-food-ordering'),
        'description' => __('Specially crafted dishes by our chef', 'olena-food-ordering')
    ],
    [
        'id' => 14,
        'name' => __('Seasonal', 'olena-food-ordering'),
        'slug' => __('seasonal', 'olena-food-ordering'),
        'description' => __('Limited time offerings using seasonal ingredients', 'olena-food-ordering')
    ],
    [
        'id' => 15,
        'name' => __('Bestseller', 'olena-food-ordering'),
        'slug' => __('bestseller', 'olena-food-ordering'),
        'description' => __('Our most popular dishes', 'olena-food-ordering')
    ],

    // Time of Day
    [
        'id' => 16,
        'name' => __('Breakfast', 'olena-food-ordering'),
        'slug' => __('breakfast', 'olena-food-ordering'),
        'description' => __('Perfect for morning dining', 'olena-food-ordering')
    ],
    [
        'id' => 17,
        'name' => __('Lunch', 'olena-food-ordering'),
        'slug' => __('lunch', 'olena-food-ordering'),
        'description' => __('Ideal for midday meals', 'olena-food-ordering')
    ],
    [
        'id' => 18,
        'name' => __('Dinner', 'olena-food-ordering'),
        'slug' => __('dinner', 'olena-food-ordering'),
        'description' => __('Evening dining options', 'olena-food-ordering')
    ],
    [
        'id' => 19,
        'name' => __('Late Night', 'olena-food-ordering'),
        'slug' => __('late-night', 'olena-food-ordering'),
        'description' => __('Perfect for after-hours cravings', 'olena-food-ordering')
    ],

    // Preparation Method
    [
        'id' => 20,
        'name' => __('Grilled', 'olena-food-ordering'),
        'slug' => __('grilled', 'olena-food-ordering'),
        'description' => __('Prepared on our grill for perfect char and flavor', 'olena-food-ordering')
    ],
    [
        'id' => 21,
        'name' => __('Fried', 'olena-food-ordering'),
        'slug' => __('fried', 'olena-food-ordering'),
        'description' => __('Crispy fried dishes', 'olena-food-ordering')
    ],
    [
        'id' => 22,
        'name' => __('Baked', 'olena-food-ordering'),
        'slug' => __('baked', 'olena-food-ordering'),
        'description' => __('Oven-baked specialties', 'olena-food-ordering')
    ],
    [
        'id' => 23,
        'name' => __('Smoked', 'olena-food-ordering'),
        'slug' => __('smoked', 'olena-food-ordering'),
        'description' => __('Slowly smoked for rich flavor', 'olena-food-ordering')
    ],

    // Allergen Information
    [
        'id' => 24,
        'name' => __('Contains Nuts', 'olena-food-ordering'),
        'slug' => __('contains-nuts', 'olena-food-ordering'),
        'description' => __('This dish contains nuts or nut products', 'olena-food-ordering')
    ],
    [
        'id' => 25,
        'name' => __('Contains Eggs', 'olena-food-ordering'),
        'slug' => __('contains-eggs', 'olena-food-ordering'),
        'description' => __('This dish contains eggs or egg products', 'olena-food-ordering')
    ],
    [
        'id' => 26,
        'name' => __('Contains Shellfish', 'olena-food-ordering'),
        'slug' => __('contains-shellfish', 'olena-food-ordering'),
        'description' => __('This dish contains shellfish or shellfish products', 'olena-food-ordering')
    ],
    [
        'id' => 27,
        'name' => __('Contains Soy', 'olena-food-ordering'),
        'slug' => __('contains-soy', 'olena-food-ordering'),
        'description' => __('This dish contains soy or soy products', 'olena-food-ordering')
    ]
];
