<?php

namespace App\Models\Central;

use Illuminate\Database\Eloquent\Model;

class CentralSetting extends Model
{
    protected $connection = 'mysql';

    protected $table = 'central_settings';

    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set/update a setting value.
     */
    public static function set(string $key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
