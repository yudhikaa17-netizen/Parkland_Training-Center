<?php

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table            = 'site_settings';
    protected $primaryKey       = 'setting_key';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'setting_group',
        'description',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = '';
    protected $updatedField  = 'updated_at';

    /**
     * Get a setting value by key with optional default fallback
     */
    public function getSetting(string $key, $default = null)
    {
        $row = $this->where('setting_key', $key)->first();
        if ($row && $row['setting_value'] !== null) {
            $val = $row['setting_value'];
            // If valid JSON, decode it
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                return $decoded;
            }
            return $val;
        }
        return $default;
    }

    /**
     * Set a setting value by key
     */
    public function setSetting(string $key, $value, string $group = 'general', ?string $description = null): bool
    {
        $strValue = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        
        $existing = $this->where('setting_key', $key)->first();
        if ($existing) {
            return (bool) $this->update($key, [
                'setting_value' => $strValue,
                'setting_group' => $group,
                'description'   => $description ?? $existing['description'],
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool) $this->insert([
            'setting_key'   => $key,
            'setting_value' => $strValue,
            'setting_group' => $group,
            'description'   => $description,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get all settings grouped by group name
     */
    public function getAllGrouped(): array
    {
        $all = $this->findAll();
        $grouped = [];
        foreach ($all as $row) {
            $val = $row['setting_value'];
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                $val = $decoded;
            }
            $grouped[$row['setting_group']][$row['setting_key']] = $val;
        }
        return $grouped;
    }
}
