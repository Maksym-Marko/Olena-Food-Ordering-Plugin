import React, { useState } from 'react';
import { __ } from '@wordpress/i18n';

export const DishDescription = ({ description }) => {

    const [showFullDescription, setShowFullDescription] = useState(false);

    // Remove shortcodes from description
    const cleanDescription = description?.replace(/\[.*?\]/g, '') || '';

    // Truncate description to first 100 characters
    const truncatedDescription = cleanDescription.length > 100
        ? cleanDescription.slice(0, 100) + '...'
        : cleanDescription;

    const shouldShowLearnMore = cleanDescription.length > 100;

    return (
        <div className="dish-description">
            {showFullDescription ? cleanDescription : truncatedDescription}
            {shouldShowLearnMore && (
                <button
                    onClick={() => setShowFullDescription(!showFullDescription)}
                    className="dish-description-learn-more-btn"
                >
                    {showFullDescription ? __('Show Less', 'olena-food-ordering') : __('Show More', 'olena-food-ordering')}
                </button>
            )}
        </div>
    );
}