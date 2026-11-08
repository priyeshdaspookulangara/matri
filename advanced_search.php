<?php
// advanced_search.php - Advanced Profile Search with Filters, Pagination, & Sorting
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/media_handler.php';
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$currentUser = getCurrentUser();

// Filters from GET
$searchId = trim($_GET['profile_id'] ?? '');
$gender = trim($_GET['gender'] ?? '');
$caste = trim($_GET['caste'] ?? '');
$ageFrom = !empty($_GET['age_from']) ? (int)$_GET['age_from'] : 18;
$ageTo = !empty($_GET['age_to']) ? (int)$_GET['age_to'] : 70;
$heightFrom = !empty($_GET['height_from']) ? (int)$_GET['height_from'] : 137;
$heightTo = !empty($_GET['height_to']) ? (int)$_GET['height_to'] : 213;
$maritalStatus = trim($_GET['marital_status'] ?? '');
$complexion = trim($_GET['complexion'] ?? '');
$bodyType = trim($_GET['body_type'] ?? '');
$physicalStatus = trim($_GET['physical_status'] ?? '');

$districts = $_GET['districts'] ?? [];
if (!is_array($districts)) {
    $districts = !empty($districts) ? [$districts] : [];
}

$livingPlace = trim($_GET['living_place'] ?? '');
$livingCountry = trim($_GET['living_country'] ?? '');
$eduCategory = trim($_GET['edu_category'] ?? '');
$employedIn = trim($_GET['employed_in'] ?? '');
$occupationCategory = trim($_GET['occupation_category'] ?? '');
$minIncome = trim($_GET['min_income'] ?? '');

$withPhotoOnly = !empty($_GET['with_photo']);
$withVideoOnly = !empty($_GET['with_video']);

$sortBy = $_GET['sort'] ?? 'relevance';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Build Dynamic SQL Query with prepared statements
$where = ["1=1"];
$params = [];

if (!empty($searchId)) {
    $where[] = "u.profile_id LIKE ?";
    $params[] = '%' . $searchId . '%';
} else {
    if (!empty($gender)) {
        $where[] = "u.gender = ?";
        $params[] = $gender;
    }

    if (!empty($caste)) {
        $where[] = "u.caste = ?";
        $params[] = $caste;
    }

    if ($ageFrom > 18 || $ageTo < 70) {
        $minDob = date('Y-m-d', strtotime("-{$ageTo} years"));
        $maxDob = date('Y-m-d', strtotime("-{$ageFrom} years"));
        $where[] = "u.dob BETWEEN ? AND ?";
        $params[] = $minDob;
        $params[] = $maxDob;
    }

    if ($heightFrom > 137 || $heightTo < 213) {
        $where[] = "u.height BETWEEN ? AND ?";
        $params[] = $heightFrom;
        $params[] = $heightTo;
    }

    if (!empty($maritalStatus)) {
        $where[] = "u.marital_status = ?";
        $params[] = $maritalStatus;
    }

    if (!empty($complexion)) {
        $where[] = "u.complexion = ?";
        $params[] = $complexion;
    }

    if (!empty($bodyType)) {
        $where[] = "u.body_type = ?";
        $params[] = $bodyType;
    }

    if (!empty($physicalStatus)) {
        $where[] = "u.physical_status = ?";
        $params[] = $physicalStatus;
    }

    if (!empty($districts)) {
        $placeholders = implode(',', array_fill(0, count($districts), '?'));
        $where[] = "u.native_district IN ($placeholders)";
        foreach ($districts as $d) {
            $params[] = $d;
        }
    }

    if (!empty($livingPlace)) {
        $where[] = "u.living_place LIKE ?";
        $params[] = '%' . $livingPlace . '%';
    }

    if (!empty($livingCountry)) {
        $where[] = "u.living_country LIKE ?";
        $params[] = '%' . $livingCountry . '%';
    }

    if (!empty($eduCategory)) {
        $where[] = "u.edu_category = ?";
        $params[] = $eduCategory;
    }

    if (!empty($employedIn)) {
        $where[] = "u.employed_in = ?";
        $params[] = $employedIn;
    }

    if (!empty($occupationCategory)) {
        $where[] = "u.occupation_category = ?";
        $params[] = $occupationCategory;
    }

    if ($withPhotoOnly) {
        $where[] = "EXISTS (SELECT 1 FROM user_photos up WHERE up.user_id = u.id)";
    }

    if ($withVideoOnly) {
        $where[] = "EXISTS (SELECT 1 FROM user_videos uv WHERE uv.user_id = u.id)";
    }
}

