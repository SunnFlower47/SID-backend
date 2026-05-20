import React, { useEffect, useRef, forwardRef, useImperativeHandle } from 'react';

const RecaptchaV2 = forwardRef(({ onVerify, onExpire, siteKey, enabled = true }, ref) => {
    const containerRef = useRef(null);
    const widgetIdRef = useRef(null);

    // Expose reset method to parent components
    useImperativeHandle(ref, () => ({
        reset: () => {
            if (enabled && window.grecaptcha && widgetIdRef.current !== null) {
                try {
                    window.grecaptcha.reset(widgetIdRef.current);
                } catch (e) {
                    console.error('Error resetting reCAPTCHA v2:', e);
                }
            }
        }
    }));

    const callbacksRef = useRef({ onVerify, onExpire });

    // Keep callbacks updated without triggering useEffect re-runs
    useEffect(() => {
        callbacksRef.current = { onVerify, onExpire };
    }, [onVerify, onExpire]);

    useEffect(() => {
        if (!enabled || !siteKey) return;

        let isMounted = true;
        const scriptId = 'recaptcha-v2-script';

        const renderWidget = () => {
            if (window.grecaptcha && containerRef.current && isMounted) {
                try {
                    // Avoid double rendering
                    containerRef.current.innerHTML = '';
                    
                    widgetIdRef.current = window.grecaptcha.render(containerRef.current, {
                        sitekey: siteKey,
                        callback: (token) => {
                            if (callbacksRef.current.onVerify) {
                                callbacksRef.current.onVerify(token);
                            }
                        },
                        'expired-callback': () => {
                            if (callbacksRef.current.onExpire) {
                                callbacksRef.current.onExpire();
                            }
                        }
                    });
                } catch (e) {
                    console.error('Failed to render reCAPTCHA v2:', e);
                }
            }
        };

        // If grecaptcha is already loaded, render immediately
        if (window.grecaptcha && window.grecaptcha.render) {
            renderWidget();
            return;
        }

        // Otherwise load the script dynamically
        let script = document.getElementById(scriptId);
        if (!script) {
            script = document.createElement('script');
            script.id = scriptId;
            script.src = 'https://www.recaptcha.net/recaptcha/api.js?render=explicit';
            script.async = true;
            script.defer = true;
            document.body.appendChild(script);
        }

        // Wait until grecaptcha is fully ready on the window
        const checkInterval = setInterval(() => {
            if (window.grecaptcha && window.grecaptcha.render) {
                clearInterval(checkInterval);
                renderWidget();
            }
        }, 100);

        return () => {
            isMounted = false;
            clearInterval(checkInterval);
        };
    }, [siteKey, enabled]);

    if (!enabled || !siteKey) return null;

    return (
        <div className="flex justify-center my-4">
            <div ref={containerRef} className="g-recaptcha"></div>
        </div>
    );
});

RecaptchaV2.displayName = 'RecaptchaV2';

export default RecaptchaV2;
