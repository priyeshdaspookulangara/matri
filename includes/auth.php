<?php
// includes/auth.php - Authentication & Session Management

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/helpers.php';

function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function getCurrentUserId(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function getCurrentUser(): ?array {
    $userId = getCurrentUserId();
    if (!$userId) {
        return null;
    }

    static $cachedUser = null;
    if ($cachedUser !== null && (int)$cachedUser['id'] === $userId) {
        return $cachedUser;
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $cachedUser = $stmt->fetch() ?: null;
    return $cachedUser;
}

function requireAuth(): void {
    if (!isLoggedIn()) {
        $_SESSION['flash_message'] = 'Please log in to access this page.';
        $_SESSION['flash_type'] = 'warning';
        header('Location: /login.php');
        exit;
    }
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type; // success, error, warning, info
}

function getFlash(): ?array {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'] ?? 'info',
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return $flash;
    }
    return null;
}