// Don't show current logged in user in search results
if ($currentUser) {
    $where[] = "u.id != ?";
    $params[] = $currentUser['id'];
}

$whereClause = implode(" AND ", $where);

// Count total matching
$countSql = "SELECT COUNT(*) FROM users u WHERE $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRecords = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($totalRecords / $perPage));

// Sorting
$orderBy = match($sortBy) {
    'latest'    => 'u.created_at DESC',
    'age_asc'   => 'u.dob DESC', // younger first
    'age_desc'  => 'u.dob ASC',  // older first
    default     => 'u.is_premium DESC, u.created_at DESC',
};

// Fetch Results
$sql = "SELECT u.*,
        (SELECT thumb_path FROM user_photos up WHERE up.user_id = u.id AND up.is_main = 1 LIMIT 1) as main_thumb,
        (SELECT COUNT(*) FROM user_photos up WHERE up.user_id = u.id) as photo_count,
        (SELECT COUNT(*) FROM user_videos uv WHERE uv.user_id = u.id) as has_video
        FROM users u
        WHERE $whereClause
        ORDER BY $orderBy
        LIMIT ? OFFSET ?";

$dataParams = array_merge($params, [$perPage, $offset]);
$stmt = $pdo->prepare($sql);
$stmt->execute($dataParams);
$profiles = $stmt->fetchAll();

