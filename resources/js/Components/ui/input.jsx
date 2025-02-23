import React from "react";

const Input = ({ type = "text", placeholder, value, onChange, className }) => {
    return (
        <input
            type={type}
            placeholder={placeholder}
            value={value}
            onChange={onChange}
            className={`border rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none ${className}`}
        />
    );
};

export default Input;
