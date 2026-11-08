<?php
// dashboard/edit_profile.php - Edit Profile Page
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
        $fullName = trim($_POST['full_name'] ?? '');
        $maritalStatus = trim($_POST['marital_status'] ?? '');
        $children = trim($_POST['children'] ?? 'No');
        $motherTongue = trim($_POST['mother_tongue'] ?? 'Malayalam');
        $aboutMe = trim($_POST['about_me'] ?? '');

        $caste = trim($_POST['caste'] ?? '');
        $physicalStatus = trim($_POST['physical_status'] ?? 'Normal');
        $height = (int)($_POST['height'] ?? 0);
        $weight = !empty($_POST['weight']) ? (int)$_POST['weight'] : null;
        $bodyType = trim($_POST['body_type'] ?? 'Average');
        $complexion = trim($_POST['complexion'] ?? '');
        $bloodGroup = trim($_POST['blood_group'] ?? '');
        $diet = trim($_POST['diet'] ?? 'Non Vegetarian');

        $eduCategory = trim($_POST['edu_category'] ?? '');
        $eduDetail = trim($_POST['edu_detail'] ?? '');
        $employedIn = trim($_POST['employed_in'] ?? '');
        $occupationCategory = trim($_POST['occupation_category'] ?? '');
        $occuDetail = trim($_POST['occu_detail'] ?? '');
        $workingFirm = trim($_POST['working_firm'] ?? '');
        $workPlace = trim($_POST['work_place'] ?? '');
        $monthlyIncome = trim($_POST['monthly_income'] ?? '');

        $nativePlace = trim($_POST['native_place'] ?? '');
        $nativeDistrict = trim($_POST['native_district'] ?? '');
        $livingPlace = trim($_POST['living_place'] ?? '');
        $livingCountry = trim($_POST['living_country'] ?? '');
        $contactName = trim($_POST['contact_name'] ?? '');
        $contactRelation = trim($_POST['contact_relation'] ?? '');
        $contactPhone = trim($_POST['contact_phone'] ?? '');
        $alternatePhone = trim($_POST['alternate_phone'] ?? '');
        $comAddress = trim($_POST['com_address'] ?? '');

        if (empty($fullName)) {
            $errors[] = 'Full name is required.';
        }
        if ($height < 137 || $height > 213) {
            $errors[] = 'Height must be between 137 cm and 213 cm.';
        }
        if (empty($eduDetail)) {
            $errors[] = 'Education Detail is required.';
        }
        if (empty($contactName) || empty($contactPhone)) {
            $errors[] = 'Contact person name and phone are required.';
        }

        if (empty($errors)) {
            $updateSql = "UPDATE users SET
                full_name = ?, marital_status = ?, children = ?, mother_tongue = ?, about_me = ?,
                caste = ?, physical_status = ?, height = ?, weight = ?, body_type = ?, complexion = ?,
                blood_group = ?, diet = ?, edu_category = ?, edu_detail = ?, employed_in = ?,
                occupation_category = ?, occu_detail = ?, working_firm = ?, work_place = ?, monthly_income = ?,
                native_place = ?, native_district = ?, living_place = ?, living_country = ?,
                contact_name = ?, contact_relation = ?, contact_phone = ?, alternate_phone = ?, com_address = ?
                WHERE id = ?";

            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute([
                $fullName, $maritalStatus, $children, $motherTongue, $aboutMe ?: null,
                $caste, $physicalStatus, $height, $weight, $bodyType, $complexion,
                $bloodGroup ?: null, $diet, $eduCategory ?: null, $eduDetail, $employedIn ?: null,
                $occupationCategory ?: null, $occuDetail ?: null, $workingFirm ?: null, $workPlace ?: null, $monthlyIncome ?: null,
                $nativePlace ?: null, $nativeDistrict, $livingPlace, $livingCountry,
                $contactName, $contactRelation, $contactPhone, $alternatePhone ?: null, $comAddress ?: null,
                $currentUser['id']
            ]);

            $successMessage = 'Profile updated successfully!';
            $currentUser = getCurrentUser(); // refresh
        }
    }
}

