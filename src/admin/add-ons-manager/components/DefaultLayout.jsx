import { Outlet } from "react-router-dom"
import FlashMessages from "./FlashMessages"
import { ManagerHeader } from '@addOnsManager/components/ManagerHeader'
import { useGetGlobalSettingsQuery } from "@addOnsManager/services/Settings"
import { useDispatch } from "react-redux"
import { setGlobalSettings } from "@addOnsManager/store/slices/settings/globalSettings"
import { useEffect } from "react"

const DefaultLayout = () => {

	const postId = window?.wpApiAddOnsManager?.postId;

	const { data: globalSettings, isLoading: isGlobalSettingsLoading } = useGetGlobalSettingsQuery()

	const dispatch = useDispatch()

	useEffect(() => {
		if(globalSettings) {

			dispatch(setGlobalSettings({settings: globalSettings}))
		}
	}, [globalSettings])

	return (
		<div className="admin-container">

			<ManagerHeader />

			<Outlet />

			<FlashMessages />
		</div>       
	)
}

export default DefaultLayout