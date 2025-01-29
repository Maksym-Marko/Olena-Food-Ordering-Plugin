import { __ } from '@wordpress/i18n'
import { Container } from "@settings/components/Container";
import { BaseCard } from "@settings/components/BaseCard";
import { MainTitle } from "@settings/components/typography/MainTitle";
import { Description } from "@settings/components/typography/Description";
import { Button } from "@settings/components/Button";
import { useState, useEffect } from "react";
import { useGetImportDataInfoQuery, useDemoImportMutation } from "@settings/services/DemoImport"
import { ErrorMessage } from "@settings/components/ErrorMessage";

const Import = () => {

	const permalinkStructure = window?.wpApiSettings?.permalinkStructure;
	const permalinkPage = window?.wpApiSettings?.permalinkPage;

	const { data: importInfo, isLoading: importInfoLoading, error: importInfoError } = useGetImportDataInfoQuery();

	const [loadingError, setLoadingError] = useState(<span dangerouslySetInnerHTML={{
		__html: __('Something went wrong. Please check the website <a href="'+permalinkPage+'">Permalink Settings</a>. Use any except "Plain".', 'olena-food-ordering')
	}} />);

	const [demoImportMutation] = useDemoImportMutation();

	const [isImporting, setIsImporting] = useState(false);
	const [currentStep, setCurrentStep] = useState(0);
	const [error, setError] = useState(null);
	const [success, setSuccess] = useState(false);

	const requests = [
		{
			action: 'step-1',
			startMessage: __('Import Add-on categories', 'olena-food-ordering')
		},
		{
			action: 'step-2',
			startMessage: __('Import Add-ons', 'olena-food-ordering')
		},
		{
			action: 'step-3',
			startMessage: __('Import Menu Categories', 'olena-food-ordering')
		},
		{
			action: 'step-4',
			startMessage: __('Import Menu Tags', 'olena-food-ordering')
		},
		{
			action: 'step-5',
			startMessage: __('Import Menu Items', 'olena-food-ordering')
		},
		{
			action: 'step-6',
			startMessage: __('Import Menu Page', 'olena-food-ordering')
		},
	];

	useEffect(() => {

		if (importInfo) {

			if (importInfo.importProgress && typeof importInfo.importProgress === 'object') {

				setLoadingError(null);

				let foundIncomplete = false;
            
				for (let index = 0; index < requests.length; index++) {

					if (typeof importInfo.importProgress[requests[index].action] === 'undefined') {

						setCurrentStep(index);
						foundIncomplete = true;
						break;
					}
				}
				
				if (!foundIncomplete && requests.length > 0) {

					setCurrentStep(requests.length);
					setSuccess(true);
				}
			}

			if (!importInfo.hasOwnProperty('importProgress')) {
				
				setLoadingError(__('Something went wrong', 'olena-food-ordering'));
			}
		}
	}, [importInfo]);

	const sendPostRequest = async (request) => {

		let response = null;

		try {

			response = await demoImportMutation({ step: request.action })

			if (response?.data?.status !== 'success') {

				throw new Error(`HTTP error! status: ${response.status}`);
			}
			return true;
		} catch (error) {

			if (response?.error?.data?.message) {

				setError(response.error.data.message);
			} else {

				setError(`Failed to import ${request.startMessage}`);
			}

			return false;
		}
	};

	const runImport = async () => {

		setIsImporting(true);
		setError(null);
		setSuccess(false);

		for (let i = currentStep; i < requests.length; i++) {
			setCurrentStep(i);
			const success = await sendPostRequest(requests[i]);

			if (i === requests.length - 1) {

				setSuccess(true)

				window.location.reload();
			}

			if (!success) break;
		}

		setIsImporting(false);
	};

	const handleImport = (e) => {

		e.preventDefault();

		runImport();
	}

	return (
		<Container>
			<BaseCard>
				<MainTitle>{__('Demo Import', 'olena-food-ordering')}</MainTitle>

				{
					!importInfoLoading && <>

						{
							typeof importInfoError !== undefined && !loadingError ? <>
								<Description>
									<p>{__('You can import demo menu and add-ons to your website. This will help you get started quickly by providing pre-configured menu items and add-ons as examples.', 'olena-food-ordering')}</p>
								</Description>

								{error && <div className="error-message">{error}</div>}
								{isImporting && (
									<div className="ofo_progress">
										{requests[currentStep]?.startMessage} ({currentStep + 1}/{requests.length})
									</div>
								)}

								{
									success ? (
										<div className="ofo_success">
											{__('Import compete successfully. Please check your menu items.', 'olena-food-ordering')}
										</div>
									) : <Button
											onClick={handleImport}
											disabled={isImporting}
										>
											{isImporting ? __('Importing...', 'olena-food-ordering') : __('Import Demo Content', 'olena-food-ordering')}
										</Button>
								}
								
							</> : <ErrorMessage>
								{loadingError}
							</ErrorMessage>
						}
					</>
				}
			</BaseCard>
		</Container>
	)
}

export default Import