<?php
// register.php - User Registration Page
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    header('Location: /dashboard/index.php');
    exit;
}

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid request token. Please submit the form again.';
    }

    // Capture & clean inputs
    $createdBy = trim($_POST['created_by'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $gender = trim($_POST['gender'] ?? '');
    $dobDay = (int)($_POST['dob_day'] ?? 0);
    $dobMonth = (int)($_POST['dob_month'] ?? 0);
    $dobYear = (int)($_POST['dob_year'] ?? 0);
    $maritalStatus = trim($_POST['marital_status'] ?? '');
    $children = trim($_POST['children'] ?? 'No');
    $motherTongue = trim($_POST['mother_tongue'] ?? 'Malayalam');
    $aboutMe = trim($_POST['about_me'] ?? '');

    $religion = 'Hindu';
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
    $citizenship = 'India';
    $livingPlace = trim($_POST['living_place'] ?? 'India');
    $livingCountry = trim($_POST['living_country'] ?? 'India');
    $contactName = trim($_POST['contact_name'] ?? '');
    $contactRelation = trim($_POST['contact_relation'] ?? '');
    $contactPhone = trim($_POST['contact_phone'] ?? '');
    $alternatePhone = trim($_POST['alternate_phone'] ?? '');
    $comAddress = trim($_POST['com_address'] ?? '');

    $formData = $_POST;

    // Field Validations
    $allowedCreatedBy = ['Self', 'Parents', 'Sibling', 'Relative', 'Friend'];
    if (!in_array($createdBy, $allowedCreatedBy, true)) {
        $errors[] = 'Please select a valid "Created By" option.';
    }

    if (empty($fullName) || mb_strlen($fullName) > 100) {
        $errors[] = 'Full name is required (max 100 characters).';
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    if (!in_array($gender, ['Male', 'Female'], true)) {
        $errors[] = 'Please select Gender.';
    }

    // DOB Validation (Day 1-31, Month 1-12, Year 1957-2009)
    if ($dobDay < 1 || $dobDay > 31 || $dobMonth < 1 || $dobMonth > 12 || $dobYear < 1957 || $dobYear > 2009 || !checkdate($dobMonth, $dobDay, $dobYear)) {
        $errors[] = 'Please select a valid Date of Birth (Year between 1957 and 2009).';
    }
    $dob = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);

    if (!in_array($maritalStatus, ['Unmarried', 'Widower / Widow', 'Divorced'], true)) {
        $errors[] = 'Please select Marital Status.';
    }

    if ($maritalStatus !== 'Unmarried' && !in_array($children, ['No', 'Yes'], true)) {
        $errors[] = 'Please select whether you have children.';
    }
    if ($maritalStatus === 'Unmarried') {
        $children = 'No';
    }

    if (!in_array($motherTongue, ['Malayalam', 'English', 'Hindi', 'Tamil', 'Kannada'], true)) {
        $motherTongue = 'Malayalam';
    }

    if (!in_array($caste, ['Ezhava', 'Thiyya'], true)) {
        $errors[] = 'Please select Caste (Ezhava or Thiyya).';
    }

    if (!in_array($physicalStatus, ['Normal', 'Physically Challenged'], true)) {
        $physicalStatus = 'Normal';
    }

    // Height 137 to 213 cm
    if ($height < 137 || $height > 213) {
        $errors[] = 'Height must be between 137 cm (4\'6") and 213 cm (7\'0").';
    }

    if ($weight !== null && ($weight < 40 || $weight > 140)) {
        $errors[] = 'Weight must be between 40 kg and 140 kg.';
    }

    if (!in_array($complexion, ['Very Fair', 'Fair', 'Medium', 'Dark'], true)) {
        $errors[] = 'Please select Complexion.';
    }

    if (empty($eduDetail)) {
        $errors[] = 'Education Detail (degree/course) is required.';
    }

    $districts = getAllDistricts();
    if (!in_array($nativeDistrict, $districts, true)) {
        $errors[] = 'Please select a valid Native District.';
    }

    if (empty($contactName)) {
        $errors[] = 'Contact Person Name is required.';
    }

    $allowedRelations = ['Father', 'Mother', 'Brother', 'Sister', 'Relative'];
    if (!in_array($contactRelation, $allowedRelations, true)) {
        $errors[] = 'Please select Contact Relation.';
    }

    if (empty($contactPhone) || strlen($contactPhone) < 7 || strlen($contactPhone) > 15) {
        $errors[] = 'Valid Contact Phone number is required (7-15 digits).';
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        // Check duplicate email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with this email already exists. Please login instead.';
        } else {
            try {
                $pdo->beginTransaction();

                $profileId = generateProfileId($pdo);
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);

                $insertSql = "INSERT INTO users (
                    profile_id, email, password_hash, is_premium,
                    created_by, full_name, gender, dob, marital_status, children,
                    mother_tongue, about_me, religion, caste, physical_status,
                    height, weight, body_type, complexion, blood_group, diet,
                    edu_category, edu_detail, employed_in, occupation_category,
                    occu_detail, working_firm, work_place, monthly_income,
                    native_place, native_district, citizenship, living_place, living_country,
                    contact_name, contact_relation, contact_phone, alternate_phone, com_address
                ) VALUES (
                    ?, ?, ?, 0,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?
                )";

                $insertStmt = $pdo->prepare($insertSql);
                $insertStmt->execute([
                    $profileId, $email, $passwordHash,
                    $createdBy, $fullName, $gender, $dob, $maritalStatus, $children,
                    $motherTongue, $aboutMe ?: null, $religion, $caste, $physicalStatus,
                    $height, $weight, $bodyType, $complexion, $bloodGroup ?: null, $diet,
                    $eduCategory ?: null, $eduDetail, $employedIn ?: null, $occupationCategory ?: null,
                    $occuDetail ?: null, $workingFirm ?: null, $workPlace ?: null, $monthlyIncome ?: null,
                    $nativePlace ?: null, $nativeDistrict, $citizenship, $livingPlace, $livingCountry,
                    $contactName, $contactRelation, $contactPhone, $alternatePhone ?: null, $comAddress ?: null
                ]);

                $newUserId = (int)$pdo->lastInsertId();

                // Create initial default partner preferences
                $prefSql = "INSERT INTO partner_preferences (
                    user_id, pref_age_from, pref_age_to, pref_height_from, pref_height_to,
                    pref_marital_status, pref_physical_status
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $userAge = calculateAge($dob);
                $prefFrom = max(18, $userAge - 5);
                $prefTo = $userAge + 5;
                $prefStmt = $pdo->prepare($prefSql);
                $prefStmt->execute([
                    $newUserId, $prefFrom, $prefTo, 145, 195,
                    $maritalStatus === 'Unmarried' ? 'Unmarried' : 'Unmarried,Divorced,Widower / Widow',
                    'Normal'
                ]);

                $pdo->commit();

                // Auto login user
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['profile_id'] = $profileId;
                $_SESSION['full_name'] = $fullName;

                setFlash('success', "Registration successful! Welcome to Guruvision Matrimony. Your Profile ID is {$profileId}.");
                header('Location: /dashboard/index.php');
                exit;

            } catch (Exception $e) {
                $pdo->rollBack();
                error_log('Registration Error: ' . $e->getMessage());
                $errors[] = 'A registration error occurred. Please try again. ' . $e->getMessage();
            }
        }
    }
}

