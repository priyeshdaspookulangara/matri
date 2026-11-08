<?php
// dashboard/sidebar.php - Dashboard Navigation Partial
declare(strict_types=1);

$currentScript = basename($_SERVER['PHP_SELF']);
?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 text-center bg-gradient-to-b from-orange-50/60 to-white">
        <div class="relative inline-block mx-auto mb-3">
            <?php
            $mainPhoto = getUserMainPhoto((int)$currentUser['id']);
            $avatarUrl = $mainPhoto ? '/' . $mainPhoto['thumb_path'] : '/' . getDefaultAvatar($currentUser['gender']);
            ?>
            <img src="<?= $avatarUrl ?>" alt="<?= sanitize($currentUser['full_name']) ?>" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md mx-auto">
            <?php if ($currentUser['is_premium']): ?>
                <span class="absolute bottom-0 right-0 bg-amber-500 text-slate-950 text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider shadow">
                    GOLD
                </span>
            <?php endif; ?>
        </div>
        <h3 class="font-bold text-slate-900 text-base"><?= sanitize($currentUser['full_name']) ?></h3>
        <p class="text-xs font-mono text-primary-600 font-bold"><?= sanitize($currentUser['profile_id']) ?></p>
        <p class="text-xs text-slate-500 mt-1"><?= sanitize($currentUser['caste']) ?> &bull; <?= calculateAge($currentUser['dob']) ?> yrs</p>
    </div>

    <nav class="p-3 space-y-1 text-sm font-medium">
        <a href="/dashboard/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($currentScript === 'index.php') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
            <i class="fa-solid fa-gauge text-slate-400 w-5"></i> Dashboard Overview
        </a>
        <a href="/dashboard/edit_profile.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($currentScript === 'edit_profile.php') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
            <i class="fa-solid fa-user-pen text-slate-400 w-5"></i> Edit Profile
        </a>
        <a href="/dashboard/photos.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($currentScript === 'photos.php') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
            <i class="fa-solid fa-camera text-slate-400 w-5"></i> Manage Photos
        </a>
        <a href="/dashboard/video.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($currentScript === 'video.php') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
            <i class="fa-solid fa-video text-slate-400 w-5"></i> Intro Video
        </a>
        <a href="/dashboard/preferences.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($currentScript === 'preferences.php') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' ?>">
            <i class="fa-solid fa-sliders text-slate-400 w-5"></i> Partner Preferences
        </a>
        <a href="/profile.php?id=<?= urlencode($currentUser['profile_id']) ?>" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition">
            <i class="fa-solid fa-arrow-up-right-from-square text-slate-400 w-5"></i> View Public Profile
        </a>
        <a href="/logout.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-rose-600 hover:bg-rose-50 transition">
            <i class="fa-solid fa-power-off text-rose-400 w-5"></i> Logout
        </a>
    </nav>
</div>
