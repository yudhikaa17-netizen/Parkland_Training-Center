<?php

namespace App\Helpers;

class IconHelper
{
    /**
     * Map default topic keys to clean Bootstrap Icon classes.
     */
    public static $defaultIcons = [
        'benang_jahit'   => 'bi bi-bezier2',
        'jarum_jahit'    => 'bi bi-pin-angle-fill',
        'no_sew'         => 'bi bi-fire',
        'assembly'       => 'bi bi-gear-wide-connected',
        'sewing_machine' => 'bi bi-tools',
        'cutting_proses' => 'bi bi-scissors',
        'cutting_tools'  => 'bi bi-hammer',
        'inhouse'        => 'bi bi-mortarboard-fill',
        'material_kulit' => 'bi bi-layers-fill',
        'skill_level'    => 'bi bi-award-fill',
    ];

    /**
     * List of preset icon options for Content Manager dropdown selection.
     */
    public static function getPresetOptions(): array
    {
        return [
            'bi bi-bezier2'             => '🧵 Jalur Jahitan / Benang (bi-bezier2)',
            'bi bi-pin-angle-fill'      => '🪡 Jarum Jahit / Pin (bi-pin-angle-fill)',
            'bi bi-fire'                => '🔥 No-Sew / Hot Melt (bi-fire)',
            'bi bi-gear-wide-connected' => '⚙️ Assembly / Roda Gigi (bi-gear-wide-connected)',
            'bi bi-tools'               => '🛠️ Mesin Jahit / Tools (bi-tools)',
            'bi bi-scissors'            => '✂️ Cutting / Gunting (bi-scissors)',
            'bi bi-hammer'              => '🔨 Alat Potong / Dies (bi-hammer)',
            'bi bi-mortarboard-fill'    => '🎓 Inhouse / Kurikulum (bi-mortarboard-fill)',
            'bi bi-layers-fill'         => '📚 Material Kulit / Lapisan (bi-layers-fill)',
            'bi bi-award-fill'          => '🏆 Skill Level / Prestasi (bi-award-fill)',
            'bi bi-patch-check-fill'    => '✅ SOP Standar / QC (bi-patch-check-fill)',
            'bi bi-shield-check'        => '🛡️ K3 & Keselamatan Kerja (bi-shield-check)',
            'bi bi-stopwatch-fill'      => '⏱️ Cycle Time / Waktu (bi-stopwatch-fill)',
            'bi bi-box-seam'            => '📦 Komponen & Kemasan (bi-box-seam)',
            'bi bi-file-earmark-text'   => '📄 Dokumen Panduan (bi-file-earmark-text)',
            'bi bi-book-half'           => '📖 Buku Pelatihan (bi-book-half)',
            'bi bi-cpu-fill'            => '💻 Komputer / Otomasi (bi-cpu-fill)',
            'bi bi-lightning-charge'    => '⚡ Efisiensi & Kecepatan (bi-lightning-charge)',
        ];
    }

    /**
     * Render topic icon HTML cleanly.
     *
     * @param string|null $topicKey
     * @param string|null $rawIcon
     * @return string
     */
    public static function renderTopicIcon(?string $topicKey = null, ?string $rawIcon = null): string
    {
        $icon = trim((string)$rawIcon);

        // Check if icon is broken '????' or empty, fallback to default topic key map
        if (empty($icon) || strpos($icon, '?') !== false) {
            $icon = self::$defaultIcons[$topicKey] ?? 'bi bi-file-earmark-text';
        }

        // If it starts with an HTML tag like <i class="..."></i>, return as is
        if (str_starts_with($icon, '<i ') || str_starts_with($icon, '<span ')) {
            return $icon;
        }

        // If it looks like a Bootstrap Icon class (e.g. 'bi bi-scissors' or 'bi-award')
        if (str_starts_with($icon, 'bi ') || str_starts_with($icon, 'bi-')) {
            $class = str_starts_with($icon, 'bi ') ? $icon : ('bi ' . $icon);
            return '<i class="' . esc($class) . '"></i>';
        }

        // Otherwise return sanitized string (e.g., standard text/symbol)
        return esc($icon);
    }
}
