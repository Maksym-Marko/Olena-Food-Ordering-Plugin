import { __ } from '@wordpress/i18n';
import { useSetSelectedAddOnsMutation } from "@addOnsManager/services/AddOns"
import { useSelector } from "react-redux"
import { useEffect } from 'react';

export const ManagerHeader = () => {

    const selectedAddons = useSelector(state => state.addOnsManager.selectedAddons)

    const [setSelectedAddOnsMutation, { isLoading, isError }] = useSetSelectedAddOnsMutation()

    const handleSaveAddOns = async (e) => {
		e.preventDefault();

        const postId = window?.wpApiAddOnsManager?.postId;

		try {

			await setSelectedAddOnsMutation({
                selectedAddons,
                postId
            })
		} catch (error) {
			
			console.error(error);
		}
	}

    useEffect(() => {
        const publishButton = document.getElementById('publish');
        
        if (publishButton) {
            const handlePublishClick = async () => {

                try {
                    await setSelectedAddOnsMutation({
                        selectedAddons,
                        postId: window?.wpApiAddOnsManager?.postId
                    });
                } catch (error) {

                    console.error(error);
                }
            };

            publishButton.addEventListener('click', handlePublishClick);

            return () => {
                publishButton.removeEventListener('click', handlePublishClick);
            };
        }
    }, [selectedAddons]);

    return (
        <div className="page-title">
            {__('Menu Item Add-ons', 'olena-food-ordering')}
            <button
                className="save-btn"
                onClick={handleSaveAddOns}
            >
                {__('Save Changes', 'olena-food-ordering')}
            </button>
        </div>
    );
};