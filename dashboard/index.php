<?php
// dashboard/index.php - User Dashboard Home
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/media_handler.php';
require_once __DIR__ . '/../config/database.php';

requireAuth();
$currentUser = getCurrentUser();
$pdo = getDBConnection();

// Get counts
$photoCountStmt = $pdo->prepare("SELECT COUNT(*) FROM user_photos WHERE user_id = ?");
$photoCountStmt->execute([$currentUser['id']]);
$photoCount = (int)$photoCountStmt->fetchColumn();

$videoStmt = $pdo->prepare("SELECT * FROM user_videos WHERE user_id = ?");
$videoStmt->execute([$currentUser['id']]);
$userVideo = $videoStmt->fetch();

// Check preferences
$prefStmt = $pdo->prepare("SELECT * FROM partner_preferences WHERE user_id = ?");
$prefStmt->execute([$currentUser['id']]);
$userPref = $prefStmt->fetch();

// Fetch 4 Matching Profiles based on opposite gender and user partner preferences
$targetGender = ($currentUser['gender'] === 'Male') ? 'Female' : 'Male';
$matchQuery = "SELECT u.*,
    (SELECT thumb_path FROM user_photos up WHERE up.user_id = u.id AND up.is_main = 1 LIMIT 1) as main_thumb
    FROM users u
    WHERE u.gender = ? AND u.id != ?";
$matchParams = [$targetGender, $currentUser['id']];

if ($userPref) {
    if (!empty($userPref['pref_age_from']) && !empty($userPref['pref_age_to'])) {
        $minDob = date('Y-m-d', strtotime("-{$userPref['pref_age_to']} years"));
        $maxDob = date('Y-m-d', strtotime("-{$userPref['pref_age_from']} years"));
        $matchQuery .= " AND u.dob BETWEEN ? AND ?";
        $matchParams[] = $minDob;
        $matchParams[] = $maxDob;
    }
}
$matchQuery .= " ORDER BY u.is_premium DESC, u.created_at DESC LIMIT 4";
$matchStmt = $pdo->prepare($matchQuery);
$matchStmt->execute($matchParams);
$matchingProfiles = $matchStmt->fetchAll();

