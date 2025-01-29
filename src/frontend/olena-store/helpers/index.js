export const extractMenuItemData = (item) => {

    return {
        id: item?.id || 0,
        name: item?.title || 'empty string',
        description: item?.description || 'empty string',
        categories: item?.categories || [],
        thumbnail: item.thumbnail,
        price_per_unit: parseFloat(item?.price || 0),
        quantity: 1,
        selected_add_ons: [],
        specialInstructions: '',
        timestamps: {
            selected: {
                utc: new Date().toISOString()
            },
            lastModified: {
                utc: new Date().toISOString()
            },
            addedToCart: {}
        }
    }
}

export const updateLocalStorage = (key, data) => {
    if (data && (Array.isArray(data) ? data.length > 0 : Object.keys(data).length > 0)) {
        localStorage.setItem(key, JSON.stringify(data));
    } else {
        localStorage.removeItem(key);
    }
};

export const getCurrencySymbol = (settings) => {
    if (settings && settings.currency && settings.currency.value) {

        const currencyOption = settings.currency.options.find(option => option.value === settings.currency.value);
        if (currencyOption && currencyOption.symbol) {
            return currencyOption.symbol;
        }
    }
    return '$';
};

export const isFreeDeliveryEnabled = (settings) => {

    if (settings && settings.enable_free_delivery && settings.enable_free_delivery.value === 'yes') {
        return true;
    }
    return false;
};

export const getFreeDeliveryMinAmount = (settings) => {
    
    if (settings && settings.free_delivery_min_amount && settings.free_delivery_min_amount.value) {
        return parseFloat(settings.free_delivery_min_amount.value);
    }
    return 0;
};
