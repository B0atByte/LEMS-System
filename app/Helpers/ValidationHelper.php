<?php
/**
 * Validation Helper Functions
 * Provides input validation utilities
 */

/**
 * Validate required field
 */
function validate_required($value): bool {
    return !empty(trim($value));
}

/**
 * Validate email
 */
function validate_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate minimum length
 */
function validate_min_length(string $value, int $min): bool {
    return mb_strlen($value) >= $min;
}

/**
 * Validate maximum length
 */
function validate_max_length(string $value, int $max): bool {
    return mb_strlen($value) <= $max;
}

/**
 * Validate numeric
 */
function validate_numeric($value): bool {
    return is_numeric($value);
}

/**
 * Validate phone number (Thai format)
 */
function validate_phone(string $phone): bool {
    $pattern = '/^[0-9]{9,10}$/';
    return preg_match($pattern, $phone) === 1;
}

/**
 * Validate Thai citizen ID (13 digits)
 */
function validate_citizen_id(string $id): bool {
    if (!preg_match('/^[0-9]{13}$/', $id)) {
        return false;
    }

    // Validate checksum
    $sum = 0;
    for ($i = 0; $i < 12; $i++) {
        $sum += (int)$id[$i] * (13 - $i);
    }
    $checksum = (11 - ($sum % 11)) % 10;

    return (int)$id[12] === $checksum;
}

/**
 * Validate date format
 */
function validate_date(string $date, string $format = 'Y-m-d'): bool {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

/**
 * Validate file upload
 */
function validate_file_upload(array $file, int $maxSize = 5242880, array $allowedTypes = ['jpg', 'jpeg', 'png']): array {
    $errors = [];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error';
        return $errors;
    }

    if ($file['size'] > $maxSize) {
        $errors[] = 'File size exceeds maximum allowed (' . ($maxSize / 1024 / 1024) . 'MB)';
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
    }

    return $errors;
}

/**
 * Simple validation function
 */
function validate(array $data, array $rules): array {
    $errors = [];

    foreach ($rules as $field => $ruleString) {
        $value = $data[$field] ?? '';
        $ruleList = explode('|', $ruleString);

        foreach ($ruleList as $rule) {
            if ($rule === 'required' && !validate_required($value)) {
                $errors[$field] = ucfirst($field) . ' is required';
                break;
            }

            if (str_starts_with($rule, 'min:')) {
                $min = (int)substr($rule, 4);
                if (!validate_min_length($value, $min)) {
                    $errors[$field] = ucfirst($field) . " must be at least {$min} characters";
                    break;
                }
            }

            if (str_starts_with($rule, 'max:')) {
                $max = (int)substr($rule, 4);
                if (!validate_max_length($value, $max)) {
                    $errors[$field] = ucfirst($field) . " must not exceed {$max} characters";
                    break;
                }
            }

            if ($rule === 'email' && !validate_email($value)) {
                $errors[$field] = ucfirst($field) . ' must be a valid email address';
                break;
            }

            if ($rule === 'numeric' && !validate_numeric($value)) {
                $errors[$field] = ucfirst($field) . ' must be a number';
                break;
            }
        }
    }

    return $errors;
}
