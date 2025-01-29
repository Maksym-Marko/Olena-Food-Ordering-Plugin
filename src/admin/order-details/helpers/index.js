export const getCurrencySymbol = (settings) => {
    if (settings && settings.currency && settings.currency.value) {

        const currencyOption = settings.currency.options.find(option => option.value === settings.currency.value);
        if (currencyOption && currencyOption.symbol) {
            return currencyOption.symbol;
        }
    }
    return '$';
};