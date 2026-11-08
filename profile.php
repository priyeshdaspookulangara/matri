<?php
// profile.php - Detailed Profile View
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/media_handler.php';
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$currentUser = getCurrentUser();

$profileId = trim($_GET['id'] ?? '');
if (empty($profileId)) {
    header('Location: /advanced_search.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE profile_id = ? OR id = ? LIMIT 1");
$stmt->execute([$profileId, is_numeric($profileId) ? (int)$profileId : 0]);
$profile = $stmt->fetch();

if (!$profile) {
    $pageTitle = "Profile Not Found - Guruvision Matrimony";
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="max-w-xl mx-auto my-20 p-8 text-center bg-white rounded-2xl shadow border border-slate-200">
            <h1 class="text-2xl font-bold text-slate-800">Profile Not Found</h1>
            <p class="text-slate-500 text-sm mt-2">The profile you requested does not exist or has been removed.</p>
            <a href="/advanced_search.php" class="inline-block mt-4 bg-primary-600 text-white px-5 py-2 rounded-xl text-sm font-semibold">Search Other Profiles</a>
          </div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$profilePhotos = getUserPhotos((int)$profile['id']);
$profileVideo = getUserVideo((int)$profile['id']);

// Fetch partner preferences of this profile
$prefStmt = $pdo->prepare("SELECT * FROM partner_preferences WHERE user_id = ?");
$prefStmt->execute([$profile['id']]);
$partnerPref = $prefStmt->fetch();

$prefDistricts = [];
if (!empty($partnerPref['pref_districts'])) {
    $decoded = json_decode($partnerPref['pref_districts'], true);
    $prefDistricts = is_array($decoded) ? $decoded : explode(',', $partnerPref['pref_districts']);
}

$age = calculateAge($profile['dob']);

$pageTitle = sanitize($profile['full_name']) . " (" . sanitize($profile['profile_id']) . ") - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Back Navigation & Action Bar -->
        <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <a href="javascript:history.back()" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-primary-600 transition">
                <i class="fa-solid fa-arrow-left"></i> Back to search results
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500 font-mono">Profile ID: <strong class="text-slate-900"><?= sanitize($profile['profile_id']) ?></strong></span>
                <?php if ($profile['is_premium']): ?>
                    <span class="bg-amber-500 text-slate-950 font-black text-[10px] px-2.5 py-1 rounded-full uppercase tracking-wider">
                        <i class="fa-solid fa-crown mr-1"></i> GOLD MEMBER
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Left Col: Photo & Video Media Box -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Main Photo Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-4">
                    <div class="relative rounded-xl overflow-hidden bg-slate-100 aspect-[4/5] mb-4">
                        <?php
                        $mainPhoto = !empty($profilePhotos) ? $profilePhotos[0]['file_path'] : getDefaultAvatar($profile['gender']);
                        ?>
                        <img id="mainDisplayPhoto" src="/<?= sanitize($mainPhoto) ?>" alt="<?= sanitize($profile['full_name']) ?>" class="w-full h-full object-cover">
                    </div>

                    <!-- Thumbnails Carousel / Gallery -->
                    <?php if (count($profilePhotos) > 1): ?>
                        <div class="grid grid-cols-4 gap-2">
                            <?php foreach ($profilePhotos as $pic): ?>
                                <button type="button" onclick="document.getElementById('mainDisplayPhoto').src = '/<?= sanitize($pic['file_path']) ?>'" class="aspect-square rounded-lg overflow-hidden border-2 border-transparent hover:border-primary-500 focus:border-primary-500 transition">
                                    <img src="/<?= sanitize($pic['thumb_path']) ?>" alt="Thumb" class="w-full h-full object-cover">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Intro Video Card (if present) -->
                <?php if ($profileVideo): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-3 flex items-center gap-2">
                            <i class="fa-solid fa-circle-play text-red-600"></i> Introduction Video
                        </h3>
                        <div class="rounded-xl overflow-hidden bg-black shadow">
                            <video controls class="w-full h-auto max-h-[300px]">
                                <source src="/<?= sanitize($profileVideo['file_path']) ?>" type="<?= sanitize($profileVideo['file_type']) ?>">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Family Contact Card -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-address-book text-primary-600"></i> Contact Information
                    </h3>

                    <?php if ($currentUser): ?>
                        <div class="space-y-3 text-sm">
                            <div>
                                <span class="text-xs text-slate-500 block">Contact Person:</span>
                                <span class="font-bold text-slate-800"><?= sanitize($profile['contact_name']) ?> (<?= sanitize($profile['contact_relation']) ?>)</span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-500 block">Primary Phone:</span>
                                <a href="tel:<?= sanitize($profile['contact_phone']) ?>" class="font-bold text-primary-600 hover:underline flex items-center gap-1.5 mt-0.5">
                                    <i class="fa-solid fa-phone text-xs"></i> <?= sanitize($profile['contact_phone']) ?>
                                </a>
                            </div>
                            <?php if (!empty($profile['alternate_phone'])): ?>
                                <div>
                                    <span class="text-xs text-slate-500 block">Alternate Phone:</span>
                                    <a href="tel:<?= sanitize($profile['alternate_phone']) ?>" class="text-slate-700 hover:underline flex items-center gap-1.5 mt-0.5">
                                        <i class="fa-solid fa-phone-volume text-xs text-slate-400"></i> <?= sanitize($profile['alternate_phone']) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($profile['com_address'])): ?>
                                <div>
                                    <span class="text-xs text-slate-500 block">House Address:</span>
                                    <span class="text-xs text-slate-700 leading-relaxed"><?= nl2br(sanitize($profile['com_address'])) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-amber-50 rounded-xl p-4 border border-amber-200 text-center">
                            <i class="fa-solid fa-lock text-amber-600 text-xl mb-2"></i>
                            <p class="text-xs text-amber-900 font-semibold mb-3">Contact details are visible to logged-in members only.</p>
                            <a href="/login.php" class="inline-block bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs px-4 py-2 rounded-lg shadow">
                                Login to View Contact
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right Col: Profile Information Sections -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Main Title Header -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
                        <div>
                            <h1 class="font-serif-title text-2xl sm:text-3xl font-extrabold text-slate-900">
                                <?= sanitize($profile['full_name']) ?>
                            </h1>
                            <p class="text-xs text-slate-500 mt-1">
                                Profile created by <strong class="text-slate-700"><?= sanitize($profile['created_by']) ?></strong> &bull; Member since <?= date('M Y', strtotime($profile['created_at'])) ?>
                            </p>
                        </div>
                        <div class="text-right sm:text-right">
                            <span class="inline-block px-3 py-1 bg-orange-100 text-primary-800 rounded-full text-xs font-bold uppercase tracking-wider">
                                <?= sanitize($profile['caste']) ?> Community
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($profile['about_me'])): ?>
                        <div class="mb-6">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">About Me</h3>
                            <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                                <?= nl2br(sanitize($profile['about_me'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Key highlights bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center bg-amber-50/50 border border-amber-100 rounded-xl p-4">
                        <div>
                            <div class="text-xs text-amber-800/80 font-bold uppercase">Age</div>
                            <div class="text-base font-extrabold text-slate-900"><?= $age ?> Yrs</div>
                        </div>
                        <div>
                            <div class="text-xs text-amber-800/80 font-bold uppercase">Height</div>
                            <div class="text-base font-extrabold text-slate-900"><?= formatHeight($profile['height']) ?></div>
                        </div>
                        <div>
                            <div class="text-xs text-amber-800/80 font-bold uppercase">Marital Status</div>
                            <div class="text-base font-extrabold text-slate-900"><?= sanitize($profile['marital_status']) ?></div>
                        </div>
                        <div>
                            <div class="text-xs text-amber-800/80 font-bold uppercase">Native District</div>
                            <div class="text-base font-extrabold text-slate-900"><?= sanitize($profile['native_district']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Section: Religious & Physical -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-6 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-om text-primary-600"></i> Religious &amp; Physical Information
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Religion:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['religion']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Caste:</span>
                            <span class="font-bold text-primary-700"><?= sanitize($profile['caste']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Physical Status:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['physical_status']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Complexion:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['complexion']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Body Type:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['body_type'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Weight:</span>
                            <span class="font-bold text-slate-900"><?= $profile['weight'] ? sanitize((string)$profile['weight']) . ' kg' : 'Not Specified' ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Blood Group:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['blood_group'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Diet:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['diet']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Section: Education & Profession -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-6 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-graduation-cap text-primary-600"></i> Education &amp; Career
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Education Level:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['edu_category'] ?: 'Degree') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Education Detail:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['edu_detail']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Employed In:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['employed_in'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Occupation:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['occupation_category'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Occupation Detail:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['occu_detail'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Working Firm:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['working_firm'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Work Place:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['work_place'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Monthly Income:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['monthly_income'] ?: 'Not Specified') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Section: Location Details -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-6 flex items-center gap-2 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-location-dot text-primary-600"></i> Location &amp; Origin
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Native District:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['native_district']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Native Place:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['native_place'] ?: 'Not Specified') ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Current Living Place:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['living_place']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Living Country:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['living_country']) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-500 text-xs uppercase font-medium">Citizenship:</span>
                            <span class="font-bold text-slate-900"><?= sanitize($profile['citizenship']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Section: Partner Preferences -->
                <?php if ($partnerPref): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
                        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 mb-6 flex items-center gap-2 border-b border-slate-100 pb-3">
                            <i class="fa-solid fa-heart text-rose-500"></i> Desired Partner Preferences
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 text-xs uppercase font-medium">Preferred Age:</span>
                                <span class="font-bold text-slate-900"><?= $partnerPref['pref_age_from'] ?> to <?= $partnerPref['pref_age_to'] ?> Years</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 text-xs uppercase font-medium">Preferred Height:</span>
                                <span class="font-bold text-slate-900"><?= formatHeight($partnerPref['pref_height_from']) ?> to <?= formatHeight($partnerPref['pref_height_to']) ?></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 text-xs uppercase font-medium">Marital Status:</span>
                                <span class="font-bold text-slate-900"><?= sanitize($partnerPref['pref_marital_status'] ?: 'Any') ?></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 text-xs uppercase font-medium">Preferred Complexion:</span>
                                <span class="font-bold text-slate-900"><?= sanitize($partnerPref['pref_complexion'] ?: 'Any') ?></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 text-xs uppercase font-medium">Preferred Education:</span>
                                <span class="font-bold text-slate-900"><?= sanitize($partnerPref['pref_edu_category'] ?: 'Any') ?></span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-50">
                                <span class="text-slate-500 text-xs uppercase font-medium">Preferred Occupation:</span>
                                <span class="font-bold text-slate-900"><?= sanitize($partnerPref['pref_occupation'] ?: 'Any') ?></span>
                            </div>
                            <?php if (!empty($prefDistricts)): ?>
                                <div class="sm:col-span-2 pt-2">
                                    <span class="text-slate-500 text-xs uppercase font-medium block mb-2">Preferred Kerala Districts:</span>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach ($prefDistricts as $dist): ?>
                                            <span class="px-2.5 py-1 bg-slate-100 rounded-md text-xs font-semibold text-slate-700">
                                                <?= sanitize($dist) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