$pageTitle = "Register Free - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-10 bg-pattern min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <!-- Header Banner -->
        <div class="text-center mb-8">
            <span class="inline-block px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                100% Free Registration
            </span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950">
                Join Guruvision Matrimony
            </h1>
            <p class="text-slate-600 mt-2 text-sm sm:text-base">
                Exclusive community portal for Ezhava &amp; Thiyya brides and grooms. Complete your profile in simple steps.
            </p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
                <div class="flex items-center gap-2 font-bold mb-2">
                    <i class="fa-solid fa-triangle-exclamation"></i> Please correct the following errors:
                </div>
                <ul class="list-disc list-inside text-sm space-y-1">
                    <?php foreach ($errors as $err): ?>
                        <li><?= sanitize($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="/register.php" method="POST" class="bg-white rounded-2xl shadow-xl border border-slate-200/80 overflow-hidden">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

            <!-- Section 1: Account Information -->
            <div class="p-6 sm:p-8 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">1</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Account &amp; Login Details</h2>
                        <p class="text-xs text-slate-500">Who is creating this profile and initial credentials</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Profile Created By <span class="text-rose-500">*</span>
                        </label>
                        <select name="created_by" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                            <option value="">-- Select Created By --</option>
                            <?php foreach (['Self', 'Parents', 'Sibling', 'Relative', 'Friend'] as $opt): ?>
                                <option value="<?= $opt ?>" <?= (($formData['created_by'] ?? '') === $opt) ? 'selected' : '' ?>><?= $opt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="full_name" value="<?= sanitize($formData['full_name'] ?? '') ?>" required placeholder="e.g. Rahul Santhosh" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" value="<?= sanitize($formData['email'] ?? '') ?>" required placeholder="youremail@example.com" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="password" required placeholder="Minimum 6 characters" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Confirm Password <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="confirm_password" required placeholder="Re-enter password" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Section 2: Basic Details -->
            <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">2</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Basic &amp; Personal Details</h2>
                        <p class="text-xs text-slate-500">Gender, Date of Birth, Marital status</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Gender <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex gap-4 mt-2">
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="gender" value="Male" <?= (($formData['gender'] ?? '') === 'Male') ? 'checked' : '' ?> required class="text-primary-600 focus:ring-primary-500 mr-2">
                                Groom (Male)
                            </label>
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="gender" value="Female" <?= (($formData['gender'] ?? '') === 'Female') ? 'checked' : '' ?> required class="text-primary-600 focus:ring-primary-500 mr-2">
                                Bride (Female)
                            </label>
                        </div>
                    </div>

                    <!-- Date of Birth: Day, Month, Year -->
                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Date of Birth (Day / Month / Year) <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <select name="dob_day" required class="rounded-lg border-slate-300 border px-2.5 py-2.5 text-sm">
                                <option value="">Day</option>
                                <?php for ($d = 1; $d <= 31; $d++): ?>
                                    <option value="<?= $d ?>" <?= ((int)($formData['dob_day'] ?? 0) === $d) ? 'selected' : '' ?>><?= $d ?></option>
                                <?php endfor; ?>
                            </select>

                            <select name="dob_month" required class="rounded-lg border-slate-300 border px-2.5 py-2.5 text-sm">
                                <option value="">Month</option>
                                <?php
                                $months = [1=>'Jan', 2=>'Feb', 3=>'Mar', 4=>'Apr', 5=>'May', 6=>'Jun', 7=>'Jul', 8=>'Aug', 9=>'Sep', 10=>'Oct', 11=>'Nov', 12=>'Dec'];
                                foreach ($months as $num => $name): ?>
                                    <option value="<?= $num ?>" <?= ((int)($formData['dob_month'] ?? 0) === $num) ? 'selected' : '' ?>><?= $num ?> - <?= $name ?></option>
                                <?php endforeach; ?>
                            </select>

                            <select name="dob_year" required class="rounded-lg border-slate-300 border px-2.5 py-2.5 text-sm">
                                <option value="">Year</option>
                                <?php for ($y = 2009; $y >= 1957; $y--): ?>
                                    <option value="<?= $y ?>" <?= ((int)($formData['dob_year'] ?? 0) === $y) ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Marital Status <span class="text-rose-500">*</span>
                        </label>
                        <select name="marital_status" id="marital_status" required onchange="toggleChildrenField()" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <?php foreach (['Unmarried', 'Widower / Widow', 'Divorced'] as $ms): ?>
                                <option value="<?= $ms ?>" <?= (($formData['marital_status'] ?? '') === $ms) ? 'selected' : '' ?>><?= $ms ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="children_container" class="<?= (($formData['marital_status'] ?? '') === 'Divorced' || ($formData['marital_status'] ?? '') === 'Widower / Widow') ? '' : 'hidden' ?>">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Have Children?
                        </label>
                        <select name="children" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="No" <?= (($formData['children'] ?? '') === 'No') ? 'selected' : '' ?>>No</option>
                            <option value="Yes" <?= (($formData['children'] ?? '') === 'Yes') ? 'selected' : '' ?>>Yes</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Mother Tongue <span class="text-rose-500">*</span>
                        </label>
                        <select name="mother_tongue" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <?php foreach (['Malayalam', 'English', 'Hindi', 'Tamil', 'Kannada'] as $mt): ?>
                                <option value="<?= $mt ?>" <?= (($formData['mother_tongue'] ?? 'Malayalam') === $mt) ? 'selected' : '' ?>><?= $mt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            About Me / Bio
                        </label>
                        <textarea name="about_me" rows="3" placeholder="Briefly describe yourself, your hobbies, personality, and family background..." class="w-full rounded-lg border-slate-300 border px-3.5 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"><?= sanitize($formData['about_me'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Religious & Physical Details -->
            <div class="p-6 sm:p-8 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">3</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Religious &amp; Physical Details</h2>
                        <p class="text-xs text-slate-500">Caste (Ezhava / Thiyya), height, complexion, body type</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Religion
                        </label>
                        <input type="text" value="Hindu" readonly class="w-full rounded-lg bg-slate-100 border-slate-300 border px-3.5 py-2.5 text-sm text-slate-600 font-semibold cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Caste <span class="text-rose-500">*</span>
                        </label>
                        <select name="caste" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">-- Select Caste --</option>
                            <option value="Ezhava" <?= (($formData['caste'] ?? '') === 'Ezhava') ? 'selected' : '' ?>>Ezhava</option>
                            <option value="Thiyya" <?= (($formData['caste'] ?? '') === 'Thiyya') ? 'selected' : '' ?>>Thiyya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Physical Status <span class="text-rose-500">*</span>
                        </label>
                        <select name="physical_status" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="Normal" <?= (($formData['physical_status'] ?? '') === 'Normal') ? 'selected' : '' ?>>Normal</option>
                            <option value="Physically Challenged" <?= (($formData['physical_status'] ?? '') === 'Physically Challenged') ? 'selected' : '' ?>>Physically Challenged</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Height <span class="text-rose-500">*</span>
                        </label>
                        <select name="height" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">-- Select Height --</option>
                            <?php for ($cm = 137; $cm <= 213; $cm++): ?>
                                <option value="<?= $cm ?>" <?= ((int)($formData['height'] ?? 0) === $cm) ? 'selected' : '' ?>>
                                    <?= formatHeight($cm) ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Weight (kg)
                        </label>
                        <select name="weight" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">Select Weight</option>
                            <?php for ($kg = 40; $kg <= 140; $kg++): ?>
                                <option value="<?= $kg ?>" <?= ((int)($formData['weight'] ?? 0) === $kg) ? 'selected' : '' ?>><?= $kg ?> kg</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Body Type
                        </label>
                        <select name="body_type" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <?php foreach (['Average', 'Athletic', 'Slim', 'Heavy'] as $bt): ?>
                                <option value="<?= $bt ?>" <?= (($formData['body_type'] ?? 'Average') === $bt) ? 'selected' : '' ?>><?= $bt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Complexion <span class="text-rose-500">*</span>
                        </label>
                        <select name="complexion" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">-- Select Complexion --</option>
                            <?php foreach (['Very Fair', 'Fair', 'Medium', 'Dark'] as $cpx): ?>
                                <option value="<?= $cpx ?>" <?= (($formData['complexion'] ?? '') === $cpx) ? 'selected' : '' ?>><?= $cpx ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Blood Group
                        </label>
                        <select name="blood_group" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">Select Blood Group</option>
                            <?php foreach (['A+ve', 'A-ve', 'B+ve', 'B-ve', 'AB+ve', 'AB-ve', 'O+ve', 'O-ve'] as $bg): ?>
                                <option value="<?= $bg ?>" <?= (($formData['blood_group'] ?? '') === $bg) ? 'selected' : '' ?>><?= $bg ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Diet
                        </label>
                        <select name="diet" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <?php foreach (['Vegetarian', 'Non Vegetarian', 'Eggatarian'] as $dt): ?>
                                <option value="<?= $dt ?>" <?= (($formData['diet'] ?? 'Non Vegetarian') === $dt) ? 'selected' : '' ?>><?= $dt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 4: Education & Professional Details -->
            <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">4</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Education &amp; Professional Details</h2>
                        <p class="text-xs text-slate-500">Qualification, employment type, occupation, income</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Education Category
                        </label>
                        <select name="edu_category" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">Select Education Level</option>
                            <?php foreach (getAllEduCategories() as $cat): ?>
                                <option value="<?= $cat ?>" <?= (($formData['edu_category'] ?? '') === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Education Detail <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="edu_detail" value="<?= sanitize($formData['edu_detail'] ?? '') ?>" required placeholder="e.g. B.Tech Computer Science" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Employed In
                        </label>
                        <select name="employed_in" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">Select Sector</option>
                            <?php foreach (getAllEmployedIn() as $emp): ?>
                                <option value="<?= $emp ?>" <?= (($formData['employed_in'] ?? '') === $emp) ? 'selected' : '' ?>><?= $emp ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Occupation Category
                        </label>
                        <select name="occupation_category" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">Select Profession</option>
                            <?php foreach (getAllOccupations() as $occ): ?>
                                <option value="<?= $occ ?>" <?= (($formData['occupation_category'] ?? '') === $occ) ? 'selected' : '' ?>><?= $occ ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Occupation Detail
                        </label>
                        <input type="text" name="occu_detail" value="<?= sanitize($formData['occu_detail'] ?? '') ?>" placeholder="e.g. Senior Software Architect" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Working Firm / Company
                        </label>
                        <input type="text" name="working_firm" value="<?= sanitize($formData['working_firm'] ?? '') ?>" placeholder="e.g. TCS / Cognizant" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Work Place / City
                        </label>
                        <input type="text" name="work_place" value="<?= sanitize($formData['work_place'] ?? '') ?>" placeholder="e.g. Kochi / Bangalore" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Monthly Income
                        </label>
                        <input type="text" name="monthly_income" value="<?= sanitize($formData['monthly_income'] ?? '') ?>" placeholder="e.g. Rs. 85,000" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>
                </div>
            </div>

            <!-- Section 5: Location & Contact Details -->
            <div class="p-6 sm:p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-8 rounded-full bg-primary-600 text-white flex items-center justify-center font-bold text-sm">5</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Location &amp; Contact Details</h2>
                        <p class="text-xs text-slate-500">Native location, current residence, and family contact</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Native District <span class="text-rose-500">*</span>
                        </label>
                        <select name="native_district" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">-- Select Native District --</option>
                            <?php foreach (getAllDistricts() as $dst): ?>
                                <option value="<?= $dst ?>" <?= (($formData['native_district'] ?? '') === $dst) ? 'selected' : '' ?>><?= $dst ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Native Place
                        </label>
                        <input type="text" name="native_place" value="<?= sanitize($formData['native_place'] ?? '') ?>" placeholder="e.g. Cherthala" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Citizenship <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="citizenship" value="India" readonly class="w-full rounded-lg bg-slate-100 border-slate-300 border px-3.5 py-2.5 text-sm text-slate-600">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Current Living Place <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="living_place" value="<?= sanitize($formData['living_place'] ?? 'Ernakulam') ?>" required placeholder="e.g. Ernakulam / Dubai" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Current Living Country <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="living_country" value="<?= sanitize($formData['living_country'] ?? 'India') ?>" required placeholder="e.g. India" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Contact Person Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="contact_name" value="<?= sanitize($formData['contact_name'] ?? '') ?>" required placeholder="e.g. Santhosh Kumar" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Contact Relation <span class="text-rose-500">*</span>
                        </label>
                        <select name="contact_relation" required class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                            <option value="">Select Relation</option>
                            <?php foreach (['Father', 'Mother', 'Brother', 'Sister', 'Relative'] as $rel): ?>
                                <option value="<?= $rel ?>" <?= (($formData['contact_relation'] ?? '') === $rel) ? 'selected' : '' ?>><?= $rel ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Contact Phone <span class="text-rose-500">*</span>
                        </label>
                        <input type="tel" name="contact_phone" value="<?= sanitize($formData['contact_phone'] ?? '') ?>" required placeholder="e.g. +91 9447123456" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Alternate Phone
                        </label>
                        <input type="tel" name="alternate_phone" value="<?= sanitize($formData['alternate_phone'] ?? '') ?>" placeholder="e.g. +91 484 223344" class="w-full rounded-lg border-slate-300 border px-3.5 py-2.5 text-sm">
                    </div>

                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Communication / House Address
                        </label>
                        <textarea name="com_address" rows="2" placeholder="Full residential postal address..." class="w-full rounded-lg border-slate-300 border px-3.5 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"><?= sanitize($formData['com_address'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="mt-8 border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500">
                        By clicking "Complete Registration", you agree to our Terms of Service and Privacy Policy.
                    </p>
                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-primary-600 to-amber-600 hover:from-primary-700 hover:to-amber-700 text-white font-bold text-sm px-8 py-3.5 rounded-xl shadow-lg hover:shadow-xl transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-plus"></i> Complete Registration Free
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-6 text-center text-sm text-slate-600">
            Already registered? <a href="/login.php" class="text-primary-600 font-bold hover:underline">Log In to your Account</a>
        </div>
    </div>
</div>

<script>
function toggleChildrenField() {
    const status = document.getElementById('marital_status').value;
    const container = document.getElementById('children_container');
    if (status === 'Divorced' || status === 'Widower / Widow') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
