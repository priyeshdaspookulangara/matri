<?php
// dashboard/preferences.php - Partner Preferences Module
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

// Load existing preferences
$stmt = $pdo->prepare("SELECT * FROM partner_preferences WHERE user_id = ?");
$stmt->execute([$currentUser['id']]);
$pref = $stmt->fetch() ?: [];

// Parse districts JSON or array
$selectedDistricts = [];
if (!empty($pref['pref_districts'])) {
    $decoded = json_decode($pref['pref_districts'], true);
    $selectedDistricts = is_array($decoded) ? $decoded : explode(',', $pref['pref_districts']);
}

// Parse marital status
$selectedMaritalStatuses = [];
if (!empty($pref['pref_marital_status'])) {
    $selectedMaritalStatuses = explode(',', $pref['pref_marital_status']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid request security token.';
    } else {
        $ageFrom = (int)($_POST['pref_age_from'] ?? 18);
        $ageTo = (int)($_POST['pref_age_to'] ?? 50);
        $heightFrom = (int)($_POST['pref_height_from'] ?? 137);
        $heightTo = (int)($_POST['pref_height_to'] ?? 213);

        $maritalStatuses = $_POST['pref_marital_status'] ?? [];
        $maritalStatusStr = is_array($maritalStatuses) ? implode(',', $maritalStatuses) : (string)$maritalStatuses;

        $complexion = trim($_POST['pref_complexion'] ?? '');
        $bodyType = trim($_POST['pref_body_type'] ?? '');
        $physicalStatus = trim($_POST['pref_physical_status'] ?? 'Normal');

        $districts = $_POST['pref_districts'] ?? [];
        $districtsJson = !empty($districts) ? json_encode(array_values($districts)) : null;

        $livingPlace = trim($_POST['pref_living_place'] ?? '');
        $livingCountry = trim($_POST['pref_living_country'] ?? '');
        $eduCategory = trim($_POST['pref_edu_category'] ?? '');
        $employedIn = trim($_POST['pref_employed_in'] ?? '');
        $occupation = trim($_POST['pref_occupation'] ?? '');

        if ($ageFrom > $ageTo) {
            $errors[] = 'Minimum age preference cannot be greater than maximum age.';
        }
        if ($heightFrom > $heightTo) {
            $errors[] = 'Minimum height preference cannot be greater than maximum height.';
        }

        if (empty($errors)) {
            $sql = "INSERT INTO partner_preferences (
                        user_id, pref_age_from, pref_age_to, pref_height_from, pref_height_to,
                        pref_marital_status, pref_complexion, pref_body_type, pref_physical_status,
                        pref_districts, pref_living_place, pref_living_country,
                        pref_edu_category, pref_employed_in, pref_occupation
                    ) VALUES (
                        ?, ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?
                    ) ON DUPLICATE KEY UPDATE
                        pref_age_from = VALUES(pref_age_from),
                        pref_age_to = VALUES(pref_age_to),
                        pref_height_from = VALUES(pref_height_from),
                        pref_height_to = VALUES(pref_height_to),
                        pref_marital_status = VALUES(pref_marital_status),
                        pref_complexion = VALUES(pref_complexion),
                        pref_body_type = VALUES(pref_body_type),
                        pref_physical_status = VALUES(pref_physical_status),
                        pref_districts = VALUES(pref_districts),
                        pref_living_place = VALUES(pref_living_place),
                        pref_living_country = VALUES(pref_living_country),
                        pref_edu_category = VALUES(pref_edu_category),
                        pref_employed_in = VALUES(pref_employed_in),
                        pref_occupation = VALUES(pref_occupation),
                        updated_at = CURRENT_TIMESTAMP";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $currentUser['id'], $ageFrom, $ageTo, $heightFrom, $heightTo,
                $maritalStatusStr ?: null, $complexion ?: null, $bodyType ?: null, $physicalStatus,
                $districtsJson, $livingPlace ?: null, $livingCountry ?: null,
                $eduCategory ?: null, $employedIn ?: null, $occupation ?: null
            ]);

            $successMessage = 'Partner preferences updated successfully!';

            // Refresh pref
            $stmt = $pdo->prepare("SELECT * FROM partner_preferences WHERE user_id = ?");
            $stmt->execute([$currentUser['id']]);
            $pref = $stmt->fetch() ?: [];
            $selectedDistricts = is_array($districts) ? $districts : [];
            $selectedMaritalStatuses = is_array($maritalStatuses) ? $maritalStatuses : [];
        }
    }
}