$pageTitle = "My Dashboard - Guruvision Matrimony";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-10 bg-slate-50 min-h-[85vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Welcome Alert / Banner -->
        <div class="bg-gradient-to-r from-red-950 via-maroon-900 to-red-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-8 flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-crown text-amber-400"></i> Guruvision Member Portal
                </div>
                <h1 class="font-serif-title text-2xl sm:text-3xl font-extrabold text-white">
                    Namaskaram, <?= sanitize($currentUser['full_name']) ?>!
                </h1>
                <p class="text-amber-100/90 text-sm mt-1 max-w-2xl">
                    Profile ID: <span class="font-mono font-bold text-amber-300 bg-black/20 px-2 py-0.5 rounded"><?= sanitize($currentUser['profile_id']) ?></span> &bull;
                    Status: <span class="inline-flex items-center text-emerald-300 font-semibold"><i class="fa-solid fa-circle-check text-xs mr-1"></i> Active Member</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="/dashboard/edit_profile.php" class="bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold px-4 py-2.5 rounded-xl shadow transition">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit Profile
                </a>
                <a href="/profile.php?id=<?= urlencode($currentUser['profile_id']) ?>" target="_blank" class="bg-white/10 hover:bg-white/20 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition border border-white/20">
                    <i class="fa-solid fa-eye mr-1"></i> Preview Profile
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <?php require_once __DIR__ . '/sidebar.php'; ?>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-8">

                <!-- Quick Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-100 text-primary-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-camera"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-slate-900"><?= $photoCount ?></div>
                            <div class="text-xs text-slate-500 font-medium">Uploaded Photos</div>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-video"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-slate-900"><?= $userVideo ? '1' : '0' ?></div>
                            <div class="text-xs text-slate-500 font-medium"><?= $userVideo ? 'Video Active' : 'No Video Uploaded' ?></div>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div>
                            <div class="text-2xl font-black text-slate-900"><?= $userPref ? 'Configured' : 'Default' ?></div>
                            <div class="text-xs text-slate-500 font-medium">Partner Preferences</div>
                        </div>
                    </div>
                </div>

                <!-- Profile Completion Checklist -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary-600"></i> Profile Strength &amp; Action Items
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl border <?= $photoCount > 0 ? 'bg-emerald-50/50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900' ?>">
                            <div class="font-bold text-sm flex items-center justify-between mb-1">
                                <span>Photos</span>
                                <i class="fa-solid <?= $photoCount > 0 ? 'fa-circle-check text-emerald-600' : 'fa-circle-plus text-amber-600' ?>"></i>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Profiles with photos get 10x more responses.</p>
                            <a href="/dashboard/photos.php" class="text-xs font-bold text-primary-600 hover:underline">Upload / Manage Photos &rarr;</a>
                        </div>

                        <div class="p-4 rounded-xl border <?= $userVideo ? 'bg-emerald-50/50 border-emerald-200 text-emerald-900' : 'bg-slate-50 border-slate-200 text-slate-800' ?>">
                            <div class="font-bold text-sm flex items-center justify-between mb-1">
                                <span>Introduction Video</span>
                                <i class="fa-solid <?= $userVideo ? 'fa-circle-check text-emerald-600' : 'fa-video text-slate-400' ?>"></i>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Add a 30s video intro to build instant trust.</p>
                            <a href="/dashboard/video.php" class="text-xs font-bold text-primary-600 hover:underline">Add Video &rarr;</a>
                        </div>

                        <div class="p-4 rounded-xl border <?= $userPref ? 'bg-emerald-50/50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900' ?>">
                            <div class="font-bold text-sm flex items-center justify-between mb-1">
                                <span>Partner Preference</span>
                                <i class="fa-solid <?= $userPref ? 'fa-circle-check text-emerald-600' : 'fa-sliders text-amber-600' ?>"></i>
                            </div>
                            <p class="text-xs text-slate-600 mb-2">Set criteria to get relevant recommendations.</p>
                            <a href="/dashboard/preferences.php" class="text-xs font-bold text-primary-600 hover:underline">Configure Preferences &rarr;</a>
                        </div>
                    </div>
                </div>

                <!-- Recommended Matches for you -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                <i class="fa-solid fa-heart text-rose-500"></i> Recommended Matches For You
                            </h2>
                            <p class="text-xs text-slate-500">Based on your partner preferences &amp; community criteria</p>
                        </div>
                        <a href="/advanced_search.php?gender=<?= urlencode($targetGender) ?>" class="text-xs font-bold text-primary-600 hover:underline">
                            View All Matches &rarr;
                        </a>
                    </div>

                    <?php if (empty($matchingProfiles)): ?>
                        <div class="text-center py-8 text-slate-400 text-sm">
                            <i class="fa-solid fa-users text-3xl mb-2 text-slate-300"></i>
                            <p>No matching profiles found currently. Try broadening your partner preferences.</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <?php foreach ($matchingProfiles as $p): ?>
                                <?php
                                $thumb = !empty($p['main_thumb']) ? '/' . $p['main_thumb'] : '/' . getDefaultAvatar($p['gender']);
                                $age = calculateAge($p['dob']);
                                ?>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition group">
                                    <div class="relative h-44 overflow-hidden bg-slate-200">
                                        <img src="<?= $thumb ?>" alt="<?= sanitize($p['full_name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        <div class="absolute top-2 right-2 bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-0.5 rounded font-mono">
                                            <?= sanitize($p['profile_id']) ?>
                                        </div>
                                    </div>
                                    <div class="p-3.5">
                                        <h3 class="font-bold text-slate-900 text-sm truncate"><?= sanitize($p['full_name']) ?></h3>
                                        <div class="text-xs text-slate-500 mt-1 space-y-0.5">
                                            <p><?= $age ?> yrs, <?= formatHeight($p['height']) ?></p>
                                            <p class="truncate"><i class="fa-solid fa-graduation-cap text-slate-400 mr-1"></i><?= sanitize($p['edu_detail']) ?></p>
                                            <p class="truncate"><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i><?= sanitize($p['native_district']) ?></p>
                                        </div>
                                        <a href="/profile.php?id=<?= urlencode($p['profile_id']) ?>" class="mt-3 block text-center w-full bg-white hover:bg-primary-50 text-primary-700 border border-primary-300 hover:border-primary-400 font-semibold text-xs py-1.5 rounded-lg transition">
                                            View Profile
                                        </a>
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
