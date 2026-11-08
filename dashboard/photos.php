<?php
// dashboard/photos.php - Photo Gallery Management
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/media_handler.php';
require_once __DIR__ . '/../config/database.php';

requireAuth();
$currentUser = getCurrentUser();
$pdo = getDBConnection();

$errors = [];
$successMessage = '';

// Handle actions: upload, set_main, delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid request security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'upload') {
            if (isset($_FILES['photos'])) {
                $files = $_FILES['photos'];
                $totalFiles = is_array($files['name']) ? count($files['name']) : 1;

                // Support multiple or single
                for ($i = 0; $i < $totalFiles; $i++) {
                    $fileItem = [
                        'name'     => is_array($files['name']) ? $files['name'][$i] : $files['name'],
                        'type'     => is_array($files['type']) ? $files['type'][$i] : $files['type'],
                        'tmp_name' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'],
                        'error'    => is_array($files['error']) ? $files['error'][$i] : $files['error'],
                        'size'     => is_array($files['size']) ? $files['size'][$i] : $files['size'],
                    ];

                    if ($fileItem['error'] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }

                    $uploadResult = handlePhotoUpload($fileItem, (int)$currentUser['id']);
                    if (!$uploadResult['success']) {
                        $errors[] = "Error uploading '{$fileItem['name']}': " . $uploadResult['error'];
                    } else {
                        $successMessage = 'Photo(s) uploaded successfully!';
                    }
                }
            }
        } elseif ($action === 'set_main') {
            $photoId = (int)($_POST['photo_id'] ?? 0);
            if ($photoId > 0) {
                // Verify ownership
                $stmt = $pdo->prepare("SELECT id FROM user_photos WHERE id = ? AND user_id = ?");
                $stmt->execute([$photoId, $currentUser['id']]);
                if ($stmt->fetch()) {
                    $pdo->prepare("UPDATE user_photos SET is_main = 0 WHERE user_id = ?")->execute([$currentUser['id']]);
                    $pdo->prepare("UPDATE user_photos SET is_main = 1 WHERE id = ?")->execute([$photoId]);
                    $successMessage = 'Main profile photo updated!';
                }
            }
        } elseif ($action === 'delete') {
            $photoId = (int)($_POST['photo_id'] ?? 0);
            if ($photoId > 0) {
                $stmt = $pdo->prepare("SELECT file_path, thumb_path, is_main FROM user_photos WHERE id = ? AND user_id = ?");
                $stmt->execute([$photoId, $currentUser['id']]);
                $photo = $stmt->fetch();

                if ($photo) {
                    if (file_exists(__DIR__ . '/../' . $photo['file_path'])) {
                        @unlink(__DIR__ . '/../' . $photo['file_path']);
                    }
                    if (file_exists(__DIR__ . '/../' . $photo['thumb_path'])) {
                        @unlink(__DIR__ . '/../' . $photo['thumb_path']);
                    }

                    $pdo->prepare("DELETE FROM user_photos WHERE id = ?")->execute([$photoId]);

                    // If was main, set another one as main if exists
                    if ($photo['is_main']) {
                        $stmt = $pdo->prepare("SELECT id FROM user_photos WHERE user_id = ? ORDER BY id ASC LIMIT 1");
                        $stmt->execute([$currentUser['id']]);
                        $next = $stmt->fetch();
                        if ($next) {
                            $pdo->prepare("UPDATE user_photos SET is_main = 1 WHERE id = ?")->execute([$next['id']]);
                        }
                    }
                    $successMessage = 'Photo deleted successfully.';
                }
            }
        }
    }
}

$userPhotos = getUserPhotos((int)$currentUser['id']);

$pageTitle = "Manage Photos - Guruvision Matrimony";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-10 bg-slate-50 min-h-[85vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <?php require_once __DIR__ . '/sidebar.php'; ?>
            </div>

            <div class="lg:col-span-3 space-y-6">
                <!-- Page Title -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="font-serif-title text-2xl font-bold text-slate-900">Manage Profile Photos</h1>
                        <p class="text-xs text-slate-500 mt-1">Upload JPEG, PNG or WebM images (up to 5 MB each). Thumbnails will be generated automatically.</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary-50 text-primary-700 border border-primary-200">
                        <?= count($userPhotos) ?> / 10 Photos
                    </span>
                </div>

                <?php if (!empty($errors)): ?>
                    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($errors as $err): ?>
                                <li><?= sanitize($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($successMessage)): ?>
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> <?= sanitize($successMessage) ?>
                    </div>
                <?php endif; ?>

                <!-- Upload Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up text-primary-600"></i> Upload New Photos
                    </h2>

                    <form action="/dashboard/photos.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="upload">

                        <div class="border-2 border-dashed border-slate-300 hover:border-primary-500 rounded-2xl p-8 text-center bg-slate-50/50 hover:bg-orange-50/20 transition cursor-pointer" onclick="document.getElementById('photoInput').click()">
                            <i class="fa-solid fa-images text-4xl text-slate-400 mb-3 block"></i>
                            <span class="text-sm font-bold text-slate-700">Click to select photos or drag &amp; drop</span>
                            <p class="text-xs text-slate-500 mt-1">Supports multi-photo selection (JPG, PNG, WEBP, max 5MB per file)</p>
                            <input type="file" id="photoInput" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden" onchange="this.form.submit()">
                        </div>
                    </form>
                </div>

                <!-- Existing Photos Gallery -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-photo-film text-primary-600"></i> My Uploaded Photos
                    </h2>

                    <?php if (empty($userPhotos)): ?>
                        <div class="text-center py-12 text-slate-400">
                            <i class="fa-solid fa-image text-5xl mb-3 text-slate-300"></i>
                            <p class="text-sm">You haven't uploaded any photos yet.</p>
                            <p class="text-xs text-slate-500 mt-1">Upload high-quality, clear photographs to get noticed faster.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            <?php foreach ($userPhotos as $p): ?>
                                <div class="relative group rounded-xl border border-slate-200 overflow-hidden bg-slate-100 shadow-sm">
                                    <div class="aspect-square w-full overflow-hidden">
                                        <img src="/<?= sanitize($p['thumb_path']) ?>" alt="User Photo" class="w-full h-full object-cover">
                                    </div>

                                    <?php if ($p['is_main']): ?>
                                        <div class="absolute top-2 left-2 bg-emerald-600 text-white text-[10px] font-bold px-2 py-0.5 rounded shadow">
                                            MAIN PHOTO
                                        </div>
                                    <?php endif; ?>

                                    <!-- Action overlays -->
                                    <div class="p-2 bg-white flex items-center justify-between gap-1 text-xs border-t border-slate-100">
                                        <?php if (!$p['is_main']): ?>
                                            <form action="/dashboard/photos.php" method="POST" class="inline">
                                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                                <input type="hidden" name="action" value="set_main">
                                                <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                                                <button type="submit" class="text-primary-600 hover:text-primary-800 font-semibold text-[11px] hover:underline">
                                                    Set as Main
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-[11px] italic">Default</span>
                                        <?php endif; ?>

                                        <form action="/dashboard/photos.php" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this photo?')">
                                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="photo_id" value="<?= $p['id'] ?>">
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 p-1" title="Delete Photo">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
