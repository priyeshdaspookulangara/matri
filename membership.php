<?php
// membership.php - Membership Plans & Payment Information
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = "Membership & Payment - Guruvision Matrimony";
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold uppercase tracking-wider text-primary-600 bg-orange-100 px-3 py-1 rounded-full">Transparent Pricing</span>
            <h1 class="font-serif-title text-3xl sm:text-4xl font-extrabold text-red-950 mt-3">
                Membership Packages &amp; Payment
            </h1>
            <p class="text-slate-600 text-sm mt-2">
                Choose the right package to connect with potential brides and grooms faster. Free registration with optional premium upgrades.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Free / Silver -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">Free Silver</h3>
                    <p class="text-xs text-slate-500 mt-1">Get started and browse profiles</p>
                    <div class="mt-4 mb-6">
                        <span class="text-4xl font-extrabold text-slate-900">₹0</span>
                        <span class="text-xs text-slate-500">/ Lifetime</span>
                    </div>
                    <ul class="space-y-3 text-xs text-slate-600">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Free Profile Creation</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Upload up to 10 Photos</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Upload Introduction Video</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Set Partner Preferences</li>
                        <li class="flex items-center gap-2 text-slate-400"><i class="fa-solid fa-xmark"></i> Direct Phone Contact Numbers</li>
                        <li class="flex items-center gap-2 text-slate-400"><i class="fa-solid fa-xmark"></i> Dedicated Marriage Advisor</li>
                    </ul>
                </div>
                <div class="mt-8">
                    <a href="/register.php" class="block text-center w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs py-3 rounded-xl transition">
                        Register Free
                    </a>
                </div>
            </div>

            <!-- Gold Premium (Featured) -->
            <div class="bg-gradient-to-b from-amber-50 to-white rounded-3xl p-8 border-2 border-amber-500 shadow-xl flex flex-col justify-between relative transform md:-translate-y-2">
                <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-amber-500 text-slate-950 text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow">
                    Most Popular
                </span>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg flex items-center gap-2">
                        Premium Gold <i class="fa-solid fa-crown text-amber-500"></i>
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">Direct access &amp; high priority visibility</p>
                    <div class="mt-4 mb-6">
                        <span class="text-4xl font-extrabold text-slate-900">₹3,500</span>
                        <span class="text-xs text-slate-500">/ 6 Months</span>
                    </div>
                    <ul class="space-y-3 text-xs text-slate-700 font-medium">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600"></i> <strong>All Free Features Included</strong></li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600"></i> <strong>View 75 Verified Contact Phone Numbers</strong></li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600"></i> Gold Badge on Profile &amp; Search Results</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600"></i> Featured on Homepage &ldquo;Premium Members&rdquo;</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600"></i> SMS &amp; WhatsApp Notifications</li>
                    </ul>
                </div>
                <div class="mt-8">
                    <a href="/contact.php?plan=gold" class="block text-center w-full bg-gradient-to-r from-primary-600 to-amber-600 hover:from-primary-700 hover:to-amber-700 text-white font-bold text-xs py-3 rounded-xl transition shadow-md">
                        Upgrade to Gold
                    </a>
                </div>
            </div>

            <!-- Platinum VIP -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-lg">VIP Platinum</h3>
                    <p class="text-xs text-slate-500 mt-1">Personalized matchmaking assistance</p>
                    <div class="mt-4 mb-6">
                        <span class="text-4xl font-extrabold text-slate-900">₹7,000</span>
                        <span class="text-xs text-slate-500">/ 1 Year</span>
                    </div>
                    <ul class="space-y-3 text-xs text-slate-600">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Unlimited Contact Phone Numbers</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Dedicated Matchmaking Relationship Manager</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Horoscope Verification Support</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-500"></i> Top-Tier Ranking across all Searches</li>
                    </ul>
                </div>
                <div class="mt-8">
                    <a href="/contact.php?plan=vip" class="block text-center w-full bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs py-3 rounded-xl transition">
                        Enquire VIP Plan
                    </a>
                </div>
            </div>
        </div>

        <!-- Bank & Payment Details -->
        <div class="mt-16 bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
            <h3 class="font-bold text-slate-900 text-lg mb-4 flex items-center gap-2">
                <i class="fa-solid fa-building-columns text-primary-600"></i> Bank Transfer &amp; UPI Payment Details
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-slate-600">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Direct Bank Transfer (NEFT / IMPS)</span>
                    <p><strong>Account Name:</strong> Guruvision Matrimonial Services</p>
                    <p><strong>Account Number:</strong> 50200034981122</p>
                    <p><strong>Bank:</strong> HDFC Bank, MG Road Branch, Kochi</p>
                    <p><strong>IFSC Code:</strong> HDFC0000049</p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Google Pay / PhonePe / UPI</span>
                    <p><strong>UPI ID:</strong> guruvision@hdfcbank</p>
                    <p><strong>Registered Phone:</strong> +91 97 44 222772</p>
                    <p class="text-xs text-slate-500 mt-2">After transferring payment, please send the payment screenshot with your Profile ID to WhatsApp: <strong>+91 97 44 222772</strong> for instant activation.</p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
