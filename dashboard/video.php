<?php
// dashboard/video.php - Introduction Video Management
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid request security token.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'upload') {
            if (isset($_FILES['video'])) {
                $uploadResult = handleVideoUpload($_FILES['video'], (int)$currentUser['id']);
                if (!$uploadResult['success']) {
                    $errors[] = $uploadResult['error'];
                } else {
                    $successMessage = 'Introduction video uploaded successfully!';
                }
            } else {
                $errors[] = 'Please select a video file to upload.';
            }
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("SELECT file_path FROM user_videos WHERE user_id = ?");
            $stmt->execute([$currentUser['id']]);
            $video = $stmt->fetch();
            if ($video) {
                if (file_exists(__DIR__ . '/../' . $video['file_path'])) {
                    @unlink(__DIR__ . '/../' . $video['file_path']);
                }
                $pdo->prepare("DELETE FROM user_videos WHERE user_id = ?")->execute([$currentUser['id']]);
                $successMessage = 'Introduction video deleted.';
            }
        }
    }
}

$userVideo = getUserVideo((int)$currentUser['id']);

$pageTitle = "Introduction Video - Guruvision Matrimony";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-10 bg-slate-50 min-h-[85vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <?php require_once __DIR__ . '/sidebar.php'; ?>
            </div>

            <div class="lg:col-span-3 space-y-6">
                <!-- Header -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h1 class="font-serif-title text-2xl font-bold text-slate-900">Short Introduction Video</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Showcase your personality, communication, and authenticity. Upload a brief 30–60 second video clip (MP4 / WebM format, max 25 MB).
                    </p>
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

                <?php if ($userVideo): ?>
                    <!-- Existing Video Player -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-circle-play text-primary-600"></i> Active Introduction Video
                            </h2>
                            <form action="/dashboard/video.php" method="POST" onsubmit="return confirm('Delete this video?')">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold flex items-center gap-1 border border-rose-200 px-3 py-1 rounded-lg hover:bg-rose-50 transition">
                                    <i class="fa-solid fa-trash-can"></i> Delete Video
                                </button>
                            </form>
                        </div>

                        <div class="max-w-xl mx-auto rounded-xl overflow-hidden bg-black shadow-lg">
                            <video controls class="w-full h-auto max-h-[420px] mx-auto">
                                <source src="/<?= sanitize($userVideo['file_path']) ?>" type="<?= sanitize($userVideo['file_type']) ?>">
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        <p class="text-center text-xs text-slate-500 mt-4">
                            Uploaded on <?= date('d M Y, h:i A', strtotime($userVideo['created_at'])) ?>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Upload / Replace Video -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-file-video text-primary-600"></i> <?= $userVideo ? 'Replace Video' : 'Upload Video' ?>
                    </h2>

                    <form action="/dashboard/video.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="action" value="upload">

                        <div class="border-2 border-dashed border-slate-300 hover:border-primary-500 rounded-2xl p-8 text-center bg-slate-50/50 hover:bg-orange-50/20 transition cursor-pointer" onclick="document.getElementById('videoInput').click()">
                            <i class="fa-solid fa-video text-4xl text-slate-400 mb-3 block"></i>
                            <span class="text-sm font-bold text-slate-700">Click to select video file</span>
                            <p class="text-xs text-slate-500 mt-1">Accepts MP4, WebM (Max 25 MB). Generated file names are secured with UUIDs.</p>
                            <input type="file" id="videoInput" name="video" accept="video/mp4,video/webm" class="hidden" onchange="this.form.submit()">
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
