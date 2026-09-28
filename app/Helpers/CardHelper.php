<?php

namespace App\Helpers;

/**
 * Helper untuk konversi nomor kartu RFID antara Decimal dan Reverse Hexadecimal (Wiegand/UID standard).
 */
class CardHelper
{
    /**
     * Konversi angka Desimal ke Reverse Hexadecimal (Little Endian).
     * Contoh: 2917722844 => DCEAE8AD
     *
     * @param string|int $decimal
     * @return string
     */
    public static function decimalToReverseHex($decimal): string
    {
        $decimal = trim((string)$decimal);
        if (!ctype_digit($decimal) && !is_numeric($decimal)) {
            return '';
        }

        // Konversi ke format hexadecimal 8 digit (4 byte) padded 0 di kiri
        $hex = str_pad(dechex((int)$decimal), 8, '0', STR_PAD_LEFT);

        // Pecah per 2 digit karakter (1 byte)
        $bytes = str_split($hex, 2);

        // Balik urutan byte (Big Endian -> Little Endian / Reverse Hex)
        $reversed = array_reverse($bytes);

        return strtoupper(implode('', $reversed));
    }

    /**
     * Konversi Reverse Hexadecimal ke Desimal.
     * Contoh: DCEAE8AD => 2917722844
     *
     * @param string $hex
     * @return string
     */
    public static function reverseHexToDecimal(string $hex): string
    {
        $cleanHex = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $hex));
        if (empty($cleanHex)) {
            return '0';
        }

        // Genapkan panjang karakter jika ganjil
        if (strlen($cleanHex) % 2 !== 0) {
            $cleanHex = '0' . $cleanHex;
        }

        // Pecah per 2 digit karakter (1 byte)
        $bytes = str_split($cleanHex, 2);

        // Balik urutan byte kembali ke urutan normal (Big Endian)
        $reversed = array_reverse($bytes);
        $bigEndianHex = implode('', $reversed);

        // Konversi ke desimal tanpa overflow (menggunakan sprintf %u untuk unsigned 32-bit int)
        return sprintf('%u', hexdec($bigEndianHex));
    }

    /**
     * Parsing input mentah RFID untuk mendapatkan nilai Desimal dan Hexadecimal sekaligus.
     *
     * @param string $rawInput
     * @return array
     */
    public static function parseCard(string $rawInput): array
    {
        $rawInput = trim($rawInput);
        $isDecimal = ctype_digit($rawInput);

        if ($isDecimal) {
            $cardDec = $rawInput;
            $cardHex = self::decimalToReverseHex($rawInput);
        } else {
            $cardHex = strtoupper(preg_replace('/[^0-9A-Fa-f]/', '', $rawInput));
            $cardDec = self::reverseHexToDecimal($cardHex);
        }

        return [
            'card_dec'   => $cardDec,
            'card_hex'   => $cardHex,
            'is_decimal' => $isDecimal,
            'raw_input'  => $rawInput,
        ];
    }
}
