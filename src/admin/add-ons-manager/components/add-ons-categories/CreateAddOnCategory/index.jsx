import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { useDispatch } from "react-redux"
import { useCreateCategoryMutation } from "@addOnsManager/services/AddOns"
import { addAvailableAddOnCategory } from '@addOnsManager/store/slices/addOns/addOnsManagerSlice';
import { transliterate } from '@addOnsManager/helpers';

export const CreateAddOnCategory = () => {

    const [createCategoryMutation] = useCreateCategoryMutation()

    const [isFormOpen, setIsFormOpen] = useState(false);
    const [formData, setFormData] = useState({
        name: '',
        slug: '',
        description: ''
    });

    const dispatch = useDispatch();

    const handleInputChange = (e) => {
        const { name, value } = e.target;

        if (name === 'name') {

            const slug = transliterate(value);

            setFormData(prev => ({
                ...prev,
                [name]: value,
                slug: slug
            }));
        } else {
            setFormData(prev => ({
                ...prev,
                [name]: value
            }));
        }
    };

    const handleSubmit = async (e) => {

        e.preventDefault();

        try {

			const response = await createCategoryMutation({
                name: formData.name,
                slug: formData.slug,
                description: formData.description
            });


            if(response?.data?.status === 'success') {

                if(response?.data?.category_id) {

                    // add addon to the store
                    dispatch(addAvailableAddOnCategory({
                        categoryId: response.data.category_id,
                        name: formData.name,
                        slug: formData.slug,
                        description: formData.description
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
            description: ''
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
                    {__('New Category', 'olena-food-ordering')}
                </button>
            )}

            {isFormOpen && (
                <div className="new-category-form">
                    <div className="form-header">
                        <h3>{__('New Add-on Category', 'olena-food-ordering')}</h3>
                        <button
                            className="close-form-btn"
                            onClick={handleClose}
                        >×</button>
                    </div>
                    <form onSubmit={handleSubmit} className="form-fields">
                        <div className="form-group">
                            <label className="form-label">{__('Category Name', 'olena-food-ordering')}</label>
                            <input
                                type="text"
                                name="name"
                                value={formData.name}
                                onChange={handleInputChange}
                                className="form-input"
                                placeholder="e.g., Sauces, Sides, Toppings"
                                required
                            />
                        </div>
                        <div className="form-group">
                            <label className="form-label">{__('Category Slug', 'olena-food-ordering')}</label>
                            <input
                                type="text"
                                name="slug"
                                value={formData.slug}
                                onChange={handleInputChange}
                                className="form-input"
                                placeholder={__('e.g., sauces, sides, toppings', 'olena-food-ordering')}
                                required
                            />
                            <span className="help-text">{__('Used for system identification. Lowercase, no spaces.', 'olena-food-ordering')}</span>
                        </div>
                        <div className="form-group">
                            <label className="form-label">{__('Category Description', 'olena-food-ordering')}</label>
                            <textarea
                                name="description"
                                value={formData.description}
                                onChange={handleInputChange}
                                className="form-textarea"
                                placeholder="Enter Category description..."
                                rows="3"
                            />
                            <span className="help-text">{__('Brief description of this Category.', 'olena-food-ordering')}</span>
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
                                {__('Create Add-ons Category', 'olena-food-ordering')}
                            </button>
                        </div>
                    </form>
                </div>
            )}
        </>
    );
};