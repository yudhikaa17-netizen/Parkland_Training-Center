<?php

namespace App\Helpers;

class ContentHelper
{
    /**
     * Convert HTML content to human-readable plain text format for editing.
     *
     * @param string|null $html
     * @return string
     */
    public static function htmlToPlainText(?string $html): string
    {
        if (empty($html)) {
            return '';
        }

        $text = $html;

        // Extract part-cards: <div class="part-card">...<span class="t...">Title</span><p>Description</p></div>
        if (strpos($text, 'part-card') !== false) {
            $extractedItems = [];
            preg_match_all('/<div class="part-card">.*?<span class="num">(\d+)<\/span>.*?<span class="t[^>]*>(.*?)<\/span>.*?<p>(.*?)<\/p>.*?<\/div>/s', $text, $matches, PREG_SET_ORDER);

            if (!empty($matches)) {
                foreach ($matches as $m) {
                    $num   = trim($m[1]);
                    $title = trim(strip_tags($m[2]));
                    $desc  = trim(strip_tags($m[3]));
                    $extractedItems[] = "{$num}. {$title}\n{$desc}";
                }
                return implode("\n\n", $extractedItems);
            }
        }

        // Extract chip-flow: <span class="chip">1. Step</span><span class="arrow">→</span>...
        if (strpos($text, 'chip-flow') !== false) {
            preg_match_all('/<span class="chip">(.*?)<\/span>/s', $text, $chipMatches);
            if (!empty($chipMatches[1])) {
                $steps = array_map(function($c) {
                    return trim(strip_tags($c));
                }, $chipMatches[1]);
                return implode("\n", $steps);
            }
        }

        // Extract standard list items <li>
        if (strpos($text, '<li') !== false) {
            $text = preg_replace('/<li[^>]*>(.*?)<\/li>/is', "• $1\n", $text);
        }

        // Clean up common tags
        $text = preg_replace('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/is', "\n$1\n", $text);
        $text = preg_replace('/<p[^>]*>(.*?)<\/p>/is', "$1\n\n", $text);
        $text = preg_replace('/<br\s*\/?>/i', "\n", $text);
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }

    /**
     * Parse point items or plain text input from user into clean, beautifully styled HTML for display.
     *
     * @param string|null $plainText
     * @param array|null $pointTitles
     * @param array|null $pointDescs
     * @return string
     */
    public static function formatForDisplay(?string $plainText = null, ?array $pointTitles = null, ?array $pointDescs = null): string
    {
        // 1. If structured point items are provided from the point builder
        if (!empty($pointTitles) && is_array($pointTitles)) {
            $cards = [];
            $num = 1;
            foreach ($pointTitles as $idx => $rawTitle) {
                $title = trim((string)$rawTitle);
                $desc  = trim((string)($pointDescs[$idx] ?? ''));

                if (empty($title) && empty($desc)) {
                    continue;
                }

                $cards[] = '    <div class="part-card">' . "\n" .
                           '        <span class="num">' . $num . '</span>' . "\n" .
                           '        <span class="t text-white">' . esc($title) . '</span>' . "\n" .
                           '        <p>' . nl2br(esc($desc)) . '</p>' . "\n" .
                           '    </div>';
                $num++;
            }

            if (!empty($cards)) {
                $html = '<div class="part-grid">' . "\n" . implode("\n", $cards) . "\n" . '</div>';
                if (!empty(trim((string)$plainText))) {
                    $html .= "\n" . '<div class="p-3 rounded-2 mt-3" style="background:var(--panel-2);border:1px solid var(--border);font-size:0.88rem;">' . nl2br(esc(trim($plainText))) . '</div>';
                }
                return $html;
            }
        }

        $input = trim((string)$plainText);
        if (empty($input)) {
            return '';
        }

        // If input already starts with HTML container tag (<div, <p, <ul, <table)
        if (preg_match('/^\s*<(div|p|ul|ol|table|h[1-6])/i', $input)) {
            return $input;
        }

        // 2. Parse numbered items like "1. Judul Poin\nPenjelasan..." or "1. Judul: Penjelasan"
        $blocks = preg_split("/\n\s*\n+/", $input);
        $cards  = [];
        $regularParagraphs = [];
        $num = 1;

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            // Check if block starts with number e.g. "1." or "1)"
            if (preg_match('/^(\d+)[\.\)]\s*(.*)$/s', $block, $m)) {
                $lines = explode("\n", trim($m[2]), 2);
                $title = trim($lines[0]);
                $desc  = isset($lines[1]) ? trim($lines[1]) : '';

                // Handle format "1. Title: Description"
                if (empty($desc) && strpos($title, ':') !== false) {
                    $parts = explode(':', $title, 2);
                    $title = trim($parts[0]);
                    $desc  = trim($parts[1]);
                }

                $cards[] = '    <div class="part-card">' . "\n" .
                           '        <span class="num">' . $num . '</span>' . "\n" .
                           '        <span class="t text-white">' . esc($title) . '</span>' . "\n" .
                           '        <p>' . nl2br(esc($desc)) . '</p>' . "\n" .
                           '    </div>';
                $num++;
            } elseif (str_starts_with($block, '•') || str_starts_with($block, '- ') || str_starts_with($block, '* ')) {
                // Bullet points list
                $lines = explode("\n", $block);
                $liItems = [];
                foreach ($lines as $line) {
                    $cleanLine = trim(preg_replace('/^[•\-\*]\s*/', '', $line));
                    if (!empty($cleanLine)) {
                        $liItems[] = '<li>' . esc($cleanLine) . '</li>';
                    }
                }
                if (!empty($liItems)) {
                    $regularParagraphs[] = '<div class="p-3 rounded-2" style="background:var(--panel-2);border:1px solid var(--border);font-size:0.88rem;"><ul class="mb-0 ps-3">' . implode('', $liItems) . '</ul></div>';
                }
            } else {
                $regularParagraphs[] = '<div class="p-3 rounded-2 mb-2" style="background:var(--panel-2);border:1px solid var(--border);font-size:0.88rem;">' . nl2br(esc($block)) . '</div>';
            }
        }

        $result = '';
        if (!empty($cards)) {
            $result .= '<div class="part-grid mb-3">' . "\n" . implode("\n", $cards) . "\n" . '</div>';
        }
        if (!empty($regularParagraphs)) {
            $result .= implode("\n", $regularParagraphs);
        }

        return !empty($result) ? $result : nl2br(esc($input));
    }
}
