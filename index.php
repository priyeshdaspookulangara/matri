<?php
// index.php - Guruvision Matrimony Homepage
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/media_handler.php';
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$currentUser = getCurrentUser();

// Fetch Premium Gold Members for Grid
$premiumStmt = $pdo->query("SELECT u.*,
    (SELECT thumb_path FROM user_photos up WHERE up.user_id = u.id AND up.is_main = 1 LIMIT 1) as main_thumb,
    (SELECT COUNT(*) FROM user_photos up WHERE up.user_id = u.id) as photo_count,
    (SELECT COUNT(*) FROM user_videos uv WHERE uv.user_id = u.id) as has_video
    FROM users u
    WHERE u.is_premium = 1
    ORDER BY u.created_at DESC
    LIMIT 6");
$premiumProfiles = $premiumStmt->fetchAll();

// Dynamic matching profiles section for logged in user
$matchingProfiles = [];
if ($currentUser) {
    $targetGender = ($currentUser['gender'] === 'Male') ? 'Female' : 'Male';
    $prefStmt = $pdo->prepare("SELECT * FROM partner_preferences WHERE user_id = ?");
    $prefStmt->execute([$currentUser['id']]);
    $userPref = $prefStmt->fetch();

    $matchSql = "SELECT u.*,
        (SELECT thumb_path FROM user_photos up WHERE up.user_id = u.id AND up.is_main = 1 LIMIT 1) as main_thumb,
        (SELECT COUNT(*) FROM user_photos up WHERE up.user_id = u.id) as photo_count,
        (SELECT COUNT(*) FROM user_videos uv WHERE uv.user_id = u.id) as has_video
        FROM users u
        WHERE u.gender = ? AND u.id != ?";
    $matchParams = [$targetGender, $currentUser['id']];

    if ($userPref && !empty($userPref['pref_age_from']) && !empty($userPref['pref_age_to'])) {
        $minDob = date('Y-m-d', strtotime("-{$userPref['pref_age_to']} years"));
        $maxDob = date('Y-m-d', strtotime("-{$userPref['pref_age_from']} years"));
        $matchSql .= " AND u.dob BETWEEN ? AND ?";
        $matchParams[] = $minDob;
        $matchParams[] = $maxDob;
    }

    $matchSql .= " ORDER BY u.is_premium DESC, u.created_at DESC LIMIT 4";
    $mStmt = $pdo->prepare($matchSql);
    $mStmt->execute($matchParams);
    $matchingProfiles = $mStmt->fetchAll();
}

// Fetch success stories
$storiesStmt = $pdo->query("SELECT * FROM success_stories ORDER BY id DESC LIMIT 3");
$successStories = $storiesStmt->fetchAll();

$pageTitle = "Guruvision Matrimony - Kerala Ezhava & Thiyya Matrimonial Portal";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero & Promotional Section -->
<section class="relative bg-gradient-to-br from-red-950 via-maroon-900 to-amber-950 text-white overflow-hidden py-16 lg:py-24">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

            <!-- Hero Left: Headlines & CTA -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 bg-yellow-500/20 border border-yellow-400/30 px-3.5 py-1.5 rounded-full text-amber-300 text-xs font-bold tracking-wider uppercase">
                    <i class="fa-solid fa-crown text-amber-400"></i> No.1 Ezhava &amp; Thiyya Matrimony Portal
                </div>

                <h1 class="font-serif-title text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    Find Your <span class="text-amber-400">Soulmate</span> in Kerala's Trusted Community
                </h1>

                <p class="text-base sm:text-lg text-amber-100/90 max-w-xl mx-auto lg:mx-0 font-light leading-relaxed">
                    Thousands of verified Ezhava and Thiyya brides and grooms. Connect with genuine families across Kerala, India, and the Gulf.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <?php if (!$currentUser): ?>
                        <a href="/register.php" class="w-full sm:w-auto bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-slate-950 font-black text-sm px-8 py-4 rounded-xl shadow-xl hover:shadow-2xl transition transform hover:-translate-y-0.5 text-center flex items-center justify-center gap-2">
                            <i class="fa-solid fa-user-plus text-base"></i> Register FREE Now
                        </a>
                        <a href="/login.php" class="w-full sm:w-auto bg-white/10 hover:bg-white/20 text-white font-bold text-sm px-7 py-4 rounded-xl transition border border-white/20 text-center flex items-center justify-center gap-2">
                            <i class="fa-solid fa-lock text-sm"></i> Member Login
                        </a>
                    <?php else: ?>
                        <a href="/dashboard/index.php" class="w-full sm:w-auto bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-slate-950 font-black text-sm px-8 py-4 rounded-xl shadow-xl transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-gauge"></i> Go to My Dashboard
                        </a>
                        <a href="/advanced_search.php" class="w-full sm:w-auto bg-white/10 hover:bg-white/20 text-white font-bold text-sm px-7 py-4 rounded-xl transition border border-white/20 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Search Profiles
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Trust Badges -->
                <div class="pt-6 grid grid-cols-3 gap-4 border-t border-amber-900/60 max-w-lg mx-auto lg:mx-0 text-center">
                    <div>
                        <div class="text-2xl font-black text-amber-400">100%</div>
                        <div class="text-[11px] text-amber-200 uppercase font-medium">Verified Profiles</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-amber-400">14+</div>
                        <div class="text-[11px] text-amber-200 uppercase font-medium">Kerala Districts</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-amber-400">25k+</div>
                        <div class="text-[11px] text-amber-200 uppercase font-medium">Happy Matches</div>
                    </div>
                </div>
            </div>

            <!-- Hero Right: Quick Search Bar Widget -->
            <div class="lg:col-span-5">
                <div class="bg-white/95 backdrop-blur-md rounded-2xl p-6 sm:p-8 shadow-2xl border border-white/30 text-slate-800">
                    <div class="flex items-center gap-2 mb-6 border-b border-slate-100 pb-3">
                        <i class="fa-solid fa-bolt text-primary-600 text-lg"></i>
                        <h2 class="font-bold text-slate-900 text-lg uppercase tracking-wider">Quick Search</h2>
                    </div>

                    <form action="/advanced_search.php" method="GET" class="space-y-4">
                        <!-- Looking for Gender -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Looking For</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="border border-slate-300 rounded-lg p-2.5 flex items-center justify-center text-xs font-bold text-slate-700 hover:bg-orange-50 cursor-pointer has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700">
                                    <input type="radio" name="gender" value="Female" checked class="hidden">
                                    Bride (Female)
                                </label>
                                <label class="border border-slate-300 rounded-lg p-2.5 flex items-center justify-center text-xs font-bold text-slate-700 hover:bg-orange-50 cursor-pointer has-[:checked]:border-primary-600 has-[:checked]:bg-primary-50 has-[:checked]:text-primary-700">
                                    <input type="radio" name="gender" value="Male" class="hidden">
                                    Groom (Male)
                                </label>
                            </div>
                        </div>

                        <!-- Age Range -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Age Range (18 to 70)</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select name="age_from" class="rounded-lg border-slate-300 border px-3 py-2 text-xs">
                                    <?php for ($a = 18; $a <= 70; $a++): ?>
                                        <option value="<?= $a ?>" <?= ($a === 21) ? 'selected' : '' ?>><?= $a ?> Yrs</option>
                                    <?php endfor; ?>
                                </select>
                                <select name="age_to" class="rounded-lg border-slate-300 border px-3 py-2 text-xs">
                                    <?php for ($a = 18; $a <= 70; $a++): ?>
                                        <option value="<?= $a ?>" <?= ($a === 35) ? 'selected' : '' ?>><?= $a ?> Yrs</option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Caste -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Caste</label>
                            <select name="caste" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-xs">
                                <option value="">All (Ezhava / Thiyya)</option>
                                <option value="Ezhava">Ezhava</option>
                                <option value="Thiyya">Thiyya</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-amber-600 hover:from-primary-700 hover:to-amber-700 text-white font-bold py-3 rounded-xl text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                            <i class="fa-solid fa-search"></i> Search Matching Profiles
                        </button>
                    </form>

                    <!-- Direct Profile Id Lookup -->
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <form action="/advanced_search.php" method="GET" class="flex gap-2">
                            <input type="text" name="profile_id" placeholder="Search by Profile ID (e.g. GV1001)" class="w-full rounded-lg border-slate-300 border px-3 py-1.5 text-xs focus:ring-1 focus:ring-primary-500 focus:outline-none">
                            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg whitespace-nowrap">
                                Go
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Matching Profiles Section (for logged-in members) -->
<?php if ($currentUser && !empty($matchingProfiles)): ?>
    <section class="py-14 bg-amber-50/50 border-b border-amber-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                <div>
                    <span class="text-xs font-bold text-primary-700 uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fa-solid fa-sparkles text-amber-500"></i> Personalized for you
                    </span>
                    <h2 class="font-serif-title text-2xl sm:text-3xl font-extrabold text-red-950 mt-1">
                        Matching Profiles Based on Your Preferences
                    </h2>
                </div>
                <a href="/dashboard/preferences.php" class="text-xs font-bold text-primary-700 hover:underline">
                    Edit Preferences &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($matchingProfiles as $p): ?>
                    <?php
                    $thumb = !empty($p['main_thumb']) ? '/' . $p['main_thumb'] : '/' . getDefaultAvatar($p['gender']);
                    $age = calculateAge($p['dob']);
                    ?>
                    <div class="bg-white rounded-2xl border border-amber-200/60 shadow-sm hover:shadow-md transition overflow-hidden group">
                        <div class="relative h-52 bg-slate-100 overflow-hidden">
                            <img src="<?= $thumb ?>" alt="<?= sanitize($p['full_name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute top-2 left-2 bg-black/60 text-white font-mono text-[10px] px-2 py-0.5 rounded">
                                <?= sanitize($p['profile_id']) ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 text-sm truncate"><?= sanitize($p['full_name']) ?></h3>
                            <p class="text-xs text-amber-800 font-medium"><?= sanitize($p['caste']) ?> &bull; <?= $age ?> Yrs &bull; <?= formatHeight($p['height']) ?></p>
                            <p class="text-xs text-slate-500 mt-1 truncate"><i class="fa-solid fa-briefcase text-slate-400 mr-1"></i><?= sanitize($p['occupation_category'] ?: 'Degree') ?></p>
                            <p class="text-xs text-slate-500 truncate"><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i><?= sanitize($p['native_district']) ?></p>
                            <a href="/profile.php?id=<?= urlencode($p['profile_id']) ?>" class="mt-4 block text-center bg-primary-50 hover:bg-primary-600 text-primary-700 hover:text-white font-bold text-xs py-2 rounded-xl transition">
                                View Profile
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Premium Gold Members Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-100 text-amber-900 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                <i class="fa-solid fa-crown text-amber-500"></i> Verified Matches
            </span>
            <h2 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950">
                Premium Gold Members
            </h2>
            <p class="text-slate-600 text-sm mt-2">
                Explore handpicked profiles of established professionals from the Ezhava &amp; Thiyya community.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($premiumProfiles as $p): ?>
                <?php
                $thumb = !empty($p['main_thumb']) ? '/' . $p['main_thumb'] : '/' . getDefaultAvatar($p['gender']);
                $age = calculateAge($p['dob']);
                ?>
                <div class="bg-white rounded-2xl border border-amber-200/80 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group flex flex-col justify-between">
                    <div>
                        <div class="relative h-64 bg-slate-100 overflow-hidden">
                            <img src="<?= $thumb ?>" alt="<?= sanitize($p['full_name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                            <div class="absolute top-3 left-3 flex flex-col gap-1">
                                <span class="bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-black text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow">
                                    <i class="fa-solid fa-crown mr-1"></i> GOLD
                                </span>
                                <span class="bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-0.5 rounded font-mono">
                                    <?= sanitize($p['profile_id']) ?>
                                </span>
                            </div>

                            <div class="absolute bottom-3 right-3 flex items-center gap-1.5">
                                <?php if ($p['has_video']): ?>
                                    <span class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-md font-bold shadow flex items-center gap-1">
                                        <i class="fa-solid fa-video"></i> Video
                                    </span>
                                <?php endif; ?>
                                <span class="bg-black/60 backdrop-blur-sm text-white text-[10px] px-2 py-0.5 rounded-md font-semibold">
                                    <i class="fa-solid fa-camera mr-1"></i> <?= $p['photo_count'] ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-lg group-hover:text-primary-600 transition truncate">
                                        <?= sanitize($p['full_name']) ?>
                                    </h3>
                                    <p class="text-xs text-amber-800 font-bold mt-0.5">
                                        <?= sanitize($p['caste']) ?> &bull; <?= $age ?> Yrs &bull; <?= formatHeight($p['height']) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 space-y-2 text-xs text-slate-600">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-graduation-cap text-slate-400 w-4"></i>
                                    <span class="truncate"><?= sanitize($p['edu_detail']) ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-briefcase text-slate-400 w-4"></i>
                                    <span class="truncate"><?= sanitize($p['occupation_category'] ?: 'Degree') ?> (<?= sanitize($p['work_place'] ?: 'Kerala') ?>)</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-slate-400 w-4"></i>
                                    <span class="truncate"><?= sanitize($p['native_district']) ?>, <?= sanitize($p['native_place'] ?: 'Kerala') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-0">
                        <a href="/profile.php?id=<?= urlencode($p['profile_id']) ?>" class="block text-center w-full bg-gradient-to-r from-primary-600 to-amber-600 hover:from-primary-700 hover:to-amber-700 text-white font-bold text-xs uppercase tracking-wider py-2.5 rounded-xl transition shadow-md">
                            View Profile
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-10">
            <a href="/advanced_search.php" class="inline-flex items-center gap-2 font-bold text-sm text-primary-600 hover:text-primary-800 transition">
                <span>View All Ezhava &amp; Thiyya Profiles</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Success Stories Highlight -->
<section class="py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold text-rose-600 uppercase tracking-widest">Happy Marriages</span>
            <h2 class="font-serif-title text-3xl font-extrabold text-slate-900 mt-1">
                Recent Success Stories
            </h2>
            <p class="text-slate-600 text-sm mt-2">
                Real couples who found lifetime happiness through Guruvision Matrimony.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($successStories as $story): ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                                <i class="fa-solid fa-heart"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">
                                    <?= sanitize($story['groom_name']) ?> &amp; <?= sanitize($story['bride_name']) ?>
                                </h3>
                                <p class="text-xs text-slate-400">Married on <?= date('d M Y', strtotime($story['marriage_date'])) ?></p>
                            </div>
                        </div>
                        <p class="text-slate-600 text-xs leading-relaxed italic">
                            &ldquo;<?= sanitize($story['story']) ?>&rdquo;
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                        <span class="text-[11px] font-bold text-primary-600 flex items-center justify-end gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-500"></i> Guruvision Couple
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
