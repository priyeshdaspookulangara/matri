<?php
// includes/media_handler.php - Secure Media File Handler

declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

const UPLOAD_BASE_DIR = __DIR__ . '/../uploads';
const PHOTO_UPLOAD_DIR = UPLOAD_BASE_DIR . '/photos';
const THUMB_UPLOAD_DIR = UPLOAD_BASE_DIR . '/photos/thumbs';
const VIDEO_UPLOAD_DIR = UPLOAD_BASE_DIR . '/videos';

const MAX_PHOTO_SIZE = 5 * 1024 * 1024; // 5 MB
const MAX_VIDEO_SIZE = 25 * 1024 * 1024; // 25 MB

const ALLOWED_PHOTO_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];

const ALLOWED_VIDEO_TYPES = [
    'video/mp4'        => 'mp4',
    'video/webm'       => 'webm',
    'video/quicktime'  => 'mov',
];

function handlePhotoUpload(array $file, int $userId): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid file parameter.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Upload error code: ' . $file['error']];
    }

    if ($file['size'] > MAX_PHOTO_SIZE) {
        return ['success' => false, 'error' => 'File exceeds maximum size of 5 MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!array_key_exists($mimeType, ALLOWED_PHOTO_TYPES)) {
        return ['success' => false, 'error' => 'Invalid image format. Allowed: JPG, PNG, WEBP.'];
    }

    // Verify image data with getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return ['success' => false, 'error' => 'File is not a valid image.'];
    }

    $ext = ALLOWED_PHOTO_TYPES[$mimeType];
    $uuid = bin2hex(random_bytes(16));
    $filename = sprintf('user_%d_%s.%s', $userId, $uuid, $ext);
    $thumbFilename = sprintf('thumb_user_%d_%s.%s', $userId, $uuid, $ext);

    $destination = PHOTO_UPLOAD_DIR . '/' . $filename;
    $thumbDestination = THUMB_UPLOAD_DIR . '/' . $thumbFilename;

    if (!is_dir(PHOTO_UPLOAD_DIR)) {
        mkdir(PHOTO_UPLOAD_DIR, 0755, true);
    }
    if (!is_dir(THUMB_UPLOAD_DIR)) {
        mkdir(THUMB_UPLOAD_DIR, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Failed to save uploaded file.'];
    }

    // Generate thumbnail (max width/height 300px)
    createThumbnail($destination, $thumbDestination, 300, 300, $mimeType);

    $relPhotoPath = 'uploads/photos/' . $filename;
    $relThumbPath = 'uploads/photos/thumbs/' . $thumbFilename;

    // Check if this is the first photo for this user
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM user_photos WHERE user_id = ?");
    $stmt->execute([$userId]);
    $isFirst = ($stmt->fetchColumn() == 0) ? 1 : 0;

    $stmt = $pdo->prepare("INSERT INTO user_photos (user_id, file_path, thumb_path, is_main) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $relPhotoPath, $relThumbPath, $isFirst]);
    $photoId = (int)$pdo->lastInsertId();

    return [
        'success' => true,
        'photo_id' => $photoId,
        'file_path' => $relPhotoPath,
        'thumb_path' => $relThumbPath,
        'is_main' => $isFirst
    ];
}

function createThumbnail(string $sourcePath, string $destPath, int $maxWidth, int $maxHeight, string $mimeType): bool {
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = @imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = @imagecreatefrompng($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = @imagecreatefromwebp($sourcePath);
            break;
        default:
            $sourceImage = false;
    }

    if (!$sourceImage) {
        // Fallback: copy file
        copy($sourcePath, $destPath);
        return false;
    }

    $origWidth = imagesx($sourceImage);
    $origHeight = imagesy($sourceImage);

    // Calculate aspect ratio
    $ratio = min($maxWidth / $origWidth, $maxHeight / $origHeight);
    $newWidth = (int)round($origWidth * $ratio);
    $newHeight = (int)round($origHeight * $ratio);

    $thumb = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG and WEBP
    if ($mimeType === 'image/png' || $mimeType === 'image/webp') {
        imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }

    imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

    switch ($mimeType) {
        case 'image/jpeg':
            imagejpeg($thumb, $destPath, 85);
            break;
        case 'image/png':
            imagepng($thumb, $destPath, 6);
            break;
        case 'image/webp':
            imagewebp($thumb, $destPath, 85);
            break;
        default:
            imagejpeg($thumb, $destPath, 85);
    }

    imagedestroy($thumb);
    imagedestroy($sourceImage);
    return true;
}

function handleVideoUpload(array $file, int $userId): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Invalid video file.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Video upload error code: ' . $file['error']];
    }

    if ($file['size'] > MAX_VIDEO_SIZE) {
        return ['success' => false, 'error' => 'Video exceeds maximum size of 25 MB.'];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!array_key_exists($mimeType, ALLOWED_VIDEO_TYPES)) {
        return ['success' => false, 'error' => 'Invalid video format. Allowed: MP4, WebM.'];
    }

    $ext = ALLOWED_VIDEO_TYPES[$mimeType];
    $uuid = bin2hex(random_bytes(16));
    $filename = sprintf('video_%d_%s.%s', $userId, $uuid, $ext);

    if (!is_dir(VIDEO_UPLOAD_DIR)) {
        mkdir(VIDEO_UPLOAD_DIR, 0755, true);
    }

    $destination = VIDEO_UPLOAD_DIR . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => false, 'error' => 'Failed to save uploaded video.'];
    }

    $relVideoPath = 'uploads/videos/' . $filename;
    $pdo = getDBConnection();

    // Check if user already has a video and delete old file
    $stmt = $pdo->prepare("SELECT file_path FROM user_videos WHERE user_id = ?");
    $stmt->execute([$userId]);
    $oldVideo = $stmt->fetch();
    if ($oldVideo && file_exists(__DIR__ . '/../' . $oldVideo['file_path'])) {
        @unlink(__DIR__ . '/../' . $oldVideo['file_path']);
    }

    // Insert or update video record
    $stmt = $pdo->prepare("
        INSERT INTO user_videos (user_id, file_path, file_type)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE file_path = VALUES(file_path), file_type = VALUES(file_type), created_at = CURRENT_TIMESTAMP
    ");
    $stmt->execute([$userId, $relVideoPath, $mimeType]);

    return [
        'success' => true,
        'file_path' => $relVideoPath,
        'mime_type' => $mimeType
    ];
}

function getUserMainPhoto(int $userId): ?array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM user_photos WHERE user_id = ? AND is_main = 1 LIMIT 1");
    $stmt->execute([$userId]);
    $main = $stmt->fetch();
    if ($main) {
        return $main;
    }
    // Fallback to any photo
    $stmt = $pdo->prepare("SELECT * FROM user_photos WHERE user_id = ? ORDER BY id ASC LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: null;
}

function getUserPhotos(int $userId): array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM user_photos WHERE user_id = ? ORDER BY is_main DESC, id ASC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getUserVideo(int $userId): ?array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM user_videos WHERE user_id = ? LIMIT 1");
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: null;
}

function getDefaultAvatar(string $gender): string {
    return ($gender === 'Female') ? 'assets/images/default_female.svg' : 'assets/images/default_male.svg';
}
