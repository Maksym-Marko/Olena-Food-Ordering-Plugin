import { Outlet } from "react-router-dom"
import { Navigation } from "@orderDetails/components/Navigation"
import { useEffect } from "react"
import { useDispatch, useSelector } from "react-redux"
import FlashMessages from "./FlashMessages"
import { useGetGlobalSettingsQuery } from "@orderDetails/services/Settings"
import { setGlobalSettings } from "@orderDetails/store/slices/settings/globalSettings"


const navigationData = [
	{ name: 'Settings', path: '/', label: 'Settings' },
	{ name: 'Import', path: '/import', label: 'Demo Import' },
  ];

const DefaultLayout = () => {

	const { data: globalSettings, isLoading: isGlobalSettingsLoading } = useGetGlobalSettingsQuery()

	const dispatch = useDispatch()

	useEffect(() => {
		if(globalSettings) {

			dispatch(setGlobalSettings({settings: globalSettings}))
		}
	}, [globalSettings])
	
	return (
		<>
			{/* <Navigation navigation={navigationData} /> */}
	
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