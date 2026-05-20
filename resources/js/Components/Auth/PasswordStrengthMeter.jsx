import React, { useMemo } from 'react';

const PasswordStrengthMeter = ({ password }) => {
    const strength = useMemo(() => {
        if (!password) return 0;
        let score = 0;

        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;

        return Math.min(score, 4);
    }, [password]);

    const info = useMemo(() => {
        switch (strength) {
            case 0:
                return { text: 'Sangat Lemah', color: 'text-red-500', barColor: 'bg-red-500' };
            case 1:
                return { text: 'Lemah', color: 'text-red-500', barColor: 'bg-red-500' };
            case 2:
                return { text: 'Sedang', color: 'text-amber-500', barColor: 'bg-amber-500' };
            case 3:
                return { text: 'Kuat', color: 'text-emerald-500', barColor: 'bg-emerald-500' };
            case 4:
                return { text: 'Sangat Kuat', color: 'text-emerald-600', barColor: 'bg-emerald-600' };
            default:
                return { text: '', color: 'text-gray-400', barColor: 'bg-gray-200' };
        }
    }, [strength]);

    if (!password) {
        return (
            <div className="mt-2">
                <div className="flex space-x-1">
                    {[1, 2, 3, 4].map((i) => (
                        <div key={i} className="h-1 flex-1 bg-gray-200 rounded-sm transition-all duration-300"></div>
                    ))}
                </div>
                <p className="text-xs text-gray-500 mt-1">Masukkan password untuk melihat kekuatan</p>
            </div>
        );
    }

    return (
        <div className="mt-2">
            <div className="flex space-x-1">
                {[1, 2, 3, 4].map((i) => (
                    <div
                        key={i}
                        className={`h-1 flex-1 rounded-sm transition-all duration-300 ${
                            i <= strength ? info.barColor : 'bg-gray-200'
                        }`}
                    ></div>
                ))}
            </div>
            <p className={`text-xs mt-1 font-semibold ${info.color}`}>
                Kekuatan: {info.text}
            </p>
        </div>
    );
};

export default PasswordStrengthMeter;