$pageTitle = "Advanced Profile Search - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-10 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumb / Header -->
        <div class="mb-8">
            <h1 class="font-serif-title text-3xl font-extrabold text-red-950">
                Explore &amp; Search Profiles
            </h1>
            <p class="text-sm text-slate-600 mt-1">
                Find compatible Ezhava &amp; Thiyya brides and grooms across Kerala and worldwide.
            </p>
        </div>

        <!-- Quick Profile ID Search Card -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-8">
            <form action="/advanced_search.php" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex-1 w-full relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="profile_id" value="<?= sanitize($searchId) ?>" placeholder="Direct Search by Profile ID (e.g. GV1002)..." class="w-full rounded-xl border-slate-300 border pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                </div>
                <button type="submit" class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-search"></i> Search ID
                </button>
                <?php if (!empty($searchId)): ?>
                    <a href="/advanced_search.php" class="text-xs text-slate-500 hover:underline">Reset Search</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <form action="/advanced_search.php" method="GET" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-filter text-primary-600"></i> Search Filters
                        </h2>
                        <a href="/advanced_search.php" class="text-xs text-primary-600 hover:underline">Reset All</a>
                    </div>

                    <!-- Gender / Looking For -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Looking For</label>
                        <select name="gender" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            <option value="">Any Gender</option>
                            <option value="Female" <?= ($gender === 'Female') ? 'selected' : '' ?>>Bride (Female)</option>
                            <option value="Male" <?= ($gender === 'Male') ? 'selected' : '' ?>>Groom (Male)</option>
                        </select>
                    </div>

                    <!-- Caste -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Caste</label>
                        <select name="caste" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            <option value="">All (Ezhava &amp; Thiyya)</option>
                            <option value="Ezhava" <?= ($caste === 'Ezhava') ? 'selected' : '' ?>>Ezhava</option>
                            <option value="Thiyya" <?= ($caste === 'Thiyya') ? 'selected' : '' ?>>Thiyya</option>
                        </select>
                    </div>

                    <!-- Age Range -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Age Range</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="age_from" value="<?= $ageFrom ?>" min="18" max="70" class="w-full rounded-lg border-slate-300 border px-2.5 py-1.5 text-xs text-center">
                            <span class="text-slate-400 text-xs">to</span>
                            <input type="number" name="age_to" value="<?= $ageTo ?>" min="18" max="70" class="w-full rounded-lg border-slate-300 border px-2.5 py-1.5 text-xs text-center">
                        </div>
                    </div>

                    <!-- Height Range -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Height Range (cm)</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="height_from" value="<?= $heightFrom ?>" min="137" max="213" class="w-full rounded-lg border-slate-300 border px-2.5 py-1.5 text-xs text-center">
                            <span class="text-slate-400 text-xs">to</span>
                            <input type="number" name="height_to" value="<?= $heightTo ?>" min="137" max="213" class="w-full rounded-lg border-slate-300 border px-2.5 py-1.5 text-xs text-center">
                        </div>
                    </div>

                    <!-- Marital Status -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Marital Status</label>
                        <select name="marital_status" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            <option value="">Any Status</option>
                            <option value="Unmarried" <?= ($maritalStatus === 'Unmarried') ? 'selected' : '' ?>>Unmarried</option>
                            <option value="Divorced" <?= ($maritalStatus === 'Divorced') ? 'selected' : '' ?>>Divorced</option>
                            <option value="Widower / Widow" <?= ($maritalStatus === 'Widower / Widow') ? 'selected' : '' ?>>Widower / Widow</option>
                        </select>
                    </div>

                    <!-- Districts (Multi-select) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Native District</label>
                        <div class="max-h-40 overflow-y-auto space-y-1.5 pr-1 border border-slate-200 rounded-lg p-2.5 bg-slate-50">
                            <?php foreach (getAllDistricts() as $dst): ?>
                                <label class="flex items-center text-xs text-slate-700 hover:text-slate-900 cursor-pointer">
                                    <input type="checkbox" name="districts[]" value="<?= $dst ?>" <?= in_array($dst, $districts) ? 'checked' : '' ?> class="rounded border-slate-300 text-primary-600 mr-2">
                                    <?= $dst ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Education -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Education</label>
                        <select name="edu_category" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            <option value="">Any Education</option>
                            <?php foreach (getAllEduCategories() as $cat): ?>
                                <option value="<?= $cat ?>" <?= ($eduCategory === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Employed In -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Employed In</label>
                        <select name="employed_in" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            <option value="">Any Sector</option>
                            <?php foreach (getAllEmployedIn() as $emp): ?>
                                <option value="<?= $emp ?>" <?= ($employedIn === $emp) ? 'selected' : '' ?>><?= $emp ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Occupation Category -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Occupation</label>
                        <select name="occupation_category" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            <option value="">Any Occupation</option>
                            <?php foreach (getAllOccupations() as $occ): ?>
                                <option value="<?= $occ ?>" <?= ($occupationCategory === $occ) ? 'selected' : '' ?>><?= $occ ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Media Filters -->
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <label class="flex items-center text-xs font-bold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="with_photo" value="1" <?= $withPhotoOnly ? 'checked' : '' ?> class="rounded border-slate-300 text-primary-600 mr-2">
                            <span>With Photo Only</span>
                        </label>
                        <label class="flex items-center text-xs font-bold text-slate-700 cursor-pointer">
                            <input type="checkbox" name="with_video" value="1" <?= $withVideoOnly ? 'checked' : '' ?> class="rounded border-slate-300 text-primary-600 mr-2">
                            <span>With Intro Video Only</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-2.5 rounded-xl text-xs uppercase tracking-wider shadow transition">
                        Apply Filters
                    </button>
                </form>
            </div>

            <!-- Results Section -->
            <div class="lg:col-span-3 space-y-6">

                <!-- Results Bar (Count & Sorting) -->
                <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-slate-600 font-medium">
                        Showing <strong class="text-slate-900"><?= count($profiles) ?></strong> of <strong class="text-slate-900"><?= $totalRecords ?></strong> profiles found
                    </div>

                    <div class="flex items-center space-x-2 text-xs">
                        <span class="text-slate-500 font-bold uppercase tracking-wider">Sort by:</span>
                        <select onchange="window.location.href = updateQueryString('sort', this.value)" class="rounded-lg border-slate-300 border px-2.5 py-1.5 text-xs text-slate-700 focus:outline-none">
                            <option value="relevance" <?= ($sortBy === 'relevance') ? 'selected' : '' ?>>Relevance</option>
                            <option value="latest" <?= ($sortBy === 'latest') ? 'selected' : '' ?>>Latest Profiles</option>
                            <option value="age_asc" <?= ($sortBy === 'age_asc') ? 'selected' : '' ?>>Age: Youngest First</option>
                            <option value="age_desc" <?= ($sortBy === 'age_desc') ? 'selected' : '' ?>>Age: Oldest First</option>
                        </select>
                    </div>
                </div>

                <!-- Grid of Profiles -->
                <?php if (empty($profiles)): ?>
                    <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center">
                        <i class="fa-solid fa-user-slash text-5xl text-slate-300 mb-3 block"></i>
                        <h3 class="text-lg font-bold text-slate-800">No profiles matched your search criteria</h3>
                        <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">Try broadening your age range, clearing district filters, or removing photo/video restrictions.</p>
                        <a href="/advanced_search.php" class="inline-block mt-4 bg-primary-600 hover:bg-primary-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl">Reset All Filters</a>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <?php foreach ($profiles as $p): ?>
                            <?php
                            $thumb = !empty($p['main_thumb']) ? '/' . $p['main_thumb'] : '/' . getDefaultAvatar($p['gender']);
                            $age = calculateAge($p['dob']);
                            ?>
                            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden flex flex-col group">
                                <div class="relative h-56 bg-slate-100 overflow-hidden">
                                    <img src="<?= $thumb ?>" alt="<?= sanitize($p['full_name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                                    <!-- Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1">
                                        <?php if ($p['is_premium']): ?>
                                            <span class="bg-gradient-to-r from-amber-500 to-yellow-400 text-slate-950 font-black text-[10px] px-2.5 py-0.5 rounded-full uppercase tracking-wider shadow">
                                                <i class="fa-solid fa-crown mr-1"></i> GOLD
                                            </span>
                                        <?php endif; ?>
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

                                <div class="p-5 flex-1 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-baseline justify-between mb-1">
                                            <h3 class="font-bold text-slate-900 text-base group-hover:text-primary-600 transition truncate">
                                                <?= sanitize($p['full_name']) ?>
                                            </h3>
                                        </div>

                                        <div class="text-xs text-slate-500 font-medium mb-3">
                                            <span class="text-amber-800 font-bold"><?= sanitize($p['caste']) ?></span> &bull;
                                            <?= $age ?> Yrs &bull;
                                            <?= formatHeight($p['height']) ?>
                                        </div>

                                        <div class="space-y-1.5 text-xs text-slate-600 border-t border-slate-100 pt-3">
                                            <div class="flex items-center gap-2 truncate">
                                                <i class="fa-solid fa-graduation-cap text-slate-400 w-4"></i>
                                                <span class="truncate"><?= sanitize($p['edu_detail']) ?></span>
                                            </div>
                                            <div class="flex items-center gap-2 truncate">
                                                <i class="fa-solid fa-briefcase text-slate-400 w-4"></i>
                                                <span class="truncate"><?= sanitize($p['occupation_category'] ?: 'Not Specified') ?></span>
                                            </div>
                                            <div class="flex items-center gap-2 truncate">
                                                <i class="fa-solid fa-location-dot text-slate-400 w-4"></i>
                                                <span class="truncate"><?= sanitize($p['native_district']) ?>, <?= sanitize($p['living_place']) ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-5 pt-3 border-t border-slate-100">
                                        <a href="/profile.php?id=<?= urlencode($p['profile_id']) ?>" class="block w-full text-center bg-orange-50 hover:bg-primary-600 text-primary-700 hover:text-white font-bold text-xs py-2.5 rounded-xl transition shadow-sm">
                                            View Full Profile &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="mt-8 flex items-center justify-center space-x-2">
                            <?php if ($page > 1): ?>
                                <button onclick="window.location.href = updateQueryString('page', <?= $page - 1 ?>)" class="px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold">
                                    &larr; Prev
                                </button>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <button onclick="window.location.href = updateQueryString('page', <?= $i ?>)" class="px-3.5 py-2 rounded-lg text-xs font-bold <?= ($i === $page) ? 'bg-primary-600 text-white shadow' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
                                    <?= $i ?>
                                </button>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <button onclick="window.location.href = updateQueryString('page', <?= $page + 1 ?>)" class="px-3 py-2 rounded-lg bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold">
                                    Next &rarr;
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

    </div>
</div>

<script>
function updateQueryString(key, value) {
    const url = new URL(window.location.href);
    url.searchParams.set(key, value);
    return url.toString();
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
