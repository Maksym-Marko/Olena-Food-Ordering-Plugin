import { Outlet } from "react-router-dom"
import { Navigation } from "@settings/components/Navigation"
import { useEffect } from "react"
import { useDispatch, useSelector } from "react-redux"
// import { setUser, destroyCredentials } from "@settings/store/slices/auth/authSlice"
import FlashMessages from "./FlashMessages"
import { __ } from '@wordpress/i18n';

const DefaultLayout = () => {

	let navigationData = [
		{ name: 'Settings', path: '/', label: __('Settings', 'olena-food-ordering') },
		{ name: 'Import', path: '/import', label: __('Demo Import', 'olena-food-ordering') },
		{ name: 'AdditionalInfo', path: '/additional-info', label: __('Additional Info', 'olena-food-ordering') },
	];

	if (window.olenaFoodOrdering?.settingPages?.length > 0) {
		window.olenaFoodOrdering.settingPages.forEach(page => {
			navigationData.push({
				name: page.name,
				path: page.path,
				label: page.label,
			});
		});
	}
	
	return (
		<>
			<Navigation navigation={navigationData} />
	
			<main>
				<div>

					<Outlet />

				</div>
			</main>

			<FlashMessages />
		</>        
	)
}

export default DefaultLayout