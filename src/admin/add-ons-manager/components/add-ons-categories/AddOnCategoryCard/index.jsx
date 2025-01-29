import { __ } from '@wordpress/i18n'
import React, { useState } from 'react';
import { useDispatch, useSelector } from "react-redux"
import { setSelectedAddOns, updateAvailableAddOnsCategory, deleteAvailableAddOnsCategory } from '@addOnsManager/store/slices/addOns/addOnsManagerSlice';
import { useUpdateAddOnsCategoryMutation, useDeleteAddOnsCategoryMutation } from "@addOnsManager/services/AddOns"

export const AddOnCategoryCard = ({ addOnCategoryName = 'Sauces', addOnCategorySlug = 'sauces', addOnCategoryId = 0, children }) => {

    const [updateAddOnsCategoryMutation] = useUpdateAddOnsCategoryMutation()
    const [deleteAddOnsCategoryMutation] = useDeleteAddOnsCategoryMutation()

    const [isEditing, setIsEditing] = useState(false);
    const [name, setName] = useState(addOnCategoryName);
    const [slug, setSlug] = useState(addOnCategorySlug);

    const dispatch = useDispatch();

    const availableAddons = useSelector(state => state.addOnsManager.availableAddons)

    const restoreData = () => {

        if(!name) {

            setName(addOnCategoryName)
        }

        if(!slug) {

            setSlug(addOnCategorySlug)
        }
    };

    const handleSave = async () => {

        try {

			const response = await updateAddOnsCategoryMutation({
                categoryId: addOnCategoryId,
                newName: name,
                newSlug: slug
            });

            if(response?.data?.status === 'success') {

                // update category data in the store
                dispatch(updateAvailableAddOnsCategory({
                    categoryId: addOnCategoryId,
                    name,
                    slug
                }));

                setIsEditing(false);
            }
		} catch (error) {
			
			console.error(error);
		}

        restoreData()
    };

    const handleCancel = () => {

        setName(addOnCategoryName)
        setSlug(addOnCategorySlug)

        setIsEditing(false);
    };

    const getCategoryAddons = (availableAddons, categoryId) => {
        if (!availableAddons[categoryId]) {
            return {};
        }
    
        const addOnIds = Object.keys(availableAddons[categoryId].add_ons);
        const categoryAddons = {};
        
        categoryAddons[categoryId] = {};
        addOnIds.forEach(id => {
            categoryAddons[categoryId][id] = {
                min: 0,
                max: 1
            };
        });
    
        return categoryAddons;
    };

    const selectCategory = () => {

        dispatch(setSelectedAddOns({ data: getCategoryAddons(availableAddons, addOnCategoryId) }))
    }

    const handleCategoryDelete = async (e) => {

        e.preventDefault()

        if(!confirm(__('Are you sure you want to delete this category with all the addons?', 'olena-food-ordering'))) return

        try {

			const response = await deleteAddOnsCategoryMutation({
                categoryId: addOnCategoryId
            });

            if(response?.data?.status === 'success') {

                // delete category data from the store
                dispatch(deleteAvailableAddOnsCategory({
                    categoryId: addOnCategoryId
                }));
            }
		} catch (error) {
			
			console.error(error);
		}
    }

    return (
        <div className="category-card">

            {!isEditing ? (
                /* Normal State */
                <div className="category-header">
                    <div className="category-title">
                        <button
                            type="button"
                            className="drag-handle"
                            onClick={selectCategory}
                        >
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M12 5v14M5 12h14"></path></svg>
                        </button>
                        {name}
                    </div>
                    <div className="category-controls">
                        <button
                            className="category-btn edit-btn"
                            title="Edit category"
                            onClick={() => setIsEditing(true)}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                strokeWidth="2">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                            </svg>
                        </button>
                        <button
                            className="delete-addon-category-btn"
                            title={__('Delete category', 'olena-food-ordering')}
                            onClick={handleCategoryDelete}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>
                        </button>
                    </div>
                </div>
            ) : (
                /* Edit State */
                <div className="category-header editing">
                    <div className="category-edit-form">
                        <div className="edit-input-group">
                            <div className="edit-field">
                                <label className="edit-label">{__('Name', 'olena-food-ordering')}</label>
                                <input
                                    type="text"
                                    className="edit-input"
                                    value={name}
                                    onChange={(e) => setName(e.target.value)}
                                    placeholder="Category name"
                                />
                            </div>
                            <div className="edit-field">
                                <label className="edit-label">{__('Slug', 'olena-food-ordering')}</label>
                                <input
                                    type="text"
                                    className="edit-input"
                                    value={slug}
                                    onChange={(e) => setSlug(e.target.value)}
                                    placeholder="category-slug"
                                />
                            </div>
                        </div>
                        <div className="edit-actions">
                            <button
                                className="save-edit-btn"
                                title={__('Save changes', 'olena-food-ordering')}
                                onClick={handleSave}
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" strokeWidth="2">
                                    <path d="M20 6L9 17l-5-5" />
                                </svg>
                            </button>
                            <button
                                className="cancel-edit-btn"
                                title="Cancel editing"
                                onClick={handleCancel}
                            >
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" strokeWidth="2">
                                    <path d="M18 6L6 18M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {children}

        </div>
    );
};

export default AddOnCategoryCard;