const transliterateMap = {
    'а': 'a', 'б': 'b', 'в': 'v', 'г': 'h', 'ґ': 'g',
    'д': 'd', 'е': 'e', 'є': 'ie', 'ж': 'zh', 'з': 'z',
    'и': 'y', 'і': 'i', 'ї': 'yi', 'й': 'y', 'к': 'k',
    'л': 'l', 'м': 'm', 'н': 'n', 'о': 'o', 'п': 'p',
    'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f',
    'х': 'kh', 'ц': 'ts', 'ч': 'ch', 'ш': 'sh', 'щ': 'shch',
    'ь': 'i', 'ю': 'yu', 'я': 'ya',
    'ă': 'a', 'â': 'a', 'î': 'i', 'ș': 's', 'ț': 't',
    'Ă': 'A', 'Â': 'A', 'Î': 'I', 'Ș': 'S', 'Ț': 'T',
    'é': 'e', 'è': 'e', 'ê': 'e', 'ë': 'e',
    'à': 'a', 'â': 'a', 'ä': 'a',
    'ù': 'u', 'û': 'u', 'ü': 'u',
    'ï': 'i', 'î': 'i',
    'ô': 'o', 'ö': 'o',
    'ÿ': 'y',
    'ç': 'c',
    'É': 'E', 'È': 'E', 'Ê': 'E', 'Ë': 'E',
    'À': 'A', 'Â': 'A', 'Ä': 'A',
    'Ù': 'U', 'Û': 'U', 'Ü': 'U',
    'Ï': 'I', 'Î': 'I',
    'Ô': 'O', 'Ö': 'O',
    'Ÿ': 'Y',
    'Ç': 'C',
    'Ł': 'L',
    'Ń': 'N',
    'Ó': 'O',
    'Ö': 'O',
    'Ø': 'O',
    'Œ': 'OE',
    'Š': 'S',
    'Ş': 'S',
    'Ÿ': 'Y',
    'Þ': 'TH',
    'æ': 'ae',
    'œ': 'oe',
    'ß': 'ss',
    'þ': 'th',
    '÷': 'o',
    'ø': 'o',
    'Ø': 'O',
    'Œ': 'OE',
    'Š': 'S',
    'Ş': 'S',
};

export const transliterate = (text) => {
    return text.toLowerCase().split('').map(char => 
        transliterateMap[char] || char
    ).join('').replace(/\W+/g, '');
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

