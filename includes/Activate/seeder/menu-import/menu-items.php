<?php

defined('ABSPATH') || exit;

return [
    [
        'id' => 1,
        'title' => __('Classic Margherita', 'olena-food-ordering'),
        'description' => __('Experience the timeless elegance of our Classic Margherita pizza, crafted in true Neapolitan tradition. Each pizza features a perfect harmony of hand-stretched thin crust, topped with vibrant San Marzano tomato sauce, premium buffalo mozzarella that melts into creamy pools, and fresh basil leaves. Our dough is fermented for 24 hours to develop complex flavors and the perfect texture. Finished with a drizzle of extra virgin olive oil and baked in a high-temperature oven until the crust develops beautiful leopard-spotted char marks.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Traditional Neapolitan pizza with San Marzano tomatoes, fresh buffalo mozzarella, and aromatic basil on crispy thin crust.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/classic-margherita.jpg',
        'price' => 12.99,
        'menu_category_id' => 1, // Pizza
        'tags' => [
            1,  // vegetarian
            12, // popular
            13, // chef-special
            22  // baked
        ],
        'available_add_ons' => [
            // Toppings
            6 => ['min' => 0, 'max' => 2],  // Extra Cheese
            7 => ['min' => 0, 'max' => 3],  // Pepperoni
            8 => ['min' => 0, 'max' => 2],  // Mushrooms
            9 => ['min' => 0, 'max' => 2],  // Red Onions
            10 => ['min' => 0, 'max' => 2], // Black Olives
            11 => ['min' => 0, 'max' => 2], // Green Peppers
            // Premium Toppings
            12 => ['min' => 0, 'max' => 2], // Grilled Chicken
            13 => ['min' => 0, 'max' => 2], // Italian Sausage
            14 => ['min' => 0, 'max' => 2], // Bacon
            15 => ['min' => 0, 'max' => 1], // Shrimp
            // Crusts
            27 => ['min' => 0, 'max' => 1], // Gluten-Free
            28 => ['min' => 0, 'max' => 1], // Stuffed Crust
            29 => ['min' => 0, 'max' => 1], // Thin Crust
            // Sauces
            1 => ['min' => 0, 'max' => 2], // Ranch
            2 => ['min' => 0, 'max' => 2], // Garlic Aioli
            3 => ['min' => 0, 'max' => 2], // Spicy Mayo
            4 => ['min' => 0, 'max' => 2], // BBQ Sauce
            5 => ['min' => 0, 'max' => 2], // Buffalo Sauce
            // Dips
            19 => ['min' => 0, 'max' => 3], // Marinara
            20 => ['min' => 0, 'max' => 3], // Blue Cheese
            21 => ['min' => 0, 'max' => 3], // Honey Mustard
            22 => ['min' => 0, 'max' => 3]  // Cheese Sauce
        ]
    ],
    [
        'id' => 2,
        'title' => __('BBQ Chicken Pizza', 'olena-food-ordering'),
        'description' => __('Savor our signature BBQ Chicken Pizza, where smoky meets savory in every bite. Tender pieces of grilled chicken are generously coated in our house-made sweet and tangy BBQ sauce, then layered with perfectly melted mozzarella cheese. Thinly sliced red onions add a pleasant crunch and subtle sweetness, while a blend of fresh herbs enhances the flavors. The pizza is finished with an extra drizzle of BBQ sauce and a light sprinkle of cilantro. Our hand-tossed crust is baked to golden perfection, creating the ideal balance of crispy edges and chewy center.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Mouth-watering combination of grilled chicken, sweet and tangy BBQ sauce, melted mozzarella, and crisp red onions.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/bbq-chicken-pizza.jpg',
        'price' => 15.99,
        'menu_category_id' => 1, // Pizza
        'tags' => [
            20, // grilled
            12, // popular
            27  // contains-soy
        ],
        'available_add_ons' => [
            // Same structure as Margherita for pizza consistency
            6 => ['min' => 0, 'max' => 2],  // Extra Cheese
            7 => ['min' => 0, 'max' => 3],  // Pepperoni
            8 => ['min' => 0, 'max' => 2],  // Mushrooms
            9 => ['min' => 0, 'max' => 2],  // Red Onions
            10 => ['min' => 0, 'max' => 2], // Black Olives
            11 => ['min' => 0, 'max' => 2], // Green Peppers
            12 => ['min' => 0, 'max' => 2], // Grilled Chicken
            13 => ['min' => 0, 'max' => 2], // Italian Sausage
            14 => ['min' => 0, 'max' => 2], // Bacon
            15 => ['min' => 0, 'max' => 1], // Shrimp
            27 => ['min' => 0, 'max' => 1], // Gluten-Free
            28 => ['min' => 0, 'max' => 1], // Stuffed Crust
            29 => ['min' => 0, 'max' => 1], // Thin Crust
            1 => ['min' => 0, 'max' => 2],  // Ranch
            2 => ['min' => 0, 'max' => 2],  // Garlic Aioli
            3 => ['min' => 0, 'max' => 2],  // Spicy Mayo
            4 => ['min' => 0, 'max' => 2],  // BBQ Sauce
            5 => ['min' => 0, 'max' => 2],  // Buffalo Sauce
            19 => ['min' => 0, 'max' => 3], // Marinara
            20 => ['min' => 0, 'max' => 3], // Blue Cheese
            21 => ['min' => 0, 'max' => 3], // Honey Mustard
            22 => ['min' => 0, 'max' => 3]  // Cheese Sauce
        ]
    ],
    [
        'id' => 3,
        'title' => __('Pepperoni Supreme', 'olena-food-ordering'),
        'description' => __('Indulge in our legendary Pepperoni Supreme, a pizza that takes the classic pepperoni experience to new heights. We start with our signature tomato sauce, then layer it generously with two types of premium pepperoni - traditional and spicy Italian - creating a perfect balance of flavors and textures. The pizza is loaded with an abundant blend of melted mozzarella and aged provolone cheese, ensuring that perfect stretch with every slice. A custom blend of Italian herbs including oregano, basil, and thyme is sprinkled throughout, infusing the pizza with aromatic Mediterranean flavors. Each bite delivers a harmonious combination of spicy, savory, and cheesy goodness.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('A double layer of premium pepperoni, generous blend of melted cheeses, and aromatic Italian herbs on our classic crust.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/pepperoni-supreme.jpg',
        'price' => 14.99,
        'menu_category_id' => 1, // Pizza
        'tags' => [
            15, // bestseller
            9,  // spicy
            22  // baked
        ],
        'available_add_ons' => [
            // Same structure as other pizzas
            6 => ['min' => 0, 'max' => 2],  // Extra Cheese
            7 => ['min' => 0, 'max' => 3],  // Pepperoni
            8 => ['min' => 0, 'max' => 2],  // Mushrooms
            9 => ['min' => 0, 'max' => 2],  // Red Onions
            10 => ['min' => 0, 'max' => 2], // Black Olives
            11 => ['min' => 0, 'max' => 2], // Green Peppers
            12 => ['min' => 0, 'max' => 2], // Grilled Chicken
            13 => ['min' => 0, 'max' => 2], // Italian Sausage
            14 => ['min' => 0, 'max' => 2], // Bacon
            15 => ['min' => 0, 'max' => 1], // Shrimp
            27 => ['min' => 0, 'max' => 1], // Gluten-Free
            28 => ['min' => 0, 'max' => 1], // Stuffed Crust
            29 => ['min' => 0, 'max' => 1], // Thin Crust
            1 => ['min' => 0, 'max' => 2],  // Ranch
            2 => ['min' => 0, 'max' => 2],  // Garlic Aioli
            3 => ['min' => 0, 'max' => 2],  // Spicy Mayo
            4 => ['min' => 0, 'max' => 2],  // BBQ Sauce
            5 => ['min' => 0, 'max' => 2],  // Buffalo Sauce
            19 => ['min' => 0, 'max' => 3], // Marinara
            20 => ['min' => 0, 'max' => 3], // Blue Cheese
            21 => ['min' => 0, 'max' => 3], // Honey Mustard
            22 => ['min' => 0, 'max' => 3]  // Cheese Sauce
        ]
    ],
    [
        'id' => 4,
        'title' => __('Classic Chicago Dog', 'olena-food-ordering'),
        'description' => __('Experience the authentic taste of the Windy City with our Classic Chicago Dog. We start with a premium all-beef hot dog, steamed to perfection and nestled in a warm, poppy seed bun. Following strict Chicago tradition, it\'s "dragged through the garden" with yellow mustard, crisp diced onions, bright green sweet pickle relish, fresh tomato wedges, a kosher dill pickle spear, and zesty sport peppers. A dash of celery salt adds the finishing touch. Never ketchup - we keep it real Chicago style! Each bite delivers that perfect combination of savory, tangy, crunchy, and spicy flavors that made this hot dog a Midwest legend.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Authentic Chicago-style all-beef hot dog loaded with traditional toppings and seasoned with celery salt.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/classic-chicago-dog.jpg',
        'price' => 6.99,
        'menu_category_id' => 2, // Hot Dogs
        'tags' => [
            12, // popular
            25, // contains-eggs
            17  // lunch
        ],
        'available_add_ons' => [
            // Extras - hot dog specific
            23 => ['min' => 0, 'max' => 2], // Extra Kraut
            24 => ['min' => 0, 'max' => 2], // Jalapeños
            25 => ['min' => 0, 'max' => 2], // Pickle Spears
            26 => ['min' => 0, 'max' => 2], // Sport Peppers
            // Sauces - more liberal with hot dogs
            1 => ['min' => 0, 'max' => 3],  // Ranch
            2 => ['min' => 0, 'max' => 3],  // Garlic Aioli
            3 => ['min' => 0, 'max' => 3],  // Spicy Mayo
            4 => ['min' => 0, 'max' => 3],  // BBQ Sauce
            5 => ['min' => 0, 'max' => 3],  // Buffalo Sauce
            // Sides - reasonable portions
            16 => ['min' => 0, 'max' => 2], // Coleslaw
            17 => ['min' => 0, 'max' => 2], // Small Fries
            18 => ['min' => 0, 'max' => 2]  // Onion Rings
        ]
    ],
    [
        'id' => 5,
        'title' => __('Chili Cheese Dog', 'olena-food-ordering'),
        'description' => __('Dive into comfort food perfection with our signature Chili Cheese Dog. We start with a premium all-beef hot dog, grilled until it develops a subtle smoky char, then nestle it in a freshly steamed bun. The star of the show is our house-made beef chili, slow-simmered with a secret blend of spices, diced onions, and garlic. We generously ladle this rich, hearty chili over the hot dog and top it with a cascade of sharp cheddar cheese that melts into every crevice. Optional diced onions add extra crunch, while a sprinkle of our special seasoning blend brings all the flavors together. Each bite delivers the perfect combination of savory, spicy, and cheesy goodness.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Grilled all-beef hot dog smothered in homemade spiced chili and topped with melted sharp cheddar cheese.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/chili-cheese-dog.jpg',
        'price' => 7.99,
        'menu_category_id' => 2, // Hot Dogs
        'tags' => [
            9,  // spicy
            4,  // dairy-free
            17  // lunch
        ],
        'available_add_ons' => [
            // Same structure as Classic Chicago Dog
            23 => ['min' => 0, 'max' => 2], // Extra Kraut
            24 => ['min' => 0, 'max' => 2], // Jalapeños
            25 => ['min' => 0, 'max' => 2], // Pickle Spears
            26 => ['min' => 0, 'max' => 2], // Sport Peppers
            1 => ['min' => 0, 'max' => 3],  // Ranch
            2 => ['min' => 0, 'max' => 3],  // Garlic Aioli
            3 => ['min' => 0, 'max' => 3],  // Spicy Mayo
            4 => ['min' => 0, 'max' => 3],  // BBQ Sauce
            5 => ['min' => 0, 'max' => 3],  // Buffalo Sauce
            16 => ['min' => 0, 'max' => 2], // Coleslaw
            17 => ['min' => 0, 'max' => 2], // Small Fries
            18 => ['min' => 0, 'max' => 2]  // Onion Rings
        ]
    ],
    [
        'id' => 6,
        'title' => __('Kraut Dog', 'olena-food-ordering'),
        'description' => __('Discover our German-inspired Kraut Dog, a perfect fusion of Old World flavors. We start with a premium all-beef hot dog, grilled to perfection and nestled in a toasted artisan bun. Its crowned with our house-fermented sauerkraut, offering the perfect balance of tang and crunch. Sweet caramelized onions, slow-cooked until golden brown, provide a delicate sweetness that complements the kraut\'s tartness. A generous spread of imported spicy German mustard adds a bold, zesty kick. Each component is carefully layered to ensure every bite delivers an authentic European street food experience with a gourmet twist.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Grilled hot dog topped with tangy sauerkraut, sweet caramelized onions, and spicy German mustard.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/kraut-dog.jpg',
        'price' => 6.99,
        'menu_category_id' => 2, // Hot Dogs
        'tags' => [
            7,  // mild
            17, // lunch
            25  // contains-eggs
        ],
        'available_add_ons' => [
            // Same structure as other hot dogs
            23 => ['min' => 0, 'max' => 2], // Extra Kraut
            24 => ['min' => 0, 'max' => 2], // Jalapeños
            25 => ['min' => 0, 'max' => 2], // Pickle Spears
            26 => ['min' => 0, 'max' => 2], // Sport Peppers
            1 => ['min' => 0, 'max' => 3],  // Ranch
            2 => ['min' => 0, 'max' => 3],  // Garlic Aioli
            3 => ['min' => 0, 'max' => 3],  // Spicy Mayo
            4 => ['min' => 0, 'max' => 3],  // BBQ Sauce
            5 => ['min' => 0, 'max' => 3],  // Buffalo Sauce
            16 => ['min' => 0, 'max' => 2], // Coleslaw
            17 => ['min' => 0, 'max' => 2], // Small Fries
            18 => ['min' => 0, 'max' => 2]  // Onion Rings
        ]
    ],
    [
        'id' => 7,
        'title' => __('Loaded Fries', 'olena-food-ordering'),
        'description' => __('Indulge in the ultimate comfort food with our signature Loaded Fries. We start with premium potatoes, cut fresh daily and double-fried to achieve the perfect golden crunch on the outside while maintaining a fluffy interior. These crispy fries are generously smothered in our house-made creamy cheese sauce, crafted from a blend of aged cheddar and American cheese. Crispy bacon bits, cooked until perfectly caramelized, are scattered throughout, adding smoky bursts of flavor. Fresh-cut green onions provide a bright, crisp finish and subtle kick. Served piping hot with an extra side of cheese sauce for maximum dipping pleasure. A shareable feast that elevates the humble french fry to new heights.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Crispy double-fried potatoes smothered in cheese sauce, topped with crispy bacon and fresh green onions.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/loaded-fries.jpg',
        'price' => 8.99,
        'menu_category_id' => 3, // Street Food
        'tags' => [
            21, // fried
            12, // popular
            4,  // dairy-free
            19  // late-night
        ],
        'available_add_ons' => [
            // Sauces - generous with fries
            1 => ['min' => 0, 'max' => 3], // Ranch
            2 => ['min' => 0, 'max' => 3], // Garlic Aioli
            3 => ['min' => 0, 'max' => 3], // Spicy Mayo
            4 => ['min' => 0, 'max' => 3], // BBQ Sauce
            5 => ['min' => 0, 'max' => 3], // Buffalo Sauce
            // Premium Toppings - reasonable portions for fries
            12 => ['min' => 0, 'max' => 1], // Grilled Chicken
            13 => ['min' => 0, 'max' => 1], // Italian Sausage
            14 => ['min' => 0, 'max' => 2], // Bacon
            15 => ['min' => 0, 'max' => 1], // Shrimp
            // Dips - multiple for sharing
            19 => ['min' => 0, 'max' => 3], // Marinara
            20 => ['min' => 0, 'max' => 3], // Blue Cheese
            21 => ['min' => 0, 'max' => 3], // Honey Mustard
            22 => ['min' => 0, 'max' => 3]  // Cheese Sauce
        ]
    ],
    [
        'id' => 8,
        'title' => __('Buffalo Wings', 'olena-food-ordering'),
        'description' => __('Savor our perfectly crafted Buffalo Wings, featuring eight jumbo chicken wings, double-fried for maximum crispiness while maintaining juicy tenderness inside. Choose from our signature sauce selection: Classic Buffalo made with aged cayenne peppers and butter, Sweet & Spicy Korean BBQ, Honey Garlic, or extra-hot Ghost Pepper. Each wing is thoroughly coated in your chosen sauce and served with crisp celery and carrot sticks. Accompanied by your choice of house-made blue cheese or ranch dressing for dipping. Our unique double-frying process ensures the wings stay crispy even when sauced, delivering that perfect texture in every bite.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Eight crispy double-fried jumbo wings tossed in your choice of signature sauces, served with fresh veggies and dip.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/buffalo-wings.jpg',
        'price' => 11.99,
        'menu_category_id' => 3, // Street Food
        'tags' => [
            9,  // spicy
            21, // fried
            12, // popular
            19  // late-night
        ],
        'available_add_ons' => [
            // Sauces - wings need sauce options
            1 => ['min' => 0, 'max' => 2], // Ranch
            2 => ['min' => 0, 'max' => 2], // Garlic Aioli
            3 => ['min' => 0, 'max' => 2], // Spicy Mayo
            4 => ['min' => 0, 'max' => 2], // BBQ Sauce
            5 => ['min' => 0, 'max' => 2], // Buffalo Sauce
            // Dips - multiple for sharing
            19 => ['min' => 0, 'max' => 4], // Marinara
            20 => ['min' => 0, 'max' => 4], // Blue Cheese
            21 => ['min' => 0, 'max' => 4], // Honey Mustard
            22 => ['min' => 0, 'max' => 4], // Cheese Sauce
            // Sides
            16 => ['min' => 0, 'max' => 2], // Coleslaw
            17 => ['min' => 0, 'max' => 2], // Small Fries
            18 => ['min' => 0, 'max' => 2]  // Onion Rings
        ]
    ],
    [
        'id' => 9,
        'title' => __('Mozzarella Sticks', 'olena-food-ordering'),
        'description' => __('Experience pure cheese-pulling pleasure with our hand-crafted Mozzarella Sticks. Six generous pieces of premium whole-milk mozzarella are coated in our special blend of Italian-seasoned breadcrumbs and herbs, creating a perfectly crispy golden-brown crust. Each stick is fried to order until the exterior achieves a satisfying crunch while the center becomes deliciously melty and stretchy. Served piping hot alongside our house-made marinara sauce, simmered with vine-ripened tomatoes, fresh basil, and a touch of garlic. The perfect balance of crispy and gooey, these classic appetizers are ideal for sharing – or not!', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Six crispy breaded mozzarella sticks with melty centers, served with house-made marinara sauce.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/mozzarella-sticks.jpg',
        'price' => 7.99,
        'menu_category_id' => 3, // Street Food
        'tags' => [
            1,  // vegetarian
            21, // fried
            4,  // dairy-free
            19  // late-night
        ],
        'available_add_ons' => [
            // Sauces - for dipping
            1 => ['min' => 0, 'max' => 2], // Ranch
            2 => ['min' => 0, 'max' => 2], // Garlic Aioli
            3 => ['min' => 0, 'max' => 2], // Spicy Mayo
            4 => ['min' => 0, 'max' => 2], // BBQ Sauce
            5 => ['min' => 0, 'max' => 2], // Buffalo Sauce
            // Dips - multiple for sharing
            19 => ['min' => 1, 'max' => 3], // Marinara - at least one needed
            20 => ['min' => 0, 'max' => 3], // Blue Cheese
            21 => ['min' => 0, 'max' => 3], // Honey Mustard
            22 => ['min' => 0, 'max' => 3]  // Cheese Sauce
        ]
    ],
    [
        'id' => 10,
        'title' => __('Garlic Knots', 'olena-food-ordering'),
        'description' => __('Delight in our hand-tied Garlic Knots, crafted from our signature pizza dough that\'s fermented for 24 hours to develop rich flavor. Each batch of six knots is hand-rolled and twisted into perfect bites, then baked until golden brown. Fresh out of the oven, they\'re brushed generously with our house-made garlic butter sauce, infused with roasted garlic, fresh herbs, and a touch of extra virgin olive oil. Finished with a sprinkle of freshly grated Parmesan cheese and chopped parsley. These pillowy-soft knots offer the perfect balance of garlicky goodness and buttery richness, with a light, airy texture and slightly crispy exterior.', 'olena-food-ordering') . "\n[olena_food_ordering_single_item_button]",
        'excerpt' => __('Six freshly baked, hand-tied knots brushed with garlic butter and topped with Parmesan cheese.', 'olena-food-ordering'),
        'thumbnail' => VAJOFO_PLUGIN_ABS_PATH . 'assets/images/garlic-knots.jpg',
        'price' => 5.99,
        'menu_category_id' => 3, // Street Food
        'tags' => [
            1,  // vegetarian
            22, // baked
            4   // dairy-free
        ],
        'available_add_ons' => [
            // Sauces - for dipping
            1 => ['min' => 0, 'max' => 2], // Ranch
            2 => ['min' => 0, 'max' => 2], // Garlic Aioli
            3 => ['min' => 0, 'max' => 2], // Spicy Mayo
            4 => ['min' => 0, 'max' => 2], // BBQ Sauce
            5 => ['min' => 0, 'max' => 2], // Buffalo Sauce
            // Dips - for sharing
            19 => ['min' => 0, 'max' => 3], // Marinara
            20 => ['min' => 0, 'max' => 3], // Blue Cheese
            21 => ['min' => 0, 'max' => 3], // Honey Mustard
            22 => ['min' => 0, 'max' => 3]  // Cheese Sauce
        ]
    ]
];
