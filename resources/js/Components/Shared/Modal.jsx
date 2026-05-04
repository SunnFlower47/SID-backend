import React, { useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { X } from 'lucide-react';

export default function Modal({
    children,
    show = false,
    maxWidth = '2xl',
    closeable = true,
    onClose = () => {},
}) {
    // Prevent scrolling when modal is open
    useEffect(() => {
        if (show) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }
        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [show]);

    const maxWidthClass = {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[maxWidth];

    return (
        <AnimatePresence>
            {show && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-x-hidden overflow-y-auto outline-none focus:outline-none">
                    {/* Backdrop */}
                    <motion.div
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        onClick={closeable ? onClose : null}
                        className="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"
                    />

                    {/* Modal Content */}
                    <motion.div
                        initial={{ opacity: 0, scale: 0.95, y: 20 }}
                        animate={{ opacity: 1, scale: 1, y: 0 }}
                        exit={{ opacity: 0, scale: 0.95, y: 20 }}
                        transition={{ type: 'spring', duration: 0.5, bounce: 0.3 }}
                        className={`relative w-full ${maxWidthClass} mx-auto bg-white rounded-[2rem] shadow-2xl overflow-hidden z-[51]`}
                    >
                        {children}
                    </motion.div>
                </div>
            )}
        </AnimatePresence>
    );
}
