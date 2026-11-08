<?php
// login.php - Member Login Page
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    header('Location: /dashboard/index.php');
    exit;
}

$errors = [];
$identifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Invalid request token. Please submit the form again.';
    }

    $identifier = trim($_POST['identifier'] ?? ''); // Email or Profile ID
    $password = $_POST['password'] ?? '';

    if (empty($identifier)) {
        $errors[] = 'Please enter your Profile ID or Email Address.';
    }
    if (empty($password)) {
        $errors[] = 'Please enter your password.';
    }

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, profile_id, full_name, email, password_hash, is_premium FROM users WHERE email = ? OR profile_id = ? LIMIT 1");
        $stmt->execute([$identifier, strtoupper($identifier)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login success
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['profile_id'] = $user['profile_id'];
            $_SESSION['full_name'] = $user['full_name'];

            setFlash('success', "Welcome back, {$user['full_name']}!");
            header('Location: /dashboard/index.php');
            exit;
        } else {
            $errors[] = 'Invalid Profile ID / Email or Password. Please try again.';
        }
    }
}

$pageTitle = "Member Login - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-16 bg-pattern min-h-[75vh] flex items-center justify-center">
    <div class="max-w-md w-full mx-4">

        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-primary-600 to-amber-500 text-white shadow-lg mb-4">
                <i class="fa-solid fa-lock text-2xl"></i>
            </div>
            <h1 class="font-serif-title text-3xl font-extrabold text-red-950">
                Member Login
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Access your Guruvision Matrimony account
            </p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?>
                        <li><?= sanitize($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-2xl shadow-xl border border-slate-200/80">
            <form action="/login.php" method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Profile ID or Email Address
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="identifier" value="<?= sanitize($identifier) ?>" required placeholder="e.g. GV1001 or name@example.com" class="w-full rounded-lg border-slate-300 border pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Password
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <input type="password" name="password" required placeholder="Enter password" class="w-full rounded-lg border-slate-300 border pl-10 pr-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="inline-flex items-center text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-primary-600 mr-2">
                        Remember me
                    </label>
                    <a href="/contact.php" class="text-primary-600 hover:underline">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-primary-600 to-amber-600 hover:from-primary-700 hover:to-amber-700 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login Now
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 mb-2">Demo credentials (for testing):</p>
                <div class="bg-amber-50 p-2.5 rounded-lg text-xs text-amber-900 font-mono">
                    ID: <strong>GV1001</strong> | Pass: <strong>Password123</strong>
                </div>
            </div>
        </div>

        <div class="mt-6 text-center text-sm text-slate-600">
            Don't have an account yet? <a href="/register.php" class="text-primary-600 font-bold hover:underline">Register Free</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
