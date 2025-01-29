
import { __ } from '@wordpress/i18n'
import { removeSelectedCategory } from '@addOnsManager/store/slices/addOns/addOnsManagerSlice'
import { useDispatch } from "react-redux"

export const SelectedAddOnCategoryCard = ({ categoryId = 0, categoryName = '', className = '', children }) => {

    const dispatch = useDispatch();

    const handleCategoryDelete = () => {

        dispatch(removeSelectedCategory({categoryId}))
    }

    return (
        <div 
            className="category-card dropped"
            id={`add-ons-category-${categoryId}`}
            key={`add-ons-category-${categoryId}`}
        >

            <div className="category-header">
                <div className="category-title">
                    {categoryName}
                </div>
                <div className="category-controls">
                    <button
                        className="cancel-addon-btn"
                        title={__('Remove From Selected', 'olena-food-ordering')}
                        onClick={handleCategoryDelete}
                    >
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            {children}

        </div>
    );
};