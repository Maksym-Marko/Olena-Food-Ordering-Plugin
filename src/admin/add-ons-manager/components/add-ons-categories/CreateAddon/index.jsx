import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { useDispatch } from "react-redux"
import { useCreateAddOnMutation } from "@addOnsManager/services/AddOns"
import { addAvailableAddOn } from '@addOnsManager/store/slices/addOns/addOnsManagerSlice';
import { getCurrencySymbol, transliterate } from '@addOnsManager/helpers';
import { useSelector } from 'react-redux';

export const CreateAddOn = ({ addOnCategoryId = 0 }) => {

    const [createAddOnMutation] = useCreateAddOnMutation()

    const globalSettings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(globalSettings);

    const [isFormOpen, setIsFormOpen] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        slug: '',
        description: '',
        price: ''
    });

    const dispatch = useDispatch();

    const handleInputChange = (e) => {
        const { name, value } = e.target;

        if (name === 'name') {
            // Auto-generate slug from name
            // const slug = value.toLowerCase()
            //     .replace(/[^a-z0-9\s]/g, '')
            //     .replace(/\s+/g, '-');

            const slug = transliterate(value);
            setFormData(prev => ({
                ...prev,
                [name]: value,
                slug: slug
            }));
        } else if (name === 'price') {
            // Handle price input
            if (value === '' || /^\d*\.?\d{0,2}$/.test(value)) {
                setFormData(prev => ({
                    ...prev,
                    [name]: value
                }));
            }
        } else {
            setFormData(prev => ({
                ...prev,
                [name]: value
            }));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        const addonData = {
            ...formData,
            categoryId: addOnCategoryId,
            price: Number(parseFloat(formData.price).toFixed(2)) || 0
        };

        try {

			const response = await createAddOnMutation({
                categoryId: addOnCategoryId,
                name: formData.name,
                slug: formData.slug,
                price: formData.price,
                description: formData.description
            });

            if(response?.data?.status === 'success') {

                if(response?.data?.add_on_id) {

                    // add addon to the store
                    dispatch(addAvailableAddOn({
                        addOnId: response.data.add_on_id,
                        categoryId: addOnCategoryId,
                        name: formData.name,
                        price: formData.price
                    }));
    
                    handleClose();
                }

            }
		} catch (error) {
			
			console.error(error);
		}
    };

    const handleClose = () => {
        setIsFormOpen(false);
        setFormData({
            name: '',
            slug: '',
            description: '',
            price: ''
        });
    };

    return (
        <>

            {!isFormOpen && (
                <button
                    className="add-addon-btn"
                    onClick={() => setIsFormOpen(true)}
                >
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        strokeWidth="2">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    {__('New Add-on', 'olena-food-ordering')}
                </button>
            )}

            {isFormOpen && (
                <div className="new-category-form">
                    <div className="form-header">
                        <h3>{__('New Add-on', 'olena-food-ordering')}</h3>
                        <button
                            className="close-form-btn"
                            onClick={handleClose}
                        >×</button>
                    </div>
                    <form onSubmit={handleSubmit} className="form-fields">
                        <div className="form-group">
                            <label className="form-label">{__('Add-on Name', 'olena-food-ordering')}</label>
                            <input
                                type="text"
                                name="name"
                                value={formData.name}
                                onChange={handleInputChange}
                                className="form-input"
                                placeholder={__('e.g., Garlic Sauce, French Fries', 'olena-food-ordering')}
                                required
                            />
                        </div>
                        <div className="form-group">
                            <label className="form-label">{__('Add-on Slug', 'olena-food-ordering')}</label>
                            <input
                                type="text"
                                name="slug"
                                value={formData.slug}
                                onChange={handleInputChange}
                                className="form-input"
                                placeholder={__('e.g., garlic-sauce, french-fries', 'olena-food-ordering')}
                                required
                            />
                            <span className="help-text">{__('Used for system identification. Lowercase, no spaces.', 'olena-food-ordering')}</span>
                        </div>
                        <div className="form-group">
                            <label className="form-label">{__('Add-on Description', 'olena-food-ordering')}</label>
                            <textarea
                                name="description"
                                value={formData.description}
                                onChange={handleInputChange}
                                className="form-textarea"
                                placeholder={__('Enter add-on description...', 'olena-food-ordering')}
                                rows="3"
                            />
                            <span className="help-text">{__('Brief description of this add-on.', 'olena-food-ordering')}</span>
                        </div>
                        <div className="form-group">
                            <label className="form-label">{__('Add-on Price', 'olena-food-ordering')}</label>
                            <div className="price-input-wrapper">
                                <span className="currency-symbol">{currencySymbol}</span>
                                <input
                                    type="text"
                                    name="price"
                                    value={formData.price}
                                    onChange={handleInputChange}
                                    className="form-input price-input"
                                    placeholder="0.00"
                                    required
                                />
                            </div>
                            <span className="help-text">{__('Price of the add-on.', 'olena-food-ordering')}</span>
                        </div>
                        <div className="form-actions">
                            <button
                                type="button"
                                className="cancel-btn"
                                onClick={handleClose}
                            >
                                {__('Cancel', 'olena-food-ordering')}
                            </button>
                            <button
                                type="submit"
                                className="create-btn"
                            >
                                {__('Create Add-on', 'olena-food-ordering')}
                            </button>
                        </div>
                    </form>
                </div>
            )}
        </>
    );
};