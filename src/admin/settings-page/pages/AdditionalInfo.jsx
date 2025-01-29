import React from 'react';
import { __ } from '@wordpress/i18n';
import { Container } from "@settings/components/Container";
import { BaseCard } from "@settings/components/BaseCard";
import { MainTitle } from "@settings/components/typography/MainTitle";
import { FlashBox } from "@settings/components/FlashBox";
import { NavLink } from "react-router-dom";
import { useSelector } from "react-redux";
import { useGetSettingsQuery } from "@settings/services/Settings"
import { useEffect, useState } from "react";
import { ErrorMessage } from "@settings/components/ErrorMessage";

const AdditionalInfo = () => {

    const permalinkPage = window?.wpApiSettings?.permalinkPage;

    const [loadingError, setLoadingError] = useState(<span dangerouslySetInnerHTML={{
        __html: __('Something went wrong. Please check the website <a href="' + permalinkPage + '">Permalink Settings</a>. Use any except "Plain".', 'olena-food-ordering')
    }} />);

    const [storeUrl, setStoreUrl] = useState(null);

    const { data: settings, isLoading, error } = useGetSettingsQuery();

    useEffect(() => {

        if (settings && Object.keys(settings).length > 0) {

            if (settings?.store_url?.value) {

                setStoreUrl(settings.store_url.value)
            }
        }
    }, [settings]);

    const updateButtonText = (button) => {
        const originalText = button.textContent;
        button.textContent = __('Copied!', 'olena-food-ordering');
        setTimeout(() => {
            button.textContent = originalText;
        }, 2000);
    };

    const handleCopy = async (text, event) => {
        // Try using Clipboard API first
        if (navigator.clipboard && navigator.clipboard.writeText) {
            try {
                await navigator.clipboard.writeText(text);
                updateButtonText(event.target);
            } catch (err) {
                console.error('Failed to copy text:', err);
            }
        } else {
            // Fallback: Create temporary textarea
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';  // Prevent scrolling to bottom
            document.body.appendChild(textarea);
            textarea.select();

            try {
                document.execCommand('copy');
                updateButtonText(event.target);
            } catch (err) {
                console.error('Failed to copy text:', err);
            }

            document.body.removeChild(textarea);
        }
    };

    return (
        <Container>
            <BaseCard>
                <MainTitle>{__('Additional Info', 'olena-food-ordering')}</MainTitle>

                {
                    typeof error !== 'undefined' ?
                        <ErrorMessage>
                            {loadingError}
                        </ErrorMessage> : <>

                            <div className="settings-section-divider">{__('Shortcodes', 'olena-food-ordering')}</div>

                            <div className="shortcode-box-wrapper">

                                <h3 className="additional-info-title">{__('Food Ordering Store', 'olena-food-ordering')}</h3>

                                <div className="shortcode-box">
                                    <code>[olena_food_ordering_store]</code>
                                    <button
                                        className="copy-button"
                                        onClick={(event) => handleCopy('[olena_food_ordering_store]', event)}
                                    >
                                        {__('Copy', 'olena-food-ordering')}
                                    </button>
                                </div>

                                <p className="description">
                                    {__('This shortcode allows you to display the food ordering store interface on any page or post of your website.', 'olena-food-ordering')}
                                </p>

                            </div>

                            <div className="shortcode-box-wrapper">

                                <h3 className="additional-info-title">{__('Single Item Button', 'olena-food-ordering')}</h3>

                                {!storeUrl && (
                                    <FlashBox className="fo-warning">
                                        {__('You need to set the Store URL in the ', 'olena-food-ordering')}<NavLink to="/">{__('plugin settings', 'olena-food-ordering')}</NavLink>{__(' page before using this shortcode.', 'olena-food-ordering')}
                                    </FlashBox>
                                )}

                                <div className="shortcode-box">
                                    <code>[olena_food_ordering_single_item_button]</code>
                                    <button
                                        className="copy-button"
                                        onClick={(event) => handleCopy('[olena_food_ordering_single_item_button]', event)}
                                    >
                                        {__('Copy', 'olena-food-ordering')}
                                    </button>
                                </div>

                                <p className="description">
                                    {__('This shortcode allows you to display the single item button on any page or post of your website.', 'olena-food-ordering')}
                                </p>

                                <p className="description">
                                    {__('You can use the post_id attribute to specify the ID of the post you want to display the single item button for. Eg. [olena_food_ordering_single_item_button post_id="123"].', 'olena-food-ordering')}
                                </p>

                            </div>

                            <div className="shortcode-box-wrapper">

                                <h3 className="additional-info-title">{__('Cart Widget', 'olena-food-ordering')}</h3>

                                {!storeUrl && (
                                    <FlashBox className="fo-warning">
                                        {__('You need to set the Store URL in the ', 'olena-food-ordering')}<NavLink to="/">{__('plugin settings', 'olena-food-ordering')}</NavLink>{__(' page before using this shortcode.', 'olena-food-ordering')}
                                    </FlashBox>
                                )}

                                <div className="shortcode-box">
                                    <code>[olena_food_ordering_cart]</code>
                                    <button
                                        className="copy-button"
                                        onClick={(event) => handleCopy('[olena_food_ordering_cart]', event)}
                                    >
                                        {__('Copy', 'olena-food-ordering')}
                                    </button>
                                </div>

                                <p className="description">
                                    {__('This shortcode allows you to display the cart widget on any page, post header or footer of your website.', 'olena-food-ordering')}
                                </p>

                            </div>

                        </>
                }



            </BaseCard>
        </Container>
    );
};

export default AdditionalInfo;
