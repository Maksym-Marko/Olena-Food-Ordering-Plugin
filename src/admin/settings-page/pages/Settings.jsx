import { __ } from '@wordpress/i18n';
import { useState, useEffect } from "react";
import { Container } from "@settings/components/Container";
import { BaseCard } from "@settings/components/BaseCard";
import { MainTitle } from "@settings/components/typography/MainTitle";
import { FormField } from "@settings/components/form/FormField";
import { FormActions } from "@settings/components/form/FormActions";
import { Button } from "@settings/components/Button";
import { ErrorMessage } from "@settings/components/ErrorMessage";
import { useDispatch, useSelector } from "react-redux"
import { setSetting } from "@settings/store/slices/settings/settingsSlice"
import { useSetSettingsMutation, useGetSettingsQuery } from "@settings/services/Settings"

const Settings = () => {

	const permalinkStructure = window?.wpApiSettings?.permalinkStructure;
	const permalinkPage = window?.wpApiSettings?.permalinkPage;

	const { data: settings, isLoading, error } = useGetSettingsQuery();

	const [formData, setFormData] = useState({});
	const [loadingError, setLoadingError] = useState(null);

	useEffect(() => {

		if (settings && Object.keys(settings).length > 0) {

			const formFields = Object.entries(settings).reduce((acc, [key, item]) => {				
				acc[key] = {
					value: item.value || '',
					label: item.label,
					type: item.type || 'text',
					options: item.options || []
				};
				return acc;
			}, {});
			setFormData(formFields);

		} else {

			if(permalinkStructure.length === 0) {

				setLoadingError(<span dangerouslySetInnerHTML={{
					__html: __('Something went wrong. Please check the website <a href="'+permalinkPage+'">Permalink Settings</a>. Use any except "Plain".', 'olena-food-ordering')
				  }} />);
			} else {
				
				setLoadingError(__('Something went wrong', 'olena-food-ordering'));
			}
		}
	}, [settings]);

	const dispatch = useDispatch();

	useEffect(() => {

		if (Object.keys(formData).length > 0) {

			const fieldsData = Object.entries(formData).reduce((acc, [key, item]) => {
				if (item?.type !== 'section_divider') {
					acc[key] = item.value;
				}
				return acc;
			}, {});

			dispatch(setSetting({ data: fieldsData }))
		}
	}, [formData]);

	const handleChange = (e) => {

		const { name, value } = e.target;

		setFormData(prev => ({
			...prev,
			[name]: {
				...prev[name],
				value: value
			}
		}));
	};

	const handleRadioChange = (e) => {
		const { name, checked } = e.target;
		setFormData(prev => ({
			...prev,
			[name]: {
				...prev[name],
				value: checked
			}
		}));
	};

	const fields = useSelector(state => state.settings.fields)

	const [setSettingsMutation, { isLoading: settingsLoading, isError }] = useSetSettingsMutation()

	const handleSubmit = async (e) => {
		e.preventDefault();

		try {

			await setSettingsMutation({settings: fields})
		} catch (error) {
			
			console.error(error);
		}
	}

	return (
		(isLoading ? 'Loading' : <Container>
			<BaseCard>
				<MainTitle>Settings</MainTitle>
				{
					error ? (<ErrorMessage>
						{loadingError}
						</ErrorMessage>)
						: (<>
							<form onSubmit={handleSubmit}>

								{formData &&
									Object.entries(formData || {}).map(([fieldName, item]) => (
										<div key={fieldName}>
											{(fieldName === 'free_delivery_min_amount' || fieldName === 'free_delivery_requirements') && 
											 formData['enable_free_delivery']?.value === 'no' ? null : (
												<>
													{item?.type === 'section_divider' ? (
														<div className="settings-section-divider">
															{item.label}
														</div>
													) : (
														<FormField
															label={item.label}
															name={fieldName}
															value={formData[fieldName].value || ''}
															onChange={handleChange}
															error={!formData[fieldName].value && "This field is required"}
															type={item.type}
															options={item.options}
														/>
													)}
												</>
											)}
										</div>
									))}

								<FormActions>
									<Button type="submit">Save Settings</Button>
								</FormActions>
							</form>
						</>)
				}

			</BaseCard>
		</Container>)
	)
}

export default Settings;
