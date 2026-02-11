<?php
/**
 * File Helper Functions
 * Provides file upload and management utilities
 */

/**
 * Upload single file
 */
function upload_file(array $file, string $destination, array $allowedExtensions = ['jpg', 'jpeg', 'png'], int $maxSize = 5242880): array {
    $result = [
        'success' => false,
        'message' => '',
        'filename' => ''
    ];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['message'] = 'File upload error';
        return $result;
    }

    // Check file size
    if ($file['size'] > $maxSize) {
        $result['message'] = 'File size exceeds maximum allowed (' . ($maxSize / 1024 / 1024) . 'MB)';
        return $result;
    }

    // Check file extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExtensions)) {
        $result['message'] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions);
        return $result;
    }

    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $destination . '/' . $filename;

    // Create destination directory if not exists
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        $result['success'] = true;
        $result['message'] = 'File uploaded successfully';
        $result['filename'] = $filename;
    } else {
        $result['message'] = 'Failed to move uploaded file';
    }

    return $result;
}

/**
 * Upload multiple files
 */
function upload_multiple_files(array $files, string $destination, array $allowedExtensions = ['jpg', 'jpeg', 'png'], int $maxSize = 5242880): array {
    $results = [];

    // Normalize $_FILES array
    $fileCount = count($files['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];

        $results[] = upload_file($file, $destination, $allowedExtensions, $maxSize);
    }

    return $results;
}

/**
 * Delete file
 */
function delete_file(string $filepath): bool {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get file size in human readable format
 */
function format_file_size(int $bytes): string {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Get file extension
 */
function get_file_extension(string $filename): string {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is image
 */
function is_image(string $filename): bool {
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
    return in_array(get_file_extension($filename), $imageExtensions);
}

/**
 * Generate thumbnail (requires GD library)
 */
function create_thumbnail(string $source, string $destination, int $maxWidth = 300, int $maxHeight = 300): bool {
    if (!file_exists($source)) {
        return false;
    }

    $imageInfo = getimagesize($source);
    if ($imageInfo === false) {
        return false;
    }

    list($origWidth, $origHeight, $type) = $imageInfo;

    // Calculate new dimensions
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = $origWidth * $ratio;
    $newHeight = $origHeight * $ratio;

    // Create image resource
    $srcImage = match($type) {
        IMAGETYPE_JPEG => imagecreatefromjpeg($source),
        IMAGETYPE_PNG => imagecreatefrompng($source),
        IMAGETYPE_GIF => imagecreatefromgif($source),
        default => false
    };

    if ($srcImage === false) {
        return false;
    }

    // Create thumbnail
    $dstImage = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
    }

    imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    // Save thumbnail
    $result = match($type) {
        IMAGETYPE_JPEG => imagejpeg($dstImage, $destination, 85),
        IMAGETYPE_PNG => imagepng($dstImage, $destination),
        IMAGETYPE_GIF => imagegif($dstImage, $destination),
        default => false
    };

    imagedestroy($srcImage);
    imagedestroy($dstImage);

    return $result;
}