$pageTitle = "Edit Profile - Guruvision Matrimony";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="py-10 bg-slate-50 min-h-[85vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-1">
                <?php require_once __DIR__ . '/sidebar.php'; ?>
            </div>

            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <h1 class="font-serif-title text-2xl font-bold text-slate-900">Edit My Profile</h1>
                        <p class="text-xs text-slate-500 mt-1">Keep your profile details up-to-date to attract suitable proposals.</p>
                    </div>
                    <span class="font-mono text-xs font-bold bg-amber-100 text-amber-900 px-3 py-1 rounded-full">
                        <?= sanitize($currentUser['profile_id']) ?>
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

                <form action="/dashboard/edit_profile.php" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                    <!-- Personal Information -->
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-user text-primary-600"></i> Personal Information
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                                <input type="text" name="full_name" value="<?= sanitize($currentUser['full_name']) ?>" required class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email (Read-only)</label>
                                <input type="email" value="<?= sanitize($currentUser['email']) ?>" readonly class="w-full rounded-lg bg-slate-100 border-slate-300 border px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Date of Birth (Read-only)</label>
                                <input type="text" value="<?= sanitize($currentUser['dob']) ?> (<?= calculateAge($currentUser['dob']) ?> yrs)" readonly class="w-full rounded-lg bg-slate-100 border-slate-300 border px-3 py-2 text-sm text-slate-500 cursor-not-allowed">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Marital Status</label>
                                <select name="marital_status" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php foreach (['Unmarried', 'Widower / Widow', 'Divorced'] as $ms): ?>
                                        <option value="<?= $ms ?>" <?= ($currentUser['marital_status'] === $ms) ? 'selected' : '' ?>><?= $ms ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Children</label>
                                <select name="children" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="No" <?= ($currentUser['children'] === 'No') ? 'selected' : '' ?>>No</option>
                                    <option value="Yes" <?= ($currentUser['children'] === 'Yes') ? 'selected' : '' ?>>Yes</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Mother Tongue</label>
                                <select name="mother_tongue" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php foreach (['Malayalam', 'English', 'Hindi', 'Tamil', 'Kannada'] as $mt): ?>
                                        <option value="<?= $mt ?>" <?= ($currentUser['mother_tongue'] === $mt) ? 'selected' : '' ?>><?= $mt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">About Me / Bio</label>
                                <textarea name="about_me" rows="3" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm"><?= sanitize($currentUser['about_me'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Physical & Religious -->
                    <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-om text-primary-600"></i> Religious &amp; Physical Appearance
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Caste</label>
                                <select name="caste" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="Ezhava" <?= ($currentUser['caste'] === 'Ezhava') ? 'selected' : '' ?>>Ezhava</option>
                                    <option value="Thiyya" <?= ($currentUser['caste'] === 'Thiyya') ? 'selected' : '' ?>>Thiyya</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Physical Status</label>
                                <select name="physical_status" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="Normal" <?= ($currentUser['physical_status'] === 'Normal') ? 'selected' : '' ?>>Normal</option>
                                    <option value="Physically Challenged" <?= ($currentUser['physical_status'] === 'Physically Challenged') ? 'selected' : '' ?>>Physically Challenged</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Height</label>
                                <select name="height" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php for ($cm = 137; $cm <= 213; $cm++): ?>
                                        <option value="<?= $cm ?>" <?= ((int)$currentUser['height'] === $cm) ? 'selected' : '' ?>><?= formatHeight($cm) ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Weight (kg)</label>
                                <select name="weight" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Select Weight</option>
                                    <?php for ($kg = 40; $kg <= 140; $kg++): ?>
                                        <option value="<?= $kg ?>" <?= ((int)$currentUser['weight'] === $kg) ? 'selected' : '' ?>><?= $kg ?> kg</option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Complexion</label>
                                <select name="complexion" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php foreach (['Very Fair', 'Fair', 'Medium', 'Dark'] as $cpx): ?>
                                        <option value="<?= $cpx ?>" <?= ($currentUser['complexion'] === $cpx) ? 'selected' : '' ?>><?= $cpx ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Body Type</label>
                                <select name="body_type" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php foreach (['Average', 'Athletic', 'Slim', 'Heavy'] as $bt): ?>
                                        <option value="<?= $bt ?>" <?= ($currentUser['body_type'] === $bt) ? 'selected' : '' ?>><?= $bt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Blood Group</label>
                                <select name="blood_group" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Select</option>
                                    <?php foreach (['A+ve', 'A-ve', 'B+ve', 'B-ve', 'AB+ve', 'AB-ve', 'O+ve', 'O-ve'] as $bg): ?>
                                        <option value="<?= $bg ?>" <?= ($currentUser['blood_group'] === $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Diet</label>
                                <select name="diet" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php foreach (['Vegetarian', 'Non Vegetarian', 'Eggatarian'] as $dt): ?>
                                        <option value="<?= $dt ?>" <?= ($currentUser['diet'] === $dt) ? 'selected' : '' ?>><?= $dt ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Education & Profession -->
                    <div class="p-6 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-briefcase text-primary-600"></i> Education &amp; Career
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Education Category</label>
                                <select name="edu_category" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Select Level</option>
                                    <?php foreach (getAllEduCategories() as $cat): ?>
                                        <option value="<?= $cat ?>" <?= ($currentUser['edu_category'] === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Education Detail <span class="text-rose-500">*</span></label>
                                <input type="text" name="edu_detail" value="<?= sanitize($currentUser['edu_detail']) ?>" required class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Employed In</label>
                                <select name="employed_in" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Select Sector</option>
                                    <?php foreach (getAllEmployedIn() as $emp): ?>
                                        <option value="<?= $emp ?>" <?= ($currentUser['employed_in'] === $emp) ? 'selected' : '' ?>><?= $emp ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Occupation Category</label>
                                <select name="occupation_category" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <option value="">Select Profession</option>
                                    <?php foreach (getAllOccupations() as $occ): ?>
                                        <option value="<?= $occ ?>" <?= ($currentUser['occupation_category'] === $occ) ? 'selected' : '' ?>><?= $occ ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Occupation Detail</label>
                                <input type="text" name="occu_detail" value="<?= sanitize($currentUser['occu_detail'] ?? '') ?>" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Working Firm</label>
                                <input type="text" name="working_firm" value="<?= sanitize($currentUser['working_firm'] ?? '') ?>" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Work Place</label>
                                <input type="text" name="work_place" value="<?= sanitize($currentUser['work_place'] ?? '') ?>" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Monthly Income</label>
                                <input type="text" name="monthly_income" value="<?= sanitize($currentUser['monthly_income'] ?? '') ?>" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Location & Contact -->
                    <div class="p-6 bg-slate-50/50">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-primary-600"></i> Location &amp; Contact Details
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Native District</label>
                                <select name="native_district" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php foreach (getAllDistricts() as $dst): ?>
                                        <option value="<?= $dst ?>" <?= ($currentUser['native_district'] === $dst) ? 'selected' : '' ?>><?= $dst ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Native Place</label>
                                <input type="text" name="native_place" value="<?= sanitize($currentUser['native_place'] ?? '') ?>" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Current Living Place</label>
                                <input type="text" name="living_place" value="<?= sanitize($currentUser['living_place']) ?>" required class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Person Name</label>
                                <input type="text" name="contact_name" value="<?= sanitize($currentUser['contact_name']) ?>" required class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Relation</label>
                                <select name="contact_relation" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                                    <?php foreach (['Father', 'Mother', 'Brother', 'Sister', 'Relative'] as $rel): ?>
                                        <option value="<?= $rel ?>" <?= ($currentUser['contact_relation'] === $rel) ? 'selected' : '' ?>><?= $rel ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Contact Phone</label>
                                <input type="tel" name="contact_phone" value="<?= sanitize($currentUser['contact_phone']) ?>" required class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm">
                            </div>

                            <div class="sm:col-span-2 lg:col-span-3">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Communication Address</label>
                                <textarea name="com_address" rows="2" class="w-full rounded-lg border-slate-300 border px-3 py-2 text-sm"><?= sanitize($currentUser['com_address'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-200 flex justify-end">
                            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow transition flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i> Update Profile Details
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
