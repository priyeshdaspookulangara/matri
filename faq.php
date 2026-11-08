<?php
// faq.php - Frequently Asked Questions
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$faqs = [
    [
        'q' => 'Is registration completely free on Guruvision Matrimony?',
        'a' => 'Yes! Initial registration, creating your profile, uploading up to 10 photos, uploading an introductory video, and browsing profiles are 100% free of charge.'
    ],
    [
        'q' => 'Who can register on this matrimony portal?',
        'a' => 'Guruvision Matrimony is exclusively dedicated to brides and grooms belonging to the Hindu Ezhava and Thiyya communities from Kerala and abroad.'
    ],
    [
        'q' => 'Who can view my contact phone number and address?',
        'a' => 'To protect the privacy of families, personal phone numbers and home addresses are never shown to anonymous guests. They are only viewable by registered, authenticated members.'
    ],
    [
        'q' => 'How does the Partner Preference recommendation system work?',
        'a' => 'When you configure your partner preferences (such as preferred age range, height, Kerala districts, education, and profession), Guruvision automatically matches opposite gender profiles and displays them on your homepage and dashboard.'
    ],
    [
        'q' => 'What format is supported for introduction videos?',
        'a' => 'We support standard MP4 and WebM video formats with a maximum file size of 25 MB. All files are securely processed and given unique identifier names to prevent unauthorized exposure.'
    ],
    [
        'q' => 'How can I contact customer support if I need assistance?',
        'a' => 'You can call our Ernakulam helpline directly at +91 484 4042401 or mobile/WhatsApp at +91 97 44 222772, Monday through Saturday between 9:00 AM and 6:30 PM.'
    ]
];

$pageTitle = "Frequently Asked Questions - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">

        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-primary-600 bg-orange-100 px-3 py-1 rounded-full">Help &amp; Answers</span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950 mt-3">
                Frequently Asked Questions
            </h1>
            <p class="text-slate-600 text-sm mt-2">
                Everything you need to know about using Guruvision Matrimony.
            </p>
        </div>

        <div class="space-y-4">
            <?php foreach ($faqs as $item): ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="font-bold text-slate-900 text-base mb-2 flex items-start gap-3">
                        <i class="fa-solid fa-circle-question text-primary-600 mt-1"></i>
                        <span><?= sanitize($item['q']) ?></span>
                    </h3>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed pl-7">
                        <?= sanitize($item['a']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 text-center">
            <p class="text-sm text-slate-600">Still have questions? Our support team is happy to help.</p>
            <a href="/contact.php" class="inline-block mt-3 text-xs font-bold text-primary-700 hover:underline">
                Contact Guruvision Support &rarr;
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
