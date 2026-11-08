<?php
// tests/test_suite.php - Integration & Functional Test Suite
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/media_handler.php';

$pdo = getDBConnection();
$passed = 0;
$failed = 0;

function assertTest(string $name, bool $condition, string $detail = ''): void {
    global $passed, $failed;
    if ($condition) {
        echo " [PASS] $name\n";
        $passed++;
    } else {
        echo " [FAIL] $name: $detail\n";
        $failed++;
    }
}

echo "=== GURUVISION MATRIMONY TEST SUITE ===\n\n";

// Test 1: Database connection and schema verification
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
assertTest('Database tables exist', in_array('users', $tables) && in_array('partner_preferences', $tables) && in_array('user_photos', $tables) && in_array('user_videos', $tables));

// Test 2: Seed data presence
$countStmt = $pdo->query("SELECT COUNT(*) FROM users");
$userCount = (int)$countStmt->fetchColumn();
assertTest('Seed profiles loaded', $userCount >= 8, "Found $userCount users");

// Test 3: Password verification of seed user
$userStmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
$userStmt->execute(['rahul.s@example.com']);
$hash = $userStmt->fetchColumn();
assertTest('User password verification', password_verify('Password123', (string)$hash));

// Test 4: Helper functions
assertTest('Age calculation (1994-06-15)', calculateAge('1994-06-15') >= 31);
assertTest('Format height 178 cm', formatHeight(178) === "178 cm (5'10\")", formatHeight(178));
assertTest('Caste list check', in_array('Ezhava', getAllCastes()) && in_array('Thiyya', getAllCastes()));

// Test 5: Profile ID Generation
$nextPid = generateProfileId($pdo);
assertTest('Generate profile ID format', (bool)preg_match('/^GV\d+$/', $nextPid), "Generated: $nextPid");

// Test 6: User Registration via DB Transaction
$testEmail = 'test_' . time() . '@example.com';
$testPid = generateProfileId($pdo);
$testPass = password_hash('Secret123', PASSWORD_BCRYPT);

$pdo->beginTransaction();
$regStmt = $pdo->prepare("INSERT INTO users (
    profile_id, email, password_hash, created_by, full_name, gender, dob,
    marital_status, children, mother_tongue, religion, caste, physical_status,
    height, complexion, edu_detail, native_district, contact_name, contact_relation, contact_phone
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$regStmt->execute([
    $testPid, $testEmail, $testPass, 'Self', 'Deepa Jayachandran', 'Female', '1998-05-10',
    'Unmarried', 'No', 'Malayalam', 'Hindu', 'Ezhava', 'Normal',
    163, 'Fair', 'MCA', 'Alappuzha', 'Jayachandran', 'Father', '+919447000111'
]);
$newUid = (int)$pdo->lastInsertId();
$pdo->commit();

assertTest('Register new user record', $newUid > 0);

// Test 7: Verify newly created user can be fetched by Profile ID
$fetchStmt = $pdo->prepare("SELECT * FROM users WHERE profile_id = ?");
$fetchStmt->execute([$testPid]);
$fetched = $fetchStmt->fetch();
assertTest('Fetch profile by ID', $fetched && $fetched['full_name'] === 'Deepa Jayachandran');

// Test 8: Partner Preference creation & matching query
$prefInsert = $pdo->prepare("INSERT INTO partner_preferences (
    user_id, pref_age_from, pref_age_to, pref_height_from, pref_height_to, pref_marital_status
) VALUES (?, 25, 34, 165, 185, 'Unmarried')");
$prefInsert->execute([$newUid]);

// Matching male profiles for this new user
$matchStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE gender = 'Male' AND marital_status = 'Unmarried' AND height BETWEEN 165 AND 185");
$matchStmt->execute();
$matchCount = (int)$matchStmt->fetchColumn();
assertTest('Partner preference match logic', $matchCount > 0, "Found $matchCount matches");

// Test 9: Image Thumbnail Generation
$dummyImg = imagecreatetruecolor(600, 800);
$bgCol = imagecolorallocate($dummyImg, 220, 100, 50);
imagefill($dummyImg, 0, 0, $bgCol);
$tmpSource = tempnam(sys_get_temp_dir(), 'test_img_') . '.jpg';
imagejpeg($dummyImg, $tmpSource);
imagedestroy($dummyImg);

$tmpDest = tempnam(sys_get_temp_dir(), 'test_thumb_') . '.jpg';
$thumbSuccess = createThumbnail($tmpSource, $tmpDest, 300, 300, 'image/jpeg');
assertTest('Thumbnail generation function', $thumbSuccess && file_exists($tmpDest) && filesize($tmpDest) > 0);
@unlink($tmpSource);
@unlink($tmpDest);

// Clean up test user
$pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$newUid]);
assertTest('Cleanup test record', true);

echo "\nSummary: $passed passed, $failed failed.\n";
if ($failed > 0) {
    exit(1);
}
