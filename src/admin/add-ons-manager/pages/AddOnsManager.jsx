// Import WordPress internationalization utility
import { __ } from '@wordpress/i18n'

// Import layout components
import { Container } from "@addOnsManager/components/Container"
import { LeftColumn } from "@addOnsManager/components/LeftColumn"
import { RightColumn } from "@addOnsManager/components/RightColumn"

// Import components for managing available add-on categories
import { AddOnCategoryCard } from "@addOnsManager/components/add-ons-categories/AddOnCategoryCard"
import { CreateAddOnCategory } from "@addOnsManager/components/add-ons-categories/CreateAddOnCategory"
import { AddOnItem } from '@addOnsManager/components/add-ons-categories/AddOnItem'
import { CreateAddOn } from "@addOnsManager/components/add-ons-categories/CreateAddOn"
import { ErrorMessage } from "@addOnsManager/components/ErrorMessage";

// Import components for managing selected add-on categories
import { SelectedAddOnCategoryCard } from "@addOnsManager/components/selected-add-on-categories/SelectedAddOnCategoryCard"
import { SelectedAddOn } from "@addOnsManager/components/selected-add-on-categories/SelectedAddOn"
import { AddOnSelector } from "@addOnsManager/components/selected-add-on-categories/AddOnSelector"

// Import API query hooks
import {
	useGetSelectedAddOnsQuery,
	useGetAvailableAddOnsQuery
} from "@addOnsManager/services/AddOns"

// Import React and Redux hooks
import { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux"
import { setAvailableAddons, setSelectedAddOns } from "@addOnsManager/store/slices/addOns/addOnsManagerSlice"

/**
 * AddOnsManager Component
 * 
 * A React component that manages the UI for add-on categories and items.
 * Provides functionality to view, select, and manage add-ons in a two-column layout.
 */
const AddOnsManager = () => {

	const postId = window?.wpApiAddOnsManager?.postId;
	const permalinkStructure = window?.wpApiAddOnsManager?.permalinkStructure;
	const permalinkPage = window?.wpApiAddOnsManager?.permalinkPage;

	const [loadingError, setLoadingError] = useState(null);

	const dispatch = useDispatch();

	// Fetch available add-ons data using RTK Query
	const {
		data: availableAddOnsData,
		isLoading: availableAddOnsIsLoading,
		error: availableAddOnsError
	} = useGetAvailableAddOnsQuery();

	// Update Redux store when available add-ons data changes
	useEffect(() => {

		if (availableAddOnsData && Object.keys(availableAddOnsData).length > 0) {

			if (availableAddOnsData?.availableAddOns) {

				setLoadingError(null)

				dispatch(setAvailableAddons({ data: availableAddOnsData.availableAddOns }));
			}
		} else {

			if (permalinkStructure.length === 0) {

				setLoadingError(<span dangerouslySetInnerHTML={{
					__html: __('Something went wrong. Please check the website <a href="' + permalinkPage + '">Permalink Settings</a>. Use any except "Plain".', 'olena-food-ordering')
				}} />);
			} else {

				setLoadingError(__('Something went wrong', 'olena-food-ordering'));
			}
		}

	}, [availableAddOnsData]);

	// Fetch selected add-ons data using RTK Query
	const {
		data: selectedAddOnsData,
		isLoading: selectedAddOnsIsLoading,
		error: selectedAddOnsError
	} = useGetSelectedAddOnsQuery(postId);

	// Update Redux store when selected add-ons data changes
	useEffect(() => {

		if (selectedAddOnsData && Object.keys(selectedAddOnsData).length > 0) {

			if (selectedAddOnsData?.addOns) {

				dispatch(setSelectedAddOns({ data: selectedAddOnsData.addOns }));
			}
		}
	}, [selectedAddOnsData]);

	/**
	 * Helper function to get category name by ID
	 * @param {string} categoryId - The ID of the category
	 * @returns {string|undefined} The name of the category or undefined if not found
	 */
	const getCategoryNameById = (categoryId) => {
		if (!categoryId) return
		if (!availableAddons[categoryId]) return
		if (!availableAddons[categoryId].name) return
		return availableAddons[categoryId].name
	}

	// Get available and selected add-ons from Redux store
	const availableAddons = useSelector(state => state.addOnsManager.availableAddons)
	const selectedAddons = useSelector(state => state.addOnsManager.selectedAddons)

	return (
		<Container className="addons-section">
			{/* Left column: Available add-on categories */}
			<LeftColumn className="available-categories">

				{
					!loadingError && <>

						<h3 className="section-title">
							{__('Available Add-on Categories', 'olena-food-ordering')}
						</h3>

						{/* Display available categories */}
						<div className="categories-area">
							{Object.entries(availableAddons || {}).map(([categoryId, category]) => (
								<AddOnCategoryCard
									key={'available-add-on-category-'+categoryId}
									addOnCategoryName={category.name}
									addOnCategorySlug={category.slug}
									addOnCategoryId={categoryId}
								>
									{/* Display add-ons for each category */}
									<div className="addons-list">
										<div className="addons-area">
											{Object.entries(category.add_ons).map(([itemId, item]) => (
												<AddOnItem
													key={itemId}
													addOnName={item.name}
													addOnPrice={item.price}
													categoryId={categoryId}
													addOnId={itemId}
												/>
											))}
										</div>

										{/* Button to create new add-on in category */}
										<div>
											<CreateAddOn addOnCategoryId={categoryId} />
										</div>
									</div>
								</AddOnCategoryCard>
							))}
						</div>
						
						{/* Button to create new category */}
						<div className="categories-footer">
							<CreateAddOnCategory />
						</div>
					</>
				}
				
			</LeftColumn>

			{/* Right column: Selected add-on categories */}
			<RightColumn className="drop-area">

				{
					!loadingError ? <>
						<h3 className="section-title">
							{__('Selected Add-on Categories', 'olena-food-ordering')}
						</h3>

						{/* Display selected categories and their add-ons */}
						<div className="dropzone">
							{Object.entries(selectedAddons).map(([categoryId, selectedIds]) => (
								availableAddons[categoryId] && (
									<SelectedAddOnCategoryCard
										key={'selected-add-on-category-'+categoryId}
										categoryId={categoryId}
										categoryName={getCategoryNameById(categoryId)}
									>
										{/* Display selected add-ons */}
										<div className="addons-list">
											{Object.keys(selectedAddons[categoryId] || {}).map(addonId => {
												const addon = availableAddons[categoryId].add_ons[addonId];
												return addon && (
													<SelectedAddOn
														key={addonId}
														categoryId={categoryId}
														addOnId={addonId}
														addonName={addon.name}
														addonPrice={addon.price}
													/>
												);
											})}
										</div>

										{/* Add-on selector for category */}
										<AddOnSelector
											addOnsCategoryId={categoryId}
											selectedAddons={selectedIds}
											availableAddons={availableAddons[categoryId].add_ons}
										/>
									</SelectedAddOnCategoryCard>
								)
							))}

							{/* Placeholder when no categories are selected */}
							<div className="drop-indicator">
								{__('Select category from sidebar', 'olena-food-ordering')}
							</div>
						</div>
					</> : <ErrorMessage>
						{loadingError}
					</ErrorMessage>
				}
			</RightColumn>
		</Container>
	)
}

export default AddOnsManager;