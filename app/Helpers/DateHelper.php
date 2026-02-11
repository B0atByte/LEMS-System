<?php
/**
 * Date Helper Functions
 * Provides date formatting and manipulation utilities
 */

/**
 * Format date to Thai format
 */
function format_date_thai(string $date): string {
    if (empty($date)) return '-';

    $thaiMonths = [
        1 => 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
        'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
    ];

    $d = new DateTime($date);
    $day = $d->format('d');
    $month = $thaiMonths[(int)$d->format('n')];
    $year = $d->format('Y') + 543;

    return "{$day} {$month} {$year}";
}

/**
 * Format datetime to Thai format
 */
function format_datetime_thai(string $datetime): string {
    if (empty($datetime)) return '-';

    $d = new DateTime($datetime);
    $date = format_date_thai($datetime);
    $time = $d->format('H:i');

    return "{$date} เวลา {$time} น.";
}

/**
 * Format date (Y-m-d)
 */
function format_date(string $date): string {
    if (empty($date)) return '-';
    $d = new DateTime($date);
    return $d->format('Y-m-d');
}

/**
 * Format datetime (Y-m-d H:i:s)
 */
function format_datetime(string $datetime): string {
    if (empty($datetime)) return '-';
    $d = new DateTime($datetime);
    return $d->format('Y-m-d H:i:s');
}

/**
 * Get current date
 */
function current_date(): string {
    return date('Y-m-d');
}

/**
 * Get current datetime
 */
function current_datetime(): string {
    return date('Y-m-d H:i:s');
}

/**
 * Get current timestamp
 */
function current_timestamp(): int {
    return time();
}

/**
 * Calculate days between dates
 */
function days_between(string $date1, string $date2): int {
    $d1 = new DateTime($date1);
    $d2 = new DateTime($date2);
    $diff = $d1->diff($d2);
    return abs($diff->days);
}

/**
 * Format relative time (ago)
 */
function time_ago(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'เมื่อสักครู่';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "{$minutes} นาทีที่แล้ว";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "{$hours} ชั่วโมงที่แล้ว";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "{$days} วันที่แล้ว";
    } else {
        return format_date_thai($datetime);
    }
}
