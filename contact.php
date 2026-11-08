<?php
// contact.php - Contact Us Page
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$messageSent = false;
$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verifyCsrfToken($_POST['csrf_token'] ?? null)) {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $msg = trim($_POST['message'] ?? '');

        if (!empty($name) && !empty($email) && !empty($msg)) {
            $messageSent = true;
            $feedback = "Thank you {$name}! Your message has been received. Our support team will get in touch with you shortly.";
        }
    }
}

$pageTitle = "Contact Us - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-pattern min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-primary-600 bg-orange-100 px-3 py-1 rounded-full">We Are Here For You</span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950 mt-3">
                Contact Guruvision Matrimony
            </h1>
            <p class="text-slate-600 text-sm mt-2">
                Get in touch with our relationship advisors in Ernakulam for personalized assistance.
            </p>
        </div>

        <?php if ($messageSent): ?>
            <div class="mb-8 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm text-center">
                <i class="fa-solid fa-circle-check text-emerald-600 text-xl block mb-1"></i>
                <?= sanitize($feedback) ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Contact Info Cards -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <h2 class="font-bold text-slate-900 text-lg border-b border-slate-100 pb-3">Office Details</h2>

                    <div class="flex items-start gap-4 text-sm text-slate-600">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-primary-600 flex items-center justify-center shrink-0 text-base">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">Corporate Office</span>
                            <p class="text-xs">Guruvision Towers, 3rd Floor, MG Road, Ernakulam, Kerala 682016, India</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 text-sm text-slate-600">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 text-base">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">Telephone Helpline</span>
                            <p class="text-xs font-mono font-bold text-slate-800"><a href="tel:+914844042401" class="hover:underline">+91 484 4042401</a></p>
                            <p class="text-xs font-mono font-bold text-slate-800"><a href="tel:+919744222772" class="hover:underline">+91 97 44 222772 (WhatsApp)</a></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 text-sm text-slate-600">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 text-base">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">Email Support</span>
                            <p class="text-xs"><a href="mailto:support@guruvisionmatrimony.com" class="hover:underline">support@guruvisionmatrimony.com</a></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 text-sm text-slate-600">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 text-base">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 block mb-0.5">Working Hours</span>
                            <p class="text-xs">Monday &ndash; Saturday: 9:00 AM &ndash; 6:30 PM (IST)</p>
                            <p class="text-xs text-slate-400">Sunday Closed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="lg:col-span-7">
                <form action="/contact.php" method="POST" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">

                    <h2 class="font-bold text-slate-900 text-lg border-b border-slate-100 pb-3">Send Us a Message</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Your Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" required class="w-full rounded-xl border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number</label>
                            <input type="tel" name="phone" placeholder="+91 ..." class="w-full rounded-xl border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required class="w-full rounded-xl border-slate-300 border px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Message / Inquiry <span class="text-rose-500">*</span></label>
                        <textarea name="message" rows="4" required placeholder="Write your query or question here..." class="w-full rounded-xl border-slate-300 border px-3.5 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white font-bold text-xs uppercase tracking-wider px-8 py-3 rounded-xl shadow transition">
                        Submit Message
                    </button>
                </form>
            </div>

        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
