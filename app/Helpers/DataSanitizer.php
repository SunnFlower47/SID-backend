<?php

namespace App\Helpers;

class DataSanitizer
{

    /**
     * Hash individual value using HMAC for search compatibility
     */
    public static function hashValue($value)
    {
        if (empty($value)) {
            return $value;
        }

        // Use HMAC-SHA256 for consistent hashing that can be searched
        // Frontend can use the same algorithm to search
        // Using app key instead of API key for security
        return hash_hmac('sha256', $value, 'app-key-for-hmac');
    }

    /**
     * Hash sensitive data for response (NIK, NKK, etc.)
     */
    public static function hashSensitiveData($value)
    {
        if (empty($value)) {
            return $value;
        }

        // Hash data sensitif untuk response dengan salt yang lebih kuat
        $salt = config('app.key') . 'sensitive-data-salt';
        return hash('sha256', $value . $salt);
    }

    /**
     * Generate searchable hash for frontend search
     * Frontend can use this to search by hashing the same input
     */
    public static function generateSearchableHash($value)
    {
        if (empty($value)) {
            return $value;
        }

        // Same algorithm as hashValue for consistency
        // Using app key instead of API key for security
        return hash_hmac('sha256', $value, 'app-key-for-hmac');
    }

    /**
     * Mask data sensitif untuk logging
     */
    public static function maskSensitiveData($data, $fields = ['nik', 'nkk', 'no_ktp', 'no_kk'])
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (in_array(strtolower($key), $fields)) {
                    $data[$key] = self::maskValue($value);
                } elseif (is_array($value)) {
                    $data[$key] = self::maskSensitiveData($value, $fields);
                }
            }
        }

        return $data;
    }

    /**
     * Mask individual value
     */
    public static function maskValue($value)
    {
        if (empty($value) || strlen($value) < 4) {
            return '***';
        }

        return substr($value, 0, 2) . str_repeat('*', strlen($value) - 4) . substr($value, -2);
    }
}
