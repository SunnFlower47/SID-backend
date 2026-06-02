import React from 'react';
import { cn } from '@/lib/utils';

/**
 * FormField — Wrapper label + input/select/textarea dengan styling konsisten + error display.
 *
 * Ada 4 cara penggunaan:
 * 1. Wrapper generik: <FormField label="..." error={...}><input .../></FormField>
 * 2. Shorthand: <FormField.Input label="..." value={...} onChange={...} error={...} />
 * 3. Shorthand: <FormField.Select label="..." value={...} onChange={...} options={[...]} />
 * 4. Shorthand: <FormField.Textarea label="..." value={...} onChange={...} rows={3} />
 *
 * @param {string}          label     - Label field
 * @param {string}          error     - Pesan error validasi (dari Inertia errors object)
 * @param {boolean}         required  - Tampilkan asterisk *
 * @param {string}          className - Override wrapper class
 * @param {ReactNode}       children  - Input element (untuk wrapper generik)
 *
 * Style input konsisten: bg-gray-50 border rounded-2xl px-4 py-3 text-sm font-bold
 * focus:ring-4 focus:ring-green-500/10 focus:border-green-500
 */

// Shared input class
const inputClass = (error, extra = '') => cn(
    'w-full px-4 py-3 bg-gray-50 border rounded-2xl text-sm font-bold outline-none',
    'focus:ring-4 focus:ring-green-500/10 focus:border-green-500 transition-all',
    error ? 'border-red-400 focus:ring-red-500/10 focus:border-red-500' : 'border-gray-100',
    extra
);

// ─── Wrapper generik ────────────────────────────────────────────────────────
function FormField({ label, error, required = false, className = '', children }) {
    return (
        <div className={cn('space-y-1.5', className)}>
            {label && (
                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">
                    {label}{required && <span className="text-red-500 ml-0.5">*</span>}
                </label>
            )}
            {children}
            {error && (
                <p className="text-[10px] font-bold text-red-600 uppercase tracking-tight ml-1">
                    {error}
                </p>
            )}
        </div>
    );
}

// ─── Input shorthand ─────────────────────────────────────────────────────────
FormField.Input = function FormFieldInput({
    label,
    error,
    required = false,
    className = '',
    inputClassName = '',
    ...props
}) {
    return (
        <FormField label={label} error={error} required={required} className={className}>
            <input
                className={inputClass(error, inputClassName)}
                required={required}
                {...props}
            />
        </FormField>
    );
};

// ─── Select shorthand ────────────────────────────────────────────────────────
FormField.Select = function FormFieldSelect({
    label,
    error,
    required = false,
    className = '',
    options = [],
    placeholder,
    children,
    ...props
}) {
    return (
        <FormField label={label} error={error} required={required} className={className}>
            <select className={inputClass(error)} required={required} {...props}>
                {placeholder && <option value="">{placeholder}</option>}
                {options.map((opt) => {
                    // Support: string[] atau { value, label }[]
                    const val = typeof opt === 'string' ? opt : opt.value;
                    const lbl = typeof opt === 'string' ? opt : opt.label;
                    return <option key={val} value={val}>{lbl}</option>;
                })}
                {children}
            </select>
        </FormField>
    );
};

// ─── Textarea shorthand ──────────────────────────────────────────────────────
FormField.Textarea = function FormFieldTextarea({
    label,
    error,
    required = false,
    className = '',
    rows = 3,
    ...props
}) {
    return (
        <FormField label={label} error={error} required={required} className={className}>
            <textarea
                rows={rows}
                className={inputClass(error, 'resize-none')}
                required={required}
                {...props}
            />
        </FormField>
    );
};

export default FormField;
