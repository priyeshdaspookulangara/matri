<?php
// includes/helpers.php - General Helper Functions

declare(strict_types=1);

function sanitize(string|null $data): string {
    if ($data === null) {
        return '';
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function calculateAge(string $dob): int {
    try {
        $birthDate = new DateTime($dob);
        $today = new DateTime('today');
        return $birthDate->diff($today)->y;
    } catch (Exception $e) {
        return 0;
    }
}

function formatHeight(int|string|null $cm): string {
    if (!$cm || !is_numeric($cm)) {
        return 'Not Specified';
    }
    $totalInches = round((float)$cm / 2.54);
    $feet = floor($totalInches / 12);
    $inches = $totalInches % 12;
    return sprintf("%d cm (%d'%d\")", $cm, $feet, $inches);
}

function getAllDistricts(): array {
    return [
        'Alappuzha',
        'Ernakulam',
        'Idukki',
        'Kannur',
        'Kottayam',
        'Kollam',
        'Kozhikode',
        'Kasaragod',
        'Malappuram',
        'Palakkad',
        'Pathanamthitta',
        'Thiruvananthapuram',
        'Thrissur',
        'Wayanad',
        'Other'
    ];
}

function getAllCastes(): array {
    return ['Ezhava', 'Thiyya'];
}

function getAllEduCategories(): array {
    return [
        'Ph.D / Doctorate',
        'Masters',
        'Bachelors',
        'Diploma',
        'Higher Secondary',
        'Secondary (SSLC)',
        'Other'
    ];
}

function getAllEmployedIn(): array {
    return [
        'Abroad',
        'Agriculture',
        'Business / Self Employed',
        'Government',
        'MNC',
        'Networking',
        'Own Firm',
        'Public Ltd',
        'Private',
        'Professionals'
    ];
}

function getAllOccupations(): array {
    return [
        'Accountant',
        'Architect',
        'Bank Officer',
        'Civil Services / IAS / IPS',
        'Doctor',
        'Defense / Police',
        'Engineer',
        'Executive / Manager',
        'Lawyer / Legal',
        'Nurse / Healthcare',
        'Professor / Lecturer',
        'Scientist / Researcher',
        'Software Engineer / IT',
        'Teacher',
        'Business / Entrepreneur',
        'Other'
    ];
}

function generateProfileId(PDO $pdo): string {
    // Generate next unique profile ID like GV1009, GV1010...
    $stmt = $pdo->query("SELECT MAX(id) AS max_id FROM users");
    $row = $stmt->fetch();
    $nextId = ($row && $row['max_id']) ? ((int)$row['max_id'] + 1) : 1001;
    if ($nextId < 1000) {
        $nextId += 1000;
    }
    return 'GV' . $nextId;
}
