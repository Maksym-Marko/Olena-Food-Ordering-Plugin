import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { updateSetting } from '@settings/store/settingsSlice';

const SettingsField = ({
    fieldId,
    label,
    type = 'text',
    placeholder = ''
}) => {
    const dispatch = useDispatch();
    // Get value from Redux store

    
    const value = useSelector(state => {
        console.log(state);
        // state.settings.settings[fieldId] ?? ''
    });

    const handleChange = (e) => {
        dispatch(updateSetting({
            fieldId,
            value: type === 'checkbox' ? e.target.checked : e.target.value
        }));
    };

    return (
        <div className="mb-4">
            <label className="block mb-2">{label}</label>
            {type === 'textarea' ? (
                <textarea
                    value={value}
                    onChange={handleChange}
                    placeholder={placeholder}
                    className="w-full p-2 border rounded"
                />
            ) : type === 'checkbox' ? (
                <input
                    type="checkbox"
                    checked={value}
                    onChange={handleChange}
                    className="mr-2"
                />
            ) : (
                <input
                    type={type}
                    value={value}
                    onChange={handleChange}
                    placeholder={placeholder}
                    className="w-full p-2 border rounded"
                />
            )}
        </div>
    );
};

export default SettingsField;