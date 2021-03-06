import React from 'react';
import './UnderlinedInput.css';

export default function UnderlinedInput({
    name,
    value: initialValue = '',
    type = 'text',
    padding = 10,
    autofocus,
    placeholder: placeholderValue,
    ...props
}) {
    const [value, setValue] = React.useState(initialValue);
    const handleChange = (event) => {
        setValue(event.target.value);
    };
    const placeholderPad = padding - value.length;
    const placeholder = '_'.repeat(placeholderPad > 0 ? placeholderPad : 0);
    const viewValue = type === 'password' ? '*'.repeat(value.length) : value;

    if (autofocus) {
        props.ref = (input) => input && input.focus();
    }

    return (
        <div className="underlinedinput" {...props}>
            <input type={type} name={name} value={value} onChange={handleChange} className="underlinedinput-typography" />
            <div className="underlinedinput-view">
                {value ? (
                    <>
                        <span className="underlinedinput-value">{viewValue}</span>
                        <span className="underlinedinput-carat">&nbsp;</span>
                    </>
                ) : (
                    <>
                        <span className="underlinedinput-carat">&nbsp;</span>
                        <span className="underlinedinput-placeholder">{placeholderValue}</span>
                    </>
                )}
            </div>
            <div className="underlinedinput-underline">
                <span style={{ opacity: 0 }}>{viewValue}</span>
                <span>{placeholder}</span>
            </div>
        </div>
    );
}
