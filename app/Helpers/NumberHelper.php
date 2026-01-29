<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Format number to compact Indonesian Rupiah format
     * Examples:
     * - 500000 => "Rp 500.000"
     * - 1500000 => "Rp 1,5 Jt"
     * - 1500000000 => "Rp 1,5 M"
     * - 1500000000000 => "Rp 1,5 T"
     *
     * @param float|int $number The number to format
     * @param bool $compact Whether to use compact format with suffixes
     * @return string
     */
    public static function formatRupiah($number, bool $compact = false): string
    {
        if (!$compact) {
            return 'Rp ' . number_format($number, 0, ',', '.');
        }

        $absNumber = abs($number);
        $sign = $number < 0 ? '-' : '';

        // Triliun (Trillion)
        if ($absNumber >= 1_000_000_000_000) {
            $formatted = $absNumber / 1_000_000_000_000;
            return $sign . 'Rp ' . self::formatDecimal($formatted) . ' T';
        }

        // Miliar (Billion)
        if ($absNumber >= 1_000_000_000) {
            $formatted = $absNumber / 1_000_000_000;
            return $sign . 'Rp ' . self::formatDecimal($formatted) . ' M';
        }

        // Juta (Million)
        if ($absNumber >= 1_000_000) {
            $formatted = $absNumber / 1_000_000;
            return $sign . 'Rp ' . self::formatDecimal($formatted) . ' Jt';
        }

        // Kurang dari 1 juta - tampilkan penuh
        return $sign . 'Rp ' . number_format($absNumber, 0, ',', '.');
    }

    /**
     * Format decimal number for display
     * Shows 1 decimal place only if needed
     *
     * @param float $number
     * @return string
     */
    private static function formatDecimal(float $number): string
    {
        // If it's a whole number, don't show decimals
        if ($number == floor($number)) {
            return number_format($number, 0, ',', '.');
        }

        // Show up to 1 decimal place
        return number_format($number, 1, ',', '.');
    }

    /**
     * Get full formatted rupiah (for tooltips)
     *
     * @param float|int $number
     * @return string
     */
    public static function formatRupiahFull($number): string
    {
        $sign = $number < 0 ? '-' : '';
        return $sign . 'Rp ' . number_format(abs($number), 0, ',', '.');
    }
}
