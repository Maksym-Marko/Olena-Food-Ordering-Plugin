import { Outlet, useLocation } from "react-router-dom"
import { __ } from '@wordpress/i18n';
import FlashMessages from "./FlashMessages"
import { useGetGlobalSettingsQuery } from "@olenaStore/services/Settings"
import { useDispatch, useSelector } from "react-redux"
import { setGlobalSettings } from "@olenaStore/store/slices/settings/globalSettings"
import { useEffect } from "react"
import { useGetMenuItemsQuery } from "@olenaStore/services/Menu"
import { setMenuItems, setPerPage } from "@olenaStore/store/slices/menu/menuSlice"
import { useParams } from 'react-router-dom';
import { setCurrentPage } from "@olenaStore/store/slices/menu/menuSlice"

const navigationData = [
	{ name: 'Settings', path: '/', label: 'Settings' },
	{ name: 'Import', path: '/import', label: 'Demo Import' },
];

const DefaultLayout = () => {

	const location = useLocation();

	useEffect(() => {
		
		const customEvent = new CustomEvent('olenaRouterChangedEvent', {
			detail: {
				type: 'OLENA_ROUTER_CHANGED',
			},
			bubbles: true
		});
		document.dispatchEvent(customEvent);
	}, [location]);

	const { page } = useParams();

	useEffect(() => {
		if (page && !isNaN(parseInt(page))) {
			dispatch(setCurrentPage(parseInt(page)));
		}
	}, [page]);

	// Get global settings
	const { data: globalSettings, isLoading: isGlobalSettingsLoading } = useGetGlobalSettingsQuery()

	const dispatch = useDispatch()

	useEffect(() => {
		if (globalSettings) {

			dispatch(setGlobalSettings({ settings: globalSettings }))
		}
	}, [globalSettings]);

	// Set per page
	useEffect(() => {
		if (globalSettings) {

			dispatch(setPerPage(globalSettings?.items_per_page?.value || 10))
		}
	}, [globalSettings]);

	// Get menu items
	const currentPage = useSelector(state => state.menu.currentPage)
	const perPage = useSelector(state => state.menu.perPage)

	const { data: menuData, isLoading: loadingMenu, error: menuError } = useGetMenuItemsQuery({
		currentPage: currentPage || 1,
		perPage: perPage || 10,
		order: 'DESC'
	});

	useEffect(() => {

		if (menuData && typeof menuData === 'object' && 'menuItems' in menuData) {

			dispatch(setMenuItems({ data: menuData }))
		}
	}, [menuData]);

	return (
		<div className="ofo-store-container">

			{
				loadingMenu ?
					<div>{__('Loading', 'olena-food-ordering')}</div>
					: 
					<>
						{ 
							menuError ? <div>{__('Something went wrong', 'olena-food-ordering')}</div>
							: <><Outlet /></>
						}
					</>
			}

			<FlashMessages />
		</div>
	)
}

export default DefaultLayout