<?php
// stories.php - Success Stories
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();
$storiesStmt = $pdo->query("SELECT * FROM success_stories ORDER BY marriage_date DESC");
$stories = $storiesStmt->fetchAll();

$pageTitle = "Success Stories - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-pattern min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-3 py-1 rounded-full">Testimonials</span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950 mt-3">
                Guruvision Matrimony Success Stories
            </h1>
            <p class="text-slate-600 text-sm mt-2">
                Thousands of Ezhava &amp; Thiyya weddings have been blessed through our dedicated platform.
            </p>
        </div>

        <div class="space-y-8">
            <?php foreach ($stories as $story): ?>
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-md flex flex-col md:flex-row items-center gap-8">
                    <div class="w-32 h-32 rounded-2xl bg-gradient-to-tr from-rose-400 to-amber-400 flex items-center justify-center text-white text-4xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <div class="space-y-3 flex-1 text-center md:text-left">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                            <h2 class="font-serif-title text-2xl font-bold text-slate-900">
                                <?= sanitize($story['groom_name']) ?> &amp; <?= sanitize($story['bride_name']) ?>
                            </h2>
                            <span class="text-xs font-bold text-amber-800 bg-amber-100 px-3 py-1 rounded-full">
                                Married: <?= date('F j, Y', strtotime($story['marriage_date'])) ?>
                            </span>
                        </div>
                        <p class="text-slate-700 text-sm leading-relaxed italic">
                            &ldquo;<?= sanitize($story['story']) ?>&rdquo;
                        </p>
                        <div class="text-xs text-emerald-600 font-bold flex items-center justify-center md:justify-start gap-1">
                            <i class="fa-solid fa-circle-check"></i> Verified Guruvision Marriage
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 text-center bg-white rounded-3xl p-8 border border-slate-200">
            <h3 class="font-bold text-slate-900 text-lg">Did you meet your life partner on Guruvision?</h3>
            <p class="text-xs text-slate-500 mt-1 mb-4">We would love to celebrate your story and send you our wedding congratulations gift!</p>
            <a href="/contact.php" class="bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow transition">
                Share Your Success Story
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
