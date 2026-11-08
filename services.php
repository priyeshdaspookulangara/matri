<?php
// services.php - Matrimonial Services
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = "Services - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-primary-600 bg-orange-100 px-3 py-1 rounded-full">Dedicated Support</span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950 mt-3">
                Our Matrimonial Services
            </h1>
            <p class="text-slate-600 text-sm mt-2">
                End-to-end matchmaking services specifically crafted for the Ezhava &amp; Thiyya community.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-orange-100 text-primary-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-id-card"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Strict Profile Verification</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        We screen and verify phone numbers and credentials before approving profiles to maintain highest security standards and eliminate spam.
                    </p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-video"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Intro Video Gallery</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Members can upload short authentic video introductions, helping families appreciate personality, poise, and spoken communication naturally.
                    </p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Personalized Relationship Managers</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        For busy professionals and Gulf NRI families, our experienced relationship managers handpick matching profiles and initiate initial family dialogues.
                    </p>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-moon"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Kerala Jathakam / Horoscope Compatibility</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Connect with verified traditional Kerala astrologers to compute Porutham, Papasamyam, and Dasa Sandhi for astrological compatibility.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
