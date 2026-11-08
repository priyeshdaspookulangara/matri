<?php
// about.php - About Us
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = "About Us - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-pattern">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-200">
            <span class="text-xs font-bold uppercase tracking-wider text-primary-600 bg-primary-50 px-3 py-1 rounded-full">Our Heritage</span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950 mt-3 mb-6">
                About Guruvision Matrimony
            </h1>

            <div class="prose text-slate-700 space-y-4 text-sm sm:text-base leading-relaxed">
                <p>
                    <strong>Guruvision Matrimony</strong> was founded with the singular vision of catering exclusively to the matrimonial aspirations of the <strong>Ezhava and Thiyya community</strong> in Kerala, pan-India, and across the globe.
                </p>
                <p>
                    Deeply rooted in the egalitarian philosophy of Sree Narayana Guru (&ldquo;One Caste, One Religion, One God for Man&rdquo;), we believe marriage is a sacred union not only of two individuals, but of values, cultural heritage, and families.
                </p>

                <h3 class="font-bold text-slate-900 text-lg pt-4">Why Guruvision Matrimony?</h3>
                <ul class="list-disc list-inside space-y-2 text-slate-600">
                    <li><strong>100% Community Centric:</strong> Tailored specifically for Ezhava and Thiyya brides and grooms.</li>
                    <li><strong>Strict Verification:</strong> Every profile is verified for authenticity, contact phone numbers, and identity.</li>
                    <li><strong>Privacy &amp; Security:</strong> Robust data privacy controls protecting family contact details and photos.</li>
                    <li><strong>Statewide Reach:</strong> Active members across all 14 districts of Kerala—from Thiruvananthapuram to Kasaragod—as well as the Middle East, UK, US, and Australia.</li>
                </ul>

                <div class="mt-8 p-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900">
                    <h4 class="font-bold text-base mb-1">Our Ernakulam Headquarters</h4>
                    <p class="text-xs sm:text-sm">Guruvision Towers, MG Road, Ernakulam, Kerala 682016</p>
                    <p class="text-xs sm:text-sm mt-1">Helpline: +91 484 4042401, +91 97 44 222772</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
