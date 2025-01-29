import { __ } from '@wordpress/i18n';

export const MenuListItem = ({ item }) => {

    const defaultImage = window?.vajofoMainMenuLocalizer?.defaultImage;

    return (
        <div className="menu-item">
            <img
                src={item.thumbnail || defaultImage}
                alt={item.title || __('Menu Item', 'olena-food-ordering')}
                onError={(e) => {
                    if (e.target.src !== defaultImage) {
                        e.target.src = defaultImage;
                    }
                }}
            />
            <div className="menu-item-content">
                <div className="menu-item-header">
                    <div className="menu-item-title">{item.title}</div>
                    <div className="menu-item-price">${item.price}</div>
                </div>
                <div className="menu-item-description">
                    {item.description}
                </div>
                <div className="menu-item-footer">
                    <div className="dietary-info">
                        <span className="dietary-tag">Gluten-Free</span>
                        <span className="dietary-tag">High Protein</span>
                    </div>
                    <button className="add-to-cart">{__('Add to Cart', 'olena-food-ordering')}</button>
                </div>
            </div>
        </div>
    )
};