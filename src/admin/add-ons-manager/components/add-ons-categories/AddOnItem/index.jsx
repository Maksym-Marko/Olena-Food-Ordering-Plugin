import { __ } from '@wordpress/i18n';
import React, { useState } from 'react';
import { useDispatch } from "react-redux"
import { setSelectedAddOn, updateAvailableAddOn, deleteAvailableAddOn } from '@addOnsManager/store/slices/addOns/addOnsManagerSlice';
import { useUpdateAddOnMutation, useDeleteAddOnMutation } from "@addOnsManager/services/AddOns"
import { useSelector } from "react-redux"
import { getCurrencySymbol } from "@addOnsManager/helpers"

export const AddOnItem = ({ addOnName = '', addOnPrice = '0', categoryId = 0, addOnId = 0 }) => {

    const [updateAddOnMutation] = useUpdateAddOnMutation()
    const [deleteAddOnMutation] = useDeleteAddOnMutation()

    const [isEditing, setIsEditing] = useState(false);
    const [name, setName] = useState(addOnName);
    const [price, setPrice] = useState(addOnPrice);

    const dispatch = useDispatch();	

    const restoreData = () => {

        if(!name) {

            setName(addOnName)
        }

        if(!price) {

            setPrice(addOnPrice)
        }
    };

    const handleSave = async () => {

        try {

			const response = await updateAddOnMutation({
                categoryId,
                addOnId,
                newName: name,
                newPrice: price
            });

            if(response?.data?.status === 'success') {

                // update category data in the store
                dispatch(updateAvailableAddOn({
                    categoryId, addOnId, name, price
                }));

                setIsEditing(false);
            }
		} catch (error) {
			
			console.error(error);
		}

        restoreData()
    };

    const handleCancel = (e) => {

        e.preventDefault()

        setName(addOnName)
        setPrice(addOnPrice)

        setIsEditing(false);
    };

    const handleAddAddOn = () => {

        dispatch(setSelectedAddOn({categoryId, addOnId}));
    }

    const handleDeleteAddOn = async (e) => {

        e.preventDefault()

        if(!confirm(__('Are you sure You want to delete this add-on?', 'olena-food-ordering'))) return

        try {

			const response = await deleteAddOnMutation({
                addOnId
            });

            if(response?.data?.status === 'success') {

                // update category data in the store
                dispatch(deleteAvailableAddOn({
                    addOnId,
                    categoryId
                }));
            }
		} catch (error) {
			
			console.error(error);
		}
    }

    const settings = useSelector(state => state.globalSettings.settings);

    const currencySymbol = getCurrencySymbol(settings)

    return (
        <>
            {!isEditing ? (
                <div className="addon-item" id={addOnId}>
                    <div className="addon-info">

                        <div className="addon-info-header">
                            <button
                                type="button"
                                className="drag-handle"
                                onClick={handleAddAddOn}
                            >
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M12 5v14M5 12h14"></path></svg>
                            </button>
                            <span className="addon-name">{name}</span>
                        </div>

                        <span className="addon-price">{currencySymbol}{price}</span>
                    </div>
                    <div className="addon-controls">
                        <button
                            className="edit-addon-btn"
                            title="Edit addon"
                            onClick={() => setIsEditing(true)}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" strokeWidth="2">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                            </svg>
                        </button>
                        <button 
                            className="delete-addon-btn"
                            type="button"
                            title={__('Delete addon', 'olena-food-ordering')}
                            onClick={handleDeleteAddOn}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" strokeWidth="2">
                                <path
                                    d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            ) : (
                <div className="addon-item editing" key={addOnId}>
                    <div className="addon-info">
                        <div className="addon-input-group">
                            <input
                                type="text"
                                className="addon-input name-input"
                                value={name}
                                onChange={(e) => setName(e.target.value)}
                                placeholder="Addon name"
                            />
                            <div className="input-group price-input-group">
                                <span className="currency-symbol">{currencySymbol}</span>
                                <input
                                    type="number"
                                    className="addon-input price-input"
                                    value={price}
                                    onChange={(e) => setPrice(e.target.value)}
                                    step="0.01"
                                    min="0"
                                    placeholder="0.00"
                                />
                            </div>
                        </div>
                    </div>
                    <div className="addon-controls">
                        <button
                            className="save-addon-btn"
                            title={__('Save changes', 'olena-food-ordering')}
                            onClick={handleSave}
                        >
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" strokeWidth="2">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </button>
                        <button
                            className="cancel-addon-btn"
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
            )}
        </>
    );
};