$pageTitle = "Partner Preferences - Guruvision Matrimony";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-10 bg-slate-50 min-h-[85vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <?php require_once __DIR__ . '/sidebar.php'; ?>
            </div>

            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                    <h1 class="font-serif-title text-2xl font-bold text-slate-900">Partner Preferences</h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Tell us what you are looking for in your ideal life partner. We use these criteria to generate daily matching profiles.
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

                <form action="/dashboard/preferences.php" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                    <!-- Section: Age & Height -->
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-arrows-up-down-left-right text-primary-600"></i> Age &amp; Height Criteria
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Age -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Age Range (Years)</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <label class="text-[11px] text-slate-500">From</label>
                                        <select name="pref_age_from" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                            <?php for ($a = 18; $a <= 70; $a++): ?>
                                                <option value="<?= $a ?>" <?= ((int)($pref['pref_age_from'] ?? 18) === $a) ? 'selected' : '' ?>><?= $a ?> yrs</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <span class="text-slate-400 mt-5">to</span>
                                    <div class="flex-1">
                                        <label class="text-[11px] text-slate-500">To</label>
                                        <select name="pref_age_to" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                            <?php for ($a = 18; $a <= 70; $a++): ?>
                                                <option value="<?= $a ?>" <?= ((int)($pref['pref_age_to'] ?? 40) === $a) ? 'selected' : '' ?>><?= $a ?> yrs</option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Height -->
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Height Range</label>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <label class="text-[11px] text-slate-500">From</label>
                                        <select name="pref_height_from" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                            <?php for ($h = 137; $h <= 213; $h++): ?>
                                                <option value="<?= $h ?>" <?= ((int)($pref['pref_height_from'] ?? 140) === $h) ? 'selected' : '' ?>><?= formatHeight($h) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <span class="text-slate-400 mt-5">to</span>
                                    <div class="flex-1">
                                        <label class="text-[11px] text-slate-500">To</label>
                                        <select name="pref_height_to" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                            <?php for ($h = 137; $h <= 213; $h++): ?>
                                                <option value="<?= $h ?>" <?= ((int)($pref['pref_height_to'] ?? 200) === $h) ? 'selected' : '' ?>><?= formatHeight($h) ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Marital & Physical -->
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-person-circle-check text-primary-600"></i> Marital Status &amp; Physical Appearance
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Acceptable Marital Statuses</label>
                                <div class="flex flex-wrap gap-4 mt-1">
                                    <?php foreach (['Unmarried', 'Widower / Widow', 'Divorced'] as $ms): ?>
                                        <label class="inline-flex items-center text-sm">
                                            <input type="checkbox" name="pref_marital_status[]" value="<?= $ms ?>" <?= in_array($ms, $selectedMaritalStatuses) ? 'checked' : '' ?> class="rounded border-slate-300 text-primary-600 mr-2">
                                            <?= $ms ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Preferred Complexion</label>
                                <select name="pref_complexion" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Any Complexion</option>
                                    <?php foreach (['Very Fair', 'Fair', 'Medium', 'Dark'] as $cpx): ?>
                                        <option value="<?= $cpx ?>" <?= (($pref['pref_complexion'] ?? '') === $cpx) ? 'selected' : '' ?>><?= $cpx ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Preferred Body Type</label>
                                <select name="pref_body_type" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Any Body Type</option>
                                    <?php foreach (['Average', 'Athletic', 'Slim', 'Heavy'] as $bt): ?>
                                        <option value="<?= $bt ?>" <?= (($pref['pref_body_type'] ?? '') === $bt) ? 'selected' : '' ?>><?= $bt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Location Preferences -->
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-primary-600"></i> Location Preferences
                        </h2>

                        <div class="mb-5">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                Preferred Kerala Districts (Select Multiple)
                            </label>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2">
                                <?php foreach (getAllDistricts() as $dst): ?>
                                    <label class="inline-flex items-center text-xs p-2 rounded-lg border border-slate-200 hover:bg-orange-50/50 cursor-pointer">
                                        <input type="checkbox" name="pref_districts[]" value="<?= $dst ?>" <?= in_array($dst, $selectedDistricts) ? 'checked' : '' ?> class="rounded border-slate-300 text-primary-600 mr-2">
                                        <span class="truncate"><?= $dst ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Preferred Living Place / City</label>
                                <input type="text" name="pref_living_place" value="<?= sanitize($pref['pref_living_place'] ?? '') ?>" placeholder="e.g. Kerala / Kochi / Bangalore" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Preferred Living Country</label>
                                <input type="text" name="pref_living_country" value="<?= sanitize($pref['pref_living_country'] ?? '') ?>" placeholder="e.g. India / UAE" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Education & Profession -->
                    <div class="p-6 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-graduation-cap text-primary-600"></i> Education &amp; Profession
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Education Category</label>
                                <select name="pref_edu_category" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Any Qualification</option>
                                    <?php foreach (getAllEduCategories() as $cat): ?>
                                        <option value="<?= $cat ?>" <?= (($pref['pref_edu_category'] ?? '') === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employed In (Sector)</label>
                                <select name="pref_employed_in" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Any Sector</option>
                                    <?php foreach (getAllEmployedIn() as $emp): ?>
                                        <option value="<?= $emp ?>" <?= (($pref['pref_employed_in'] ?? '') === $emp) ? 'selected' : '' ?>><?= $emp ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Occupation</label>
                                <select name="pref_occupation" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Any Occupation</option>
                                    <?php foreach (getAllOccupations() as $occ): ?>
                                        <option value="<?= $occ ?>" <?= (($pref['pref_occupation'] ?? '') === $occ) ? 'selected' : '' ?>><?= $occ ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-200 flex justify-end">
                            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow transition flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i> Save Partner Preferences
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
