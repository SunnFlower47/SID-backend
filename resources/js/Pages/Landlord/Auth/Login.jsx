import { useState, useEffect } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';

export default function Login({ status }) {
    const { recaptcha } = usePage().props;
    const [clientError, setClientError] = useState('');

    const { data, setData, post, processing, errors, reset, transform } = useForm({
        email: '',
        password: '',
        remember: false,
        recaptcha_token: '',
    });

    useEffect(() => {
        // Dynamically load reCAPTCHA v3 script if enabled
        if (recaptcha?.enabled && recaptcha?.v3_site_key) {
            const scriptId = 'recaptcha-v3-script';
            let script = document.getElementById(scriptId);
            if (!script) {
                script = document.createElement('script');
                script.id = scriptId;
                script.src = `https://www.recaptcha.net/recaptcha/api.js?render=${recaptcha.v3_site_key}`;
                script.async = true;
                script.defer = true;
                document.body.appendChild(script);
            }
        }

        return () => {
            reset('password');
        };
    }, []);

    const executeRecaptcha = () => {
        return new Promise((resolve, reject) => {
            if (!recaptcha?.enabled || !recaptcha?.v3_site_key) {
                resolve('');
                return;
            }

            if (!window.grecaptcha || !window.grecaptcha.execute) {
                reject(new Error('reCAPTCHA tidak termuat dengan benar. Silakan coba lagi.'));
                return;
            }

            window.grecaptcha.ready(() => {
                window.grecaptcha.execute(recaptcha.v3_site_key, { action: 'landlord_login' })
                    .then(resolve)
                    .catch(reject);
            });
        });
    };

    const submit = async (e) => {
        e.preventDefault();
        setClientError('');

        try {
            let token = '';
            if (recaptcha?.enabled) {
                token = await executeRecaptcha();
                if (!token) {
                    token = await executeRecaptcha();
                }
            }

            transform((data) => ({
                ...data,
                recaptcha_token: token
            }));

            post(route('landlord.login.post'));
        } catch (err) {
            console.error('reCAPTCHA error:', err);
            setClientError('Verifikasi keamanan gagal. Silakan klik tombol masuk lagi.');
        }
    };

    return (
        <div className="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <Head title="Landlord Login" />

            <div className="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-md overflow-hidden sm:rounded-lg">
                <div className="text-center mb-8">
                    <h1 className="text-2xl font-bold text-gray-800">Landlord Dashboard</h1>
                    <p className="text-gray-500 text-sm mt-1">Sistem Multi-Tenant Diskominfo Purwakarta</p>
                </div>

                {status && <div className="mb-4 font-medium text-sm text-green-600">{status}</div>}

                {clientError && (
                    <div className="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-bold" role="alert">
                        <span>{clientError}</span>
                    </div>
                )}

                {(errors.recaptcha_token || errors['g-recaptcha-response']) && (
                    <div className="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-xs font-bold" role="alert">
                        <span>{errors.recaptcha_token || errors['g-recaptcha-response']}</span>
                    </div>
                )}

                <form onSubmit={submit}>
                    <div>
                        <label htmlFor="email" className="block font-medium text-sm text-gray-700">Email Admin Diskominfo</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value={data.email}
                            className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            autoComplete="username"
                            autoFocus
                            onChange={(e) => setData('email', e.target.value)}
                        />
                        {errors.email && <p className="text-sm text-red-600 mt-2">{errors.email}</p>}
                    </div>

                    <div className="mt-4">
                        <label htmlFor="password" className="block font-medium text-sm text-gray-700">Password</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            value={data.password}
                            className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            autoComplete="current-password"
                            onChange={(e) => setData('password', e.target.value)}
                        />
                        {errors.password && <p className="text-sm text-red-600 mt-2">{errors.password}</p>}
                    </div>

                    <div className="flex items-center justify-end mt-6">
                        <button 
                            type="submit"
                            className="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 ms-4" 
                            disabled={processing}
                        >
                            Log in ke Landlord
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
